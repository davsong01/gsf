<?php

use App\Modules\Blog\Http\Controllers\BlogCategoryController;
use App\Modules\Blog\Http\Controllers\BlogController;
use App\Modules\Blog\Http\Controllers\BlogPublicController;
use Illuminate\Support\Facades\Route;

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogPublicController::class, 'index'])->name('index');
    Route::get('/rss.xml', [BlogPublicController::class, 'rss'])->name('rss');
    Route::get('/sitemap.xml', [BlogPublicController::class, 'sitemap'])->name('sitemap');
    Route::get('/network/{scopeType}/{scopeId}', [BlogPublicController::class, 'network'])->name('network');
    Route::get('/{slug}', [BlogPublicController::class, 'show'])->name('show');
    Route::post('/{post}/comments', [BlogPublicController::class, 'comment'])->name('comments.store');
});

Route::middleware(['auth', 'SwitchUser'])->prefix('admin/blog')->name('admin.blog.')->group(function () {
    Route::resource('posts', BlogController::class)->except(['show']);
    Route::post('media/upload', [BlogController::class, 'uploadMedia'])->name('media.upload');
    Route::post('posts/{post}/review', [BlogController::class, 'review'])->name('posts.review');
    Route::post('posts/{post}/publish', [BlogController::class, 'publish'])->name('posts.publish');
    Route::post('posts/{post}/archive', [BlogController::class, 'archive'])->name('posts.archive');
    Route::get('categories', [BlogCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [BlogCategoryController::class, 'store'])->name('categories.store');
    Route::delete('categories/{category}', [BlogCategoryController::class, 'destroy'])->name('categories.destroy');
});

Route::middleware('stakeholder')->prefix('stakeholders/blog')->name('stakeholders.blog.')->group(function () {
    Route::resource('posts', BlogController::class)->except(['show']);
    Route::post('media/upload', [BlogController::class, 'uploadMedia'])->name('media.upload');
    Route::post('posts/{post}/review', [BlogController::class, 'review'])->name('posts.review');
});
