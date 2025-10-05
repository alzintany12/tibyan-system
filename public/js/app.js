/**
 * منظومة التبيان - الجافا سكريبت الرئيسي
 * نظام إدارة الشركات القانونية
 */

// إعدادات عامة
const TibyanApp = {
    // إعدادات Ajax
    setupAjax() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    },

    // تهيئة التطبيق
    init() {
        this.setupAjax();
        this.initTooltips();
        this.initAlerts();
        this.initSidebar();
        this.initDateTime();
        this.initTables();
        this.initForms();
        this.initModals();
        this.initCharts();
        this.bindEvents();
    },

    // تفعيل الـ tooltips
    initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },

    // إدارة التنبيهات
    initAlerts() {
        // إخفاء التنبيهات تلقائياً بعد 5 ثوان
        setTimeout(() => {
            $('.alert:not(.alert-permanent)').fadeOut('slow');
        }, 5000);

        // إضافة أزرار الإغلاق
        $('.alert').each(function() {
            if (!$(this).find('.btn-close').length) {
                $(this).append('<button type="button" class="btn-close" data-bs-dismiss="alert"></button>');
            }
        });
    },

    // إدارة الشريط الجانبي
    initSidebar() {
        // تفعيل/إلغاء تفعيل الشريط الجانبي في الشاشات الصغيرة
        $('.sidebar-toggle').on('click', function() {
            $('.sidebar').toggleClass('show');
        });

        // إغلاق الشريط الجانبي عند النقر خارجه
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.sidebar, .sidebar-toggle').length) {
                $('.sidebar').removeClass('show');
            }
        });

        // تفعيل الرابط الحالي
        const currentPath = window.location.pathname;
        $('.sidebar .nav-link').each(function() {
            if ($(this).attr('href') === currentPath) {
                $(this).addClass('active');
            }
        });
    },

    // إدارة التاريخ والوقت
    initDateTime() {
        // تحديث الوقت الحالي
        this.updateCurrentTime();
        setInterval(() => {
            this.updateCurrentTime();
        }, 1000);

        // تهيئة منتقي التاريخ
        $('.datepicker').each(function() {
            $(this).attr('type', 'date');
        });

        // تهيئة منتقي الوقت
        $('.timepicker').each(function() {
            $(this).attr('type', 'time');
        });
    },

    // تحديث الوقت الحالي
    updateCurrentTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('ar-SA');
        const dateString = now.toLocaleDateString('ar-SA');
        
        $('#current-time').text(timeString);
        $('#current-date').text(dateString);
    },

    // تهيئة الجداول
    initTables() {
        // تفعيل DataTables للجداول الكبيرة
        $('.data-table').each(function() {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
                    },
                    responsive: true,
                    pageLength: 25,
                    order: [[0, 'desc']],
                    columnDefs: [
                        { targets: 'no-sort', orderable: false }
                    ]
                });
            }
        });

        // تحديد جميع الصفوف
        $('.select-all').on('change', function() {
            const table = $(this).closest('table');
            table.find('.select-row').prop('checked', $(this).is(':checked'));
        });
    },

    // تهيئة النماذج
    initForms() {
        // التحقق من صحة النماذج
        $('.needs-validation').on('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(this).addClass('was-validated');
        });

        // البحث التفاعلي
        $('.search-input').on('input', debounce(function() {
            const query = $(this).val();
            const target = $(this).data('target');
            if (query.length >= 2) {
                TibyanApp.performSearch(query, target);
            }
        }, 300));

        // تحميل الملفات بالسحب والإفلات
        this.initFileUpload();
    },

    // تهيئة النوافذ المنبثقة
    initModals() {
        // تنظيف النماذج عند إغلاق النوافذ المنبثقة
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('form')[0]?.reset();
            $(this).find('.was-validated').removeClass('was-validated');
        });
    },

    // تهيئة الرسوم البيانية
    initCharts() {
        // رسم بياني للإيرادات الشهرية
        if ($('#monthly-revenue-chart').length) {
            this.createMonthlyRevenueChart();
        }

        // رسم بياني للقضايا حسب النوع
        if ($('#cases-by-type-chart').length) {
            this.createCasesByTypeChart();
        }

        // رسم بياني للقضايا حسب الحالة
        if ($('#cases-by-status-chart').length) {
            this.createCasesByStatusChart();
        }
    },

    // ربط الأحداث
    bindEvents() {
        // تأكيد الحذف
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            const message = $(this).data('message') || 'هل أنت متأكد من الحذف؟';
            if (TibyanApp.confirmDelete(message)) {
                if ($(this).closest('form').length) {
                    $(this).closest('form').submit();
                } else {
                    window.location.href = $(this).attr('href');
                }
            }
        });

        // تحديث حالة القضية
        $(document).on('change', '.case-status-select', function() {
            const caseId = $(this).data('case-id');
            const status = $(this).val();
            TibyanApp.updateCaseStatus(caseId, status);
        });

        // تحديث تقدم المهمة
        $(document).on('change', '.task-progress-input', function() {
            const taskId = $(this).data('task-id');
            const progress = $(this).val();
            TibyanApp.updateTaskProgress(taskId, progress);
        });

        // طباعة الصفحة
        $(document).on('click', '.print-btn', function() {
            window.print();
        });

        // تصدير البيانات
        $(document).on('click', '.export-btn', function() {
            const format = $(this).data('format') || 'xlsx';
            const url = $(this).data('url');
            TibyanApp.exportData(url, format);
        });
    },

    // تهيئة تحميل الملفات
    initFileUpload() {
        $('.file-upload-area').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });

        $('.file-upload-area').on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        });

        $('.file-upload-area').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            const files = e.originalEvent.dataTransfer.files;
            TibyanApp.handleFileUpload(files, $(this));
        });
    },

    // معالجة تحميل الملفات
    handleFileUpload(files, container) {
        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        const url = container.data('upload-url');
        if (!url) return;

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                container.find('.upload-progress').show();
            },
            success: function(response) {
                TibyanApp.showAlert('تم تحميل الملفات بنجاح', 'success');
                location.reload();
            },
            error: function() {
                TibyanApp.showAlert('حدث خطأ أثناء تحميل الملفات', 'danger');
            },
            complete: function() {
                container.find('.upload-progress').hide();
            }
        });
    },

    // البحث التفاعلي
    performSearch(query, target) {
        $.get('/api/search', { query: query, target: target })
            .done(function(data) {
                const resultsContainer = $(`.search-results[data-target="${target}"]`);
                resultsContainer.empty();
                
                if (data.length > 0) {
                    data.forEach(function(item) {
                        resultsContainer.append(`
                            <div class="search-result-item" data-id="${item.id}">
                                <strong>${item.name || item.title}</strong>
                                <small class="text-muted d-block">${item.description || ''}</small>
                            </div>
                        `);
                    });
                    resultsContainer.show();
                } else {
                    resultsContainer.hide();
                }
            });
    },

    // تحديث حالة القضية
    updateCaseStatus(caseId, status) {
        $.ajax({
            url: `/cases/${caseId}/status`,
            method: 'PATCH',
            data: { status: status },
            success: function(response) {
                TibyanApp.showAlert(response.message, 'success');
                location.reload();
            },
            error: function() {
                TibyanApp.showAlert('حدث خطأ أثناء تحديث الحالة', 'danger');
            }
        });
    },

    // تحديث تقدم المهمة
    updateTaskProgress(taskId, progress) {
        $.ajax({
            url: `/tasks/${taskId}/progress`,
            method: 'PATCH',
            data: { progress: progress },
            success: function(response) {
                TibyanApp.showAlert(response.message, 'success');
                $(`.task-progress-bar[data-task-id="${taskId}"] .progress-bar`).css('width', progress + '%');
            },
            error: function() {
                TibyanApp.showAlert('حدث خطأ أثناء تحديث التقدم', 'danger');
            }
        });
    },

    // تصدير البيانات
    exportData(url, format) {
        TibyanApp.showAlert('جاري تحضير الملف...', 'info');
        window.location.href = `${url}?format=${format}`;
    },

    // عرض تنبيه
    showAlert(message, type = 'info') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${this.getAlertIcon(type)} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('#alerts-container').prepend(alertHtml);
        
        // إزالة التنبيه بعد 5 ثوان
        setTimeout(() => {
            $('#alerts-container .alert').first().fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    },

    // الحصول على أيقونة التنبيه
    getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    },

    // تأكيد الحذف
    confirmDelete(message = 'هل أنت متأكد من الحذف؟') {
        return confirm(message);
    },

    // إنشاء رسم بياني للإيرادات الشهرية
    createMonthlyRevenueChart() {
        const ctx = document.getElementById('monthly-revenue-chart').getContext('2d');
        const data = window.monthlyRevenueData || [];
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => `${item.month}/${item.year}`),
                datasets: [{
                    label: 'الإيرادات الشهرية',
                    data: data.map(item => item.total),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                family: 'Cairo'
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('ar-SA') + ' ريال';
                            }
                        }
                    }
                }
            }
        });
    },

    // إنشاء رسم بياني للقضايا حسب النوع
    createCasesByTypeChart() {
        const ctx = document.getElementById('cases-by-type-chart').getContext('2d');
        const data = window.casesByTypeData || [];
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(item => item.case_type),
                datasets: [{
                    data: data.map(item => item.count),
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#48c6ef',
                        '#43e97b'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'Cairo'
                            }
                        }
                    }
                }
            }
        });
    },

    // إنشاء رسم بياني للقضايا حسب الحالة
    createCasesByStatusChart() {
        const ctx = document.getElementById('cases-by-status-chart').getContext('2d');
        const data = window.casesByStatusData || [];
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => this.getStatusName(item.status)),
                datasets: [{
                    label: 'عدد القضايا',
                    data: data.map(item => item.count),
                    backgroundColor: data.map(item => this.getStatusColor(item.status))
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                family: 'Cairo'
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    },

    // الحصول على اسم الحالة
    getStatusName(status) {
        const names = {
            'active': 'نشطة',
            'completed': 'مكتملة',
            'postponed': 'مؤجلة',
            'rejected': 'مرفوضة',
            'suspended': 'معلقة',
            'pending': 'قيد الانتظار'
        };
        return names[status] || status;
    },

    // الحصول على لون الحالة
    getStatusColor(status) {
        const colors = {
            'active': '#28a745',
            'completed': '#007bff',
            'postponed': '#ffc107',
            'rejected': '#dc3545',
            'suspended': '#6c757d',
            'pending': '#17a2b8'
        };
        return colors[status] || '#6c757d';
    }
};

// دالة تأخير التنفيذ
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// تهيئة التطبيق عند تحميل الصفحة
$(document).ready(function() {
    TibyanApp.init();
});

// إضافة دعم للتصفح بالكيبورد
$(document).keydown(function(e) {
    // Ctrl + S للحفظ
    if (e.ctrlKey && e.keyCode === 83) {
        e.preventDefault();
        const saveBtn = $('.btn-save').first();
        if (saveBtn.length) {
            saveBtn.click();
        }
    }
    
    // Escape لإغلاق النوافذ المنبثقة
    if (e.keyCode === 27) {
        $('.modal.show').modal('hide');
    }
});

// تصدير الكائن للاستخدام العام
window.TibyanApp = TibyanApp;