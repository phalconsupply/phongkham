<?php

namespace App\Modules\Prescription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'medication_name',
        'dosage',
        'frequency',
        'duration',
        'route',
        'quantity',
        'unit',
        'instructions',
    ];

    // Relationship
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
