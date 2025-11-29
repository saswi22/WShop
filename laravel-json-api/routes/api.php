<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;
use App\Http\Controllers\Api\V2\Auth\LoginController;
use App\Http\Controllers\Api\V2\Auth\LogoutController;
use App\Http\Controllers\Api\V2\Auth\RegisterController;
use App\Http\Controllers\Api\V2\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V2\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V2\MeController;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth routes WITHOUT json.api middleware (uses standard JSON format)
Route::prefix('v2')->group(function () {
    Route::post('/login', LoginController::class)->name('login');
    Route::post('/logout', LogoutController::class)->middleware('auth:api');
    Route::post('/register', RegisterController::class);
    Route::post('/password-forgot', ForgotPasswordController::class);
    Route::post('/password-reset', ResetPasswordController::class)->name('password.reset');
});

// Public API endpoints (Hospital Profile Endpoints)
Route::prefix('v2')->group(function () {
    // Doctors
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{id}', [DoctorController::class, 'show']);
    
    // Services
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    
    // Schedules
    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
    
    // Galleries
    Route::get('/galleries', [GalleryController::class, 'index']);
    Route::get('/galleries/{id}', [GalleryController::class, 'show']);
    
    // News
    Route::get('/news', [NewsController::class, 'index']);
    Route::get('/news/{id}', [NewsController::class, 'show']);
    
    // Careers
    Route::get('/careers', [CareerController::class, 'index']);
    Route::get('/careers/{id}', [CareerController::class, 'show']);
    
    // Contact (Public - can submit)
    Route::post('/contacts', [ContactController::class, 'store']);
    
    // Applications (Public - can submit)
    Route::post('/applications', [ApplicationController::class, 'store']);
    
    // Upload CV (Public - for job applications)
    Route::post('/upload-cv', [UploadController::class, 'uploadCV']);
    
    // Facilities
    Route::get('/facilities', [FacilityController::class, 'index']);
    Route::get('/facilities/{id}', [FacilityController::class, 'show']);

    // Testimonials (Public)
    Route::get('/testimonials', [TestimonialController::class, 'index']);
    Route::post('/testimonials/public', [TestimonialController::class, 'storePublic']);
    
    // Pages (public pages)
    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/{slug}', [PageController::class, 'show']);

    // Partners
    Route::get('/partners', [PartnerController::class, 'index']);

    // Settings (public - for frontend)
    Route::get('/settings', [SettingController::class, 'index']);

    // Chat (Public - untuk user)
    Route::post('/chat/room', [ChatController::class, 'createOrGetRoom']);
    Route::post('/chat/message', [ChatController::class, 'sendMessage']);
    Route::get('/chat/messages/{roomId}', [ChatController::class, 'getMessages']);
    Route::get('/chat/queue-status/{roomId}', [ChatController::class, 'getQueueStatus']);
});

// Protected Admin API endpoints
Route::prefix('v2')->middleware('auth:api')->group(function () {
    // Image Upload
    Route::post('/upload', [UploadController::class, 'upload']);
    
    // Doctors
    Route::post('/doctors', [DoctorController::class, 'store']);
    Route::put('/doctors/{id}', [DoctorController::class, 'update']);
    Route::delete('/doctors/{id}', [DoctorController::class, 'destroy']);
    
    // Services
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    
    // Schedules
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
    Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
    
    // Galleries
    Route::post('/galleries', [GalleryController::class, 'store']);
    Route::put('/galleries/{id}', [GalleryController::class, 'update']);
    Route::delete('/galleries/{id}', [GalleryController::class, 'destroy']);
    
    // News
    Route::post('/news', [NewsController::class, 'store']);
    Route::put('/news/{id}', [NewsController::class, 'update']);
    Route::delete('/news/{id}', [NewsController::class, 'destroy']);
    
    // Careers
    Route::post('/careers', [CareerController::class, 'store']);
    Route::put('/careers/{id}', [CareerController::class, 'update']);
    Route::delete('/careers/{id}', [CareerController::class, 'destroy']);
    
    // Contacts
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::put('/contacts/{id}', [ContactController::class, 'update']);
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);
    
    // Applications admin
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/{id}', [ApplicationController::class, 'show']);
    Route::put('/applications/{id}', [ApplicationController::class, 'update']);
    Route::delete('/applications/{id}', [ApplicationController::class, 'destroy']);
    
    // Facilities admin
    Route::post('/facilities', [FacilityController::class, 'store']);
    Route::put('/facilities/{id}', [FacilityController::class, 'update']);
    Route::delete('/facilities/{id}', [FacilityController::class, 'destroy']);

    // Testimonials admin
    Route::get('/testimonials/all', [TestimonialController::class, 'all']);
    Route::post('/testimonials', [TestimonialController::class, 'store']);
    Route::put('/testimonials/{id}/approve', [TestimonialController::class, 'approve']);
    Route::delete('/testimonials/{id}', [TestimonialController::class, 'destroy']);

    // Pages admin
    Route::get('/pages/all', [PageController::class, 'adminIndex']);
    Route::post('/pages', [PageController::class, 'store']);
    Route::put('/pages/{id}', [PageController::class, 'update']);
    Route::delete('/pages/{id}', [PageController::class, 'destroy']);

    // Partners admin
    Route::post('/partners', [PartnerController::class, 'store']);
    Route::put('/partners/{id}', [PartnerController::class, 'update']);
    Route::delete('/partners/{id}', [PartnerController::class, 'destroy']);

    // Settings admin
    Route::get('/settings/admin', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update']);

    // Chat admin
    Route::get('/chat/rooms', [ChatController::class, 'getRooms']);
    Route::put('/chat/room/{roomId}/read', [ChatController::class, 'markAsRead']);
    Route::put('/chat/room/{roomId}/close', [ChatController::class, 'closeChat']);
    Route::delete('/chat/room/{roomId}', [ChatController::class, 'deleteRoom']);
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount']);
});

JsonApiRoute::server('v2')->prefix('v2')->resources(function (ResourceRegistrar $server) {
    $server->resource('users', JsonApiController::class);
    Route::get('me', [MeController::class, 'readProfile']);
    Route::patch('me', [MeController::class, 'updateProfile']);
});