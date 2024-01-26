<?php

use Illuminate\Support\Facades\Route;


Route::group(['namespace' => 'VS'], function () {
    Route::get('/get-shipping-types', 'SupllierController@getTypeShipping');

    Route::post('/shopping-cart', 'ShoppingCartController@store');
    Route::get('/shopping-cart', 'ShoppingCartController@index');
    Route::get('/get-shopping-cart', 'ShoppingCartController@getCart');
    Route::post('/get-user-shopping-cart', 'ShoppingCartController@getUserCart');
    Route::put('/inactive-shopping-cart-array', 'ShoppingCartController@updateWithArray');
    Route::put('/inactive-shopping-cart-by-user', 'ShoppingCartController@updateByUserID');

    Route::get('rename-all-product-files', 'ProductController@renameAllProductFilesName');

    Route::post('shipping-calculate', 'CorreiosController@calculateShippingPrice');

    Route::get('track-package/{trackingCode}', 'CorreiosController@trackPackage');

    Route::get("categories", 'CategoryController@index');

    Route::get("get-sub-categories/{id}", 'CategoryController@getSubCategories');

    Route::get('sub-categories', 'SubCategoryController@index');

    Route::group(['middleware' => ['jwt.auth']], function (){

        Route::get('brands', 'BrandController@index');
        Route::post('brands', 'BrandController@create');
        Route::put('brands', 'BrandController@update');
        Route::get('paginated-brands', 'BrandController@paginate');

        Route::group(['middleware' => ['maintenance.adm']], function (){
            Route::get('/scoring-rules', 'ScoringRuleController@index');

            Route::get('/group-of-drugs/{uuid}', 'GroupOfDrugController@index');
            Route::post('/group-of-drugs/{uuid}', 'GroupOfDrugController@create');
            Route::put('/group-of-drugs/{uuid}', 'GroupOfDrugController@update');
            Route::put('/status-group-of-drugs/{uuid}', 'GroupOfDrugController@changeStatus');

            Route::post('/image-group-of-drugs/{uuid}', 'GroupOfDrugController@addImage');
            Route::get('/image-group-of-drugs/{id}', 'GroupOfDrugController@getImages');
            Route::delete('/image-group-of-drugs/{uuid}', 'GroupOfDrugController@removeImage');

            Route::get('/group-discount-list/{uuid}', 'GroupDiscountListController@index');
            Route::post('/group-discount-list/{uuid}', 'GroupDiscountListController@create');
            Route::put('/group-discount-list/{uuid}', 'GroupDiscountListController@update');
            Route::put('/status-group-discount-list/{uuid}', 'GroupDiscountListController@changeStatus');

            Route::post('/import-discount-list/{uuid}', 'DiscountListController@importSpreadSheet');
            Route::post('/insert-discount-list/{uuid}', 'DiscountListController@insertDiscountList');
            Route::post('/get-actual-list', 'DiscountListController@actualList');

            Route::post('/import-drugs-list/{uuid}', 'DrugsListController@import');
            Route::post('/insert-drugs-list/{uuid}', 'DrugsListController@insert');

            Route::post('payment-report/{uuid}', 'OrderController@admPaymentReport');
            Route::post('payment-report-xls/{uuid}', 'OrderController@admPaymentReportXls');
            Route::post('search-vs-order/{uuid}', 'OrderController@search');
            Route::post('approve-vs-order/{uuid}', 'OrderController@approveVsOrder');
            Route::post('approve-external-order/{uuid}', 'OrderController@approveExternalOrder');
        });

        Route::group(['middleware' => ['maintenance']], function (){

            Route::get('suppliers', 'SupllierController@index');
            Route::get('supplier/{id}', 'SupllierController@show');
            Route::put('supplier/{id}', 'SupllierController@update');
            Route::post('supplier', 'SupllierController@create');

            Route::get('products/{id}', 'ProductController@index');
            Route::get('product-list/{id}', 'ProductController@productList');
            Route::post('get-products/{id}', 'ProductController@getProducts');
            Route::get('product/{id}', 'ProductController@show');
            Route::post('set-product-image/{id}', 'ProductController@setProductImage');
            Route::post('set-massive-product-image/{id}', 'ProductController@setMassiveProductImages');
            Route::get('get-product-image-list/{id}', 'ProductController@getProductImageList');
            Route::get('get-product-image-name-list/{id}', 'ProductController@getProductImageNameList');
            Route::get('get-product-list', 'ProductController@getProductList');
            Route::delete('remove-product-image/{id}/{filename}', 'ProductController@removeProductImage');

            Route::post('order', 'OrderController@store');
            Route::post('get-vs-order-list', 'OrderController@getVsOrderList');
            Route::post('confirm-delivery/{uuid}', 'OrderController@confirmDelivery');
            Route::post('search-order/{uuid}', 'OrderController@searchOrder');
            Route::delete('order-boleto-cancel/{id}', 'OrderController@cancel');
            Route::post('cancel-order', 'OrderController@cancelOrder');
            Route::post('order-add-payment-voucher/{id}', 'OrderController@addPaymentVoucher');
            Route::get('order-get-payment-voucher/{id}', 'OrderController@getPaymentVoucher');
            Route::delete('order-remove-payment-voucher/{id}', 'OrderController@removePaymentVoucher');
            Route::post('order-get-transfer', 'OrderController@getTransfer');
            Route::post('order-get-transfer-external', 'OrderController@getTransferExternal');
            Route::get('orders/{id}', 'OrderController@show');
            Route::post('order-item-list', 'OrderController@getOrderItemList');
            Route::post('order-store', [\App\Http\Controllers\BoletoController::class, 'store']);
            Route::post('external-order-store', [\App\Http\Controllers\BoletoController::class, 'externalBillet']);
            Route::post('get-delivery-list/{uuid}', 'OrderController@getDeliveryList');
            Route::post('get-delivery-product-list/{uuid}', 'OrderController@getDeliveryProductList');
            Route::put('update-delivery/{uuid}', 'OrderController@updateDeliveryData');
            Route::put('update-tracking-code/{uuid}', 'OrderController@updateTrackingCode');
            Route::post('send-a-package/{uuid}', 'OrderController@sendAPackage');
            Route::post('get-order-data', 'OrderController@getOrderData');
            Route::post('confirm-delivery-to-client', 'OrderController@confirmDeliveryToClient');
            Route::post('adm-confirm-delivery-to-client/{uuid}', 'OrderController@admConfirmDeliveryToClient');
            Route::post('set-real-shipping-cost/{uuid}', 'OrderController@setRealShippingCost');

            Route::get('user-address/{id}', 'UserAddressController@show');
            Route::get('ex-user-address/{id}', 'UserAddressController@externalShow');
            Route::post('user-address', 'UserAddressController@create');
            Route::put('user-address/{id}', 'UserAddressController@update');
            Route::delete('user-address/{id}', 'UserAddressController@delete');
            Route::put('user-address/change-status/{id}', 'UserAddressController@chageStatus');

            Route::get("category/{id}", 'CategoryController@show');
            Route::put("category/{id}", 'CategoryController@update');
            Route::post("category/{uuid}", 'CategoryController@create');
            Route::post('/category/add-image/{uuid}', 'CategoryController@addImage');
            Route::get('/category/get-images/{id}', 'CategoryController@getImages');
            Route::delete('/category/delete-image/{uuid}', 'CategoryController@removeImage');

            Route::get('sub-category/{id}', 'SubCategoryController@show');
            Route::post('sub-category/{uuid}', 'SubCategoryController@create');
            Route::put('sub-category/{id}/{uuid}', 'SubCategoryController@update');
            Route::put('sub-category/status/{id}/{uuid}', 'SubCategoryController@status');

            Route::get('measurement', 'MeasurementController@index');
            Route::get('measurement/{id}', 'MeasurementController@show');
            Route::post('measurement/{uuid}', 'MeasurementController@create');
            Route::put('measurement/{id}/{uuid}', 'MeasurementController@update');
        });
    });
});
