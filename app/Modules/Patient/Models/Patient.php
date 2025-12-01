<?php

namespace App\Modules\Patient\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_code',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'id_number',
        'insurance_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'notes',
        'medical_history',
        'allergies',
        'blood_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected $appends = ['full_name', 'age'];

    public function getFullNameAttribute()
    {
        return "{$this->last_name} {$this->first_name}";
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->patient_code)) {
                $patient->patient_code = static::generatePatientCode();
            }
        });
    }

    public static function generatePatientCode()
    {
        $date = now()->format('Ymd');
        $lastPatient = static::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastPatient ? ((int) substr($lastPatient->patient_code, -4)) + 1 : 1;

        return 'BN' . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
