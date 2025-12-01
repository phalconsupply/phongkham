<?php

namespace App\Modules\Encounter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Patient\Models\Patient;
use App\Models\User;

class Encounter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'encounter_code',
        'patient_id',
        'doctor_id',
        'encounter_date',
        'encounter_time',
        'type',
        'status',
        'chief_complaint',
        'history',
        'examination',
        'diagnosis',
        'treatment_plan',
        'notes',
        'temperature',
        'blood_pressure',
        'heart_rate',
        'respiratory_rate',
        'weight',
        'height',
        'bmi',
        'spo2',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'encounter_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($encounter) {
            if (empty($encounter->encounter_code)) {
                $encounter->encounter_code = self::generateEncounterCode();
            }
        });
    }

    private static function generateEncounterCode()
    {
        $date = date('Ymd');
        $lastEncounter = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastEncounter ? intval(substr($lastEncounter->encounter_code, -4)) + 1 : 1;
        
        return 'ENC' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'scheduled' => 'Đã Hẹn',
            'in-progress' => 'Đang Khám',
            'completed' => 'Hoàn Thành',
            'cancelled' => 'Đã Hủy',
            default => $this->status,
        };
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'outpatient' => 'Ngoại Trú',
            'inpatient' => 'Nội Trú',
            'emergency' => 'Cấp Cứu',
            'followup' => 'Tái Khám',
            default => $this->type,
        };
    }
}
