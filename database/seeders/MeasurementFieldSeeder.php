<?php

namespace Database\Seeders;

use App\Models\MeasurementField;
use Illuminate\Database\Seeder;

class MeasurementFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            ['name' => 'chest',          'label' => 'Chest'],
            ['name' => 'waist',          'label' => 'Waist'],
            ['name' => 'hips',           'label' => 'Hips'],
            ['name' => 'shoulder',       'label' => 'Shoulder Width'],
            ['name' => 'sleeve_length',  'label' => 'Sleeve Length'],
            ['name' => 'neck',           'label' => 'Neck'],
            ['name' => 'trouser_length', 'label' => 'Trouser Length'],
            ['name' => 'inseam',         'label' => 'Inseam'],
            ['name' => 'thigh',          'label' => 'Thigh'],
            ['name' => 'caftan_length',  'label' => 'Caftan Length'],
            ['name' => 'agbada_length',  'label' => 'Agbada Length'],
            ['name' => 'dress_length',   'label' => 'Dress Length'],
            ['name' => 'bust',           'label' => 'Bust'],
            ['name' => 'top_length',     'label' => 'Top Length'],
            ['name' => 'gown_length',    'label' => 'Gown Length'],
            ['name' => 'ankle',          'label' => 'Ankle'],
            ['name' => 'knee',           'label' => 'Knee'],
            ['name' => 'cap_size',       'label' => 'Cap Size'],
        ];

        foreach ($fields as $field) {
            MeasurementField::firstOrCreate(
                ['name' => $field['name']],
                ['label' => $field['label'], 'is_system' => true, 'is_active' => true]
            );
        }

        $this->command->info('Measurement fields seeded.');
    }
}
