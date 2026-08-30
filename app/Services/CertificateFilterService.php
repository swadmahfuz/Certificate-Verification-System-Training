<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class CertificateFilterService
{
    public function expiryColumn(): string
    {
        return config('cvs.certificate_filters.expiry_column', 'expiry_date');
    }

    public function applyFilter(Builder $query, ?string $filter): Builder
    {
        $filter = $filter ?? '';

        if ($filter === '' || $filter === 'all') {
            return $query;
        }

        $column = $this->expiryColumn();
        $today = now()->format('Y-m-d');

        switch ($filter) {
            case 'approved':
                return $query->approved()
                    ->where(function (Builder $builder) use ($column, $today) {
                        $builder->whereNull($column)
                            ->orWhere($column, '>=', $today);
                    });

            case 'expired':
                return $query->whereNotNull($column)
                    ->where($column, '<', $today);

            case 'expiring_30':
                return $this->applyExpiringWithin($query, 30);

            case 'expiring_60':
                return $this->applyExpiringWithin($query, 60);

            case 'expiring_90':
                return $this->applyExpiringWithin($query, 90);

            default:
                return $query;
        }
    }

    public function applyExpiringWithin(Builder $query, int $days): Builder
    {
        $column = $this->expiryColumn();
        $today = now()->format('Y-m-d');
        $until = now()->addDays($days)->format('Y-m-d');

        return $query->approved()
            ->whereNotNull($column)
            ->whereBetween($column, [$today, $until]);
    }

    public function countExpiringWithin(Builder $query, int $days): int
    {
        return $this->applyExpiringWithin(clone $query, $days)->count();
    }

    public function countExpired(Builder $query): int
    {
        $column = $this->expiryColumn();
        $today = now()->format('Y-m-d');

        return $query->whereNotNull($column)
            ->where($column, '<', $today)
            ->count();
    }

    public function countApproved(Builder $query): int
    {
        $column = $this->expiryColumn();
        $today = now()->format('Y-m-d');

        return $query->approved()
            ->where(function (Builder $builder) use ($column, $today) {
                $builder->whereNull($column)
                    ->orWhere($column, '>=', $today);
            })
            ->count();
    }

    /** @return array{title: string, subtitle: string} */
    public function filterLabels(?string $filter): array
    {
        return match ($filter) {
            'approved' => [
                'title' => 'Approved Certificates',
                'subtitle' => 'Approved certificates that have not expired.',
            ],
            'expired' => [
                'title' => 'Expired Certificates',
                'subtitle' => 'Certificates with an expiry date in the past.',
            ],
            'expiring_30' => [
                'title' => 'Expiring in 30 Days',
                'subtitle' => 'Approved certificates expiring within the next 30 days.',
            ],
            'expiring_60' => [
                'title' => 'Expiring in 60 Days',
                'subtitle' => 'Approved certificates expiring within the next 60 days.',
            ],
            'expiring_90' => [
                'title' => 'Expiring in 90 Days',
                'subtitle' => 'Approved certificates expiring within the next 90 days.',
            ],
            default => [
                'title' => 'Certificates',
                'subtitle' => 'Search, verify, and manage certificates.',
            ],
        };
    }
}
