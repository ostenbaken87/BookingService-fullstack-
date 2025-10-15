<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Enums\BookingStatus;

class Booking extends Model
{
    protected $fillable = [
        'service_id',
        'client_name',
        'client_phone',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => BookingStatus::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Подтвержденные бронирования
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', BookingStatus::CONFIRMED);
    }

    /**
     * Бронирования в определенном временном диапазоне
     */
    public function scopeInTimeRange($query, Carbon $startTime, Carbon $endTime)
    {
        return $query->where(function ($q) use ($startTime, $endTime) {
            $q->whereBetween('start_time', [$startTime, $endTime])
              ->orWhereBetween('end_time', [$startTime, $endTime])
              ->orWhere(function ($q2) use ($startTime, $endTime) {
                  $q2->where('start_time', '<=', $startTime)
                     ->where('end_time', '>=', $endTime);
              });
        });
    }

    /**
     * Бронирования для конкретной услуги в заданном временном диапазоне
     */
    public function scopeForServiceInTimeRange($query, int $serviceId, Carbon $startTime, Carbon $endTime)
    {
        return $query->where('service_id', $serviceId)
                    ->confirmed()
                    ->inTimeRange($startTime, $endTime);
    }

    /**
     * Проверка на пересечение с другим временным диапазоном
     */
    public function overlapsWith(Carbon $startTime, Carbon $endTime): bool
    {
        return $this->start_time < $endTime && $this->end_time > $startTime;
    }

    /**
     * Длительность бронирования в минутах
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
    }

    /**
     * Проверка на активность бронирования
     */
    public function isActive(): bool
    {
        return $this->status === BookingStatus::CONFIRMED && $this->end_time->isFuture();
    }
}
