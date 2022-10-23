<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\QuestionController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources([
    'products' => ProductController::class,
    'users' => UserController::class,
    'category' => CategoryController::class,
    'order' => OrderController::class,
    'carts' => CartController::class,
    'answers' => AnswerController::class,
    'question' => QuestionController::class,
]);

Route::apiResource('products/{product}/reviews', ReviewController::class)
    ->only('store', 'update', 'destroy');



Route::get('question/{product_id}/product', [QuestionController::class, 'questionOfProduct']);
Route::post('questions/{id}/answer', [QuestionController::class, 'answerForQuestion']);
Route::put('answers/{answer}', [QuestionController::class, 'answerUpdate']);
Route::delete('answers/{answer}', [QuestionController::class, 'answerDelete']);
Route::get('/me', [AuthController::class, 'me']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/image', [ProductImageController::class, 'imageStore']);
