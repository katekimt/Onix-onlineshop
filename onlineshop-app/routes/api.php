<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::apiResources([
    'products' => ProductController::class,
    'users' => UserController::class,
    'category' => CategoryController::class,
    'order' => OrderController::class,
    'carts' => CartController::class,
    'question' => QuestionController::class,
]);

Route::apiResource('products/{product}/reviews', ReviewController::class)
    ->only('store', 'update', 'destroy');

Route::get('question/{product_id}/product', [QuestionController::class, 'questionOfProduct']);
Route::post('questions/{id}/answer', [QuestionController::class, 'answerForQuestion']);
Route::put('answers/{answer}', [QuestionController::class, 'answerUpdate']);
Route::delete('answers/{answer}', [QuestionController::class, 'answerDelete']);

Route::get('orders/orderItem', [OrderController::class, 'getOrderItem']);

Route::get('/me', [AuthController::class, 'me']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/image', [ProductImageController::class, 'imageStore']);
