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
        Schema::create('programs', function (Blueprint $table) {
            $table->string('id')->primary(); // شناسه برنامه/درس
            $table->string('title'); // عنوان کلاس یا درس
            $table->string('type'); // اصلی، مشاوره، پژوهش، دروس ۵ شنبه، سایر
            $table->string('day')->nullable(); // روز تکی هفته (در صورت لزوم)
            $table->json('days')->nullable(); // روزهای برگزاری کلاس (مثال: ['شنبه', 'دوشنبه']) به صورت جی‌سان
            $table->string('time')->nullable(); // بازه زمانی نمایشی (مثال: "08:00 تا 09:30")
            $table->string('startTime')->nullable(); // زمان شروع دقیق (مثال: "08:00")
            $table->string('endTime')->nullable(); // زمان پایان دقیق (مثال: "09:30")
            $table->string('teacher')->nullable()->index(); // نام استاد یا شناسه استاد انتسابی از جدول اساتید
            $table->string('classroom')->nullable()->index(); // نام مَدرَس یا شناسه کلاس از جدول کلاس‌ها
            $table->string('grade')->nullable()->index(); // پایه تحصیلی هدف (پایه ۷، ۸ و...)
            $table->integer('capacity')->nullable();
            $table->text('notes')->nullable();
            $table->string('mentorId')->nullable()->index(); // شناسه استاد راهنما (مسئول پایه)
            $table->string('parentProgramId')->nullable()->index(); // شناسه برنامه مرجع (برای کلاس‌های همبسته یا مشاوره‌ای)
            $table->json('representativeStudentIds')->nullable(); // شناسه‌های طلاب نماینده کلاس
            $table->json('representativeNames')->nullable(); // نام‌های نمایندگان کلاس
            $table->string('customRepresentative')->nullable(); // نماینده متفرقه خارج از طلاب
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
