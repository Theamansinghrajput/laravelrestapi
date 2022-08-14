<?php

use App\Http\Controllers\AccountAccessModelController;
use App\Http\Controllers\LogoutAccountController;
use App\Http\Controllers\ApiValidationController;
use App\Http\Controllers\BeforeLoginController;
use App\Http\Controllers\CompanyInformationUpdateController;
use App\Http\Controllers\ErrorCodeController;
use App\Http\Controllers\KycAccessController;
use App\Http\Controllers\MailModelController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SmsModelController;
use App\Http\Controllers\AccountInformationController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\SocialSignupController;
use App\Http\Controllers\SupportTicketsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
//api codes
/*before login start*/
Route::get("/status-codes/{key}", [ErrorCodeController::class, "status_code_handler"]);
//api codes updates
Route::POST("/status-codes/{key}", [ErrorCodeController::class, "status_code_handler_update"]);
//user logout
Route::post("/user-logout", [LogoutAccountController::class, "account_logout_validate"]);
//manual login via third party google/facebook
Route::post("/social-login", [SocialLoginController::class, "social_login_validate"]);
//manual login
Route::post("/login", [BeforeLoginController::class, "login_validate"]);
//forget password
Route::post("/forget-password", [BeforeLoginController::class, "forget_validate"]);
//account information
Route::post("/account-info", [AccountInformationController::class, "account_info"]);
//Create New account
Route::post("/signup", [BeforeLoginController::class, "create_an_account"]);
//Create New account via third party google/facebook
Route::post("/social-signup", [SocialSignupController::class, "create_an_account_via_social"]);
//logout user
Route::post("/user-logout", [LogoutAccountController::class, "account_logout_validate"]);
/*before login end*/

/*company based information start*/
//new user kyc
Route::post("/kyc-information", [KycAccessController::class, "kyc_information"]);
//user compnay inforamtion update
Route::post("/company-information-modification", [CompanyInformationUpdateController::class, "company_information_modification"]);
/*company based information end*/

/*order start*/
//order_mangement - create new orders
Route::post("/orders", [OrderController::class, "create_new_order"]);//order_mangement - orders lising
Route::post("/orders-listing", [OrderController::class, "order_listing"]);
//order_mangement - filtered orders lising
Route::post("/filtered-orders-listing", [OrderController::class, "filtered_order_listing"]);
//order_mangement - order export
Route::post("/orders-export", [OrderController::class, "export_order_listing"]);
//order_mangement - order import
Route::post("/orders-import", [OrderController::class, "import_order_listing"]);
//order_mangement - order details
Route::post("/orders-details/{order_id}", [OrderController::class, "details_order_listing"]);
//order_mangement - order update
Route::post("/orders-update/{order_id}", [OrderController::class, "update_order_listing"]);
//order_mangement - order delete
Route::post("/orders-delete/{order_id}", [OrderController::class, "delete_order_listing"]);
/*order end*/

/*support ticketing start*/
//SupportTickets_mangement - create New SupportTickets
Route::post("/SupportTickets", [SupportTicketsController::class, "create_new_SupportTickets"]);
//SupportTickets_mangement - SupportTickets lising
Route::post("/SupportTickets-listing", [SupportTicketsController::class, "SupportTickets_listing"]);
//SupportTickets_mangement - filtered SupportTickets lising
Route::post("/filtered-SupportTickets-listing", [SupportTicketsController::class, "filtered_SupportTickets_listing"]);
//SupportTickets_mangement - SupportTickets export
Route::post("/SupportTickets-export", [SupportTicketsController::class, "export_SupportTickets_listing"]);
//SupportTickets_mangement - SupportTickets details
Route::post("/SupportTickets-details/{SupportTickets_id}", [SupportTicketsController::class, "details_SupportTickets_listing"]);
//SupportTickets_mangement - SupportTickets update
Route::post("/SupportTickets-update/{SupportTickets_id}", [SupportTicketsController::class, "update_SupportTickets_listing"]);
//SupportTickets_mangement - SupportTickets delete
Route::post("/SupportTickets-delete/{SupportTickets_id}", [SupportTicketsController::class, "delete_SupportTickets_listing"]);
/*support ticketing end*/




//Route::post("send-email", [PHPMailerController::class, "composeEmail"])->name("send-email");
