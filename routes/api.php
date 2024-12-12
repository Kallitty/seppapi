<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\API\QuizResultController;
use App\Http\Controllers\API\UserController;
use Illuminate\View\Concerns\ManagesFragments;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Routes accessible by all authenticated users
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/checkingAuthenticated', function () {
        return response()->json(['message' => 'You are in', 'status' => 200], 200);
    });

    Route::post('logout', [AuthController::class, 'logout']);
   
    Route::post('/start-quiz', [QuizResultController::class, 'startQuiz']);
    Route::post('/store-quiz-results', [QuizResultController::class, 'storeQuizResults']);
    Route::get('/user-quiz-results', [QuizResultController::class, 'getUserResults']);

    Route::get('/quizzes', [QuizController::class, 'index']);
    Route::get('/quizzes/{id}', [QuizController::class, 'show']);

    Route::post('/user/profile-picture', [UserController::class, 'updateProfilePicture']);
    Route::get('/user', [UserController::class, 'getUser']);
});

// Routes accessible only by admin users
Route::middleware(['auth:sanctum', 'isAPIAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AuthController::class, 'dashboard']);

    // Quiz management
    Route::post('/create-quiz', [QuizController::class, 'store']);
    
    
    Route::put('/quizzes/{id}', [QuizController::class, 'update']);
    Route::delete('/quizzes/{id}', [QuizController::class, 'destroy']);

    //User Management

    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);


    // Category management
    // Route::post('/store-category', [CategoryController::class, 'store']);
    // Route::get('/view-category', [CategoryController::class, 'index']);
});


