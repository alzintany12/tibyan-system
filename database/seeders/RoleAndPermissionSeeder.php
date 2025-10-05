<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // إيقاف التحقق من المفاتيح الخارجية مؤقتاً لتجنب أخطاء عند إعادة التشغيل
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // تنظيف الجداول لتجنب تكرار الأسماء عند تشغيل seeder أكثر من مرة
        Permission::query()->delete();
        Role::query()->delete();

        // إعادة تعيين المفاتيح التلقائية (id)
        DB::statement('ALTER TABLE permissions AUTO_INCREMENT = 1;');
        DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // إنشاء الصلاحيات
        $permissions = [
            // إدارة المستخدمين
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // إدارة العملاء
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',
            'clients.export',
            
            // إدارة القضايا
            'cases.view',
            'cases.create',
            'cases.edit',
            'cases.delete',
            'cases.assign',
            'cases.archive',
            'cases.export',
            
            // إدارة المستندات
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',
            'documents.download',
            'documents.upload',
            
            // إدارة الفواتير
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'invoices.send',
            'invoices.payments',
            'invoices.export',
            
            // إدارة الجلسات
            'hearings.view',
            'hearings.create',
            'hearings.edit',
            'hearings.delete',
            'hearings.complete',
            
            // إدارة المهام
            'tasks.view',
            'tasks.create',
            'tasks.edit',
            'tasks.delete',
            'tasks.assign',
            'tasks.complete',
            
            // إدارة المصروفات
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',
            'expenses.approve',
            'expenses.export',
            
            // التقارير
            'reports.view',
            'reports.financial',
            'reports.cases',
            'reports.productivity',
            'reports.export',
            
            // النسخ الاحتياطي
            'backups.view',
            'backups.create',
            'backups.download',
            'backups.restore',
            'backups.delete',
            
            // الإعدادات
            'settings.view',
            'settings.edit',
            
            // التقويم
            'calendar.view',
            'calendar.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // إنشاء الأدوار
        
        // دور المدير
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all()); // جميع الصلاحيات

        // دور المحامي
        $lawyer = Role::create(['name' => 'lawyer']);
        $lawyer->givePermissionTo([
            'clients.view', 'clients.create', 'clients.edit',
            'cases.view', 'cases.create', 'cases.edit', 'cases.assign',
            'documents.view', 'documents.create', 'documents.edit', 'documents.upload', 'documents.download',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send', 'invoices.payments',
            'hearings.view', 'hearings.create', 'hearings.edit', 'hearings.complete',
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.assign', 'tasks.complete',
            'expenses.view', 'expenses.create', 'expenses.edit',
            'reports.view', 'reports.cases', 'reports.productivity',
            'calendar.view', 'calendar.manage',
        ]);

        // دور السكرتير
        $secretary = Role::create(['name' => 'secretary']);
        $secretary->givePermissionTo([
            'clients.view', 'clients.create', 'clients.edit',
            'cases.view',
            'documents.view', 'documents.create', 'documents.upload', 'documents.download',
            'invoices.view', 'invoices.create', 'invoices.send',
            'hearings.view', 'hearings.create', 'hearings.edit',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'expenses.view', 'expenses.create',
            'calendar.view',
        ]);

        // دور المساعد القانوني
        $assistant = Role::create(['name' => 'legal_assistant']);
        $assistant->givePermissionTo([
            'clients.view',
            'cases.view',
            'documents.view', 'documents.create', 'documents.upload', 'documents.download',
            'hearings.view',
            'tasks.view', 'tasks.edit', 'tasks.complete',
            'expenses.view',
            'calendar.view',
        ]);

        // دور المحاسب
        $accountant = Role::create(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'clients.view',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.send', 'invoices.payments', 'invoices.export',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.approve', 'expenses.export',
            'reports.view', 'reports.financial', 'reports.export',
        ]);
    }
}
