<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ۱. جدول دوره‌های محاسباتی شهریه طلاب (Tuition Periods)
        Schema::create('tuition_periods', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title'); // عنوان دوره پرداخت (مثال: شهریه آبان‌ماه ۱۴۰۳)
            $table->string('startDate'); // تاریخ شروع محاسباتی دوره شمسی
            $table->string('endDate'); // تاریخ پایان محاسباتی دوره شمسی
            $table->string('status')->default('draft'); // وضعیت دوره: draft / finalized / paid
            $table->integer('totalStudentsCalculated')->default(0); // تعداد کل محاسبات صورت گرفته
            $table->decimal('totalPayoutAmount', 15, 2)->default(0); // مجموع کل پرداختی ناخالص این دوره (تومان)
            $table->json('calculations')->nullable(); // کل محاسبات تفصیلی طلاب به صورت گزارش جی‌سان
            $table->timestamps();
        });

        // ۲. جدول تنظیمات سراسری محاسبات فرمولی شهریه (Tuition Settings)
        Schema::create('tuition_settings', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->boolean('isBaseTuitionEqualForMarried')->default(false); // آیا شهریه مجرد و متاهل برابر است؟
            $table->decimal('singleBaseTuition', 15, 2)->default(0); // شهریه پایه مجردین (تومان)
            $table->decimal('marriedBaseTuition', 15, 2)->default(0); // شهریه پایه متاهلین (تومان)
            
            // وضعیت تاهل
            $table->boolean('hasMarriageBonus')->default(false);
            $table->string('marriageBonusType')->default('fixed'); // fixed / percentage
            $table->decimal('marriageBonusAmount', 15, 2)->default(0);
            $table->decimal('marriageBonusPercent', 5, 2)->default(0);

            // حق اولاد
            $table->boolean('hasChildAllowance')->default(false);
            $table->decimal('childAllowance', 15, 2)->default(0); // به ازای هر فرزند

            // پاداش تلبس و معمم بودن
            $table->boolean('hasTurbanAllowance')->default(false);
            $table->decimal('turbanAllowance', 15, 2)->default(0);

            // کمک‌هزینه مسکن
            $table->boolean('hasHousingAllowance')->default(false);
            $table->decimal('housingAllowanceRented', 15, 2)->default(0); // اجاره‌نشینی
            $table->decimal('housingAllowanceDorm', 15, 2)->default(0); // خوابگاهی

            // تنظیمات تشویقی/تعدیلی ساعات مطالعه طلاب
            $table->boolean('studyBonusEnabled')->default(false);
            $table->string('studyBonusBase')->default('mandatory'); // نسبت به ساعت موظفی (mandatory) یا میانگین کل طلاب (average)
            $table->boolean('studyBonusTiered')->default(false); // محاسبه پله‌ای؟
            $table->json('studyBonusTiers')->nullable(); // تعریف پله‌های پاداش مطالعه به صورت جی‌سان
            $table->decimal('studyBonusPerHour', 15, 2)->default(0); // پاداش هر ساعت مازاد بر تعهد
            $table->decimal('studyBonusFixedAmount', 15, 2)->default(0); // پاداش ثابت

            $table->timestamps();
        });

        // ۳. جدول وضعیت و حساب مالی مستقل هر طلبه (Student Financial Profiles)
        Schema::create('student_financial_profiles', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->index();
            $table->decimal('debtAmount', 15, 2)->default(0); // بدهی قبلی انباشته (تومان)
            $table->decimal('creditAmount', 15, 2)->default(0); // بستانکاری یا طلب انباشته (تومان)
            $table->text('bankDetails')->nullable(); // جزئیات حساب‌های ثبت شده
            $table->boolean('isExemptFromTuition')->default(false); // آیا از دریافت شهریه معاف/محروم است؟
            $table->decimal('customAllowanceAmount', 15, 2)->default(0); // مبلغ کمک‌هزینه اختصاصی و دستی مستمر
            $table->decimal('customDeductionAmount', 15, 2)->default(0); // مبلغ کسورات دستی اختصاصی و مستمر
            $table->string('customReason')->nullable(); // دلیل کمک‌هزینه یا کسر دستی
            $table->timestamps();

            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });

        // ۴. جدول صندوق قرض‌الحسنه و وام‌های فعال طلاب (Finance Loans)
        Schema::create('finance_loans', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->index();
            $table->decimal('loanAmount', 15, 2)->default(0); // مبلغ وام پرداختی (تومان)
            $table->decimal('installmentAmount', 15, 2)->default(0); // مبلغ هر قسط (تومان)
            $table->integer('totalInstallments')->default(0); // تعداد کل اقساط وام
            $table->integer('remainingInstallments')->default(0); // تعداد اقساط باقی‌مانده جهت تسویه
            $table->integer('paidInstallmentsCount')->default(0); // تعداد اقساط پرداخت شده
            $table->string('status')->default('active'); // وضعیت وام: active / settled (تسویه شده)
            $table->string('loanDate')->nullable(); // تاریخ پرداخت وام
            $table->text('description')->nullable(); // توضیحات و دلایل وام
            $table->timestamps();

            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });

        // ۵. جدول هزینه‌ها، اسناد و تنخواه‌گردان مدرسه (Finance Expenses)
        Schema::create('finance_expenses', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title'); // عنوان هزینه (مثلاً خرید اقلام اداری)
            $table->decimal('amount', 15, 2)->default(0); // مبلغ هزینه (تومان)
            $table->string('category')->index(); // دسته‌بندی فاکتور (اداری، عمرانی، نهارخوری، مناسبتی و...)
            $table->string('date')->index(); // تاریخ هزینه شمسی
            $table->string('payTo')->nullable(); // دریافت کننده مبلغ / نام فروشگاه
            $table->text('factorPhotoUrl')->nullable(); // پیوست تصویر فاکتور خرید
            $table->string('status')->default('approved'); // وضعیت فاکتور: pending / approved / rejected
            $table->string('recordedBy')->nullable(); // کاربر ثبت کننده هزینه
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_expenses');
        Schema::dropIfExists('finance_loans');
        Schema::dropIfExists('student_financial_profiles');
        Schema::dropIfExists('tuition_settings');
        Schema::dropIfExists('tuition_periods');
    }
};
