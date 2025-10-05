@extends('layouts.app')

@section('title', 'عرض الفاتورة - نظام تبيان')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-file-invoice-dollar ms-2"></i>
        الفاتورة رقم: {{ $invoice->invoice_number }}
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            @if($invoice->status === 'draft')
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> تعديل
                </a>
            @endif
            
            <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-outline-secondary" target="_blank">
                <i class="fas fa-print"></i> طباعة
            </a>
            
            <div class="btn-group">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i> إجراءات
                </button>
                <ul class="dropdown-menu">
                    @if($invoice->status !== 'paid')
                        <li>
                            <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item" onclick="return confirm('هل أنت متأكد من وضع علامة مدفوع؟')">
                                    <i class="fas fa-check-circle text-success"></i> تحديد كمدفوعة
                                </button>
                            </form>
                        </li>
                    @endif
                    
                    @if($invoice->status === 'draft')
                        <li>
                            <form action="{{ route('invoices.send', $invoice) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-paper-plane text-primary"></i> إرسال الفاتورة
                                </button>
                            </form>
                        </li>
                    @endif
                    
                    <li>
                        <a class="dropdown-item" href="{{ route('invoices.duplicate', $invoice) }}">
                            <i class="fas fa-copy text-info"></i> نسخ الفاتورة
                        </a>
                    </li>
                    
                    <li><hr class="dropdown-divider"></li>
                    
                    @if($invoice->status !== 'paid')
                        <li>
                            <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="dropdown-item text-warning" onclick="return confirm('هل أنت متأكد من إلغاء الفاتورة؟')">
                                    <i class="fas fa-ban"></i> إلغاء الفاتورة
                                </button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> العودة للفواتير
        </a>
    </div>
</div>

<!-- معلومات الحالة -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'info') }}">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <strong>الحالة:</strong> 
                    @switch($invoice->status)
                        @case('draft')
                            <span class="badge bg-secondary">مسودة</span>
                            @break
                        @case('sent')
                            <span class="badge bg-primary">مرسلة</span>
                            @break
                        @case('viewed')
                            <span class="badge bg-info">تم الاطلاع</span>
                            @break
                        @case('paid')
                            <span class="badge bg-success">مدفوعة</span>
                            @break
                        @case('overdue')
                            <span class="badge bg-danger">متأخرة</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-warning">ملغية</span>
                            @break
                        @default
                            <span class="badge bg-secondary">{{ $invoice->status }}</span>
                    @endswitch
                </div>
                <div class="col-md-6 text-end">
                    <strong>المبلغ الإجمالي:</strong> 
                    <span class="h5 text-primary">{{ number_format($invoice->total_amount, 2) }} دينار ليبي</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- تفاصيل الفاتورة -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>تفاصيل الفاتورة</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>رقم الفاتورة:</strong> {{ $invoice->invoice_number }}
                    </div>
                    <div class="col-md-6">
                        <strong>تاريخ الفاتورة:</strong> {{ $invoice->invoice_date->format('Y/m/d') }}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>تاريخ الاستحقاق:</strong> {{ $invoice->due_date->format('Y/m/d') }}
                    </div>
                    <div class="col-md-6">
                        <strong>اسم العميل:</strong> {{ $invoice->client_name }}
                    </div>
                </div>
                
                @if($invoice->case)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>القضية المرتبطة:</strong> 
                            <a href="{{ route('cases.show', $invoice->case) }}" class="text-decoration-none">
                                {{ $invoice->case->case_number }} - {{ $invoice->case->client_name }}
                            </a>
                        </div>
                    </div>
                @endif
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>وصف الخدمة:</strong>
                        <p class="mt-2">{{ $invoice->description }}</p>
                    </div>
                </div>
                
                @if($invoice->notes)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>ملاحظات:</strong>
                            <p class="mt-2">{{ $invoice->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- بنود الفاتورة -->
        @if($invoice->items->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5>بنود الفاتورة</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الوصف</th>
                                    <th class="text-center">الكمية</th>
                                    <th class="text-center">السعر</th>
                                    <th class="text-center">المجموع</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->description }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-center">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-center">{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- معلومات إضافية -->
    <div class="col-md-4">
        <!-- ملخص المبالغ -->
        <div class="card">
            <div class="card-header">
                <h5>ملخص المبالغ</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-8">المبلغ الأساسي:</div>
                    <div class="col-4 text-end">{{ number_format($invoice->amount, 2) }}</div>
                </div>
                
                @if($invoice->tax_amount > 0)
                    <div class="row mb-2">
                        <div class="col-8">الضريبة:</div>
                        <div class="col-4 text-end">{{ number_format($invoice->tax_amount, 2) }}</div>
                    </div>
                @endif
                
                @if($invoice->discount_amount > 0)
                    <div class="row mb-2">
                        <div class="col-8">الخصم:</div>
                        <div class="col-4 text-end text-danger">-{{ number_format($invoice->discount_amount, 2) }}</div>
                    </div>
                @endif
                
                <hr>
                
                <div class="row">
                    <div class="col-8"><strong>المجموع الكلي:</strong></div>
                    <div class="col-4 text-end"><strong>{{ number_format($invoice->total_amount, 2) }}</strong></div>
                </div>
                
                @if($invoice->paid_amount > 0)
                    <div class="row mt-2">
                        <div class="col-8">المبلغ المدفوع:</div>
                        <div class="col-4 text-end text-success">{{ number_format($invoice->paid_amount, 2) }}</div>
                    </div>
                    
                    @if($invoice->total_amount - $invoice->paid_amount > 0)
                        <div class="row">
                            <div class="col-8">المبلغ المتبقي:</div>
                            <div class="col-4 text-end text-danger">{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
        
        <!-- تواريخ مهمة -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>تواريخ مهمة</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">تاريخ الإنشاء</small><br>
                    <span>{{ $invoice->created_at->format('Y/m/d H:i') }}</span>
                </div>
                
                @if($invoice->sent_at)
                    <div class="mb-3">
                        <small class="text-muted">تاريخ الإرسال</small><br>
                        <span>{{ $invoice->sent_at->format('Y/m/d H:i') }}</span>
                    </div>
                @endif
                
                @if($invoice->paid_at)
                    <div class="mb-3">
                        <small class="text-muted">تاريخ الدفع</small><br>
                        <span>{{ $invoice->paid_at->format('Y/m/d H:i') }}</span>
                    </div>
                @endif
                
                <div class="mb-3">
                    <small class="text-muted">تاريخ آخر تحديث</small><br>
                    <span>{{ $invoice->updated_at->format('Y/m/d H:i') }}</span>
                </div>
                
                @if($invoice->created_by)
                    <div class="mb-3">
                        <small class="text-muted">تم الإنشاء بواسطة</small><br>
                        <span>{{ $invoice->created_by }}</span>
                    </div>
                @endif
                
                @if($invoice->updated_by)
                    <div>
                        <small class="text-muted">تم التحديث بواسطة</small><br>
                        <span>{{ $invoice->updated_by }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection