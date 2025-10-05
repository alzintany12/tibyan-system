<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('document_templates')->insert([
            [
                'name' => 'قالب عقد',
                'content' => '<h1>عقد اتفاق</h1><p>هذا نص تجريبي لعقد اتفاق.</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'قالب مذكرة',
                'content' => '<h1>مذكرة قانونية</h1><p>هذا نص تجريبي لمذكرة قانونية.</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'قالب شكوى',
                'content' => '<h1>نموذج شكوى</h1><p>هذا نص تجريبي لنموذج شكوى.</p>',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
