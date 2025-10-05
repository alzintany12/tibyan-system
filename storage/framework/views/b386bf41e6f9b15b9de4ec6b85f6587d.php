<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة <?php echo e($invoice->invoice_number); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            direction: rtl;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .company-info {
            color: #666;
            font-size: 11px;
        }
        
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            color: #333;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .invoice-info, .client-info {
            width: 48%;
        }
        
        .info-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #007bff;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .items-table td:first-child {
            text-align: right;
        }
        
        .total-section {
            float: left;
            width: 300px;
            margin-top: 20px;
        }
        
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .total-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
        }
        
        .total-table .total-label {
            text-align: right;
            font-weight: bold;
        }
        
        .total-table .total-amount {
            text-align: left;
            width: 100px;
        }
        
        .grand-total {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        .notes {
            clear: both;
            margin-top: 40px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-paid {
            background-color: #28a745;
            color: white;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #333;
        }
        
        .status-overdue {
            background-color: #dc3545;
            color: white;
        }
        
        .status-draft {
            background-color: #6c757d;
            color: white;
        }
        
        @media print {
            .invoice-container {
                max-width: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">نظام تبيان للمحاماة</div>
            <div class="company-info">
                نظام إدارة شؤون المحاماة والاستشارات القانونية<br>
                المملكة العربية السعودية
            </div>
        </div>
        
        <!-- Invoice Title -->
        <div class="invoice-title">فـــاتــــورة</div>
        
        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="invoice-info">
                <div class="info-title">تفاصيل الفاتورة</div>
                <div class="info-item">
                    <span class="label">رقم الفاتورة:</span>
                    <?php echo e($invoice->invoice_number); ?>

                </div>
                <div class="info-item">
                    <span class="label">تاريخ الفاتورة:</span>
                    <?php echo e($invoice->invoice_date->format('Y/m/d')); ?>

                </div>
                <div class="info-item">
                    <span class="label">تاريخ الاستحقاق:</span>
                    <?php echo e($invoice->due_date->format('Y/m/d')); ?>

                </div>
                <div class="info-item">
                    <span class="label">الحالة:</span>
                    <?php switch($invoice->status):
                        case ('paid'): ?>
                            <span class="status-badge status-paid">مدفوعة</span>
                            <?php break; ?>
                        <?php case ('overdue'): ?>
                            <span class="status-badge status-overdue">متأخرة</span>
                            <?php break; ?>
                        <?php case ('draft'): ?>
                            <span class="status-badge status-draft">مسودة</span>
                            <?php break; ?>
                        <?php default: ?>
                            <span class="status-badge status-pending">في الانتظار</span>
                    <?php endswitch; ?>
                </div>
            </div>
            
            <div class="client-info">
                <div class="info-title">بيانات العميل</div>
                <div class="info-item">
                    <span class="label">اسم العميل:</span>
                    <?php echo e($invoice->client_name); ?>

                </div>
                <?php if($invoice->case): ?>
                    <div class="info-item">
                        <span class="label">رقم القضية:</span>
                        <?php echo e($invoice->case->case_number); ?>

                    </div>
                    <?php if($invoice->case->court_name): ?>
                        <div class="info-item">
                            <span class="label">المحكمة:</span>
                            <?php echo e($invoice->case->court_name); ?>

                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Description -->
        <?php if($invoice->description): ?>
            <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border: 1px solid #ddd;">
                <strong>وصف الخدمة:</strong><br>
                <?php echo e($invoice->description); ?>

            </div>
        <?php endif; ?>
        
        <!-- Items Table -->
        <?php if($invoice->items->count() > 0): ?>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">الوصف</th>
                        <th style="width: 15%;">الكمية</th>
                        <th style="width: 20%;">السعر</th>
                        <th style="width: 15%;">المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td style="text-align: right;"><?php echo e($item->description); ?></td>
                            <td><?php echo e($item->quantity); ?></td>
                            <td><?php echo e(number_format($item->unit_price, 2)); ?></td>
                            <td><?php echo e(number_format($item->total_price, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <!-- Total Section -->
        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td class="total-label">المبلغ الأساسي:</td>
                    <td class="total-amount"><?php echo e(number_format($invoice->amount, 2)); ?></td>
                </tr>
                
                <?php if($invoice->tax_amount > 0): ?>
                    <tr>
                        <td class="total-label">الضريبة:</td>
                        <td class="total-amount"><?php echo e(number_format($invoice->tax_amount, 2)); ?></td>
                    </tr>
                <?php endif; ?>
                
                <?php if($invoice->discount_amount > 0): ?>
                    <tr>
                        <td class="total-label">الخصم:</td>
                        <td class="total-amount">-<?php echo e(number_format($invoice->discount_amount, 2)); ?></td>
                    </tr>
                <?php endif; ?>
                
                <tr class="grand-total">
                    <td class="total-label">المجموع الكلي:</td>
                    <td class="total-amount"><?php echo e(currency($invoice->total_amount)); ?></td>
                </tr>
                
                <?php if($invoice->paid_amount > 0): ?>
                    <tr>
                        <td class="total-label">المبلغ المدفوع:</td>
                        <td class="total-amount"><?php echo e(number_format($invoice->paid_amount, 2)); ?></td>
                    </tr>
                    
                    <?php if($invoice->total_amount - $invoice->paid_amount > 0): ?>
                        <tr style="color: #dc3545; font-weight: bold;">
                            <td class="total-label">المبلغ المتبقي:</td>
                            <td class="total-amount"><?php echo e(number_format($invoice->total_amount - $invoice->paid_amount, 2)); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
            </table>
        </div>
        
        <!-- Notes -->
        <?php if($invoice->notes): ?>
            <div class="notes">
                <div class="notes-title">ملاحظات:</div>
                <div><?php echo e($invoice->notes); ?></div>
            </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="footer">
            <p>تم إنشاء هذه الفاتورة بواسطة نظام تبيان للمحاماة</p>
            <p>تاريخ الطباعة: <?php echo e(now()->format('Y/m/d H:i')); ?></p>
        </div>
    </div>
</body>
</html><?php /**PATH C:\laragon\www\tibyan-system\resources\views/invoices/print.blade.php ENDPATH**/ ?>