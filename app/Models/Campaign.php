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
        $mockDonors = [
            1 => 324,
            2 => 892,
            3 => 567,
            4 => 214,
            5 => 448,
            6 => 1205,
            7 => 378,
            8 => 2341,
            9 => 612,
            10 => 291,
            11 => 987,
            12 => 176,
            13 => 820,
            14 => 550,
            15 => 200,
            16 => 75
        ];
        $base = $mockDonors[$this->id] ?? 0;
        return $base + $this->donations()->count();
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
