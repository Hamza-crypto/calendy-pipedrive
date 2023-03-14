<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Task extends Model
{
    use HasFactory;

    public function scopeFilters($query, $filters)
    {
        if (isset($filters['stage']) && $filters['stage'] != -100) {
            $query->where('stage', $filters['stage']);
        }
        if (isset($filters['sms_status']) && $filters['sms_status'] != -100) {
            $query->where('sms_status', $filters['sms_status']);
        }
        if (isset($filters['daterange'])) {
            $dateRange = explode(' - ', $filters['daterange']);
            $query->whereBetween('created_at', [Carbon::parse($dateRange[0])->format('Y-m-d'), Carbon::parse($dateRange[1])->format('Y-m-d')]);
        }

    }
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
}
