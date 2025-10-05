@extends('layouts.app')

@section('title', 'تقويم الجلسات - نظام تبيان')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<style>
    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: bold;
    }
    
    .fc-button {
        border-radius: 8px !important;
    }
    
    .fc-event {
        border-radius: 5px !important;
        border: none !important;
        padding: 2px 5px !important;
        cursor: pointer;
    }
    
    .fc-daygrid-event {
        margin: 1px 0 !important;
    }
    
    .fc-event-title {
        font-weight: 500;
    }
    
    .calendar-card {
        min-height: 700px;
    }
    
    .status-scheduled { 
        background-color: #007bff !important; 
        border-left: 4px solid #0056b3 !important;
    }
    .status-completed { 
        background-color: #28a745 !important; 
        border-left: 4px solid #1e7e34 !important;
    }
    .status-postponed { 
        background-color: #ffc107 !important; 
        color: #000 !important; 
        border-left: 4px solid #e0a800 !important;
    }
    .status-cancelled { 
        background-color: #dc3545 !important; 
        border-left: 4px solid #c82333 !important;
    }
    
    .quick-actions {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .stats-item {
        text-align: center;
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 10px;
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .stats-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .legend-color {
        width: 20px;
        height: 15px;
        border-radius: 3px;
        margin-left: 10px;
    }
    
    .fc-day-today {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .fc-daygrid-day-number {
        font-weight: 600;
    }
    
    .hearing-tooltip {
        background: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.9rem;
        max-width: 300px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="fas fa-calendar-alt ms-2"></i>
        تقويم الجلسات
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('hearings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة جلسة جديدة
            </a>
            <a href="{{ route('hearings.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> عرض القائمة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-9">
        <div class="card calendar-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar ms-2"></i>
                    التقويم
                </h5>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <!-- الإحصائيات السريعة -->
        <div class="quick-actions">
            <h6 class="fw-bold mb-3">إحصائيات سريعة</h6>
            
            <div class="stats-item">
                <div class="stats-number text-primary" id="today-count">0</div>
                <div class="stats-label">جلسات اليوم</div>
            </div>
            
            <div class="stats-item">
                <div class="stats-number text-warning" id="week-count">0</div>
                <div class="stats-label">هذا الأسبوع</div>
            </div>
            
            <div class="stats-item">
                <div class="stats-number text-info" id="month-count">0</div>
                <div class="stats-label">هذا الشهر</div>
            </div>
            
            <div class="stats-item">
                <div class="stats-number text-success" id="upcoming-count">0</div>
                <div class="stats-label">الجلسات القادمة</div>
            </div>
        </div>
        
        <!-- دليل الألوان -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-palette ms-2"></i>
                    دليل الألوان
                </h6>
            </div>
            <div class="card-body">
                <div class="legend-item">
                    <div class="legend-color status-scheduled"></div>
                    <span>مجدولة</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color status-completed"></div>
                    <span>مكتملة</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color status-postponed"></div>
                    <span>مؤجلة</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color status-cancelled"></div>
                    <span>ملغية</span>
                </div>
            </div>
        </div>
        
        <!-- الجلسات القادمة -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-clock ms-2"></i>
                    الجلسات القادمة
                </h6>
            </div>
            <div class="card-body" id="upcoming-hearings">
                <div class="text-center text-muted">
                    <i class="fas fa-spinner fa-spin"></i>
                    جاري التحميل...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal لتفاصيل الجلسة -->
<div class="modal fade" id="hearingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل الجلسة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="hearing-details">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i>
                    جاري التحميل...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" class="btn btn-primary" id="view-hearing-btn">عرض التفاصيل</a>
                <a href="#" class="btn btn-outline-warning" id="edit-hearing-btn">تعديل</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/ar.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    let calendar;
    
    // إعداد التقويم
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ar',
        initialView: 'dayGridMonth',
        direction: 'rtl',
        height: 600,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'اليوم',
            month: 'شهر',
            week: 'أسبوع',
            day: 'يوم'
        },
        events: {
            url: '{{ route("hearings.calendar.events") }}',
            method: 'GET',
            failure: function() {
                alert('حدث خطأ في تحميل الجلسات');
            }
        },
        eventDisplay: 'block',
        dayMaxEvents: 3,
        moreLinkText: function(num) {
            return 'المزيد +' + num;
        },
        eventClick: function(info) {
            showHearingModal(info.event);
        },
        eventDidMount: function(info) {
            // إضافة classes للحالات
            const status = info.event.extendedProps.status;
            info.el.classList.add('status-' + status);
            
            // إضافة tooltip
            const tooltip = createTooltip(info.event);
            info.el.setAttribute('title', tooltip);
            info.el.setAttribute('data-bs-toggle', 'tooltip');
            info.el.setAttribute('data-bs-html', 'true');
        },
        dateClick: function(info) {
            // إضافة جلسة جديدة في التاريخ المحدد
            const addHearingUrl = '{{ route("hearings.create") }}?date=' + info.dateStr;
            window.open(addHearingUrl, '_blank');
        },
        datesSet: function(info) {
            updateStatistics();
            loadUpcomingHearings();
        }
    });
    
    calendar.render();
    
    // تحديث الإحصائيات
    function updateStatistics() {
        fetch('{{ route("hearings.quick-stats") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('today-count').textContent = data.today || 0;
                document.getElementById('week-count').textContent = data.this_week || 0;
                document.getElementById('month-count').textContent = data.this_month || 0;
                document.getElementById('upcoming-count').textContent = data.upcoming || 0;
            })
            .catch(error => {
                console.error('Error loading statistics:', error);
            });
    }
    
    // تحميل الجلسات القادمة
    function loadUpcomingHearings() {
        const upcomingContainer = document.getElementById('upcoming-hearings');
        
        const startDate = new Date().toISOString().split('T')[0];
        const endDate = new Date(Date.now() + 30*24*60*60*1000).toISOString().split('T')[0];
        
        fetch(`{{ route("hearings.calendar.events") }}?start=${startDate}&end=${endDate}`)
            .then(response => response.json())
            .then(events => {
                if (events.length === 0) {
                    upcomingContainer.innerHTML = '<div class="text-muted">لا توجد جلسات قادمة</div>';
                    return;
                }
                
                let html = '';
                events.slice(0, 5).forEach(event => {
                    const eventDate = new Date(event.start);
                    const statusClass = getStatusColor(event.extendedProps.status);
                    
                    html += `
                        <div class="d-flex align-items-center mb-2 p-2 border rounded">
                            <div class="me-3">
                                <span class="badge bg-${statusClass}">${getStatusText(event.extendedProps.status)}</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">${event.title}</div>
                                <small class="text-muted">
                                    ${eventDate.toLocaleDateString('ar-SA')} - ${eventDate.toLocaleTimeString('ar-SA', {hour: '2-digit', minute:'2-digit'})}
                                </small>
                                ${event.extendedProps.case_number ? `<br><small>قضية: ${event.extendedProps.case_number}</small>` : ''}
                            </div>
                            <div>
                                <a href="/hearings/${event.id}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    `;
                });
                
                upcomingContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading upcoming hearings:', error);
                upcomingContainer.innerHTML = '<div class="text-danger">خطأ في تحميل البيانات</div>';
            });
    }
    
    // إنشاء tooltip للجلسة
    function createTooltip(event) {
        const props = event.extendedProps;
        return `
            <div class="hearing-tooltip">
                <strong>${event.title}</strong><br>
                <small>القضية: ${props.case_number || 'غير محدد'}</small><br>
                <small>العميل: ${props.client_name || 'غير محدد'}</small><br>
                <small>المحكمة: ${props.court_name || 'غير محدد'}</small><br>
                <small>الحالة: ${getStatusText(props.status)}</small>
            </div>
        `;
    }
    
    // عرض modal تفاصيل الجلسة
    function showHearingModal(event) {
        const modal = new bootstrap.Modal(document.getElementById('hearingModal'));
        const props = event.extendedProps;
        
        // تحديث المحتوى
        document.getElementById('hearing-details').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>معلومات الجلسة</h6>
                    <p><strong>العنوان:</strong> ${event.title}</p>
                    <p><strong>التاريخ:</strong> ${event.start.toLocaleDateString('ar-SA')}</p>
                    <p><strong>الوقت:</strong> ${event.start.toLocaleTimeString('ar-SA', {hour: '2-digit', minute:'2-digit'})}</p>
                    <p><strong>الحالة:</strong> <span class="badge bg-${getStatusColor(props.status)}">${getStatusText(props.status)}</span></p>
                </div>
                <div class="col-md-6">
                    <h6>معلومات القضية</h6>
                    <p><strong>رقم القضية:</strong> ${props.case_number || 'غير محدد'}</p>
                    <p><strong>العميل:</strong> ${props.client_name || 'غير محدد'}</p>
                    <p><strong>المحكمة:</strong> ${props.court_name || 'غير محدد'}</p>
                    <p><strong>نوع الجلسة:</strong> ${getHearingTypeText(props.hearing_type)}</p>
                </div>
            </div>
        `;
        
        // تحديث أزرار الإجراءات
        document.getElementById('view-hearing-btn').href = `/hearings/${event.id}`;
        document.getElementById('edit-hearing-btn').href = `/hearings/${event.id}/edit`;
        
        modal.show();
    }
    
    // وظائف مساعدة
    function getStatusText(status) {
        const statuses = {
            'scheduled': 'مجدولة',
            'completed': 'مكتملة',
            'postponed': 'مؤجلة',
            'cancelled': 'ملغية'
        };
        return statuses[status] || status;
    }
    
    function getStatusColor(status) {
        const colors = {
            'scheduled': 'primary',
            'completed': 'success',
            'postponed': 'warning',
            'cancelled': 'danger'
        };
        return colors[status] || 'secondary';
    }
    
    function getHearingTypeText(type) {
        const types = {
            'initial': 'جلسة أولى',
            'evidence': 'جلسة بينات',
            'pleading': 'جلسة مرافعة',
            'judgment': 'جلسة حكم',
            'appeal': 'جلسة استئناف',
            'execution': 'جلسة تنفيذ',
            'other': 'أخرى'
        };
        return types[type] || type;
    }
    
    // تحديث الإحصائيات عند التحميل
    updateStatistics();
    loadUpcomingHearings();
    
    // تحديث الإحصائيات كل 5 دقائق
    setInterval(updateStatistics, 300000);
});
</script>
@endpush