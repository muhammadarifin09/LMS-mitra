<?php

namespace App\Services;

use App\Models\MaterialProgress;
use App\Models\Kursus;

class NilaiService
{
    public function hitungNilai($userId, Kursus $kursus)
    {
        $materialIds = $kursus->materials->pluck('id');

        $progress = MaterialProgress::where('user_id', $userId)
            ->whereIn('material_id', $materialIds)
            ->get();

        $totalScore = 0;
        $totalTest = 0;

        foreach ($progress as $p) {
            if ($p->pretest_score !== null) {
                $totalScore += $p->pretest_score;
                $totalTest++;
            }
            if ($p->posttest_score !== null) {
                $totalScore += $p->posttest_score;
                $totalTest++;
            }
        }

        return $totalTest > 0
            ? round($totalScore / $totalTest, 2)
            : null;
    }

    public function statusNilai(?float $nilai): string
    {
        if ($nilai === null) {
            return 'belum';
        }

        return $nilai >= 60 ? 'lulus' : 'tidak_lulus';
    }
}
