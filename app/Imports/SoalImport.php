<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SoalImport implements ToCollection, WithStartRow, WithHeadingRow
{
    /**
     * Start reading from row 2 (skip header)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Use heading row
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Convert Excel to collection
     */
    public function collection(Collection $rows)
    {
        return $rows;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D,a,b,c,d',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages(): array
    {
        return [
            'pertanyaan.required' => 'Kolom Pertanyaan harus diisi',
            'pilihan_a.required' => 'Kolom Pilihan A harus diisi',
            'pilihan_b.required' => 'Kolom Pilihan B harus diisi',
            'jawaban_benar.required' => 'Kolom Jawaban Benar harus diisi',
            'jawaban_benar.in' => 'Jawaban Benar harus A, B, C, atau D',
        ];
    }
}