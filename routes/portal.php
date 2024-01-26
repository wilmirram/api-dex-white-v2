<?php

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'Portal'], function () {

    Route::get('/announcement', 'AnnouncementController@index');

    Route::post('/news', 'NewsController@index');
    Route::post('/news-test', 'NewsController@indexEspelho');

    Route::get('/get-news/{uuid}', 'NewsController@getNews');

    Route::get('/get-news-images/{id}', 'NewsController@getNewsImageList');
    Route::get('/news-category', 'NewsCategoryController@index');

    Route::group(['middleware' => ['jwt.auth']], function (){
        Route::post('/news-category/{uuid}', 'NewsCategoryController@store');
        Route::put('/news-category/{uuid}', 'NewsCategoryController@update');
        Route::put('/news-category-status/{uuid}', 'NewsCategoryController@changeStatus');

        Route::post('/news/{uuid}', 'NewsController@store');
        Route::put('/news/{uuid}', 'NewsController@update');
        Route::put('/news-status/{uuid}', 'NewsController@status');

        Route::post('/news/add-images/{id}', 'NewsController@setImages');
        Route::delete('/news/remove-image/{id}', 'NewsController@removeImage');

        Route::get('/advertising-location', 'AdvertisingLocationController@index');
        Route::get('/advertising-location/{id}', 'AdvertisingLocationController@show');
        Route::post('/advertising-location', 'AdvertisingLocationController@store');
        Route::put('/advertising-location/{id}', 'AdvertisingLocationController@update');
        Route::put('/advertising-location/change-status/{id}', 'AdvertisingLocationController@changeStatus');

        Route::get('/advertiser', 'AdvertiserController@index');
        Route::get('/advertiser/{id}', 'AdvertiserController@show');
        Route::post('/advertiser', 'AdvertiserController@store');
        Route::put('/advertiser/{id}', 'AdvertiserController@update');
        Route::put('/advertiser/change-status/{id}', 'AdvertiserController@changeStatus');

        Route::get('/announcement/{id}', 'AnnouncementController@show');
        Route::post('/announcement', 'AnnouncementController@store');
        Route::put('/announcement/{id}', 'AnnouncementController@update');
        Route::put('/announcement/change-status/{id}', 'AnnouncementController@changeStatus');

        Route::post('/announcement/set-image-list/{id}', 'AnnouncementController@setImages');
        Route::delete('/announcement/remove-image/{id}', 'AnnouncementController@removeImage');
        Route::get('/type-person', 'AnnouncementController@typePerson');
    });
});

