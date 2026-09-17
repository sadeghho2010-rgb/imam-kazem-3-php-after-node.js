@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Hero / Summary Banner -->
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800">داشبورد جامع مدیریت حوزه علمیه</h2>
            <p class="text-sm text-stone-500 mt-1">کلیه ماژول‌ها، سرویس‌های مالی پله‌ای، سوابق تحصیلی و ارتباطات هوش مصنوعی جمینای آماده بهره‌برداری در هاست است.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-lg bg-stone-100 text-stone-700 text-sm font-medium border border-stone-200">
                نسخه سرور: Laravel 12.x
            </span>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-sm">
            <span class="text-xs font-medium text-stone-500">ماژول آموزشی و طلاب</span>
            <p class="text-2xl font-bold text-stone-800 mt-2">فعال</p>
            <span class="text-xs text-emerald-600 mt-1 block">پرونده‌ها و حضور و غیاب</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-sm">
            <span class="text-xs font-medium text-stone-500">سیستم محاسبات شهریه پله‌ای</span>
            <p class="text-2xl font-bold text-stone-800 mt-2">فعال</p>
            <span class="text-xs text-blue-600 mt-1 block">پاداش مطالعه، اولاد و مسکن</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-sm">
            <span class="text-xs font-medium text-stone-500">صندوق وام و تنخواه‌گردان</span>
            <p class="text-2xl font-bold text-stone-800 mt-2">فعال</p>
            <span class="text-xs text-amber-600 mt-1 block">کسر خودکار اقساط از فیش</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-stone-200 shadow-sm">
            <span class="text-xs font-medium text-stone-500">سلف تغذیه و پژوهش</span>
            <p class="text-2xl font-bold text-stone-800 mt-2">فعال</p>
            <span class="text-xs text-purple-600 mt-1 block">منو، رزرو و امتحانات شفاهی</span>
        </div>
    </div>

    <!-- Modules Navigation Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Students & Classrooms Card -->
        <div class="bg-white rounded-xl border border-stone-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-stone-800 text-lg">آموزش و پرونده طلاب</h3>
                <p class="text-sm text-stone-500 mt-2 leading-relaxed">
                    مدیریت پرونده‌های جامع، سوابق تحصیلی، ثبت‌نام در دروس، مدیریت مدرس‌ها و حضور و غیاب کلاسی همراه با ثبت اخطار آموزشی.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                <span>کنترلر: <code>StudentController</code></span>
                <span class="text-emerald-600 font-semibold">تکمیل شده</span>
            </div>
        </div>

        <!-- Financial & Loans Card -->
        <div class="bg-white rounded-xl border border-stone-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-stone-800 text-lg">امور مالی و شهریه پله‌ای</h3>
                <p class="text-sm text-stone-500 mt-2 leading-relaxed">
                    موتور محاسباتی شهریه با فرمول‌های حق تلبس، اولاد، مسکن، پاداش ساعات مطالعه، صندوق قرض‌الحسنه و فاکتورهای تنخواه.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                <span>سرویس: <code>TuitionCalculator</code></span>
                <span class="text-emerald-600 font-semibold">تکمیل شده</span>
            </div>
        </div>

        <!-- AI Assistant Card -->
        <div class="bg-white rounded-xl border border-stone-200 p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-stone-800 text-lg">دستیار هوش مصنوعی جمینای</h3>
                <p class="text-sm text-stone-500 mt-2 leading-relaxed">
                    تحلیل خودکار پرونده‌های تحصیلی طلاب و ارائه مشاوره‌های تربیتی و برنامه‌ریزی درسی ویژه مدیران و اساتید.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                <span>سرویس: <code>GeminiService</code></span>
                <span class="text-emerald-600 font-semibold">تکمیل شده</span>
            </div>
        </div>

    </div>

</div>
@endsection
