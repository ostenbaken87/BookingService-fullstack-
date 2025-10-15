<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class BookingService
{
    /**
     * Проверить доступность слота для бронирования
     */
    public function isSlotAvailable(int $serviceId, Carbon $startTime, Carbon $endTime): bool
    {
        $service = Service::findOrFail($serviceId);
        
        // Проверяем базовые правила
        if (!$service->isAvailableForBooking($startTime)) {
            return false;
        }
        
        // Проверяем пересечения с существующими бронированиями
        $conflictingBookings = Booking::forServiceInTimeRange($serviceId, $startTime, $endTime)->count();
        
        return $conflictingBookings === 0;
    }

    /**
     * Получить доступные слоты для услуги на конкретную дату
     */
    public function getAvailableSlots(int $serviceId, Carbon $date): array
    {
        $service = Service::findOrFail($serviceId);
        $slots = [];
        
        // Генерируем слоты с 10:00 до 20:00 с интервалом 30 минут
        $startHour = 10;
        $endHour = 20;
        
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $slotStart = $date->copy()->setTime($hour, $minute);
                $slotEnd = $slotStart->copy()->addMinutes($service->total_duration);
                
                // Проверяем, что слот не выходит за рабочие часы
                if ($slotEnd->hour >= $endHour) {
                    continue;
                }
                
                // Проверяем доступность слота
                if ($this->isSlotAvailable($serviceId, $slotStart, $slotEnd)) {
                    $slots[] = [
                        'start_time' => $slotStart->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'start_datetime' => $slotStart->toISOString(),
                        'end_datetime' => $slotEnd->toISOString(),
                        'duration' => $service->total_duration,
                    ];
                }
            }
        }
        
        return $slots;
    }

    /**
     * Создать бронирование с проверкой race condition
     */
    public function createBooking(array $data): Booking
    {
        $service = Service::findOrFail($data['service_id']);
        $startTime = Carbon::parse($data['start_time']);
        $endTime = $startTime->copy()->addMinutes($service->total_duration);
        
        // Проверяем доступность перед созданием
        if (!$this->isSlotAvailable($data['service_id'], $startTime, $endTime)) {
            throw new \Exception('Выбранный слот недоступен для бронирования');
        }
        
        try {
            return DB::transaction(function () use ($data, $startTime, $endTime) {
                // Проверяем не забронирован ли слот другим пользователем
                $conflictingBookings = Booking::forServiceInTimeRange(
                    $data['service_id'], 
                    $startTime, 
                    $endTime
                )->count();
                
                if ($conflictingBookings > 0) {
                    throw new \Exception('Слот был забронирован другим пользователем');
                }
                
                // Создаем бронирование
                return Booking::create([
                    'service_id' => $data['service_id'],
                    'client_name' => $data['client_name'],
                    'client_phone' => $data['client_phone'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => BookingStatus::CONFIRMED,
                    'notes' => $data['notes'] ?? null,
                ]);
            });
        } catch (QueryException $e) {
            if ($e->getCode() === '23505') {
                throw new \Exception('Слот был забронирован другим пользователем');
            }
            throw $e;
        }
    }

    /**
     * Получить бронирования для недели
     */
    public function getWeekBookings(Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->addDays(6)->endOfDay();
        
        return Booking::with('service')
            ->confirmed()
            ->whereBetween('start_time', [$weekStart, $weekEnd])
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($booking) {
                return $booking->start_time->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Получить статистику бронирований
     */
    public function getBookingStats(): array
    {
        $totalBookings = Booking::confirmed()->count();
        $todayBookings = Booking::confirmed()
            ->whereDate('start_time', today())
            ->count();
        $weekBookings = Booking::confirmed()
            ->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        
        return [
            'total' => $totalBookings,
            'today' => $todayBookings,
            'this_week' => $weekBookings,
        ];
    }
}
