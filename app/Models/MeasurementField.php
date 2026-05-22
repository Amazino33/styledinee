<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementField extends Model
{
    protected $fillable = ['name', 'label', 'is_system', 'is_active'];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];
}
