<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Certificate;
use App\Models\Trainer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    public function data(): array
    {
        $ttl = config('cvs.cache_ttl.dashboard', 300);
        $userId = Auth::id() ?? 0;

        return Cache::remember(
            'cvs.dashboard.' . config('cvs.app_key') . '.' . $userId,
            $ttl,
            fn () => $this->buildData()
        );
    }

    private function buildData(): array
    {
        $total = Certificate::count();
        $pendingReview = Certificate::pendingReview()->count();
        $pendingApproval = Certificate::pendingApproval()->count();
        $expired = Certificate::approved()
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->format('Y-m-d'))
            ->count();
        $approved = Certificate::approved()
            ->where(function ($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>=', now()->format('Y-m-d'));
            })
            ->count();

        $statusCounts = [
            'Approved' => $approved,
            'Pending Review' => $pendingReview,
            'Pending Approval' => $pendingApproval,
            'Expired' => $expired,
        ];

        $myAssignments = $this->myAssignments();

        return [
            'stats' => [
                'total' => $total,
                'approved' => $approved,
                'pending_review' => $pendingReview,
                'pending_approval' => $pendingApproval,
                'expired' => $expired,
                'active_trainers' => Trainer::where('is_active', true)->count(),
            ],
            'myAssignments' => $myAssignments,
            'percentages' => collect($statusCounts)->map(function ($count) use ($total) {
                return $total > 0 ? round(($count / $total) * 100, 1) : 0;
            })->all(),
            'statusChart' => [
                'labels' => array_keys($statusCounts),
                'values' => array_values($statusCounts),
            ],
            'monthlyChart' => $this->monthlyIssues(),
            'recentCertificates' => Certificate::latest('created_at')->limit(5)->get([
                'id',
                'certificate_number',
                'participant_name',
                'status',
                'issue_date',
            ]),
            'recentActivities' => $this->recentActivities(),
            'expiringSoon' => $this->expiringSoon(),
        ];
    }

    /**
     * Counts of certificates waiting on the current (or given) user.
     */
    public function myAssignments(?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return [
                'review' => 0,
                'approval' => 0,
                'total' => 0,
            ];
        }

        $review = Certificate::assignedForReview($userId)->count();
        $approval = Certificate::assignedForApproval($userId)->count();

        return [
            'review' => $review,
            'approval' => $approval,
            'total' => $review + $approval,
        ];
    }

    private function monthlyIssues(): array
    {
        $months = collect(range(11, 0))->map(function ($monthsAgo) {
            return now()->startOfMonth()->subMonths($monthsAgo);
        });

        $start = $months->first()->format('Y-m-d');

        $driver = DB::connection()->getDriverName();
        $monthExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m', issue_date)"
            : "DATE_FORMAT(issue_date, '%Y-%m')";

        $counts = Certificate::query()
            ->whereNotNull('issue_date')
            ->where('issue_date', '>=', $start)
            ->selectRaw($monthExpression . ' as month_key, COUNT(*) as total')
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        return [
            'labels' => $months->map->format('M')->all(),
            'values' => $months->map(function ($month) use ($counts) {
                return (int) $counts->get($month->format('Y-m'), 0);
            })->all(),
        ];
    }

    private function recentActivities()
    {
        $table = config('cvs.app_key', 'training') . '_activity_logs';

        if (!Schema::hasTable($table)) {
            return collect();
        }

        return ActivityLog::latest('created_at')->limit(6)->get();
    }

    private function expiringSoon(int $withinDays = 30)
    {
        $today = now()->format('Y-m-d');
        $until = now()->addDays($withinDays)->format('Y-m-d');

        return Certificate::approved()
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $until])
            ->orderBy('expiry_date')
            ->limit(10)
            ->get([
                'id',
                'certificate_number',
                'participant_name',
                'expiry_date',
            ]);
    }
}
