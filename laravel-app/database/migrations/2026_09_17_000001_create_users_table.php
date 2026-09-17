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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('name');
            $table->string('fullName')->nullable();
            $table->tinyInteger('level')->default(3); // 1: Admin/Manager, 2: Mentor/Officer, 3: Student/Rep
            $table->string('role'); // super_admin, school_manager, education_manager, etc.
            $table->string('roleTitle'); // عنوان نمایشی نقش (مثلاً سوپر ادمین)
            $table->string('scope')->default('self'); // all, global, grade_7, grade_8, grade_9, grade_10, class, self
            $table->string('gradeLabel')->nullable(); // برچسب پایه مانند "پایه ۷"
            $table->json('managedGrades')->nullable(); // پایه‌های تحت مسئولیت به صورت آرایه جی‌سان
            $table->string('mentorId')->nullable(); // hayati, hosseini, soleimani, asadi, shahpoori
            $table->string('studentId')->nullable(); // در صورت داشتن ارتباط با جدول طلاب
            $table->string('studentName')->nullable();
            $table->string('linkedStudentId')->nullable();
            $table->boolean('isReadOnly')->default(false);
            $table->boolean('canEdit')->default(true);
            $table->boolean('canManageUsers')->default(false);
            $table->boolean('canBackup')->default(false);
            $table->boolean('isActive')->default(true);
            $table->json('allowedTabs')->nullable(); // تب‌های مجاز به صورت آرایه جی‌سان
            $table->string('avatarBg')->nullable(); // رنگ پس‌زمینه آواتار
            $table->string('phone')->nullable();
            $table->string('nationalId')->nullable()->index();
            $table->string('personnelCode')->nullable();
            $table->string('bankName')->nullable();
            $table->string('bankAccount')->nullable();
            $table->string('bankSheba')->nullable();
            $table->timestamp('lastLogin')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
