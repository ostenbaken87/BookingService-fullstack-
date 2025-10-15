<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'duration',
        'description',
        'price',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Активные услуги
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Услуги по длительности
     */
    public function scopeByDuration($query, int $duration)
    {
        return $query->where('duration', $duration);
    }

    /**
     * Общая длительность с запасом (услуга + 30 минут)
     */
    public function getTotalDurationAttribute(): int
    {
        return $this->duration + 30;
    }

    /**
     * Проверка на доступность для бронирования. 
     * Условие: время в рабочем диапазоне (10:00-20:00 МСК) и не воскресенье
     */
    public function isAvailableForBooking(\Carbon\Carbon $startTime): bool
    {
        $hour = $startTime->setTimezone('Europe/Moscow')->hour;
        if ($hour < 10 || $hour >= 20) {
            return false;
        }

        if ($startTime->setTimezone('Europe/Moscow')->isSunday()) {
            return false;
        }

        return true;
    }
}
