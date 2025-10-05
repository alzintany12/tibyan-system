@extends('layouts.app')

@section('page-title', 'صفحة مؤقتة - tasks.create')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item active">tasks.create</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    <h5>صفحة مؤقتة: tasks.create</h5>
    <p>تم إنشاء ملف العرض تلقائيًا لأنه كان مفقود. استبدل هذا المحتوى بالمظهر الصحيح.</p>
    <p><a href="{{ url()->previous() }}" class="btn btn-secondary">رجوع</a></p>
  </div>
</div>
@endsection
