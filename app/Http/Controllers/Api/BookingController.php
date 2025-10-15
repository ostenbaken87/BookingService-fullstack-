<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingCollection;
use App\Http\Requests\StoreBookingRequest;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * Создать новое бронирование
     */
    public function store(StoreBookingRequest $request): JsonResponse|BookingResource
    {
        try {
            $validated = $request->validated();
            $booking = $this->bookingService->createBooking($validated);

            return response()->json([
                'success' => true,
                'message' => 'Бронирование успешно создано',
                'data' => new BookingResource($booking->load('service')),
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Получить информацию о бронировании
     */
    public function show(int $id): JsonResponse|BookingResource
    {
        try {
            $booking = Booking::with('service')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => new BookingResource($booking),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Бронирование не найдено',
            ], 404);
        }
    }

    /**
     * Получить бронирования для недели
     */
    public function getWeekBookings(Request $request): JsonResponse|BookingCollection
    {
        $request->validate([
            'week_start' => 'required|date',
        ]);

        $weekStart = Carbon::parse($request->week_start)->startOfDay();
        $bookings = $this->bookingService->getWeekBookings($weekStart);

        return response()->json([
            'success' => true,
            'data' => [
                'week_start' => $weekStart->format('Y-m-d'),
                'week_end' => $weekStart->copy()->addDays(6)->format('Y-m-d'),
                'bookings' => new BookingCollection(collect($bookings)->flatten()),
            ],
        ]);
    }

    /**
     * Получить статистику бронирований
     */
    public function getStats(): JsonResponse
    {
        $stats = $this->bookingService->getBookingStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Отменить бронирование
     */
    public function cancel(int $id): JsonResponse|BookingResource
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if (!$booking->status->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Данное бронирование нельзя отменить',
                ], 400);
            }

            $booking->update(['status' => BookingStatus::CANCELLED]);

            return response()->json([
                'success' => true,
                'message' => 'Бронирование успешно отменено',
                'data' => new BookingResource($booking),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Бронирование не найдено',
            ], 404);
        }
    }
}
