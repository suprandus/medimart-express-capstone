<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\VendorController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\FlashSaleController;
use App\Http\Controllers\Frontend\FrontendProductController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\UserAddressController;
use App\Http\Controllers\Frontend\UserDashboardController;
use App\Http\Controllers\Frontend\UserProfileController;
use App\Http\Controllers\Frontend\CheckOutController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Frontend\ProductTrackController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\UserMessageController;
use App\Http\Controllers\Frontend\UserOrderController;
use App\Http\Controllers\Frontend\UserVendorReqeustController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\VendorListController;
use App\Http\Controllers\Frontend\NearbyPharmacyController;
use App\Http\Controllers\OCRController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

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
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

Route::get('/', [HomeController::class, 'index'])->name('home');

/** OCR Prescription */
Route::post('process-prescription', [OCRController::class, 'processPrescription'])->name('process.prescription');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::get('flash-sale', [FlashSaleController::class, 'index'])->name('flash-sale');

//Product route
Route::get('products', [FrontendProductController::class, 'productsIndex'])->name('products.index');
Route::get('product-detail/{slug}', [FrontendProductController::class, 'showProduct'])->name('product-detail');
Route::get('change-product-list-view', [FrontendProductController::class, 'chageListView'])->name('change-product-list-view');
    
//Cart routes
Route::post('add-to-cart', [CartController::class, 'addToCart'])->name('add-to-cart');
Route::get('cart-details', [CartController::class, 'cartDetails'])->name('cart-details');
Route::get('clear-cart', [CartController::class, 'clearCart'])->name('clear.cart');
Route::get('cart/remove-product/{product_id}', [CartController::class, 'removeProduct'])->name('cart.remove-product');
Route::get('cart-count', [CartController::class, 'getCartCount'])->name('cart-count');
Route::get('cart-products', [CartController::class, 'getCartProducts'])->name('cart-products');
Route::post('cart/remove-sidebar-product', [CartController::class, 'removeSidebarProduct'])->name('cart.remove-sidebar-product');
Route::get('cart/sidebar-product-total', [CartController::class, 'cartTotal'])->name('cart.sidebar-product-total');
Route::get('/cart/mini', [CartController::class, 'miniCart'])->name('cart.mini');
Route::post('/cart/update-checked-status', [CartController::class, 'updateCheckedStatus'])->name('cart.update-checked-status');
Route::post('cart/update-quantity', [CartController::class, 'updateProductQty'])->name('cart.update-quantity');

Route::get('apply-coupon', [CartController::class, 'applyCoupon'])->name('apply-coupon');
Route::get('coupon-calculation', [CartController::class, 'couponCalculation'])->name('coupon-calculation');

//vendor page routes
Route::get('pharmacy', [HomeController::class, 'vendorPage'])->name('vendor.index');
Route::get('pharmacy-product/{id}', [HomeController::class, 'vendorProductsPage'])->name('vendor.products');

//about page route
Route::get('about', [PageController::class, 'about'])->name('about');
//terms and conditions page route */
Route::get('terms-and-conditions', [PageController::class, 'termsAndCondition'])->name('terms-and-conditions');
//contact route
Route::get('contact', [PageController::class, 'contact'])->name('contact');
Route::post('contact', [PageController::class, 'handleContactForm'])->name('handle-contact-form');

//Product track route
Route::get('product-traking', [ProductTrackController::class, 'index'])->name('product-traking.index');

//Nearby Pharmacies routes
Route::get('nearby-pharmacies', [NearbyPharmacyController::class, 'index'])->name('nearby-pharmacies');
Route::post('nearest-pharmacies', [VendorListController::class, 'nearestVendors'])->name('nearest-vendors');


//Product routes
Route::get('show-product-modal/{id}', [HomeController::class, 'ShowProductModal'])->name('show-product-modal');

/** add product in wishlist */
Route::get('wishlist/add-product', [WishlistController::class, 'addToWishlist'])->name('wishlist.store');


//Removed routes
// blog routes 
Route::get('blog-details/{slug}', [BlogController::class, 'blogDetails'])->name('blog-details');
Route::get('blog', [BlogController::class, 'blog'])->name('blog');
// Newsletter routes
Route::post('newsletter-request', [NewsletterController::class, 'newsLetterRequset'])->name('newsletter-request');
Route::get('newsletter-verify/{token}', [NewsletterController::class, 'newsLetterEmailVarify'])->name('newsletter-verify');

//Route groups
Route::group(['middleware' => ['auth', 'verified'], 'prefix' => 'user', 'as' => 'user.'], function () {
    Route::get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [UserProfileController::class, 'index'])->name('profile'); // user.profile
    Route::put('profile', [UserProfileController::class, 'updateProfile'])->name('profile.update'); // user.profile.update
    Route::post('profile', [UserProfileController::class, 'updatePassword'])->name('profile.update.password');

    //Message Route
    Route::get('messages', [UserMessageController::class, 'index'])->name('messages.index');
    Route::post('send-message', [UserMessageController::class, 'sendMessage'])->name('send-message');
    Route::get('get-messages', [UserMessageController::class, 'getMessages'])->name('get-messages');

    //User Address Route
    Route::resource('address', UserAddressController::class);
    //Order Routes
    Route::get('orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/show/{id}', [UserOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/status/{id}', [UserOrderController::class, 'orderStatus'])->name('orders.status');

    //Wishlist routes
    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::get('wishlist/remove-product/{id}', [WishlistController::class, 'destory'])->name('wishlist.destory');

    Route::get('reviews', [ReviewController::class, 'index'])->name('review.index');

    //Vendor request route
    Route::get('pharmacy-request', [UserVendorReqeustController::class, 'index'])->name('vendor-request.index');
    Route::post('pharmacy-request', [UserVendorReqeustController::class, 'create'])->name('vendor-request.create');

    //product review routes
    Route::post('review', [ReviewController::class, 'create'])->name('review.create');

    //blog comment routes
    Route::post('blog-comment', [BlogController::class, 'comment'])->name('blog-comment');

    //Checkout routes

    Route::get('checkout', [CheckOutController::class, 'index'])->name('user.checkout');
    Route::get('checkout', [CheckOutController::class, 'index'])->name('checkout');
    Route::post('checkout/address-create', [CheckOutController::class, 'createAddress'])->name('checkout.address.create');
    Route::post('checkout/form-submit', [CheckOutController::class, 'checkOutFormSubmit'])->name('checkout.form-submit');
    
    //Payment Routes
    Route::get('payment', [PaymentController::class, 'index'])->name('payment');
    Route::get('payment-success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');

    //PayMongo routes
    Route::get('paymongo/payment', [PaymentController::class, 'payWithPayMongo'])->name('paymongo.payment');
    Route::get('paymongo/success', [PaymentController::class, 'paymongoSuccess'])->name('paymongo.success');
    Route::get('paymongo/cancel', [PaymentController::class, 'paymongoCancel'])->name('paymongo.cancel');
    
    //COD routes
    Route::get('cod/payment', [PaymentController::class, 'payWithCod'])->name('cod.payment');

    /** Notification routes */
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('notifications/{id}', [NotificationController::class, 'viewNotification'])->name('view-notification');
    Route::post('notifcations', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
});
