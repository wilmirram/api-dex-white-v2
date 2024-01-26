<?php

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'Market'], function () {
    Route::get('/dashboard-values', 'ProductController@getDashboardItems');

    Route::post('/search-products', 'ProductController@search');

    Route::post('/registration-request', 'RegistrationRequestController@store');

    Route::get('/get-spotlight-products', 'ProductController@spotLightProducts');

    Route::get('/get-banner/{id}', 'DashBoardBannerController@getBannerImages');

    Route::get('/get-brands', 'ProductController@getBrands');

    Route::get('/get-product-evaluation/{id}', 'ProductController@getEvaluation');

    Route::get('/get-product/{id}', 'ProductController@getProduct');
    Route::get('/get-product-history-price/{id}', 'ProductController@getHistoryPrice');

    Route::group(['middleware' => ['jwt.auth']], function (){
        Route::post('create-variations/{uuid}', 'VariationsController@createVariation');
        Route::get('get-variations', 'VariationsController@indexVariation');
        Route::put('update-variations/{uuid}', 'VariationsController@updateVariations');

        Route::post('create-variations-value/{uuid}', 'VariationsController@createVariationValue');
        Route::get('get-variations-value/{id}', 'VariationsController@indexVariationValue');
        Route::put('update-variations-value/{uuid}', 'VariationsController@updateVariationsValue');


        Route::post('new-product/{uuid}', 'ProductController@newProduct');
        Route::get('verify-reference-code/{referenceCode}', 'ProductController@verifyReferenceCode');
        Route::post('new-product-price/{uuid}', 'ProductController@newProductPrice');
        Route::put('update-product/{uuid}', 'ProductController@update');
        Route::post('search-product/{uuid}', 'ProductController@searchProduct');

        Route::post('/approve-evaluation/{uuid}', 'ProductController@approveEvaluation');
        Route::post('/comment-evaluation/{uuid}', 'ProductController@commentEvaluation');
        Route::post('/change-status-evaluation/{uuid}', 'ProductController@changeStatus');
        Route::post('/add-product-evaluation', 'ProductController@evaluation');
        Route::post('/add-banner/{uuid}', 'DashBoardBannerController@addBanner');
        Route::get('/get-pendent-evaluations/{uuid}', 'ProductController@getPendentEvaluations');
        Route::get('/get-evaluations/{uuid}', 'ProductController@getEvaluations');
        Route::delete('/remove-banner/{uuid}', 'DashBoardBannerController@removeBanner');
    });
});

