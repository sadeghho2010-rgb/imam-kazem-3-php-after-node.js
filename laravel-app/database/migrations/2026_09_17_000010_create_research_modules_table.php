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
        // ۱. جدول پرونده‌های پژوهشی طلاب (Research Records)
        Schema::create('research_records', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->index();
            $table->string('topic')->nullable(); // موضوع پژوهش
            $table->string('type')->default('individual'); // نوع: individual / group
            $table->json('teamMemberIds')->nullable(); // شناسه‌های اعضای هم‌گروهی در پژوهش‌های گروهی
            $table->string('stage'); // مرحله پیشرفت پژوهش (طرح، فیش‌نویسی، تدوین، نهایی)
            $table->text('description')->nullable();
            $table->text('professorNotes')->nullable(); // یادداشت استاد راهنما
            $table->text('supervisorNotes')->nullable(); // یادداشت ارزیاب
            $table->text('criticNotes')->nullable(); // یادداشت استاد ناقد
            $table->string('score')->nullable(); // نمره یا رتبه پژوهش
            $table->json('usages')->nullable(); // کاربردها (ارائه کلاس، جشنواره رشد، آرشیو)
            $table->boolean('needsFollowUp')->default(false);
            $table->string('followUpTodoId')->nullable();
            $table->timestamps();

            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });

        // ۲. جدول تاریخچه و آرشیو پژوهش‌ها (Research History)
        Schema::create('research_history', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->index();
            $table->string('topic');
            $table->string('type')->nullable();
            $table->string('stage')->nullable();
            $table->string('academicYearOrPeriod')->nullable(); // سال تحصیلی یا دوره
            $table->text('description')->nullable();
            $table->text('summary')->nullable(); // خلاصه پژوهش
            $table->string('score')->nullable();
            $table->text('professorNotes')->nullable();
            $table->text('supervisorNotes')->nullable();
            $table->text('criticNotes')->nullable();
            $table->json('usages')->nullable();
            $table->string('archivedAt'); // تاریخ آرشیو شمسی
            $table->json('originalRecordSnapshot')->nullable(); // تصویر لحظه‌ای رکورد اصلی به صورت جی‌سان
            $table->timestamps();

            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });

        // ۳. تعریف مهارت‌های پژوهشی حوزوی (Research Skills Definition)
        Schema::create('research_skills_def', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title'); // عنوان مهارت (مثال: روش فیش‌نویسی، روش منبع‌یابی)
            $table->string('category')->default('عمومی'); // روش و ابزار، نگارش و ویرایش، نرم‌افزار و دیجیتال، زبان و ترجمه، عمومی
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ۴. جدول انتساب مهارت‌های پژوهشی طلاب (Student Research Skills)
        Schema::create('student_research_skills', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->index();
            $table->json('skillIds')->nullable(); // شناسه‌های مهارت‌های کسب شده از جدول تعاریف
            $table->json('customSkills')->nullable(); // مهارت‌های سفارشی متفرقه
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });

        // ۵. جدول امتحانات شفاهی طلاب (Oral Exams)
        Schema::create('oral_exams', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->index();
            $table->string('title'); // عنوان امتحان (مثلاً امتحان شفاهی کفایه)
            $table->string('subjectType'); // فقه، اصول، امتحان ورودی، سایر
            $table->decimal('score', 4, 2)->default(0); // نمره امتحان شفاهی
            $table->string('examinerName'); // نام استاد ممتحن
            $table->string('date')->index(); // تاریخ امتحان شمسی
            $table->boolean('isRetake')->default(false); // آیا امتحان مجدد (تجدیدنظر/جبرانی) است؟
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });

        // ۶. جدول گروه‌های مباحثه سنتی طلاب (Discussion Groups)
        Schema::create('discussion_groups', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('title'); // نام گروه مباحثه
            $table->string('subject')->nullable(); // موضوع مورد مباحثه
            $table->string('grade')->nullable(); // پایه هدف (پایه ۷، ۸ و...)
            $table->string('mentorId')->nullable(); // استاد راهنمای ناظر
            $table->string('programId')->nullable(); // برنامه درسی همبسته
            $table->string('programTitle')->nullable();
            $table->json('memberStudentIds')->nullable(); // شناسه‌های طلاب عضو گروه مباحثه
            $table->json('externalMembers')->nullable(); // هم‌بحث‌های خارجی مهمان
            $table->string('room')->nullable(); // محل مباحثه (مدرس یا حجره)
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_groups');
        Schema::dropIfExists('oral_exams');
        Schema::dropIfExists('student_research_skills');
        Schema::dropIfExists('research_skills_def');
        Schema::dropIfExists('research_history');
        Schema::dropIfExists('research_records');
    }
};
