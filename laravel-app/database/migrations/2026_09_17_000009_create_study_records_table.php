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
        // ۱. جدول بازه‌های زمانی مطالعه موظفی (Study Periods)
        Schema::create('study_periods', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title'); // عنوان دوره مطالعه (مثال: مهرماه ۱۴۰۳)
            $table->string('startDate'); // تاریخ شروع دوره شمسی
            $table->string('endDate'); // تاریخ پایان دوره شمسی
            $table->decimal('mandatoryHours', 8, 2)->default(0); // ساعت موظفی تعهد شده
            $table->string('deadlineDate')->nullable(); // مهلت نهایی ثبت
            $table->boolean('isClosed')->default(false); // دوره بسته شده؟
            $table->boolean('closedManually')->default(false);
            $table->json('exemptStudentIds')->nullable(); // طلاب مستثنی شده به صورت جی‌سان
            $table->json('exemptGrades')->nullable(); // پایه‌های مستثنی شده
            $table->json('targetGrades')->nullable(); // پایه‌های هدف
            $table->string('warningRule')->default('none'); // ضابطه اخطار: none / below_mandatory / below_mandatory_and_avg
            $table->boolean('autoWarningGenerated')->default(false);
            $table->string('mentorId')->nullable(); // مسئول پایه ناظر بر این دوره
            $table->timestamps();
        });

        // ۲. جدول گزارش‌های دوره‌ای مطالعه طلاب (Periodic Study Logs)
        Schema::create('periodic_study_logs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('periodId')->index(); // شناسه دوره مربوطه
            $table->string('studentId')->index(); // شناسه طلبه
            $table->decimal('hours', 8, 2)->default(0); // جمع کل ساعات مطالعه
            $table->decimal('studyHours', 8, 2)->default(0); // ساعات مطالعه فردی
            $table->decimal('discussionHours', 8, 2)->default(0); // ساعات مباحثه علمی
            $table->boolean('isExempt')->default(false); // طلبه معاف است؟
            $table->string('exemptionReason')->nullable(); // دلیل معافیت
            $table->integer('warningsCount')->default(0); // تعداد اخطارها
            $table->string('submittedBy')->nullable(); // ثبت شده توسط: student / grade_supervisor / officer
            $table->string('lastModifiedAt')->nullable();
            $table->timestamps();

            // روابط خارجی
            $table->foreign('periodId')->references('id')->on('study_periods')->onDelete('cascade');
            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });

        // ۳. جدول آمارهای روزانه مطالعه و مباحثه طلاب (Study Stats)
        Schema::create('study_stats', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->index();
            $table->string('date')->index(); // تاریخ روزانه شمسی (YYYY/MM/DD)
            $table->decimal('studyHours', 5, 2)->default(0); // ساعات مطالعه فردی در این روز
            $table->decimal('discussionHours', 5, 2)->default(0); // ساعات مباحثه علمی در این روز
            $table->timestamps();

            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_stats');
        Schema::dropIfExists('periodic_study_logs');
        Schema::dropIfExists('study_periods');
    }
};
