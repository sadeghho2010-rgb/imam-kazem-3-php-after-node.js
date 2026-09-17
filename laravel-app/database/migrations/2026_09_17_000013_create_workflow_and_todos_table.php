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
        // ۱. جدول جریان‌های کاری و کارتابل اداری طلاب (Workflow Items)
        Schema::create('workflow_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->nullable()->index(); // شناسه طلبه متقاضی
            $table->string('studentName')->nullable();
            $table->string('title'); // عنوان درخواست (مثلاً درخواست مرخصی اضطراری)
            $table->text('description')->nullable(); // توضیحات درخواست
            $table->string('category')->index(); // دسته‌بندی: loan (وام)، vacation (مرخصی)، transition (انتقال)، other (سایر)
            $table->string('status')->default('pending'); // وضعیت: pending / approved / rejected / needing_info
            $table->string('senderUserId')->nullable()->index(); // فرستنده اولیه
            $table->string('receiverUserId')->nullable()->index(); // گیرنده کارتابلی فعلی
            $table->string('senderName')->nullable();
            $table->string('receiverName')->nullable();
            $table->integer('step')->default(1); // مرحله کارتابل فعلی
            $table->json('history')->nullable(); // تاریخچه ارجاع‌ها، یادداشت‌های تایید و رد به صورت جی‌سان
            $table->text('attachmentUrl')->nullable(); // پیوست مدرک یا سند
            $table->timestamps();
        });

        // ۲. جدول دسته‌بندی وظایف کاربران (User Todo Categories)
        Schema::create('user_todo_categories', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('userId')->index();
            $table->string('name'); // نام دسته‌بندی (مثلاً شخصی، کاری، مالی)
            $table->string('color')->nullable(); // رنگ شاخص هگز یا کلاس تیل‌وند
            $table->timestamps();
        });

        // ۳. جدول کارهای شخصی کاربران (Personal Todos)
        Schema::create('personal_todos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('userId')->index();
            $table->string('userName')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('عمومی'); // نام دسته‌بندی
            $table->boolean('completed')->default(false);
            $table->string('completedAt')->nullable(); // تاریخ تکمیل
            $table->boolean('archived')->default(false);
            $table->string('archivedAt')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->string('dueDate')->nullable(); // مهلت انجام شمسی
            $table->timestamps();
        });

        // ۴. جدول کارهای ارجاعی به سایر اعضا (Assigned Todos)
        Schema::create('assigned_todos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('senderUserId')->index();
            $table->string('senderUserName')->nullable();
            $table->string('senderRoleTitle')->nullable();
            $table->string('recipientUserId')->index();
            $table->string('recipientUserName')->nullable();
            $table->string('recipientRoleTitle')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->string('dueDate')->nullable(); // مهلت انجام شمسی
            $table->string('status')->default('pending'); // pending / completed
            $table->string('completedAt')->nullable();
            $table->text('completionNote')->nullable(); // یادداشت نهایی اتمام کار
            $table->boolean('archivedBySender')->default(false);
            $table->boolean('archivedByRecipient')->default(false);
            $table->timestamps();
        });

        // ۵. جدول وظایف پیگیری طلاب و سوابق (General Student Todos)
        Schema::create('todos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('studentId')->nullable()->index(); // شناسه طلبه منتسب
            $table->string('title');
            $table->boolean('completed')->default(false);
            $table->string('dueDate')->nullable(); // تاریخ سررسید انجام شمسی
            $table->boolean('isResearchFollowUp')->default(false); // آیا مربوط به پیگیری پروژه‌های پژوهشی است؟
            $table->boolean('isStudyFollowUp')->default(false); // آیا مربوط به پیگیری مطالعات است؟
            $table->string('researchRecordId')->nullable()->index(); // شناسه پرونده پژوهشی همبسته
            $table->string('periodId')->nullable()->index(); // شناسه دوره مطالعاتی همبسته
            $table->string('mentorId')->nullable()->index(); // شناسه استاد راهنمای مربوطه
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
        Schema::dropIfExists('assigned_todos');
        Schema::dropIfExists('personal_todos');
        Schema::dropIfExists('user_todo_categories');
        Schema::dropIfExists('workflow_items');
    }
};
