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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('userId')->nullable()->index(); // شناسه کاربر فاعل
            $table->string('userName')->nullable(); // نام کاربر
            $table->string('roleTitle')->nullable(); // نقش کاربر در زمان وقوع رویداد
            $table->string('action')->index(); // رویداد (مانند ثبت نمره، حذف طلبه، ویرایش شهریه)
            $table->string('module')->index(); // ماژول مربوطه (مالی، آموزشی، انضباطی، پژوهش)
            $table->json('details')->nullable(); // اطلاعات تغییرات یا پارامترهای جزئی به صورت جی‌سان
            $table->string('ip')->nullable(); // آی‌پی کاربر
            $table->string('studentId')->nullable()->index(); // پرونده طلبه هدف رویداد (در صورت همبستگی)
            $table->string('studentName')->nullable();
            $table->string('timestamp')->nullable(); // زمان ثبت وقوع رویداد به جلالی/میلادی
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
