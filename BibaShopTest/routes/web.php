<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CouponController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\StripeController;
use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\ShippingAreaController;
use App\Http\Controllers\Backend\SliderController;
use App\Http\Controllers\Backend\CategoryControler;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\SubCategoryControler;
use App\Http\Controllers\Backend\VendorProductController;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Frontend\CartController;




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

// Route::get('/', function () {
//     return view('frontend.index');
// })->name('home');

Route::get('/' , [IndexController::class , 'Index'])->name('home');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/dashboard' , [UserController::class , 'UserDashboard'])->name('dashboard');
    Route::post('/user/profile/store' , [UserController::class , 'UserProfileStore'])->name('user.profile.store');
    Route::get('/user/logout' , [UserController::class , 'UserLogout'])->name('user.logout');
}); // Gorup Milldeware End


// Route::get('/dashboard' , function () {
//  return view('dashboard');
// })->middleware(['auth'])->name('dashboard');



/// Admin Dashboard
Route::middleware(['auth','role:admin'])->group(function(){
    Route::get('/admin/dashboard' , [AdminController::class , 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/admin/logout' , [AdminController::class , 'AdminDestroy'])->name('admin.logout');
    Route::get('/admin/profile' , [AdminController::class , 'AdminProfile'])->name('admin.profile');
    Route::post('/admin/profile/store' , [AdminController::class , 'AdminProfileStore'])->name('admin.profile.store');
    Route::get('/admin/change/password' , [AdminController::class , 'AdminChangePassword'])->name('admin.change.password');
    Route::post('/admin/update/password' , [AdminController::class , 'AdminUpdatePassword'])->name('update.password');

});

/// Vendor Dashboard

Route::middleware(['auth','role:vendor'])->group(function(){
    Route::get('/vendor/dashboard' , [VendorController::class , 'VendorDashboard'])->name('vendor.dashboard');
    Route::get('/vendor/logout' , [VendorController::class , 'VendorDestroy'])->name('vendor.logout');
    Route::get('/vendor/profile' , [VendorController::class , 'VendorProfile'])->name('vendor.profile');
    Route::post('/vendor/profile/store' , [VendorController::class , 'VendorProfileStore'])->name('vendor.profile.store');
    Route::get('/vendor/change/password' , [VendorController::class , 'VendorChangePassword'])->name('vendor.change.password');
    Route::post('/vendor/update/password' , [VendorController::class , 'VendorUpdatePassword'])->name('vendor.update.password');


    // Vendor Add Product All Route
   Route::controller(VendorProductController::class)->group(function(){
    Route::get('/vendor/all/product' , 'VendorAllProduct')->name('vendor.all.product');
    // Route::get('/add/brand' , 'AddBrand')->name('add.brand');
    // Route::post('/store/brand' , 'StoreBrand')->name('store.brand');
    // Route::get('/edit/brand/{id}' , 'EditBrand')->name('edit.brand');
    // Route::post('/update/brand' , 'UpdateBrand')->name('update.brand');
    // Route::get('/delete/brand/{id}' , 'DeleteBrand')->name('delete.brand');
    });
});

Route::get('/admin/login' , [AdminController::class , 'AdminLogin'])->middleware(RedirectIfAuthenticated::class);
Route::get('/vendor/login' , [VendorController::class , 'VendorLogin'])->name('vendor.login')->middleware(RedirectIfAuthenticated::class);

Route::get('/become/vendor' , [VendorController::class , 'BecomeVendor'])->name('become.vendor');
Route::post('/become/register' , [VendorController::class , 'VendorRegister'])->name('vendor.register');


Route::middleware(['auth','role:admin'])->group(function(){

    // Brand All Route
   Route::controller(BrandController::class)->group(function(){
       Route::get('/all/brand' , 'AllBrand')->name('all.brand');
       Route::get('/add/brand' , 'AddBrand')->name('add.brand');
       Route::post('/store/brand' , 'StoreBrand')->name('store.brand');
       Route::get('/edit/brand/{id}' , 'EditBrand')->name('edit.brand');
       Route::post('/update/brand' , 'UpdateBrand')->name('update.brand');
       Route::get('/delete/brand/{id}' , 'DeleteBrand')->name('delete.brand');
   });

    // Category All Route
    Route::controller(CategoryControler::class)->group(function(){
        Route::get('/all/category' , 'AllCategory')->name('all.category');
        Route::get('/add/category' , 'AddCategory')->name('add.category');
        Route::post('/store/category' , 'StoreCategory')->name('store.category');
        Route::get('/edit/category/{id}' , 'EditCategory')->name('edit.category');
        Route::post('/update/category' , 'UpdateCategory')->name('update.category');
        Route::get('/delete/category/{id}' , 'DeleteCategory')->name('delete.category');
    });

    // SubCategory All Route
    Route::controller(SubCategoryControler::class)->group(function(){
        Route::get('/all/subcategory' , 'AllSubCategory')->name('all.subcategory');
        Route::get('/add/subcategory' , 'AddSubCategory')->name('add.subcategory');
        Route::post('/store/subcategory' , 'StoreSubCategory')->name('store.subcategory');
        Route::get('/edit/subcategory/{id}' , 'EditSubCategory')->name('edit.subcategory');
        Route::post('/update/subcategory' , 'UpdateSubCategory')->name('update.subcategory');
        Route::get('/delete/subcategory/{id}' , 'DeleteSubCategory')->name('delete.subcategory');
        Route::get('/subcategory/ajax/{category_id}' , 'GetSubCategory');
    });

     // Vendor Active and Inactive  All Route
     Route::controller(AdminController::class)->group(function(){
        Route::get('/inactive/vendor' , 'InactiveVendor')->name('inactive.vendor');
        Route::get('/active/vendor' , 'ActiveVendor')->name('active.vendor');
        Route::get('/inactive/vendor/details/{id}' , 'InactiveVendorDetails')->name('inactive.vendor.details');
        Route::post('/active/vendor/approve' , 'ActiveVendorApprove')->name('active.vendor.approve');
        Route::get('/active/vendor/details/{id}' , 'ActiveVendorDetails')->name('active.vendor.details');
        Route::post('/inactive/vendor/approve' , 'InactiveVendorApprove')->name('inactive.vendor.approve');
    });

       // Product All Route
       Route::controller(ProductController::class)->group(function(){
        Route::get('/all/product' , 'AllProduct')->name('all.product');
        Route::get('/add/product' , 'AddProduct')->name('add.product');
        Route::post('/store/product' , 'StoreProduct')->name('store.product');
        Route::get('/edit/product/{id}' , 'EditProduct')->name('edit.product');
        Route::post('/update/product' , 'UpdateProduct')->name('update.product');
        Route::post('/update/product/thambnail' , 'UpdateProductThambnail')->name('update.product.thambnail');
        Route::post('/update/product/multiimage' , 'UpdateProductMultiimage')->name('update.product.multiimage');
        Route::get('/product/multiimg/delete/{id}' , 'MultiImageDelete')->name('product.multiimg.delete');
        Route::get('/inactive/product/{id}' , 'ProductInactive')->name('inactive.product');
        Route::get('/active/product/{id}' , 'ProductActive')->name('active.product');
        Route::get('/delete/product/{id}' , 'ProductDelete' )->name('delete.product');
    });


     // Slider All Route
     Route::controller(SliderController::class)->group(function(){
        Route::get('/all/slider' , 'AllSlider')->name('all.slider');
        Route::get('/add/slider' , 'AddSlider')->name('add.slider');
        Route::post('/store/slider' , 'StoreSlider')->name('store.slider');
        Route::get('/edit/slider/{id}' , 'EditSlider')->name('edit.slider');
        Route::post('/update/slider' , 'UpdateSlider')->name('update.slider');
        Route::get('/delete/slider/{id}' , 'DeleteSlider')->name('delete.slider');
        // Route::get('/' , 'AfficherSlider')->name('affiche.slider');
    });

       // Banner All Route
       Route::controller(BannerController::class)->group(function(){
        Route::get('/all/banner' , 'AllBanner')->name('all.banner');
        Route::get('/add/banner' , 'AddBanner')->name('add.banner');
        Route::post('/store/banner' , 'StoreBanner')->name('store.banner');
        Route::get('/edit/banner/{id}' , 'EditBanner')->name('edit.banner');
        Route::post('/update/banner' , 'UpdateBanner')->name('update.banner');
        Route::get('/delete/banner/{id}' , 'DeleteBanner')->name('delete.banner');
    });



    // Coupon All Route
   Route::controller(CouponController::class)->group(function(){
    Route::get('/all/coupon' , 'AllCoupon')->name('all.coupon');
    Route::get('/add/coupon' , 'AddCoupon')->name('add.coupon');
    Route::post('/store/coupon' , 'StoreCoupon')->name('store.coupon');
    Route::get('/edit/coupon/{id}' , 'EditCoupon')->name('edit.coupon');
    Route::post('/update/coupon' , 'UpdateCoupon')->name('update.coupon');
    Route::get('/delete/coupon/{id}' , 'DeleteCoupon')->name('delete.coupon');
});

    // Shipping Division All Route
    Route::controller(ShippingAreaController::class)->group(function(){
        Route::get('/all/division' , 'AllDivision')->name('all.division');
        Route::get('/add/division' , 'AddDivision')->name('add.division');
        Route::post('/store/division' , 'StoreDivision')->name('store.division');
        Route::get('/edit/division/{id}' , 'EditDivision')->name('edit.division');
        Route::post('/update/division' , 'UpdateDivision')->name('update.division');
        Route::get('/delete/division/{id}' , 'DeleteDivision')->name('delete.division');
    });


 // Shipping District All Route
Route::controller(ShippingAreaController::class)->group(function(){
    Route::get('/all/district' , 'AllDistrict')->name('all.district');
    Route::get('/add/district' , 'AddDistrict')->name('add.district');
    Route::post('/store/district' , 'StoreDistrict')->name('store.district');
    Route::get('/edit/district/{id}' , 'EditDistrict')->name('edit.district');
    Route::post('/update/district' , 'UpdateDistrict')->name('update.district');
    Route::get('/delete/district/{id}' , 'DeleteDistrict')->name('delete.district');

});

 // Shipping State All Route
 Route::controller(ShippingAreaController::class)->group(function(){
    Route::get('/all/state' , 'AllState')->name('all.state');
    Route::get('/add/state' , 'AddState')->name('add.state');
    Route::post('/store/state' , 'StoreState')->name('store.state');
    Route::get('/edit/state/{id}' , 'EditState')->name('edit.state');
    Route::post('/update/state' , 'UpdateState')->name('update.state');
    Route::get('/delete/state/{id}' , 'DeleteState')->name('delete.state');

    Route::get('/district/ajax/{division_id}' , 'GetDistrict');

});



});

//// Frontend Product Details All Route
Route::get('/product/details/{id}/{slug}' , [IndexController::class , 'ProductDetails']);

Route::get('/vendor/details/{id}' , [IndexController::class , 'VendorDetails'])->name('vendor.details');

Route::get('/vendor/all' , [IndexController::class , 'VendorAll'])->name('vendor.all');

Route::get('/product/category/{id}/{slug}' , [IndexController::class , 'CatWiseProduct']);

Route::get('/product/subcategory/{id}/{slug}' , [IndexController::class , 'SubCatWiseProduct']);



/// Add to Wishlist
Route::post('/add-to-wishlist/{product_id}', [WishlistController::class, 'AddToWishList']);

/// Frontend Coupon Option
Route::post('/coupon-apply', [CartController::class, 'CouponApply']);

Route::get('/coupon-calculation', [CartController::class, 'CouponCalculation']);

Route::get('/coupon-remove', [CartController::class, 'CouponRemove']);


/// Checkout Page Route
Route::get('/checkout', [CartController::class, 'CheckoutCreate'])->name('checkout');







/// Product View Modal With Ajax
Route::get('/product/view/modal/{id}' , [IndexController::class , 'ProductViewAjax']);


///Add to cart store data
Route::post('/cart/data/store/{id}' , [CartController::class , 'AddToCart']);



///Get Data from mini Cart
Route::get('/product/mini/cart' , [CartController::class , 'AddMiniCart']);

///Remove Data from mini Cart
Route::get('/minicart/product/remove/{id}' , [CartController::class , 'RemoveMiniCart']);

///Add to cart store data for product details page
Route::post('/dcart/data/store/{id}' , [CartController::class , 'AddToCartDetails']);

  // Cart All Route
  Route::controller(CartController::class)->group(function(){
    Route::get('/mycart'  , 'MyCart')->name('mycart');
    Route::get('/get-cart-product' , 'GetCartProduct');
    Route::get('/cart-remove/{id}' , 'CartRemove');
    Route::get('/cart-decrement/{id}' , 'cartDecrement');
    Route::get('/cart-increment/{id}' , 'cartIncrement');
    // Route::get('/edit/coupon/{id}' , 'EditCoupon')->name('edit.coupon');
    // Route::post('/update/coupon' , 'UpdateCoupon')->name('update.coupon');
});





/// User All Route
Route::middleware(['auth','role:user'])->group(function(){

    // Wishlist All Route
    Route::controller(WishlistController::class)->group(function(){
        Route::get('/wishlist' , 'AllWishlist')->name('wishlist');
        Route::get('/get-wishlist-product' , 'AllWishlist')->name('GetWishlistProduct');

    });

    // Checkout All Route
    Route::controller(CheckoutController::class)->group(function(){
        Route::get('/district-get/ajax/{division_id}' , 'DistrictGetAjax');
        Route::get('/state-get/ajax/{district_id}' , 'StateGetAjax');
        Route::post('/checkout/store' , 'CheckoutStore')->name('checkout.store');
    });

        // Stripe All Route
        Route::controller(StripeController::class)->group(function(){
            Route::post('/stripe/order' , 'StripeOrder')->name('stripe.order');
        });

});






require __DIR__.'/auth.php';


