<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// لوحة التحكم
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/statistics', [DashboardController::class, 'getStatistics'])->name('dashboard.statistics');
Route::get('/dashboard/calendar-events', [DashboardController::class, 'getCalendarEvents'])->name('dashboard.calendar-events');

// إدارة القضايا
Route::prefix('cases')->name('cases.')->group(function () {
    Route::get('/', [CaseController::class, 'index'])->name('index');
    Route::get('/create', [CaseController::class, 'create'])->name('create');
    Route::post('/', [CaseController::class, 'store'])->name('store');
    Route::get('/{case}', [CaseController::class, 'show'])->name('show');
    Route::get('/{case}/edit', [CaseController::class, 'edit'])->name('edit');
    Route::put('/{case}', [CaseController::class, 'update'])->name('update');
    Route::delete('/{case}', [CaseController::class, 'destroy'])->name('destroy');
    Route::post('/{case}/invoice', [CaseController::class, 'createInvoice'])->name('create-invoice');
    Route::get('/api/statistics', [CaseController::class, 'statistics'])->name('statistics');
});

// إدارة الجلسات
Route::prefix('hearings')->name('hearings.')->group(function () {
    Route::get('/', [HearingController::class, 'index'])->name('index');
    Route::get('/create', [HearingController::class, 'create'])->name('create');
    Route::post('/', [HearingController::class, 'store'])->name('store');
    Route::get('/{hearing}', [HearingController::class, 'show'])->name('show');
    Route::get('/{hearing}/edit', [HearingController::class, 'edit'])->name('edit');
    Route::put('/{hearing}', [HearingController::class, 'update'])->name('update');
    Route::delete('/{hearing}', [HearingController::class, 'destroy'])->name('destroy');
    
    // التقويم
    Route::get('/calendar/view', [HearingController::class, 'calendar'])->name('calendar');
    Route::get('/calendar/events', [HearingController::class, 'getCalendarEvents'])->name('calendar.events');
    
    // إجراءات سريعة
    Route::post('/{hearing}/complete', [HearingController::class, 'complete'])->name('complete');
    Route::post('/{hearing}/postpone', [HearingController::class, 'postpone'])->name('postpone');
    Route::get('/api/quick-stats', [HearingController::class, 'quickStats'])->name('quick-stats');
    
    // API Routes
    Route::post('/api/update-missed', [HearingController::class, 'updateMissedHearings'])->name('update-missed');
    Route::post('/api/send-reminders', [HearingController::class, 'sendReminders'])->name('send-reminders');
});

// إدارة الفواتير
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('index');
    Route::get('/create', [InvoiceController::class, 'create'])->name('create');
    Route::post('/', [InvoiceController::class, 'store'])->name('store');
    Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
    Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
    Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
    Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    
    // طباعة وإرسال
    Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('print');
    Route::patch('/{invoice}/send', [InvoiceController::class, 'send'])->name('send');
    Route::patch('/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('mark-paid');
    Route::patch('/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('cancel');
    Route::get('/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('duplicate');
    
    // إحصائيات وتحديثات
    Route::get('/api/statistics', [InvoiceController::class, 'statistics'])->name('statistics');
    Route::get('/api/update-overdue', [InvoiceController::class, 'updateOverdueStatus'])->name('update-overdue');
});