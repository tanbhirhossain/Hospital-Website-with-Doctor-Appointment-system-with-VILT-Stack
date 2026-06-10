<?php

namespace Modules\WEBSITE\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\WEBSITE\Models\Doctor;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = __DIR__ . '/data/doctors.csv';
        if (!file_exists($filePath)) {
            $this->command->error("CSV file not found at: {$filePath}");
            return;
        }

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        $this->command->info('Importing doctors...');

        // FIX: Read line-by-line through a while loop
        while (($row = fgetcsv($file)) !== FALSE) {
            
            // Safe guard against empty rows or trailing lines
            if (empty($row) || $row[0] === null) {
                continue;
            }

            $data = array_combine($header, $row);

            Doctor::updateOrCreate(
                [
                    'id' => (int)$data['id']
                ],
                [
                    'slug'             => $data['slug'],
                    'name'             => $data['name'],
                    'specialty'        => $data['specialty'],
                    'qualifications'   => $data['qualification'],
                    'designation'      => $data['designation'],
                    'institute'        => $data['institute'],
                    'department_id'    => $data['department_id'] !== '' ? (int)$data['department_id'] : null,
                    'biography'        => $data['biography'],
                    'visit_fee'        => $data['visit_fee'] !== '' ? (float)$data['visit_fee'] : 0.00,
                    'phone'            => $data['phone'],
                    'email'            => $data['email'],
                    'mis_code'         => $data['mis_code'],
                    // Note: Ensure your CSV header spells it exactly "isActive" or "is_active" 
                    'is_active'        => isset($data['isActive']) ? (bool)$data['isActive'] : (bool)($data['is_active'] ?? true),
                    'chamber_location' => $data['chamber_location'] ?? null,
                    'created_by'       => 1,  
                    'updated_by'       => 1,
                ]
            );
        }
        
        fclose($file);
        $this->command->info('Doctors successfully imported into SQLite!');
    }
}