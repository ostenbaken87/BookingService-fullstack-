<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service' => $this->whenLoaded('service', function () {
                return new ServiceResource($this->service);
            }),
            'service_id' => $this->service_id,
            'client_name' => $this->client_name,
            'client_phone' => $this->client_phone,
            'start_time' => $this->start_time?->format('Y-m-d H:i:s'),
            'end_time' => $this->end_time?->format('Y-m-d H:i:s'),
            'start_time_formatted' => $this->start_time?->format('d.m.Y H:i'),
            'end_time_formatted' => $this->end_time?->format('d.m.Y H:i'),
            'duration' => $this->duration_in_minutes,
            'status' => $this->status?->value,
            'status_label' => $this->status?->getLabel(),
            'status_color' => $this->status?->getColor(),
            'notes' => $this->notes,
            'is_active' => $this->isActive(),
            'can_be_cancelled' => $this->status?->canBeCancelled(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
