<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'certificates_training_trainers';

    /**
     * The attributes that may be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'designation',
        'signature_path',
        'is_active',
        'created_by_id',
    ];

    /**
     * Attribute type conversions.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Always store trainer email addresses in lowercase.
     *
     * @param  string  $value
     * @return void
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    /**
     * User who created the trainer record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}