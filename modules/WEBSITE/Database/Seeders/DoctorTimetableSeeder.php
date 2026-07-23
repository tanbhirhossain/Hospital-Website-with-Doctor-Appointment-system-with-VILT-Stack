<?php

namespace Modules\WEBSITE\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WEBSITE\Models\DoctorTimetable;

class DoctorTimetableSeeder extends Seeder
{
    public function run(): void
    {

    $filePath = __DIR__ . '/data/timetables.csv';

        if (!file_exists($filePath)) {
            $this->command->error("CSV file not found at: {$filePath}");
            return;
        }

        $file = fopen($filePath, 'r');
        
        // Fetch the header line from the CSV
        $header = fgetcsv($file);

        $this->command->info('Importing Doctor Timetables...');

        // Read line by line to keep memory footprint safe and handle large items
        while (($row = fgetcsv($file)) !== FALSE) {
            // Combine header keys with row values
            $data = array_combine($header, $row);

            DoctorTimetable::updateOrCreate(
                ['id' => (int)$data['id']],
                [
                    'doctor_id'         => $data['doctor_id'],
                    'day_of_week' => $data['day_of_week'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'slot_duration' => 15,
                    'created_by'   => 1,
                    'updated_by'   => 1,
                ]
            );
        }

        fclose($file);
        $this->command->info('Doctor Timetables successfully imported into SQLite!');
    }
}