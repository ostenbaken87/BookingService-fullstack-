<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BookingStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('client_name');
            $table->string('client_phone');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->enum('status', BookingStatus::values())->default(BookingStatus::CONFIRMED->value);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Индексы для проверки пересечений
            $table->index(['service_id', 'start_time', 'end_time']);
            $table->index(['start_time', 'end_time']);
            $table->index('status');
            
            // Индекс для предотвращения дублирования
            $table->unique(['service_id', 'start_time', 'end_time', 'status'], 'unique_booking_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
