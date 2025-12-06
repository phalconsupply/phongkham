<?php

namespace Database\Seeders;

use App\Models\ICD10Code;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ICD10Seeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/data/icd10_byt.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("File không tồn tại: {$csvFile}");
            return;
        }

        $this->command->info('Đang đọc file CSV...');
        
        $handle = fopen($csvFile, 'r');
        
        // Skip header row
        fgetcsv($handle);
        
        $batch = [];
        $batchSize = 500;
        $count = 0;
        $seenCodes = []; // Track seen codes to avoid duplicates
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        ICD10Code::truncate();
        
        $this->command->info('Đang import dữ liệu ICD-10...');
        $progressBar = $this->command->getOutput()->createProgressBar();
        
        while (($row = fgetcsv($handle)) !== false) {
            // Skip nếu không có mã bệnh
            if (empty($row[16])) {
                continue;
            }
            
            $code = trim($row[16]);
            
            // Skip duplicates
            if (isset($seenCodes[$code])) {
                continue;
            }
            $seenCodes[$code] = true;
            
            // Áp dụng rules tự động
            $rules = $this->applyAutomaticRules($code);
            
            $batch[] = [
                'code' => $code,
                'code_normalized' => trim($row[17]) ?: $code,
                'name_vi' => trim($row[19]) ?: null,
                'name_en' => trim($row[18]) ?: null,
                
                'chapter_code' => trim($row[1]) ?: null,
                'chapter_name_vi' => trim($row[3]) ?: null,
                'chapter_name_en' => trim($row[2]) ?: null,
                
                'main_group_code' => trim($row[4]) ?: null,
                'main_group_name_vi' => trim($row[6]) ?: null,
                'main_group_name_en' => trim($row[5]) ?: null,
                
                'sub_group1_code' => trim($row[7]) ?: null,
                'sub_group1_name_vi' => trim($row[9]) ?: null,
                'sub_group1_name_en' => trim($row[8]) ?: null,
                
                'sub_group2_code' => trim($row[10]) ?: null,
                'sub_group2_name_vi' => trim($row[12]) ?: null,
                'sub_group2_name_en' => trim($row[11]) ?: null,
                
                'type_code' => trim($row[13]) ?: null,
                'type_name_vi' => trim($row[15]) ?: null,
                'type_name_en' => trim($row[14]) ?: null,
                
                'notes' => trim($row[20]) ?: null,
                
                // Áp dụng rules
                'gender' => $rules['gender'],
                'min_age' => $rules['min_age'],
                'max_age' => $rules['max_age'],
                'reportable' => $rules['reportable'],
                'can_be_primary' => $rules['can_be_primary'],
                'is_chronic' => $rules['is_chronic'],
                
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            $count++;
            $progressBar->advance();
            
            // Insert batch
            if (count($batch) >= $batchSize) {
                ICD10Code::insert($batch);
                $batch = [];
            }
        }
        
        // Insert remaining
        if (!empty($batch)) {
            ICD10Code::insert($batch);
        }
        
        fclose($handle);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $progressBar->finish();
        $this->command->newLine();
        $this->command->info("✅ Đã import {$count} mã ICD-10 thành công!");
    }

    /**
     * Áp dụng rules tự động dựa trên mã ICD-10
     */
    private function applyAutomaticRules($code): array
    {
        $rules = [
            'gender' => 'both',
            'min_age' => null,
            'max_age' => null,
            'reportable' => false,
            'can_be_primary' => true,
            'is_chronic' => false,
        ];

        $prefix = substr($code, 0, 1);
        $range = substr($code, 0, 3);

        // Bệnh truyền nhiễm (A-B) - Báo cáo
        if (in_array($prefix, ['A', 'B'])) {
            $rules['reportable'] = true;
        }

        // Thai sản (O00-O99) - Chỉ nữ
        if ($prefix === 'O') {
            $rules['gender'] = 'female';
            $rules['min_age'] = 15 * 12; // 15 tuổi
            $rules['max_age'] = 50 * 12; // 50 tuổi
        }

        // Bệnh chu sinh (P00-P96) - Trẻ sơ sinh
        if ($prefix === 'P') {
            $rules['max_age'] = 1; // 1 tháng
        }

        // Bệnh nam giới (N40-N51)
        if (in_array($range, ['N40', 'N41', 'N42', 'N43', 'N44', 'N45', 'N46', 'N47', 'N48', 'N49', 'N50', 'N51'])) {
            $rules['gender'] = 'male';
        }

        // Bệnh phụ khoa (N70-N98)
        if (in_array($prefix, ['N']) && intval(substr($code, 1, 2)) >= 70 && intval(substr($code, 1, 2)) <= 98) {
            $rules['gender'] = 'female';
        }

        // Dị tật bẩm sinh (Q00-Q99)
        if ($prefix === 'Q') {
            $rules['is_chronic'] = true;
        }

        // Bệnh mạn tính
        if (in_array($range, ['E10', 'E11', 'I10', 'I11', 'I12', 'I13', 'I50', 'J44', 'J45', 'N18'])) {
            $rules['is_chronic'] = true;
        }

        // Z codes - Không phải bệnh chính
        if ($prefix === 'Z') {
            $rules['can_be_primary'] = false;
        }

        return $rules;
    }
}
