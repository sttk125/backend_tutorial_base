<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;

// 認証フロー
Route::post('/auth/register/request',  [AuthController::class, 'registerRequest']);
Route::post('/auth/register/verify',   [AuthController::class, 'registerVerify']);
Route::post('/auth/register/complete', [AuthController::class, 'registerComplete']);
Route::post('/auth/login',             [AuthController::class, 'login']);
Route::post('/auth/logout',            [AuthController::class, 'logout'])->middleware('auth:sanctum');

// 公開（ゲスト可）
Route::get('/articles/{article}/comments', [CommentController::class, 'index']); // コメント一覧

// 認証必須（ログインユーザーのみ） 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/articles/{article}/comments', [CommentController::class, 'store']); // コメント投稿
    Route::put('/comments/{comment}', [CommentController::class, 'update']);          // コメント更新
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);      // コメント削除
});
