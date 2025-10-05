<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hearing;
use App\Models\CaseModel;
use Carbon\Carbon;

class HearingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = CaseModel::all();

        // جلسات سابقة
        Hearing::create([
            'case_id' => $cases->first()->id,
            'hearing_date' => now()->subDays(20),
            'hearing_time' => '10:00:00',
            'title' => 'جلسة أولى',
            'description' => 'الجلسة الأولى لسماع المرافعات',
            'court_name' => 'المحكمة التجارية بالرياض',
            'court_room' => 'قاعة 101',
            'judge_name' => 'القاضي أحمد العلي',
            'hearing_type' => 'initial',
            'status' => 'completed',
            'result' => 'تأجيل للمرافعة',
            'notes' => 'تم تأجيل القضية لجلسة أخرى للمرافعة',
            'completed_at' => now()->subDays(20),
            'created_by' => 'المسؤول',
        ]);

        Hearing::create([
            'case_id' => $cases->first()->id,
            'hearing_date' => now()->subDays(5),
            'hearing_time' => '11:30:00',
            'title' => 'جلسة مرافعة',
            'description' => 'جلسة مرافعة الأطراف',
            'court_name' => 'المحكمة التجارية بالرياض',
            'court_room' => 'قاعة 101',
            'judge_name' => 'القاضي أحمد العلي',
            'hearing_type' => 'pleading',
            'status' => 'completed',
            'result' => 'حجز للحكم',
            'notes' => 'تم الاستماع للمرافعات وحجزت القضية للحكم',
            'completed_at' => now()->subDays(5),
            'created_by' => 'المسؤول',
        ]);

        // جلسات قادمة
        Hearing::create([
            'case_id' => $cases->first()->id,
            'hearing_date' => now()->addDays(7),
            'hearing_time' => '09:00:00',
            'title' => 'جلسة النطق بالحكم',
            'description' => 'جلسة النطق بالحكم النهائي',
            'court_name' => 'المحكمة التجارية بالرياض',
            'court_room' => 'قاعة 101',
            'judge_name' => 'القاضي أحمد العلي',
            'hearing_type' => 'judgment',
            'status' => 'scheduled',
            'created_by' => 'المسؤول',
        ]);

        Hearing::create([
            'case_id' => $cases->skip(1)->first()->id,
            'hearing_date' => now()->addDays(3),
            'hearing_time' => '10:30:00',
            'title' => 'جلسة أولى - أحوال شخصية',
            'description' => 'الجلسة الأولى لقضية الطلاق',
            'court_name' => 'محكمة الأحوال الشخصية',
            'court_room' => 'قاعة 205',
            'judge_name' => 'القاضي محمد السالم',
            'hearing_type' => 'initial',
            'status' => 'scheduled',
            'created_by' => 'المسؤول',
        ]);

        Hearing::create([
            'case_id' => $cases->skip(2)->first()->id,
            'hearing_date' => now()->addDays(14),
            'hearing_time' => '14:00:00',
            'title' => 'جلسة بينات - عمالية',
            'description' => 'جلسة تقديم البينات والمستندات',
            'court_name' => 'محكمة العمل',
            'court_room' => 'قاعة 301',
            'judge_name' => 'القاضي سعد النصر',
            'hearing_type' => 'evidence',
            'status' => 'scheduled',
            'created_by' => 'المسؤول',
        ]);

        // جلسة اليوم
        Hearing::create([
            'case_id' => $cases->first()->id,
            'hearing_date' => now(),
            'hearing_time' => '16:00:00',
            'title' => 'جلسة طارئة',
            'description' => 'جلسة طارئة لمناقشة طلب عاجل',
            'court_name' => 'المحكمة التجارية بالرياض',
            'court_room' => 'قاعة 102',
            'judge_name' => 'القاضي فهد الأحمد',
            'hearing_type' => 'other',
            'status' => 'scheduled',
            'created_by' => 'المسؤول',
        ]);

        // تحديث تواريخ الجلسات القادمة في القضايا
        foreach ($cases as $case) {
            $nextHearing = $case->hearings()
                ->where('status', 'scheduled')
                ->where('hearing_date', '>=', now())
                ->orderBy('hearing_date')
                ->orderBy('hearing_time')
                ->first();
            
            if ($nextHearing) {
                $case->update([
                    'next_hearing_date' => $nextHearing->hearing_date,
                    'next_hearing_time' => $nextHearing->hearing_time
                ]);
            }
        }
    }
}