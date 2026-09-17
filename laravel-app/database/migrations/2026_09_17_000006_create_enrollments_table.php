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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->string('id')->primary(); // شناسه منحصر‌به‌فرد ثبت‌نام
            $table->string('studentId')->index(); // شناسه طلبه
            $table->string('programId')->index(); // شناسه برنامه/درس

            // کلیدهای خارجی با توجه به شناسه از نوع رشته
            $table->foreign('studentId')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('programId')->references('id')->on('programs')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
