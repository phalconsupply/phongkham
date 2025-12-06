<?php

namespace App\Modules\Shared\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ICD10Code;
use Illuminate\Http\Request;

class ICD10Controller extends Controller
{
    /**
     * Search ICD-10 codes by code or name
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $limit = min($request->input('limit', 20), 50);
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        $results = ICD10Code::search($query, $limit);
        
        return response()->json($results->map(function ($code) {
            return [
                'id' => $code->id,
                'code' => $code->code,
                'name_vi' => $code->name_vi,
                'name_en' => $code->name_en,
                'gender' => $code->gender,
                'gender_label' => $code->gender_label,
                'min_age' => $code->min_age,
                'max_age' => $code->max_age,
                'can_be_primary' => $code->can_be_primary,
                'is_chronic' => $code->is_chronic,
                'reportable' => $code->reportable,
                'display' => $code->code . ' - ' . $code->name_vi,
            ];
        }));
    }
    
    /**
     * Get ICD-10 code details
     */
    public function show($id)
    {
        $code = ICD10Code::findOrFail($id);
        
        return response()->json([
            'id' => $code->id,
            'code' => $code->code,
            'code_normalized' => $code->code_normalized,
            'name_vi' => $code->name_vi,
            'name_en' => $code->name_en,
            'gender' => $code->gender,
            'gender_label' => $code->gender_label,
            'min_age' => $code->min_age,
            'max_age' => $code->max_age,
            'can_be_primary' => $code->can_be_primary,
            'is_chronic' => $code->is_chronic,
            'reportable' => $code->reportable,
            'chapter_code' => $code->chapter_code,
            'chapter_name_vi' => $code->chapter_name_vi,
            'main_group_code' => $code->main_group_code,
            'main_group_name_vi' => $code->main_group_name_vi,
            'notes' => $code->notes,
        ]);
    }
    
    /**
     * Validate ICD-10 code for a patient
     */
    public function validate(Request $request)
    {
        $request->validate([
            'icd10_code_id' => 'required|exists:icd10_codes,id',
            'patient_id' => 'required|exists:patients,id',
        ]);
        
        $code = ICD10Code::findOrFail($request->icd10_code_id);
        $patient = \App\Modules\Patient\Models\Patient::findOrFail($request->patient_id);
        
        $isValid = $code->isValidForPatient($patient);
        $warnings = [];
        
        if (!$isValid) {
            // Generate specific warning messages
            if ($code->gender !== 'both' && $patient->gender !== $code->gender) {
                $warnings[] = "Mã ICD-10 này chỉ dành cho " . $code->gender_label;
            }
            
            $patientAgeInMonths = $patient->date_of_birth 
                ? now()->diffInMonths($patient->date_of_birth) 
                : null;
            
            if ($patientAgeInMonths !== null) {
                if ($code->min_age !== null && $patientAgeInMonths < $code->min_age) {
                    $minYears = floor($code->min_age / 12);
                    $warnings[] = "Mã ICD-10 này chỉ áp dụng cho bệnh nhân từ {$minYears} tuổi trở lên";
                }
                
                if ($code->max_age !== null && $patientAgeInMonths > $code->max_age) {
                    $maxYears = floor($code->max_age / 12);
                    $maxMonths = $code->max_age % 12;
                    if ($maxYears > 0) {
                        $warnings[] = "Mã ICD-10 này chỉ áp dụng cho bệnh nhân dưới {$maxYears} tuổi";
                    } else {
                        $warnings[] = "Mã ICD-10 này chỉ áp dụng cho bệnh nhân dưới {$maxMonths} tháng tuổi";
                    }
                }
            }
        }
        
        return response()->json([
            'valid' => $isValid,
            'warnings' => $warnings,
            'code' => [
                'id' => $code->id,
                'code' => $code->code,
                'name_vi' => $code->name_vi,
                'gender' => $code->gender,
                'gender_label' => $code->gender_label,
                'min_age' => $code->min_age,
                'max_age' => $code->max_age,
                'is_chronic' => $code->is_chronic,
                'reportable' => $code->reportable,
            ],
        ]);
    }
}
