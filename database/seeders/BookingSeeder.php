<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Booking;
use Carbon\Carbon;
use App\Enums\BookingStatus;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем услуги согласно тестовому заданию
        $quadBike30 = Service::create([
            'name' => 'Поездка на квадроцикле',
            'duration' => 30,
            'description' => 'Увлекательная поездка на квадроцикле по пересеченной местности (30 минут)',
            'price' => 2500.00,
            'is_active' => true,
        ]);

        $quadBike60 = Service::create([
            'name' => 'Поездка на квадроцикле',
            'duration' => 60,
            'description' => 'Увлекательная поездка на квадроцикле по пересеченной местности (60 минут)',
            'price' => 4000.00,
            'is_active' => true,
        ]);

        $enduro60 = Service::create([
            'name' => 'Тур на эндуро',
            'duration' => 60,
            'description' => 'Экстремальный тур на эндуро мотоцикле (60 минут)',
            'price' => 3500.00,
            'is_active' => true,
        ]);

        $enduro120 = Service::create([
            'name' => 'Тур на эндуро',
            'duration' => 120,
            'description' => 'Экстремальный тур на эндуро мотоцикле (120 минут)',
            'price' => 6000.00,
            'is_active' => true,
        ]);

        // Создаем занятые слоты согласно тестовому заданию
        // 16.10 - Поездка на квадроцикле 30 минут в 13:00 и 16:00
        Booking::create([
            'service_id' => $quadBike30->id,
            'client_name' => 'Иван Петров',
            'client_phone' => '+7 (999) 123-45-67',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 13:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 14:00'), // 30 мин + 30 мин запас
            'status' => BookingStatus::CONFIRMED,
        ]);

        Booking::create([
            'service_id' => $quadBike30->id,
            'client_name' => 'Мария Сидорова',
            'client_phone' => '+7 (999) 234-56-78',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 16:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 17:00'),
            'status' => BookingStatus::CONFIRMED,
        ]);

        // 16.10 - Поездка на квадроцикле 60 минут в 10:00
        Booking::create([
            'service_id' => $quadBike60->id,
            'client_name' => 'Алексей Козлов',
            'client_phone' => '+7 (999) 345-67-89',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 10:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 11:30'), // 60 мин + 30 мин запас
            'status' => BookingStatus::CONFIRMED,
        ]);

        // 16.10 - Тур на эндуро 60 минут в 10:00, 11:30, 18:30
        Booking::create([
            'service_id' => $enduro60->id,
            'client_name' => 'Дмитрий Волков',
            'client_phone' => '+7 (999) 456-78-90',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 10:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 11:30'),
            'status' => BookingStatus::CONFIRMED,
        ]);

        Booking::create([
            'service_id' => $enduro60->id,
            'client_name' => 'Елена Морозова',
            'client_phone' => '+7 (999) 567-89-01',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 11:30'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 13:00'),
            'status' => BookingStatus::CONFIRMED,
        ]);

        Booking::create([
            'service_id' => $enduro60->id,
            'client_name' => 'Сергей Новиков',
            'client_phone' => '+7 (999) 678-90-12',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 18:30'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-16 20:00'),
            'status' => BookingStatus::CONFIRMED,
        ]);

        // 17.10 - Поездка на квадроцикле 30 минут в 10:00, 11:00, 13:00, 18:00
        Booking::create([
            'service_id' => $quadBike30->id,
            'client_name' => 'Анна Соколова',
            'client_phone' => '+7 (999) 789-01-23',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 10:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 11:00'),
            'status' => BookingStatus::CONFIRMED,
        ]);

        Booking::create([
            'service_id' => $quadBike30->id,
            'client_name' => 'Павел Лебедев',
            'client_phone' => '+7 (999) 890-12-34',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 11:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 12:00'),
            'status' => BookingStatus::CONFIRMED,
        ]);

        Booking::create([
            'service_id' => $quadBike30->id,
            'client_name' => 'Ольга Кузнецова',
            'client_phone' => '+7 (999) 901-23-45',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 13:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 14:00'),
            'status' => BookingStatus::CONFIRMED,
        ]);

        Booking::create([
            'service_id' => $quadBike30->id,
            'client_name' => 'Николай Орлов',
            'client_phone' => '+7 (999) 012-34-56',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 18:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 19:00'),
            'status' => BookingStatus::CONFIRMED,
        ]);

        // 17.10 - Тур на эндуро 120 минут в 14:00
        Booking::create([
            'service_id' => $enduro120->id,
            'client_name' => 'Владимир Соколов',
            'client_phone' => '+7 (999) 123-45-78',
            'start_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 14:00'),
            'end_time' => Carbon::createFromFormat('Y-m-d H:i', '2024-10-17 16:30'), // 120 мин + 30 мин запас
            'status' => BookingStatus::CONFIRMED,
        ]);

        $this->command->info('Тестовые данные успешно загружены!');
        $this->command->info('Создано услуг: ' . Service::count());
        $this->command->info('Создано бронирований: ' . Booking::count());
    }
}
