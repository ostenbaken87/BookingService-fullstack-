<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceCollection;
use App\Models\Service;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * Список активных услуг
     */
    public function index(): JsonResponse|ServiceCollection
    {
        $services = Service::active()->get();
        
        return response()->json([
            'success' => true,
            'data' => new ServiceCollection($services),
        ]);
    }

    /**
     * Получить доступные слоты для услуги на конкретную дату
     */
    public function getAvailableSlots(Request $request, int $serviceId): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = Carbon::parse($request->date);

        if ($date->isSunday()) {
            return response()->json([
                'success' => false,
                'message' => 'Бронирование недоступно по воскресеньям',
                'data' => [],
            ]);
        }

        try {
            $slots = $this->bookingService->getAvailableSlots($serviceId, $date);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date->format('Y-m-d'),
                    'day_name' => $date->locale('ru')->dayName,
                    'slots' => $slots,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Услуга не найдена',
            ], 404);
        }
    }

    /**
     * Информация об услуге
     */
    public function show(int $id): JsonResponse
    {
        try {
            $service = Service::active()->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => new ServiceResource($service),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Услуга не найдена',
            ], 404);
        }
    }
}
