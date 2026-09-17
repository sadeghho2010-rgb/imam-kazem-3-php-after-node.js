<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TuitionController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\AIController;

/*
|--------------------------------------------------------------------------
| Full API Routes for Hawzah ERP System in Laravel 12
|--------------------------------------------------------------------------
*/

// Public Authentication
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Users
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/users/{id}/credentials', [AuthController::class, 'updateCredentials']);

    // 1. Students Module
    Route::middleware('roleOrTab:students')->group(function () {
        Route::get('/students', [StudentController::class, 'index']);
        Route::post('/students', [StudentController::class, 'store']);
        Route::get('/students/{id}', [StudentController::class, 'show']);
        Route::put('/students/{id}', [StudentController::class, 'update']);
        Route::patch('/students/{id}/status', [StudentController::class, 'toggleActiveStatus']);
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
    });

    // 2. Programs & Classes Module
    Route::middleware('roleOrTab:programs')->group(function () {
        Route::get('/programs', [ProgramController::class, 'index']);
        Route::post('/programs', [ProgramController::class, 'store']);
        Route::put('/programs/{id}', [ProgramController::class, 'update']);
        Route::post('/programs/{id}/enrollments', [ProgramController::class, 'enrollStudents']);
        Route::delete('/programs/{id}', [ProgramController::class, 'destroy']);
    });

    // 3. Attendance Module
    Route::middleware('roleOrTab:attendance')->group(function () {
        Route::get('/attendance/sessions', [AttendanceController::class, 'index']);
        Route::post('/attendance/sessions', [AttendanceController::class, 'saveSession']);
        Route::patch('/attendance/details/{detailId}/excuse', [AttendanceController::class, 'excuseAbsence']);
    });

    // 4. Tuition & Finance ERP
    Route::middleware('roleOrTab:tuition')->group(function () {
        Route::get('/tuition/settings', [TuitionController::class, 'getSettings']);
        Route::put('/tuition/settings', [TuitionController::class, 'updateSettings']);
        Route::post('/tuition/calculate', [TuitionController::class, 'calculatePeriod']);
        Route::get('/tuition/periods', [TuitionController::class, 'getPeriods']);
    });

    // 5. Loans Box
    Route::middleware('roleOrTab:loans')->group(function () {
        Route::get('/loans', [LoanController::class, 'index']);
        Route::post('/loans', [LoanController::class, 'store']);
        Route::post('/loans/{id}/pay-installment', [LoanController::class, 'payInstallment']);
    });

    // 6. Expenses & Petty Cash
    Route::middleware('roleOrTab:expenses')->group(function () {
        Route::get('/expenses', [ExpenseController::class, 'index']);
        Route::post('/expenses', [ExpenseController::class, 'store']);
    });

    // 7. Meals & Self-Service
    Route::middleware('roleOrTab:meals')->group(function () {
        Route::get('/meals/menus', [MealController::class, 'getMenus']);
        Route::post('/meals/menus', [MealController::class, 'saveMenu']);
        Route::post('/meals/reserve', [MealController::class, 'reserveMeal']);
        Route::delete('/meals/reserve/{id}', [MealController::class, 'cancelReservation']);
    });

    // 8. Research & Oral Exams
    Route::middleware('roleOrTab:research')->group(function () {
        Route::get('/research/records', [ResearchController::class, 'index']);
        Route::post('/research/records', [ResearchController::class, 'store']);
        Route::get('/research/oral-exams', [ResearchController::class, 'oralExams']);
        Route::post('/research/oral-exams', [ResearchController::class, 'storeOralExam']);
        Route::get('/research/discussion-groups', [ResearchController::class, 'discussionGroups']);
        Route::post('/research/discussion-groups', [ResearchController::class, 'storeDiscussionGroup']);
    });

    // 9. Workflow & Tasks
    Route::get('/workflow/items', [WorkflowController::class, 'index']);
    Route::post('/workflow/items', [WorkflowController::class, 'store']);
    Route::patch('/workflow/items/{id}/status', [WorkflowController::class, 'updateStatus']);

    // 10. AI Consultation & Records Analysis
    Route::post('/ai/consult', [AIController::class, 'consult']);
    Route::post('/ai/analyze/{studentId}', [AIController::class, 'analyzeStudentRecord']);

});
