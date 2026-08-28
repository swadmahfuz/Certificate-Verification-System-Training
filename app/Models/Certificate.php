<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'training_certificates';

    protected $guarded = [];

    protected $dates = [
        'deleted_at',
        'pdf_uploaded_at',
        'reviewed_at',
        'approved_at',
    ];

    protected $casts = [
        'is_refresher' => 'boolean',
        'has_practical' => 'boolean',
        'internal_audit_training' => 'boolean',
        'online_training' => 'boolean',
    ];

    public function scopeApproved(Builder $query)
    {
        return $query->whereIn('status', ['Approved', 'approved', ' APPROVED']);
    }

    public function scopePendingReview(Builder $query)
    {
        return $query->whereIn('status', ['Pending Review', 'Pending']);
    }

    public function scopePendingApproval(Builder $query)
    {
        return $query->whereIn('status', ['Pending Approval', 'Reviewed']);
    }

    public function scopeAssignedForReview(Builder $query, int $userId)
    {
        return $query->pendingReview()->where('review_by_id', $userId);
    }

    public function scopeAssignedForApproval(Builder $query, int $userId)
    {
        return $query->pendingApproval()->where('approval_by_id', $userId);
    }

    public function scopeAssignedToUser(Builder $query, int $userId)
    {
        return $query->where(function ($builder) use ($userId) {
            $builder->where(function ($inner) use ($userId) {
                $inner->whereIn('status', ['Pending Review', 'Pending'])
                    ->where('review_by_id', $userId);
            })->orWhere(function ($inner) use ($userId) {
                $inner->whereIn('status', ['Pending Approval', 'Reviewed'])
                    ->where('approval_by_id', $userId);
            });
        });
    }

    public function trainerRecord()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    public function signatoryRecord()
    {
        return $this->belongsTo(Signatory::class, 'signatory_id');
    }
}