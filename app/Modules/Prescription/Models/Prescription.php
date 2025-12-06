<?php

namespace App\Modules\Prescription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Patient\Models\Patient;
use App\Modules\Encounter\Models\Encounter;
use App\Models\User;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prescription_code',
        'patient_id',
        'encounter_id',
        'doctor_id',
        'prescription_date',
        'diagnosis',
        'notes',
        'status',
        'icd10_code_id',
    ];

    protected $casts = [
        'prescription_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prescription) {
            if (empty($prescription->prescription_code)) {
                $prescription->prescription_code = self::generatePrescriptionCode();
            }
        });
    }

    private static function generatePrescriptionCode()
    {
        $date = date('Ymd');
        $lastPrescription = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastPrescription ? intval(substr($lastPrescription->prescription_code, -4)) + 1 : 1;
        
        return 'RX' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function icd10Code()
    {
        return $this->belongsTo(\App\Models\ICD10Code::class, 'icd10_code_id');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'active' => 'Đang Dùng',
            'completed' => 'Hoàn Thành',
            'cancelled' => 'Đã Hủy',
            default => $this->status,
        };
    }
}
