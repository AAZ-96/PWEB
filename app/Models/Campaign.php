<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'cat',
        'img',
        'location',
        'fundraiser',
        'desc',
        'target',
        'collected',
        'days',
        'start_date',
        'status',
        'pic_name',
        'pic_phone',
        'social',
        'budget',
        'created_by'
    ];

    protected $casts = [
        'budget' => 'array',
        'start_date' => 'date',
    ];

    protected $appends = ['donors'];

    public function getDonorsAttribute()
    {
        return $this->donations()
            ->where('status', 'success')
            ->distinct()
            ->count('user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
