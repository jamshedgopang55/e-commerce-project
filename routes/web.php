<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\subCategoryController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\adminCategoryController;
use App\Http\Controllers\Admin\brandController;
use App\Http\Controllers\Admin\TempController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {

    ///Routes For Guest Users
    Route::group(['middleware' => 'admin.guest'], function () {
        ///Routes For AdminLoginController
        Route::controller(AdminLoginController::class)->group(function () {
            Route::get('login', 'index')->name('admin.login');
            Route::post('/authenticate', 'authenticate')->name('admin.authenticate');
        });
    });

    ///Routes For  Users authenticated Users
    Route::group(['middleware' => 'admin.auth'], function () {
        //Routes For HomeController
        Route::controller(HomeController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('admin.dashboard');
            Route::get('/logout', 'logout')->name('admin.logout');
        });

        //Routes For adminCategoryController
        Route::controller(adminCategoryController::class)->group(function () {
            Route::get('/categories', 'index')->name('category.index');
            Route::get('/categories/create', 'create')->name('category.create');
            Route::post('/categories/store', 'store')->name('category.store');
            Route::get('/categories/{category}/edit', 'edit')->name('category.edit');
            Route::put('/categories{category}/update', 'update')->name('category.update');
            Route::delete('/categories{category}', 'destroy')->name('category.delete');

        });

        //Sub Category Controller
        Route::controller(subCategoryController::class)->group(function () {
            Route::get('/sub-Categories', 'index')->name('sub-Category.index');
            Route::get('/sub-Categories/create', 'create')->name('sub-Category.create');
            Route::post('/sub-Categories/store', 'store')->name('sub-category.store');
            Route::get('/sub-Categories/{category}/edit', 'edit')->name('sub-Category.edit');
            Route::put('/sub-Categories{category}/update', 'update')->name('sub-Category.update');
            Route::post('/categories{category}', 'destroy')->name('sub-Category.delete');
        });
        ///Brands Routes
        Route::controller(brandController::class)->group(function () {
            Route::get('/brands', 'index')->name('brand.index');
            Route::get('/brand/create', 'create')->name('brand.create');
            Route::post('/brand/store', 'store')->name('brand.store');
            Route::get('/brand/{brand}/edit', 'edit')->name('brand.edit');
            Route::post('/brand{brand}/update', 'update')->name('brand.update');
            Route::post('/brand{brand}', 'destroy')->name('brand.delete');
        });

        // temp-images.create
        Route::controller(TempController::class)->group(function () {
            Route::post('/upload-temp-img', 'create')->name('temp-images.create');
        });


        ///Slug Routes
        Route::get('getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug,
            ]);

        })->name('getSlug');

        // Route::get('/', [HomeController::class,'logout'])->name('admin.logout');
    });
});
