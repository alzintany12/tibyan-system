@extends('layouts.app')

@section('page-title', 'إدارة المهام')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">المهام</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-tasks"></i>
                        إدارة المهام
                    </h4>
                    <a href="{{ route('tasks.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus"></i>
                        إنشاء مهمة جديدة
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- فلاتر البحث -->
                <form method="GET" action="{{ route('tasks.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>البحث</label>
                                <input type="text" name="search" class="form-control" 
                                    placeholder="عنوان المهمة..." 
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>الحالة</label>
                                <select name="status" class="form-control">
                                    <option value="">جميع الحالات</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>الأولوية</label>
                                <select name="priority" class="form-control">
                                    <option value="">جميع الأولويات</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>المُكلف</label>
                                <select name="assigned_to" class="form-control">
                                    <option value="">جميع المستخدمين</option>
                                    @foreach(\App\Models\User::where('is_active', true)->get() as $user)
                                        <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                                <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- إحصائيات سريعة -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5>{{ $taskStats['total'] ?? 0 }}</h5>
                                <small>إجمالي المهام</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5>{{ $taskStats['pending'] ?? 0 }}</h5>
                                <small>المهام المعلقة</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5>{{ $taskStats['in_progress'] ?? 0 }}</h5>
                                <small>قيد التنفيذ</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5>{{ $taskStats['completed'] ?? 0 }}</h5>
                                <small>المهام المكتملة</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- جدول المهام -->
                @if($tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>العنوان</th>
                                <th>المُكلف</th>
                                <th>القضية</th>
                                <th>الأولوية</th>
                                <th>تاريخ الاستحقاق</th>
                                <th>التقدم</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr class="{{ $task->is_overdue ? 'table-danger' : '' }}">
                                <td>
                                    <strong>{{ $task->title }}</strong>
                                    @if($task->description)
                                        <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($task->assignedTo)
                                        <a href="{{ route('users.show', $task->assignedTo) }}" class="text-decoration-none">
                                            {{ $task->assignedTo->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->legalCase)
                                        <a href="{{ route('cases.show', $task->legalCase) }}" class="text-decoration-none">
                                            {{ Str::limit($task->legalCase->title, 30) }}
                                        </a>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $priorityClasses = [
                                            'low' => 'success',
                                            'medium' => 'warning',
                                            'high' => 'danger',
                                            'urgent' => 'dark'
                                        ];
                                        $priorityNames = [
                                            'low' => 'منخفضة',
                                            'medium' => 'متوسطة',
                                            'high' => 'عالية',
                                            'urgent' => 'عاجلة'
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $priorityClasses[$task->priority] ?? 'secondary' }}">
                                        {{ $priorityNames[$task->priority] ?? $task->priority }}
                                    </span>
                                </td>
                                <td>
                                    @if($task->due_date)
                                        <span class="{{ $task->is_overdue ? 'text-danger font-weight-bold' : '' }}">
                                            {{ $task->due_date->format('Y-m-d') }}
                                        </span>
                                        @if($task->is_overdue)
                                            <br><small class="text-danger">متأخرة</small>
                                        @endif
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" 
                                             role="progressbar" 
                                             style="width: {{ $task->progress ?? 0 }}%"
                                             aria-valuenow="{{ $task->progress ?? 0 }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $task->progress ?? 0 }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'pending' => 'warning',
                                            'in_progress' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'dark'
                                        ];
                                        $statusNames = [
                                            'pending' => 'معلقة',
                                            'in_progress' => 'قيد التنفيذ',
                                            'completed' => 'مكتملة',
                                            'cancelled' => 'ملغاة'
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $statusClasses[$task->status] ?? 'secondary' }}">
                                        {{ $statusNames[$task->status] ?? $task->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('tasks.show', $task) }}" 
                                            class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('tasks.edit', $task) }}" 
                                            class="btn btn-sm btn-outline-secondary" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($task->status != 'completed')
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                data-toggle="modal" data-target="#progressModal{{ $task->id }}"
                                                title="تحديث التقدم">
                                            <i class="fas fa-percentage"></i>
                                        </button>
                                        <form method="POST" action="{{ route('tasks.complete', $task) }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('هل أنت متأكد من إكمال هذه المهمة؟')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                    title="إكمال المهمة">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- الصفحات -->
                <div class="d-flex justify-content-center">
                    {{ $tasks->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد مهام</h5>
                    <p class="text-muted">لم يتم العثور على أي مهام</p>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إنشاء مهمة جديدة
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal تحديث التقدم -->
@foreach($tasks as $task)
@if($task->status != 'completed')
<div class="modal fade" id="progressModal{{ $task->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديث تقدم المهمة: {{ $task->title }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('tasks.update-progress', $task) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label>نسبة التقدم (%)</label>
                        <input type="range" name="progress" class="form-control-range" 
                               min="0" max="100" value="{{ $task->progress ?? 0 }}"
                               oninput="this.nextElementSibling.value = this.value">
                        <output>{{ $task->progress ?? 0 }}</output>%
                    </div>
                    <div class="form-group">
                        <label>الحالة</label>
                        <select name="status" class="form-control">
                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>معلقة</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection

@push('styles')
<style>
.card {
    border: none;
    border-radius: 10px;
}
.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.9rem;
}
.btn-group .btn {
    border-radius: 0;
}
.btn-group .btn:first-child {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px;
}
.btn-group .btn:last-child {
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
}
.progress {
    background-color: #e9ecef;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // إضافة تأثيرات على الجدول
    $('tbody tr').hover(function() {
        $(this).addClass('table-active');
    }, function() {
        $(this).removeClass('table-active');
    });
});
</script>
@endpush