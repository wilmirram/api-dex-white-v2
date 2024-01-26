<?php

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'School'], function () {

    Route::get('/show-vouchers/{id}', 'VoucherController@show');
    Route::get('/show-vouchers', 'VoucherController@index');

    Route::post('/shopping-cart', 'ShoppingCartController@store');
    Route::post('/get-shopping-cart', 'ShoppingCartController@getShoppingCart');
    Route::post('/remove-item-shopping-cart', 'ShoppingCartController@inactiveShoppingCart');

    Route::post('/generate-order', 'OrderController@store');
    Route::post('/my-orders', 'OrderController@myOrders');
    Route::post('/get-transfer', 'OrderController@getTransfer');
    Route::post('/cancel-boleto', 'OrderController@cancel');

    Route::post('/list-orders-admin', 'OrderController@listOrderAdmin');
    Route::post('/list-order-courses-admin', 'OrderController@listOrderCoursesAdmin');

    Route::post('/show-courses', 'CourseUserController@showCourses');
    Route::post('/generate-certificate', 'CourseUserController@generateCertificate');

    Route::get('courses', 'CourseController@index');
    Route::get('course/{id}', 'CourseController@show');

    Route::get('/course-classes/{id}', 'CourseClassController@index');
    Route::post('/course-student-classes', 'CourseClassController@getStudentClasses');
    Route::get('/get-classes/{id}', 'CourseClassController@getClasses');
    Route::get('/course-class/{id}', 'CourseClassController@show');

    Route::post('start-class', 'CourseClassController@startClass');
    Route::post('finish-class', 'CourseClassController@finishClass');

    Route::post('/apply-voucher', 'VoucherController@applyVoucher');

    Route::group(['middleware' => ['jwt.auth']], function (){

        Route::post('/course/{uuid}', 'CourseController@store');
        Route::put('/course/{uuid}', 'CourseController@update');
        Route::put('/course-change-status/{uuid}', 'CourseController@changeStatus');
        Route::post('/course-set-image', 'CourseController@setPicture');
        Route::delete('/course-remove-image/{id}', 'CourseController@removePicture');

        Route::get('/combos', 'CourseComboController@index');
        Route::get('/combo/{id}', 'CourseComboController@show');
        Route::post('/combo/{uuid}', 'CourseComboController@store');
        Route::put('/combo/{uuid}', 'CourseComboController@update');
        Route::put('/combo-change-status/{uuid}', 'CourseComboController@changeStatus');

        Route::get('/course-prices', 'CoursePriceController@index');
        Route::get('/course-price/{id}', 'CoursePriceController@show');
        Route::post('/course-price/{uuid}', 'CoursePriceController@store');
        Route::put('/course-price/{uuid}', 'CoursePriceController@update');
        Route::put('/course-price-change-status/{uuid}', 'CoursePriceController@changeStatus');

        Route::post('/course-class/{uuid}', 'CourseClassController@store');
        Route::put('/course-class/{uuid}', 'CourseClassController@update');
        Route::put('/course-class-change-status/{uuid}', 'CourseClassController@changeStatus');
        Route::post('/course-class-set-image', 'CourseClassController@setPicture');
        Route::delete('/course-class-remove-image/{id}', 'CourseClassController@removePicture');
        Route::post('/course-class-set-attachment', 'CourseClassController@setAttachment');
        Route::delete('/course-class-remove-attachment/{id}', 'CourseClassController@removeAttachment');
    });
});

