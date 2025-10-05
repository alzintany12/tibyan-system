<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\CaseModel;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = CaseModel::all();

        Invoice::create([
            'invoice_number' => 'INV-2025-01-0001',
            'case_id' => $cases->first()->id,
            'client_name' => $cases->first()->client_name,
            'invoice_date' => now()->subDays(25),
            'due_date' => now()->addDays(5),
            'description' => 'أتعاب قضية نزاع تجاري - دفعة أولى',
            'amount' => 10000.00,
            'tax_amount' => 1500.00,
            'total_amount' => 11500.00,
            'paid_amount' => 11500.00,
            'status' => 'paid',
            'payment_method' => 'bank_transfer',
            'paid_at' => now()->subDays(20),
            'created_by' => 'المسؤول',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-2025-01-0002',
            'case_id' => $cases->first()->id,
            'client_name' => $cases->first()->client_name,
            'invoice_date' => now()->subDays(10),
            'due_date' => now()->addDays(20),
            'description' => 'أتعاب قضية نزاع تجاري - الدفعة المتبقية',
            'amount' => 15000.00,
            'tax_amount' => 2250.00,
            'total_amount' => 17250.00,
            'paid_amount' => 0.00,
            'status' => 'sent',
            'sent_at' => now()->subDays(10),
            'created_by' => 'المسؤول',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-2025-01-0003',
            'case_id' => $cases->skip(1)->first()->id,
            'client_name' => $cases->skip(1)->first()->client_name,
            'invoice_date' => now()->subDays(14),
            'due_date' => now()->subDays(4),
            'description' => 'أتعاب قضية أحوال شخصية',
            'amount' => 15000.00,
            'tax_amount' => 2250.00,
            'total_amount' => 17250.00,
            'paid_amount' => 17250.00,
            'status' => 'paid',
            'payment_method' => 'cash',
            'paid_at' => now()->subDays(12),
            'created_by' => 'المسؤول',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-2025-01-0004',
            'case_id' => $cases->skip(2)->first()->id,
            'client_name' => $cases->skip(2)->first()->client_name,
            'invoice_date' => now()->subDays(5),
            'due_date' => now()->addDays(25),
            'description' => 'أتعاب قضية عمالية',
            'amount' => 8000.00,
            'tax_amount' => 1200.00,
            'total_amount' => 9200.00,
            'paid_amount' => 0.00,
            'status' => 'draft',
            'created_by' => 'المسؤول',
        ]);
    }
}