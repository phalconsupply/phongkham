<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ICD10Code extends Model
{
    protected $table = 'icd10_codes';
    
    protected $fillable = [
        'code',
        'code_normalized',
        'name_vi',
        'name_en',
        'chapter_code',
        'chapter_name_vi',
        'chapter_name_en',
        'main_group_code',
        'main_group_name_vi',
        'main_group_name_en',
        'sub_group1_code',
        'sub_group1_name_vi',
        'sub_group1_name_en',
        'sub_group2_code',
        'sub_group2_name_vi',
        'sub_group2_name_en',
        'type_code',
        'type_name_vi',
        'type_name_en',
        'gender',
        'min_age',
        'max_age',
        'can_be_primary',
        'is_chronic',
        'reportable',
        'notes',
        'severity',
    ];

    protected $casts = [
        'can_be_primary' => 'boolean',
        'is_chronic' => 'boolean',
        'reportable' => 'boolean',
        'min_age' => 'integer',
        'max_age' => 'integer',
    ];

    /**
     * Tìm kiếm ICD-10 theo tên hoặc mã
     */
    public static function search($query, $limit = 20)
    {
        return self::where('code', 'like', "%{$query}%")
            ->orWhere('name_vi', 'like', "%{$query}%")
            ->orWhere('name_en', 'like', "%{$query}%")
            ->limit($limit)
            ->get(['id', 'code', 'name_vi', 'name_en', 'chapter_name_vi']);
    }

    /**
     * Kiểm tra mã ICD-10 có phù hợp với bệnh nhân không
     */
    public function isValidForPatient($patient)
    {
        // Kiểm tra giới tính
        if ($this->gender !== 'both' && $patient->gender !== $this->gender) {
            return false;
        }

        // Kiểm tra tuổi (tính theo tháng)
        if ($patient->date_of_birth) {
            $ageInMonths = $patient->date_of_birth->diffInMonths(now());
            
            if ($this->min_age !== null && $ageInMonths < $this->min_age) {
                return false;
            }
            
            if ($this->max_age !== null && $ageInMonths > $this->max_age) {
                return false;
            }
        }

        return true;
    }

    /**
     * Lấy label giới tính
     */
    public function getGenderLabelAttribute()
    {
        return match($this->gender) {
            'male' => 'Nam',
            'female' => 'Nữ',
            'both' => 'Cả hai',
            default => 'Cả hai',
        };
    }
}
