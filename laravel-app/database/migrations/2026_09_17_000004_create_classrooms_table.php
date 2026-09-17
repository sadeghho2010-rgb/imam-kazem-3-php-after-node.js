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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->string('id')->primary(); // شناسه مَدرَس
            $table->string('name'); // نام کلاس (مثلاً مدرس ۱ شیخ انصاری)
            $table->string('code')->nullable(); // کد کلاس
            $table->integer('capacity')->nullable(); // ظرفیت به نفر
            $table->string('floor')->nullable(); // طبقه
            $table->json('facilities')->nullable(); // امکانات (دیتاپروژکتور، برد و...) به صورت آرایه جی‌سان
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // رنگ شاخص برای جدول هفتگی
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
