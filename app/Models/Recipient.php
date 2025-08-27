<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'email_group_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function group()
    {
        return $this->belongsTo(EmailGroup::class, 'email_group_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 