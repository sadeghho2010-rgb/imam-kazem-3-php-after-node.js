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
        Schema::create('student_attendance_details', function (Blueprint $table) {
            $table->id();
            $table->string('sessionLogId')->index(); // شناسه جلسه مربوطه
            $table->string('studentId')->index(); // شناسه طلبه
            $table->string('studentName');
            $table->string('nationalId')->nullable();
            $table->string('status')->default('unspecified'); // present, absent, late, excused, unspecified
            $table->string('note')->nullable(); // یادداشت انضباطی
            $table->integer('lateMinutes')->default(0); // میزان تاخیر به دقیقه
            $table->boolean('isExcused')->default(false); // آیا غیبت موجه است؟
            $table->string('excuseReason')->nullable(); // علت موجه شدن
            
            // اخطارهای آموزشی صادر شده در این جلسه
            $table->boolean('hasEducationalWarning')->default(false); // آیا اخطار آموزشی ثبت شده؟
            $table->string('warningRegisteredBy')->nullable(); // کاربر صادرکننده اخطار
            $table->string('warningRegisteredAt')->nullable(); // تاریخ صدور اخطار
            
            $table->timestamps();

            // روابط خارجی
            $table->foreign('sessionLogId')->references('id')->on('attendance_session_logs')->onDelete('cascade');
            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendance_details');
    }
};
