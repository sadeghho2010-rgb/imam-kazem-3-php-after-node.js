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
        // ۱. جدول منوی غذاهای روزانه و هفتگی سلف (Meal Menus)
        Schema::create('meal_menus', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('dayOfWeek')->nullable(); // روز هفته (شنبه، یکشنبه...)
            $table->string('date')->index(); // تاریخ توزیع غذا شمسی (YYYY/MM/DD)
            $table->string('mealName'); // نام غذا (خورشت قیمه، قرمه‌سبزی...)
            $table->decimal('price', 15, 2)->default(0); // قیمت تمام شده آزاد غذا (تومان)
            $table->decimal('subsidy', 15, 2)->default(0); // سهم یارانه مدرسه برای هر غذا (تومان)
            $table->text('description')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // ۲. جدول رزرو ناهار توسط طلاب (Meal Reservations)
        Schema::create('meal_reservations', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->index(); // شناسه طلبه رزرو کننده
            $table->string('nationalId')->nullable()->index(); // کد ملی طلبه
            $table->string('date')->index(); // تاریخ رزرو غذا شمسی (YYYY/MM/DD)
            $table->string('status')->default('reserved'); // وضعیت رزرو: reserved / cancelled / served (تحویل شده)
            $table->decimal('pricePaid', 15, 2)->default(0); // مبلغ کسر شده نهایی از حساب طلبه (تومان)
            $table->timestamps();

            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_reservations');
        Schema::dropIfExists('meal_menus');
    }
};
