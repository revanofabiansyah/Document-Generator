<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserDocumentController;
use App\Http\Controllers\UserManagementController;
use App\Models\Document;
use App\Models\User;

// Halaman utama
Route::get('/', function () {
    return view('index'); // ini sesuai dengan resources/views/index.blade.php
})->name('home');

// ===== USER AUTH =====
Route::get('/login', [AuthController::class, 'showUserLogin'])->name('login');
Route::post('/login', [AuthController::class, 'loginUser'])->name('login.post');

Route::get('/register', [AuthController::class, 'showUserRegister'])->name('register');
Route::post('/register', [AuthController::class, 'registerUser'])->name('register.post');

// ===== ADMIN AUTH =====
Route::get('/login-admin', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/login-admin', [AuthController::class, 'loginAdmin'])->name('admin.login.post');

Route::get('/register-admin', [AuthController::class, 'showAdminRegister'])->name('admin.register');
Route::post('/register-admin', [AuthController::class, 'registerAdmin'])->name('admin.register.post');

// ===== LOGOUT =====
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== USER DOCUMENTS =====
Route::middleware(['auth'])->group(function () {
    Route::get('/user-{user:name}', [UserDocumentController::class, 'list'])->name('documents.user.list');
    Route::post('/user-{user:name}/template/{templateId}/start', [UserDocumentController::class, 'startFilling'])->name('documents.user.start');
    Route::get('/user-{user:name}/{document}/fill', [UserDocumentController::class, 'fill'])->name('documents.user.fill');
    Route::post('/user-{user:name}/{document}/fill', [UserDocumentController::class, 'save'])->name('documents.user.save');
    Route::delete('/user-{user:name}/{document}', [UserDocumentController::class, 'delete'])->name('documents.user.delete');
    Route::get('/user-{user:name}/{document}/preview', [UserDocumentController::class, 'preview'])->name('documents.user.preview');
    Route::get('/user-{user:name}/{document}/download', [UserDocumentController::class, 'download'])->name('documents.user.download');
});

// ===== DASHBOARD ADMIN & DOCUMENT MANAGEMENT =====
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        $totalDocuments = Document::count();
        $publishedDocuments = Document::where('is_published', true)->count();
        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        
        return view('admin.dashboard', compact('totalDocuments', 'publishedDocuments', 'totalUsers', 'adminUsers'));
    })->name('admin.dashboard');

    // Document Routes
    Route::get('/documents/input', [DocumentController::class, 'inputDocument'])->name('documents.input');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::patch('/documents/{document}/update-name', [DocumentController::class, 'updateName'])->name('documents.update-name');
    
    Route::get('/documents/edit', [DocumentController::class, 'editDocument'])->name('documents.edit');
    Route::get('/documents/{document}/editor', [DocumentController::class, 'show'])->name('documents.editor');
    Route::post('/documents/{document}/publish', [DocumentController::class, 'publish'])->name('documents.publish');
    Route::post('/documents/{document}/save-layout', [DocumentController::class, 'saveLayout'])->name('documents.save-layout');
    
    Route::patch('/document-parts/{documentPart}', [DocumentController::class, 'updatePart'])->name('document-parts.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::delete('/document-parts/{documentPart}', [DocumentController::class, 'deletePart'])->name('document-parts.delete');
    
    // User Management Routes
    Route::get('/management-user', [UserManagementController::class, 'index'])->name('admin.users.management');
    Route::post('/management-user/roles', [UserManagementController::class, 'storeRole'])->name('admin.roles.store');
    Route::delete('/management-user/roles/{role}', [UserManagementController::class, 'deleteRole'])->name('admin.roles.delete');
    Route::patch('/management-user/users/{user}/role', [UserManagementController::class, 'updateUserRole'])->name('admin.users.update-role');
    Route::post('/management-user/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::delete('/management-user/users/{user}', [UserManagementController::class, 'deleteUser'])->name('admin.users.delete');
    Route::patch('/management-user/users/{user}', [UserManagementController::class, 'updateUser'])->name('admin.users.update');
});

// Route lain (resume, projects, contact, dll) boleh ditaruh di bawah sini