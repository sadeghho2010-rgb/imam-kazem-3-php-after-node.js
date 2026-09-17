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
        Schema::create('students', function (Blueprint $table) {
            $table->string('id')->primary(); // شناسه منحصر‌به‌فرد (پشتیبانی از شناسه‌های دلخواه و لوکال)
            $table->string('name');
            $table->longText('photoUrl')->nullable(); // تصویر طلبه به صورت لینک یا رشته Base64 طولانی
            $table->string('nationalId')->nullable()->unique();
            $table->boolean('isActive')->default(true);
            $table->string('phoneNumber')->nullable();
            $table->string('grade')->nullable()->index(); // پایه تحصیلی (مثلاً پایه ۷، پایه ۸)

            // اطلاعات آموزشی مرکز مدیریت
            $table->string('managementCenterCode')->nullable();
            $table->string('instituteCode')->nullable();
            $table->string('servicesCenterCode')->nullable();

            // اطلاعات هویتی و شخصی
            $table->string('birthDate')->nullable(); // تاریخ تولد جلالی
            $table->string('birthPlace')->nullable(); // محل تولد / صادره
            $table->string('fatherName')->nullable();
            $table->string('fatherOccupation')->nullable(); // شغل پدر
            $table->string('tammomStatus')->default('غیر معمم'); // وضعیت تعمم: معمم / غیر معمم

            // وضعیت تاهل و سکونت
            $table->string('maritalStatus')->default('مجرد'); // مجرد / متاهل
            $table->integer('childrenCount')->default(0); // تعداد فرزندان
            $table->string('livingStatus')->default('پدری'); // وضعیت سکونت: پدری، خوابگاه، اجاره ای، شخصی، سایر
            $table->string('livingStatusOther')->nullable();

            // سوابق تحصیلی کلاسیک و حوزوی
            $table->string('classicEducation')->nullable(); // تحصیلات کلاسیک قبل از حوزه
            $table->string('howzaEntryYear')->nullable(); // سال ورود به حوزه
            $table->string('instituteEntryYear')->nullable(); // سال ورود به موسسه
            $table->string('levelOneSchool')->nullable(); // مدرسه سطح یک

            // وضعیت غیرفعال‌سازی پرونده
            $table->string('deactivationReason')->nullable(); // علت غیرفعال بودن
            $table->string('deactivationDate')->nullable(); // تاریخ غیرفعال‌سازی
            $table->text('deactivationNotes')->nullable(); // یادداشت‌های غیرفعال‌سازی

            // اطلاعات بانکی و شهریه
            $table->string('tuitionCode')->nullable(); // کد شهریه
            $table->string('bankName1')->nullable();
            $table->string('bankAccount1')->nullable();
            $table->string('bankSheba1')->nullable();
            $table->string('bankName2')->nullable();
            $table->string('bankAccount2')->nullable();
            $table->string('bankSheba2')->nullable();
            $table->string('activeDepositAccount')->default('account1'); // حساب فعال: account1 / account2 / both

            // سوابق پایه‌های گذشته
            $table->json('pastGrades')->nullable(); // آرایه جی‌سان پایه‌های طی شده

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
