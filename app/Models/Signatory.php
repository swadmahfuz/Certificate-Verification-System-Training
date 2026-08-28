<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signatory extends Model
{
    protected $table = 'training_signatories';

    protected $fillable = [
        'name',
        'email',
        'designation',
        'department',
        'signature_path',
        'is_active',
        'created_by_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /// Always store signatory email addresses in lowercase.
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    /// User who created the signatory record.
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}