<?php $__env->startSection('page-title', 'صفحة مؤقتة - hearings.edit'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">الرئيسية</a></li>
    <li class="breadcrumb-item active">hearings.edit</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-body">
    <h5>صفحة مؤقتة: hearings.edit</h5>
    <p>تم إنشاء ملف العرض تلقائيًا لأنه كان مفقود. استبدل هذا المحتوى بالمظهر الصحيح.</p>
    <p><a href="<?php echo e(url()->previous()); ?>" class="btn btn-secondary">رجوع</a></p>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tibyan-system\resources\views/hearings/edit.blade.php ENDPATH**/ ?>