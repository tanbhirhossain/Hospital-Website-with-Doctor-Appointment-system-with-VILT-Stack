<?php

namespace Modules\WEBSITE\Database\Seeders;
use Illuminate\Database\Seeder;
use Modules\WEBSITE\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = __DIR__ . '/departments.csv';
        if (!file_exists($filePath)) {
            $this->command->error("CSV file not found at: {$filePath}");
            return;
        }

        $file = fopen($filePath, 'r');
        
        // Fetch the header line from the CSV
        $header = fgetcsv($file);

        $this->command->info('Importing departments...');

        // Read line by line to keep memory footprint safe and handle large items
        while (($row = fgetcsv($file)) !== FALSE) {
            // Combine header keys with row values
            $data = array_combine($header, $row);

            Department::updateOrCreate(
                ['id' => (int)$data['id']],
                [
                    'name'         => $data['name'],
                    'slug'         => $data['slug'],
                    'descriptions' => $data['descriptions'],
                    // Standardize empty spaces or missing properties to null
                    'tagline'      => !empty($data['tagline']) ? $data['tagline'] : null,
                    'serial'       => $data['serial'] !== '' ? (float)$data['serial'] : null,
                    'is_active'    => (bool)$data['is_active'],
                    'created_by'   => (int)$data['created_by'],
                    'updated_by'   => (int)$data['updated_by'],
                ]
            );
        }

        fclose($file);
        $this->command->info('Departments successfully imported into SQLite!');
    }
}