<?php

use App\Modules\Blog\Http\Controllers\BlogCategoryController;
use App\Modules\Blog\Http\Controllers\BlogCommentAdminController;
use App\Modules\Blog\Http\Controllers\BlogController;
use App\Modules\Blog\Http\Controllers\BlogPublicController;
use App\Modules\Blog\Http\Controllers\BlogSettingsController;
use App\Modules\Blog\Http\Controllers\BlogTagController;
use Illuminate\Support\Facades\Route;

Route::prefix('news')->name('blog.')->group(function () {
    Route::get('/', [BlogPublicController::class, 'index'])->name('index');
    Route::get('/rss.xml', [BlogPublicController::class, 'rss'])->name('rss');
    Route::get('/sitemap.xml', [BlogPublicController::class, 'sitemap'])->name('sitemap');
    Route::get('/media', [BlogPublicController::class, 'media'])->name('media');
    Route::get('/network/{scopeType}/{scopeId}', [BlogPublicController::class, 'network'])->name('network');
    Route::get('/{slug}', [BlogPublicController::class, 'show'])->name('show');
    Route::post('/{post}/comments', [BlogPublicController::class, 'comment'])->name('comments.store');
});

Route::get('programs-events', [BlogPublicController::class, 'events'])->name('events.public.index');
Route::get('programs-events/{slug}', [BlogPublicController::class, 'show'])->name('events.public.show');

Route::middleware(['auth', 'SwitchUser'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('programs-events/bulk-action', [BlogController::class, 'bulkAction'])->name('events.posts.bulk-action');
    Route::resource('programs-events', BlogController::class)
        ->parameters(['programs-events' => 'post'])->names('events.posts')->except(['show']);
    Route::get('programs-events/{post}/preview', [BlogController::class, 'preview'])->name('events.posts.preview');
});

Route::middleware('stakeholder')->prefix('stakeholders')->name('stakeholders.')->group(function () {
    Route::post('programs-events/bulk-action', [BlogController::class, 'bulkAction'])->name('events.posts.bulk-action');
    Route::resource('programs-events', BlogController::class)
        ->parameters(['programs-events' => 'post'])->names('events.posts')->except(['show']);
    Route::get('programs-events/{post}/preview', [BlogController::class, 'preview'])->name('events.posts.preview');
});

Route::middleware(['auth', 'SwitchUser'])->prefix('admin/blog')->name('admin.blog.')->group(function () {
    Route::get('comments', [BlogCommentAdminController::class, 'index'])->name('comments.index');
    Route::post('comments/bulk-action', [BlogCommentAdminController::class, 'bulkAction'])->name('comments.bulk-action');
    Route::delete('comments/clear-all', [BlogCommentAdminController::class, 'clearAll'])->name('comments.clear-all');
    Route::get('comments/{comment}/edit', [BlogCommentAdminController::class, 'edit'])->name('comments.edit');
    Route::put('comments/{comment}', [BlogCommentAdminController::class, 'update'])->name('comments.update');
    Route::patch('comments/{comment}', [BlogCommentAdminController::class, 'moderate'])->name('comments.moderate');
    Route::delete('comments/{comment}', [BlogCommentAdminController::class, 'destroy'])->name('comments.destroy');
    Route::get('settings', [BlogSettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [BlogSettingsController::class, 'update'])->name('settings.update');
    Route::post('posts/bulk-action', [BlogController::class, 'bulkAction'])->name('posts.bulk-action');
    Route::resource('posts', BlogController::class)->except(['show']);
    Route::get('posts/{post}/preview', [BlogController::class, 'preview'])->name('posts.preview');
    Route::post('media/upload', [BlogController::class, 'uploadMedia'])->name('media.upload');
    Route::post('posts/{post}/review', [BlogController::class, 'review'])->name('posts.review');
    Route::post('posts/{post}/publish', [BlogController::class, 'publish'])->name('posts.publish');
    Route::post('posts/{post}/archive', [BlogController::class, 'archive'])->name('posts.archive');
    Route::get('categories', [BlogCategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [BlogCategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [BlogCategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [BlogCategoryController::class, 'destroy'])->name('categories.destroy');
    Route::get('tags', [BlogTagController::class, 'index'])->name('tags.index');
    Route::post('tags', [BlogTagController::class, 'store'])->name('tags.store');
    Route::put('tags/{tag}', [BlogTagController::class, 'update'])->name('tags.update');
    Route::delete('tags/{tag}', [BlogTagController::class, 'destroy'])->name('tags.destroy');
});

Route::middleware('stakeholder')->prefix('stakeholders/blog')->name('stakeholders.blog.')->group(function () {
    Route::resource('posts', BlogController::class)->except(['show']);
    Route::get('posts/{post}/preview', [BlogController::class, 'preview'])->name('posts.preview');
    Route::post('media/upload', [BlogController::class, 'uploadMedia'])->name('media.upload');
    Route::post('posts/{post}/review', [BlogController::class, 'review'])->name('posts.review');
});
