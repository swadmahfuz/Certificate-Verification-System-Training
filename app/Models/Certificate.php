<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "certificates";

    protected $guarded = [];

    protected $dates = [
        'deleted_at',
        'pdf_uploaded_at',
        'reviewed_at',
        'approved_at',
    ];
}
