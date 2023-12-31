<?php

use App\Http\Controllers\Admin\discountCodeController;
use App\Http\Controllers\Admin\oderContoller;
use App\Http\Controllers\Admin\orderContoller;
use App\Http\Controllers\Admin\pageController;
use App\Http\Controllers\Admin\settingController;
use App\Http\Controllers\Admin\shippingController;
use App\Http\Controllers\Admin\userController;
use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\subCategoryController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\adminCategoryController;
use App\Http\Controllers\Admin\brandController;
use App\Http\Controllers\Admin\productController;
use App\Http\Controllers\Admin\productSubCategoryController;
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


////Front Controller Routes
Route::controller(FrontController::class)->group(function () {
    Route::get('/', 'index')->name('front.home');
    Route::get('/page/{slug}', 'page')->name('front.page');
    Route::post('/add-to-wishlist', 'addToWishList')->name('front.addToWishList');
    Route::post('/send-contect-mail', 'sendContectEmail')->name('front.sendContectEmail');

});

Route::controller(ShopController::class)->group(function () {
    Route::get('shop', 'index')->name('front.shop');
    Route::get('/shop/{categorySlug?}/{subCategorySlug?}', 'index')->name('front.shop');
    Route::get('product/{slug}', 'product')->name('front.product');
    Route::get('saveRating{productId}', 'storeRating')->name('front.saveRating');

    Route::get('showRatigs', 'showRatigs')->name('front.showRatigs');
    Route::post('showSingleReview', 'showSingleReview')->name('front.showSingleReview');
    Route::post('saveRating{productId}', 'storeRating')->name('front.saveRating');
    Route::post('deleteRating', 'deleteRating')->name('front.deleteRating');
    Route::post('updateReview', 'updateReview')->name('front.updateReview');
});
///Cart Routes
Route::controller(CartController::class)->group(function () {
    Route::get('/thanks/{orderId}', 'thankYou')->name('front.thankYou');
    Route::get('/cart', 'cart')->name('front.cart');
    Route::get('/checkout', 'checkout')->name('account.checkout');
    Route::post('/add-to-cart', 'addToCart')->name('front.addToCart');
    Route::post('/update-cart', 'updateCart')->name('front.updateCart');
    Route::post('/delete-cart', 'deleteCart')->name('front.deleteCart');
    Route::post('/process-checkout', 'processCheckout')->name('front.processCheckout');
    Route::post('/stripeCall', 'stripeCall')->name('front.stripeCall');
    Route::get('/store-order', 'storeOrder')->name('front.storeOrder');
    Route::post('/get-oreder-summery', 'getOrderSummery')->name('front.getOrderSummery');
    Route::post('/Apply-discount', 'applyDiscount')->name('front.applyDiscount');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/forget-password', 'forgetPassword')->name('front.forgetPassword');
    Route::post('/process-forget-password', 'processForgetPassword')->name('front.processForgetPassword');
    Route::get('/reset-password/{token}', 'resetPassword')->name('front.resetPassword');
    Route::post('/process-reset-password', 'processResetPassword')->name('front.processResetPassword');
});

Route::prefix('account')->group(function () {

    Route::group(['middleware' => 'guest'], function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('/register', 'register')->name('account.register');
            Route::get('/login', 'login')->name('account.login');
            Route::post('/login', 'authenticate')->name('account.authenticate');
            Route::post('/processRegister', 'processRegister')->name('account.processRegister');
        });
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('/profile', 'profile')->name('account.profile');
            Route::get('/change-password', 'changePassword')->name('account.changePassword');
            Route::get('/logout', 'logout')->name('account.logout');
            Route::get('/my-orders', 'orders')->name('account.orders');
            Route::get('/order-detail/{id}', 'orderDetail')->name('account.orderDetail');
            Route::get('/my-wishlist', 'wishlist')->name('account.wishlist');
            Route::post('/wishlist-remove-product', 'removeProductFromWishlist')->name('account.removeProductFromWishlist');
            Route::post('/update-profile', 'updateProfile')->name('account.updateProfile');
            Route::post('/change-password', 'updatePassword')->name('account.updatePassword');
        });

    });

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

    ///Routes For  authenticated Users
    Route::group(['middleware' => 'admin.auth'], function () {
        //Routes For HomeController
        Route::controller(HomeController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('admin.dashboard');
            Route::get('/logout', 'logout')->name('admin.logout');
        });
        ///settings Routes
        Route::controller(settingController::class)->group(function () {
            Route::get('/change-password', 'changePasswordFrom')->name('admin.changePasswordFrom');
            Route::post('/process-change-password', 'updatePassword')->name('admin.updatePassword');
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

        //Products Routes
        Route::controller(productController::class)->group(function () {
            Route::get('/products', 'index')->name('products.list');
            Route::get('/product/create', 'create')->name('product.create');
            Route::post('/product/store', 'store')->name('product.store');
            Route::get('/product/{product}/edit', 'edit')->name('product.edit');
            Route::post('/product{product}/update', 'update')->name('product.update');
            Route::post('/product/delete', 'imageDelete')->name('product.imageDelete');
            Route::post('/product{product}', 'destroy')->name('product.delete');
            Route::get('getProducts/', 'getProducts')->name('front.getProducts');
        });


        ///Static Pages Routes
        Route::controller(pageController::class)->group(function () {
            Route::get('/pages', 'index')->name('page.list');
            Route::get('/page/create', 'create')->name('page.create');
            Route::post('/page/store', 'store')->name('page.store');
            Route::get('/page/{id}', 'edit')->name('page.edit');
            Route::post('/page{id}', 'update')->name('page.update');
            Route::post('/page/delete/{id}', 'destroy')->name('page.delete');
            // Route::post('/product{product}', 'destroy')->name('product.delete');
            // Route::get('getProducts/', 'getProducts')->name('front.getProducts');
        });

        // temp-images.create
        Route::controller(TempController::class)->group(function () {
            Route::post('/upload-temp-img', 'create')->name('temp-images.create');
        });
        Route::controller(productSubCategoryController::class)->group(function () {
            Route::get('/product-subCategory/create', 'index')->name('product.subCategory.index');
        });
        ///Shipping Rutes
        Route::controller(shippingController::class)->group(function () {
            Route::get('/shipping/create', 'create')->name('shipping.create');
            Route::get('/shipping/{id}', 'edit')->name('shipping.edit');
            Route::post('/shipping/store', 'store')->name('shipping.store');
            Route::post('/shipping{id}/update', 'update')->name('shipping.update');
            Route::post('/shipping{id}', 'destroy')->name('shipping.delete');

        });
        ///Discount Coupons Routes
        Route::controller(discountCodeController::class)->group(function () {
            Route::get('/coupons', 'index')->name('coupons.index');
            Route::get('/coupons/create', 'create')->name('coupons.create');
            Route::post('/coupons/store', 'store')->name('coupons.store');
            Route::get('/coupons/{id}', 'edit')->name('coupons.edit');
            Route::post('/coupons/{id}', 'update')->name('coupons.update');
            Route::post('/coupons{id}', 'destroy')->name('coupons.delete');
        });
        ///Oder Controller
        Route::controller(orderContoller::class)->group(function () {
            Route::get('orders', 'index')->name('orders.index');
            Route::get('orders/{id}', 'detail')->name('orders.detail');
            Route::post('orders/{id}', 'changeOrderStatus')->name('orders.changeOrderStatus');
            Route::post('/send-email/{id}', 'sendInvoiceEmail')->name('orders.sendInvoiceEmail');

        });
        Route::controller(userController::class)->group(function () {
            Route::get('users', 'index')->name('users.index');
            Route::post('/user{id}', 'destroy')->name('users.delete');
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

        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');
    });
});
