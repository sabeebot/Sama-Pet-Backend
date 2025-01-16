<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CodeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CollarController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PetOwnerController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\CodeusageController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\DoctorInfoController;
use App\Http\Controllers\TwilioMailController;
use App\Http\Controllers\CouponusageController;
use App\Http\Controllers\MemeberShipController;
use App\Http\Controllers\ProviderAllController;
use App\Http\Controllers\ProviderAuthController;
use App\Http\Controllers\VeterinarianController;
use App\Http\Controllers\ProductCategoryController;

Route::middleware("auth:sanctum")->get('/user', function (Request $request) {
    return $request->user();
});


Route::resource('/provider_all', ProviderAllController::class);
Route::post('/provider_all/store', [ProviderAllController::class, 'store'])->name('provider_all.store');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/check-email', [AuthController::class, 'checkEmail']);

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
});

Route::post('/registration', [ProviderAuthController::class, 'ProRegister']);
Route::post('/Prologin', [ProviderAuthController::class, 'login'])->name('login');
Route::post('/Prologout', [ProviderAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register-pending-provider', [ProviderAuthController::class, 'registerPendingProvider']);
Route::post('/approve-provider/{id}', [ProviderAuthController::class, 'approveProvider']);
Route::post('/get-provider-status', [ProviderAuthController::class, 'getProviderStatus']);
Route::prefix('provider')->group(function () {
    Route::get('profile', [ProviderAuthController::class, 'profile'])->middleware('auth:provider');
    // Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('information', [ProviderController::class, 'storeInformation']);
        Route::get('application-status', [ProviderController::class, 'checkApplicationStatus']);
        Route::post('promotions', [PromotionController::class, 'store']);
        
        Route::middleware('auth:sanctum')->get('address', [ProviderController::class, 'getAddress']);

    // });
});

Route::resource('pet_owner', PetOwnerController::class)
    ->missing(function (Request $request) {
        return response(
            content: 'Not Found',
            status: 404,
        );
    });

Route::resource('pet', PetController::class)
    ->missing(function (Request $request) {
        return response(
            content: 'Not Found',
            status: 404,
        );
    });



Route::post('pet_owner/image/{id}', [PetOwnerController::class, 'update_profile_image']);
Route::put('/pet-owners/update-password', [PetOwnerController::class, 'updatePass']);

Route::resource('pet_owner.pet', PetController::class)
    ->missing(fn() => response(status: 404))
    ->shallow();

Route::post('/send-verification-code', [TwilioMailController::class, 'sendVerificationCode']);
Route::post('/verify-code', [TwilioMailController::class, 'verifyCode']);

Route::resource('reminders', ReminderController::class)
    ->missing(function (Request $request) {
        return response()->json(['message' => 'Not Found'], 404);
    });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/petOwner/profile', [AuthController::class, 'profile']);
    Route::get('/petOwner/pets', [AuthController::class, 'pets']);
});




Route::post('/testing', [PetController::class, 'testing'])->name('testing');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/pets', [PetController::class, 'index']);
    Route::post('/pet/store', [PetController::class, 'store']);   //here
    Route::get('/pet/{id}', [PetController::class, 'show']);
    Route::post('/pet/update/{id}', [PetController::class, 'update']);
    Route::get('/pet/membership/{id}', [MemeberShipController::class, 'show']);
    Route::get('/pet_owners/{pet_owner_id}/pets', [PetController::class, 'getPetsByOwnerId']);
    Route::post('/pet/update-lost-status/{id}', [PetController::class, 'updateLostStatus']);
    Route::post('/pet/update-adoption-status/{id}', [PetController::class, 'updateAdoptionStatus']);
    Route::post('/pet/update-selling-status/{id}', [PetController::class, 'updateSellingStatus']);
    Route::post('/pet/update-mating-status/{id}', [PetController::class, 'updateMatingStatus']);
    Route::post('/pet/delete-image/{id}', [PetController::class, 'deleteImage']);
    Route::post('/pet/update-image/{id}', [PetController::class, 'updateImage']);
    // Route::get('/pet/{id}/collar/', [PetController::class, 'getPetCollar']);
    // Route::post('/pet/{id}/collar/', [PetController::class, 'addPetCollar']);
    Route::get('/allLostPets', [PetController::class, 'allLostPets']);
    Route::post('/addLostPetByFounder', [PetController::class, 'addLostPetByFounder']);
    Route::get('/showLostPetByFounder/{id}', [PetController::class, 'showLostPetByFounder']);
    Route::delete('/deleteLostPetByFounder/{id}', [PetController::class, 'deleteLostPetByFounder']);
    Route::get('/adoptingList', [PetController::class, 'getPetsForAdoption']);
});
Route::get('/membership', [MemeberShipController::class, 'index']);
Route::post('/membership', [MemeberShipController::class, 'store']);
Route::delete('/membership/{id}', [MemeberShipController::class, 'destroy']);

Route::post('/pet/{id}/documents', [PetController::class, 'addDocument']);


Route::get('/petOwners/byEmail', [PetOwnerController::class, 'getByEmail']);
Route::put('/pets/{petId}', [PetController::class, 'updatePetOwner']);




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/packages', [PackageController::class, 'index']);
    Route::get('/package/{id}', [PackageController::class, 'show']);
});



Route::get('/file-url', [PetController::class, 'getFileUrl'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/products/{id}', [ProviderController::class, 'getProductById']);
// Route::middleware('auth:sanctum')->group(function () {
    Route::get('/providers', [ProviderController::class, 'index']);
    Route::get('/providers/{id}', [ProviderController::class, 'show']);
    Route::post('/providers', [ProviderController::class, 'store']);
    Route::post('upload-image', [ProviderController::class, 'storeImage']);
    Route::put('/providers/{providerId}', [ProviderController::class, 'update']);
    Route::delete('/providers/{id}', [ProviderController::class, 'deleteProvider']);
    Route::get('/providers/name/{name}', [ProviderController::class, 'getProviderByName']);
    Route::get('/providers/update_status/{id}', [ProviderController::class, 'update_status']);
   

    Route::get('/services', [ServiceController::class, 'index']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    Route::post('/services/store', [ServiceController::class, 'store']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::get('/servicess/{id}', [ServiceController::class, 'shows']);
    Route::delete('/DeleteServicesByProviders/{provider_id}', [ServiceController::class, 'deleteAllByProvider']);
    Route::get('/services/update_status/{id}', [ServiceController::class, 'update_status']);
   

    
    Route::post('/updateService/{id}', [ServiceController::class, 'update']);
    Route::get('/providers/{provider_id}/services', [ServiceController::class, 'getServicesByProviderId']);


    Route::resource('/category', ProductCategoryController::class);
    Route::get('/category/index/{provider_id}', [ProductCategoryController::class, 'index']);
    Route::delete('/DeleteCategoriesByProviders/{provider_id}', [ProductCategoryController::class, 'deleteAllByProvider']);
    Route::get('/category/update_status/{id}', [ProductCategoryController::class, 'update_status']);
   
    Route::resource('/gallery', GalleryController::class);
    Route::get('/gallery/index/{provider_id}', [GalleryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::delete('/DeleteProductsByProviders/{provider_id}', [ProductController::class, 'deleteAllProductsByProvider']);
    Route::get('/products/update_status/{id}', [ProductController::class, 'update_status']);
   
    
    Route::get('/products/getProductbyId/{provider_id}/{id}', [ProductController::class, 'getProductbyId']);
    Route::get('/providers/{provider_id}/products', [ProductController::class, 'getProductsByProvider']);
    Route::get('/products/mostOrdered/{provider_id}', [ProductController::class, 'getMostOrderedProducts']);
    Route::post('/products/addProduct/{provider_id}', [ProductController::class, 'addProduct']);
    Route::delete('/products/deleteProduct/{provider_id}/{id}', [ProductController::class, 'deleteProduct']);
    Route::put('/products/updateProduct/{provider_id}/{product_id}', [ProductController::class, 'updateProduct']);

    Route::get('/veterinarians', [VeterinarianController::class, 'index']);
    Route::get('/veterinarians/{id}', [VeterinarianController::class, 'show']);
    Route::get('/providers/{provider_id}/veterinarians', [VeterinarianController::class, 'getVersByProviderId']);

    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews/{type}/{id}', [ReviewController::class, 'show']);
    Route::get('/services/{service_id}/reviews', [ReviewController::class, 'getReviewByServiceId']);
    Route::get('/products/{product_id}/reviews', [ReviewController::class, 'getReviewByProductId']);

    Route::get('/reminders', [ReminderController::class, 'index']);
    Route::post('/reminders', [ReminderController::class, 'store']);
    Route::get('/reminders/{id}', [ReminderController::class, 'show']);
    Route::get('/pet_owners/{pet_owner_id}/reminders', [ReminderController::class, 'getReminderByOwnerId']);
    Route::get('/pets/{pet_id}/reminders', [ReminderController::class, 'getRemindersByPetId']);
    Route::delete('/reminders/{id}', [ReminderController::class, 'destroy']);

    // Routes for Codes
    Route::get('/codes', [CodeController::class, 'index']);
    Route::get('/codes/{id}', [CodeController::class, 'show']);
    Route::post('/codes', [CodeController::class, 'store']);
    Route::put('/codes/{id}', [CodeController::class, 'update']);
    Route::delete('/codes/{id}', [CodeController::class, 'destroy']);
    Route::get('/codes/code/{code}', [CodeController::class, 'getCodeByCode']);

    // Payment Routes
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::put('/payments/{payment}', [PaymentController::class, 'update']);
    Route::get('/payments/pet-owner/{petOwnerId}', [PaymentController::class, 'getPaymentsByUserId']);

    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::middleware('auth:sanctum')->get('/orders/pet-owner/{petOwnerId}', [OrderController::class, 'getOrdersByUserId']);

    // Routes for Coupons
    Route::get('/coupons', [CouponController::class, 'index']);
    Route::middleware('auth:sanctum')->get('/coupons/membership', [CouponController::class, 'getMembershipCoupons']);
    Route::middleware('auth:sanctum')->get('/coupons/not-membership', [CouponController::class, 'getNotMembershipCoupons']);
    Route::get('/coupons/{id}', [CouponController::class, 'show']);
    Route::post('/coupons', [CouponController::class, 'store']);
    Route::put('/coupons/{id}', [CouponController::class, 'update']);
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy']);
    Route::get('/coupons/{id}', [CouponController::class, 'getCouponById']);
    Route::post('/coupons/{id}/reduce-quantity', [CouponController::class, 'reduceQuantity']);

    // Routes for CodeUsage
    Route::get('/code_usage', [CodeUsageController::class, 'index']);
    Route::get('/code_usage/{id}', [CodeUsageController::class, 'show']);
    Route::post('/code_usage', [CodeUsageController::class, 'store']);
    Route::put('/code_usage/{id}', [CodeUsageController::class, 'update']);
    Route::delete('/code_usage/{id}', [CodeUsageController::class, 'destroy']);

    // Routes for CouponUsage
    Route::get('/coupon_usage', [CouponUsageController::class, 'index']);
    Route::get('/coupon_usage/{id}', [CouponUsageController::class, 'show']);
    Route::post('/coupon_usage', [CouponUsageController::class, 'store']);
    Route::put('/coupon_usage/{id}', [CouponUsageController::class, 'update']);
    Route::delete('/coupon_usage/{id}', [CouponUsageController::class, 'destroy']);
    ////--------workkkkiiiing on it
    Route::middleware('auth:sanctum')->get('/coupon_usage/owner/{ownerId}', [CouponUsageController::class, 'getByOwnerId']);

    Route::get('/pet_owners', [PetOwnerController::class, 'index']);
    Route::get('/pet_owners/{id}', [PetOwnerController::class, 'view']);

    Route::get('/doctor_info', [DoctorInfoController::class, 'index']);
    Route::get('/doctor_info/{id}', [DoctorInfoController::class, 'show']);
    Route::post('/doctor_info/store', [DoctorInfoController::class, 'store']);
    Route::delete('/doctor_info/delete/{id}', [DoctorInfoController::class, 'destroy']);
    Route::get('/doctor_info/update_status/{id}', [DoctorInfoController::class, 'update_status']);
    Route::post('/doctor_info/update_doctor/{doctorid}/{providerid}', [DoctorInfoController::class, 'update_doctor']);
    Route::delete('/doctor_info/deleteAllDoctorsByProvider/{providerid}', [DoctorInfoController::class, 'deleteAllDoctorByProvider']);
   
    
    Route::get('/providers/{provider_id}/doctor_info', [DoctorInfoController::class, 'getDoctorInfoByProviderId']);
    route::post('/updateCollar',[CollarController::class, 'update']);
    Route::get('/collarCode/{code}', [CollarController::class, 'getPetByCode']);
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('favs', FavController::class)->except(['create', 'edit']);
    Route::resource('carts', CartController::class)->except(['create', 'edit']);
    Route::get('/favs/pet_owner/{pet_owner_id}', [FavController::class, 'getFavsByPetOwnerId']);
    Route::get('/carts/pet_owner/{pet_owner_id}', [CartController::class, 'getCartsByPetOwnerId']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/blogs', [BlogController::class, 'index']);
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
    Route::get('/blogs/{id}', [BlogController::class, 'show']);
});
