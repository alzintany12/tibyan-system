<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CaseModel;
use App\Models\Client;
use App\Models\User;

class CaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::all();
        $users = User::all();

        CaseModel::create([
            'case_number' => 'CASE-2025-0001',
            'case_title' => 'قضية نزاع تجاري',
            'client_id' => $clients->first()->id,
            'client_name' => $clients->first()->name,
            'client_phone' => $clients->first()->phone,
            'user_id' => $users->first()->id,
            'opposing_party' => 'شركة التجارة المحدودة',
            'court_name' => 'المحكمة التجارية بالرياض',
            'court_type' => 'commercial',
            'case_type' => 'commercial',
            'case_category' => 'نزاع تجاري',
            'status' => 'active',
            'priority' => 'high',
            'start_date' => now()->subDays(30),
            'expected_end_date' => now()->addDays(90),
            'description' => 'نزاع تجاري حول عقد توريد',
            'case_summary' => 'النزاع حول عقد توريد معدات مكتبية بقيمة 100,000 ريال',
            'fee_amount' => 25000.00,
            'fee_type' => 'fixed',
            'total_fees' => 25000.00,
            'fees_received' => 10000.00,
            'fees_pending' => 15000.00,
            'case_value' => 100000.00,
            'created_by' => $users->first()->id,
            'is_active' => true,
        ]);

        CaseModel::create([
            'case_number' => 'CASE-2025-0002',
            'case_title' => 'قضية أحوال شخصية',
            'client_id' => $clients->skip(1)->first()->id,
            'client_name' => $clients->skip(1)->first()->name,
            'client_phone' => $clients->skip(1)->first()->phone,
            'user_id' => $users->skip(1)->first()->id,
            'court_name' => 'محكمة الأحوال الشخصية',
            'court_type' => 'family',
            'case_type' => 'family',
            'case_category' => 'طلاق',
            'status' => 'active',
            'priority' => 'medium',
            'start_date' => now()->subDays(15),
            'expected_end_date' => now()->addDays(60),
            'description' => 'قضية طلاق وتقسيم أموال',
            'case_summary' => 'طلب طلاق للشقاق والنزاع مع تقسيم الأموال والحضانة',
            'fee_amount' => 15000.00,
            'fee_type' => 'fixed',
            'total_fees' => 15000.00,
            'fees_received' => 15000.00,
            'fees_pending' => 0.00,
            'created_by' => $users->first()->id,
            'is_active' => true,
        ]);

        CaseModel::create([
            'case_number' => 'CASE-2025-0003',
            'case_title' => 'قضية عمالية',
            'client_id' => $clients->skip(2)->first()->id,
            'client_name' => $clients->skip(2)->first()->name,
            'client_phone' => $clients->skip(2)->first()->phone,
            'user_id' => $users->first()->id,
            'opposing_party' => 'مؤسسة البناء والتطوير',
            'court_name' => 'محكمة العمل',
            'court_type' => 'labor',
            'case_type' => 'labor',
            'case_category' => 'مطالبة مالية',
            'status' => 'pending',
            'priority' => 'low',
            'start_date' => now()->subDays(7),
            'expected_end_date' => now()->addDays(45),
            'description' => 'مطالبة بمستحقات عمالية',
            'case_summary' => 'مطالبة العامل بمستحقاته المالية وتعويض الفصل التعسفي',
            'fee_amount' => 8000.00,
            'fee_type' => 'percentage',
            'fee_percentage' => 20.00,
            'total_fees' => 8000.00,
            'fees_received' => 0.00,
            'fees_pending' => 8000.00,
            'case_value' => 40000.00,
            'created_by' => $users->first()->id,
            'is_active' => true,
        ]);
    }
}