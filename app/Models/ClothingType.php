<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClothingType extends Model
{
    protected $fillable = ['name', 'measurement_field_ids', 'unit', 'is_active'];

    protected $casts = [
        'measurement_field_ids' => 'array',
        'is_active'             => 'boolean',
    ];

    public function measurementFields()
    {
        return MeasurementField::whereIn('id', $this->measurement_field_ids ?? [])->orderBy('label')->get();
    }

    public function customerMeasurements()
    {
        return $this->hasMany(CustomerMeasurement::class);
    }
}
