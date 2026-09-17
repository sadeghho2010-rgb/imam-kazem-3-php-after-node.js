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
        // ۱. جدول اساتید (Teachers)
        Schema::create('teachers', function (Blueprint $table) {
            $table->string('id')->primary(); // شناسه استاد
            $table->string('fullName');
            $table->string('nationalId')->nullable()->unique();
            $table->string('teacherCode')->nullable();
            $table->string('phoneNumber')->nullable();
            $table->string('subjectSpecialty')->nullable(); // تخصص اصلی
            $table->json('courses')->nullable(); // دروس تدریسی به صورت جی‌سان
            $table->json('managedGrades')->nullable(); // پایه‌های تحت اشراف
            $table->text('photoUrl')->nullable();
            $table->json('categories')->nullable(); // دسته‌بندی اساتید (فقه، اصول و...)
            $table->json('detailedSpecialties')->nullable(); // تخصص‌های فرعی جزئی‌تر
            $table->text('notes')->nullable();
            $table->text('experienceHistory')->nullable(); // سوابق تدریس
            $table->string('bankName')->nullable();
            $table->string('bankAccount')->nullable();
            $table->string('bankSheba')->nullable();
            $table->string('priority')->default('2'); // اولویت ۱ تا ۳
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // ۲. جدول کارکنان و کادر اجرایی (Staff Members)
        Schema::create('staff_members', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('fullName');
            $table->string('staffCode')->nullable(); // کد پرسنلی
            $table->string('nationalId')->nullable()->unique();
            $table->string('roleTitle'); // سمت (مسئول آشپزخانه، خادم، راننده و...)
            $table->string('phoneNumber')->nullable();
            $table->string('bankName')->nullable();
            $table->string('bankAccount')->nullable();
            $table->string('bankSheba')->nullable();
            $table->decimal('monthlySalary', 15, 2)->default(0); // حقوق ثابت ماهانه (تومان)
            $table->boolean('isActive')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ۳. جدول رانندگان (Driver Info)
        Schema::create('driver_info', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('fullName');
            $table->string('phoneNumber')->nullable();
            $table->string('carModel')->nullable(); // مدل خودرو
            $table->string('plateNumber')->nullable(); // شماره پلاک
            $table->boolean('isActive')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ۴. جدول زمان‌بندی سرویس ایاب و ذهاب اساتید (Teacher Transport Schedule)
        Schema::create('teacher_transport_schedules', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('teacherId')->index();
            $table->string('teacherName');
            $table->string('date')->nullable(); // تاریخ شمسی تک‌جلسه
            $table->string('dayOfWeek')->nullable(); // روز هفته
            $table->json('days')->nullable(); // روزهای تکرار شونده در هفته
            $table->string('pickupTime')->nullable(); // ساعت سوار شدن
            $table->string('returnTime')->nullable(); // ساعت برگشت
            $table->text('routeDescription')->nullable(); // مسیر رفت و آمد
            $table->string('serviceNeedType')->default('arrival_departure'); // نوع نیاز: arrival_departure / arrival_only / departure_only
            $table->string('driverId')->nullable()->index();
            $table->string('driverName')->nullable();
            $table->decimal('cost', 15, 2)->default(0); // هزینه سرویس (تومان)
            $table->text('notes')->nullable();
            $table->boolean('isApprovedByEducation')->default(false); // تایید معاون آموزش
            $table->string('approvedBy')->nullable(); // شناسه کاربر تاییدکننده
            $table->string('approvedAt')->nullable(); // تاریخ تایید
            $table->string('approvedByName')->nullable(); // نام تایید کننده
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_transport_schedules');
        Schema::dropIfExists('driver_info');
        Schema::dropIfExists('staff_members');
        Schema::dropIfExists('teachers');
    }
};
