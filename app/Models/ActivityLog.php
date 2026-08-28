<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $table = 'training_activity_logs';

    protected $fillable = [
        'event',
        'subject_type',
        'subject_id',
        'causer_id',
        'causer_name',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }
}
