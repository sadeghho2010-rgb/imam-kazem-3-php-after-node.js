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
        Schema::create('attendance_session_logs', function (Blueprint $table) {
            $table->string('id')->primary(); // شناسه جلسه (مثلا programId_date)
            $table->string('programId')->index();
            $table->string('programTitle');
            $table->string('grade')->nullable()->index();
            $table->string('date')->index(); // تاریخ شمسی جلسه (مثلاً YYYY/MM/DD)
            $table->string('dayOfWeek')->nullable();
            $table->boolean('isCancelled')->default(false); // آیا کلاس کنسل شده؟
            $table->string('cancellationReason')->nullable();
            
            // وضعیت اساتید جایگزین در جلسه
            $table->boolean('hasSubstituteTeacher')->default(false); // آیا استاد جایگزین حضور داشته؟
            $table->string('substituteTeacherId')->nullable(); // شناسه استاد جایگزین
            $table->string('substituteTeacherName')->nullable(); // نام استاد جایگزین
            $table->text('substituteTeacherNotes')->nullable();
            
            $table->text('notes')->nullable(); // یادداشت‌ها و سرفصل کلاس
            $table->string('recordedByUserId')->nullable(); // کاربر ثبت‌کننده
            $table->string('recordedByName')->nullable();
            $table->string('recordedAt')->nullable(); // زمان ثبت گزارش جلالی/میلادی
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_session_logs');
    }
};
