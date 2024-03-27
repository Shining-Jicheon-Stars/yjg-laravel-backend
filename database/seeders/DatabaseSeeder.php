<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PrivilegeSeeder::class,
            SalonSeeder::class,
            SalonBusinessHourSeeder::class,
            MeetingRoomSeeder::class,
            UserSeeder::class,
            MeetingRoomReservationSeeder::class,
            NoticeSeeder::class,
            AfterServiceSeeder::class,
            BusRouteSeeder::class,
            BusRoundSeeder::class,
            BusScheduleSeeder::class,
            SalonReservationSeeder::class,
            AbsenceListSeeder::class,
        ]);
    }
}
