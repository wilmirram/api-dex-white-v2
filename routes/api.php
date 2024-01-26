<?php
use Illuminate\Support\Facades\Route;

/*** ROTAS FORA DO MIDDLEWARE MANUTENÇÃO ***/

/*
Route::post('/paypal-callback', function (\Illuminate\Http\Request $request){
    $arquivo = fopen('paypal.txt','w');
    fwrite($arquivo, json_encode($request->all()));
    fclose($arquivo);

    return response()->json('success');
});
*/
//Route::post('/twitpay-token','TwitPayController@getToken');

//Route::post('/twitpay-user-auth','TwitPayController@authUser');

//Route::post('/pay-twitpay','TwitPayController@transfer');

//Route::post('/pay-mercadopago','MercadoPagoController@index');

//Route::post('/ipn-mercadopago', 'MercadoPagoController@ipn');

//Route::post('boleto-school', 'BoletoController@storeSchool');

//ROTAS PARA CRYPTO DA MATIC

Route::get('createaddresserc20','Crypto@createaddresserc20');
Route::post('/transfer-usdt', 'PaymentUsdt@transferirmatic');

// FIM ROTAS CRYPTO MATIC



Route::post('/insert-upline-score-chip', 'AdmController@insertUplineScoreChip');

Route::get('chip-activations', 'IndividualRouteController@chipActivations');
Route::get('chip-recharges', 'IndividualRouteController@chipRecharges');

Route::post('/remove-registration-request', 'RegistrationRequestController@removeRegistrationRequest');

Route::get('verify-chip-number/{number}', 'IndividualRouteController@verifyChipNumber');
Route::get('verify-chip-order-number/{number}', 'IndividualRouteController@verifyOrderChip');

//Route::post('/emprestimos/send-email', 'IndividualRouteController@emprestimosEmail');

//Route::post('/telefonia/send-email', 'IndividualRouteController@telefoniaEmail');

Route::post('/teste-integra-crypto', 'CryptoCoinController@login');

Route::post('/crypto/add-wallet', 'CryptoCoinController@addWallet');
Route::post('/crypto/add-receipt', 'SquadiPayController@store');

Route::get('/exchanges', 'ExchangeController@index');
Route::post('/exchanges', 'ExchangeController@store');
Route::put('/exchanges/{id}', 'ExchangeController@update');
Route::put('/exchanges/change-status/{id}', 'ExchangeController@changeStatus');

Route::get('/term-status', 'TermStatusController@index');
Route::post('/set-term-status', 'TermStatusController@set');

//Route::post('/paypal-store/{id}', 'PayPalController@store');
//Route::post('/paypal-confirm-payment', 'PayPalController@confirmPayment');

Route::get('access-token/{time}', 'JWTAuthenticateController@accessToken');

Route::post('mercado-pago/callback', 'MercadoPagoController@callback');

Route::get('mercado-pago/get-public-key', 'MercadoPagoController@getPublicKey');

Route::get('validate-access-token/{token}', 'JWTAuthenticateController@validateAccessToken');

//Route::post('mercado-pago/process_payment', 'MercadoPagoController@store');
//Route::post('mercado-pago/process_payment_api', 'MercadoPagoController@storeApi');

Route::post('mercado-pago/clean-fields', 'MercadoPagoController@cancel');

Route::get('/teste-email/{email}', 'IndividualRouteController@termosDeUso');
Route::get('/sent-terms-use/{limit}', 'IndividualRouteController@sentTermsOfUse');
Route::post('/add-termo/{uuid}', 'IndividualRouteController@addTermoDeUso');

Route::post('/teste-soap', 'IndividualRouteController@testingSOAP');
//Route::get('/show-invoice/{invoiceID}/{send}', 'OrderItemController@generateInvoice');
Route::get('/send-many-invoices/{limit}', 'IndividualRouteController@sendManyInvoices');

Route::post('/get-crypto-quote', 'IndividualRouteController@getCryptoQuote');

Route::get('/payment-methods-store', 'PaymentMethodController@paymentMethodStore');

Route::get('/payment-methods-office', 'PaymentMethodController@paymentMethodOffice');

Route::get('/currencies', 'CurrencyController@index');

Route::post('pix-generate', 'PixController@generate');
Route::post('pix-generate-store', 'PixController@generateForStore');
Route::get('pix-cancel/{id}', 'PixController@cancel');
Route::post('pix-callback', 'PixController@callback');

Route::group(['middleware' => ['jwt.auth']], function (){

    Route::post('adm/insert-vs-upline-score', 'AdmController@insertVsUplineScore');

    Route::post('/crypto/find', 'CryptoCoinController@queryWallets');
    Route::post('/crypto/import-csv', 'CryptoCoinController@importCsv');
    Route::get('/crypto/list-wallets', 'CryptoCoinController@listWallets');

    Route::get('/whatsapp-messages', 'SendWhatsappController@index');
    Route::post('/send-whatsapp', 'SendWhatsappController@store');
    Route::get('/get-not-sponsored-nicknames/{id}', 'UserAccountController@getNicknamesForCareerPath');

    Route::get('/get-career-path/{id}', 'CareerPathController@getCareerPath');
    Route::post('/career-path/{uuid}', 'CareerPathController@create');
    Route::get('/career-path/{uuid}', 'CareerPathController@index');
    Route::post('/active-or-inactive-career-path/{uuid}', 'CareerPathController@activeOrInactive');
    Route::put('/career-path/{id}/{uuid}', 'CareerPathController@update');
    Route::post('/set-career-path-pin/{uuid}', 'CareerPathController@setPin');
    Route::post('/set-career-path-cap/{uuid}', 'CareerPathController@setCap');
    Route::get('/get-career-path-images/{id}', 'CareerPathController@getImages');

    Route::get("/get-type-competence/{uuid}", 'CompetenceController@getTypeCompetence');
    Route::get("/get-type-reference-competence/{uuid}", 'CompetenceController@getReferenceCompetence');
    Route::get("/get-competence/{uuid}", 'CompetenceController@index');
    Route::post("/competence/{uuid}", 'CompetenceController@create');
    Route::post("/active-or-inactive-competence/{uuid}", 'CompetenceController@activeOrInactive');
    Route::put("/competence/{id}/{uuid}", 'CompetenceController@update');

    Route::post('/add-banner-image/{uuid}', 'IndividualRouteController@addBannerImage');
    Route::get('/get-banner-images', 'IndividualRouteController@getBannerImages');
    Route::delete('/remove-banner-image/{uuid}', 'IndividualRouteController@removeBannerImage');

    Route::post('/add-file/{uuid}', 'IndividualRouteController@insertFileIntoDownloadSection');
    Route::get('/get-files', 'IndividualRouteController@getFileFromDownloadSection');
    Route::delete('/remove-file/{uuid}', 'IndividualRouteController@removeFileFromdownloadSection');

    Route::get('/delivery-status', 'DeliveryStatusController@index');

    //COUNTRY @CountryController
    Route::get('/country', 'CountryController@index');

    //USER_ACCOUNT @UserAccountController
    Route::get('/user-account', 'UserAccountController@index');

    //User @UserController
    Route::get('/user/{id}', 'UserController@show');

    //Product @ProductController
    Route::get('/product', 'ProductController@index');

    //OrderItem @OrderItemController
    Route::get('/get-voucher/{id}', 'OrderItemController@getVoucher');

    //Boleto @BoletoController
    Route::post('/boleto/{uuid}', 'BoletoController@index');
    Route::post('/vs-boleto/{uuid}', 'BoletoController@boletoVs');

    Route::post('/billet-log/{uuid}', 'BilletLogController@index');

    //PAYMENT_METHOD @PaymentMethodController
    Route::get('/payment-methods', 'PaymentMethodController@index');

    /** ADM **/
    Route::group(['middleware' => ['maintenance.adm']], function (){

        Route::post('/adm/user-account-cashback-report', 'AdmController@CashbackReport');

        Route::post('/adm/search-report/{uuid}', 'AdmController@searchReport');

        Route::post('/adm/approve-sponsored-account/{uuid}', 'AdmController@approveSponsoredAccount');
        Route::post('/adm/manually-enter-score/{uuid}', 'AdmController@manuallyEnterUserAccountScore');
        Route::get('/adm/status-order', 'StatusOrderController@index');

        Route::post('/adm/get-user-log-list/{uuid}', 'AdmController@getUserLogList');
        Route::post('/adm/search-transfer/{uuid}', 'AdmController@searchTransfer');

        Route::get('/adm/holidays', 'HolidayController@index');
        Route::get('/adm/holidays/{id}', 'HolidayController@show');
        Route::post('/adm/holidays', 'HolidayController@store');
        Route::put('/adm/holidays/{id}', 'HolidayController@update');

        Route::post('/adm/new-adm/{uuid}', 'AdmController@createNewAdm');
        Route::get('/adm/adm-user-list/{uuid}', 'AdmController@getAdmUserList');
        Route::put('/adm/change-status-adm-user/{uuid}/{id}', 'AdmController@activeOrInactive');
        Route::put('/adm/change-access-level/{uuid}', 'AdmController@changeAdmLevel');

        //PERMISSIONS
        Route::get('/adm/permissions/{uuid}', 'PermissionsController@index');
        Route::get('/adm/permissions/{id}/{uuid}', 'PermissionsController@show');
        Route::post('/adm/permissions/{uuid}', 'PermissionsController@store');
        Route::put('/adm/permissions/{id}/{uuid}', 'PermissionsController@update');
        Route::put('/adm/active-or-inactive-permission/{id}/{uuid}', 'PermissionsController@inactiveOrActive');

        Route::post('/adm/get-privileges/{uuid}', 'PermissionsController@getAdmPrivilegesList');
        Route::put('/adm/update-privileges/{uuid}', 'PermissionsController@updateAdmPrivileges');

        Route::get('/adm/get-withdrawal-configs/{uuid}', 'ConfigController@getWithdrawalConfigs');
        Route::put('/adm/update-withdrawal-configs/{uuid}', 'ConfigController@updateWithdrawalConfigs');
        Route::post('/get-withdrawal-limits', 'ConfigController@getWithdrawalLimit');

        //ACESS_LEVEL
        Route::get('/adm/access-level/{uuid}', 'AccessLevelController@index');
        Route::post('/adm/access-level/{uuid}', 'AccessLevelController@create');
        Route::put('/adm/access-level/{uuid}/{id}', 'AccessLevelController@update');
        Route::put('/adm/change-access-level/{uuid}/{id}', 'AccessLevelController@changeStatus');

        //TYPE_OBJECT
        Route::get('/adm/type-object/{uuid}', 'TypeObjectController@index');
        Route::get('/adm/type-object/{id}/{uuid}', 'TypeObjectController@show');
        Route::post('/adm/type-object/{uuid}', 'TypeObjectController@store');
        Route::put('/adm/type-object/{id}/{uuid}', 'TypeObjectController@update');
        Route::put('/adm/active-or-inactive-type-object/{id}/{uuid}', 'TypeObjectController@activeOrInactive');

        Route::post('/adm/order-payment-report/{uuid}', 'OrderItemController@admPaymentReport');

        Route::post('/adm/order-payment-report-xls/{uuid}', 'OrderItemController@admPaymentReportXls');

        Route::put('/adm/new-user-email/{uuid}', 'AdmController@newUserEmail');

        Route::post('/adm/find-nickname-by-user/{uuid}', 'AdmController@findNicknameByUser');
        Route::post('/adm/find-external-user/{uuid}', 'AdmController@findExternalUser');

        Route::post('/boleto-details/{uuid}', 'BoletoController@show');

        Route::put('/new-adm-password/{uuid}', 'RecoveryPasswordController@newAdminPassword');

        //PAYMENT_ORDER @PaymentOrderController
        Route::post('/approve-payment', 'PaymentOrderController@approvePayment');


        //ORDER_ITEM @OrderItemController
        Route::post('/search-order-item/{uuid}', 'OrderItemController@search');
        Route::put('/update-order-item/{uuid}', 'OrderItemController@admUpdate');
        Route::put('/update-order-item/{uuid}', 'OrderItemController@admUpdate');
        Route::post('/export-order-item/{uuid}', 'OrderItemController@exportsSearch');

        //WITHDRAWAL_REQUEST @WithDrawlRequestController
        Route::post('/search-withdrawal-request/{uuid}', 'WithDrawlRequestController@search');
        Route::put('/update-withdrawal-request/{uuid}', 'WithDrawlRequestController@admUpdate');
        //Route::post('/withdrawal-spreadsheet-export', 'WithDrawlRequestController@spreadsheetExport');
        //Route::post('/prepare-withdrawal-spread-report/{uuid}', 'WithDrawlRequestController@prepareWithdrawalSpreadReport');
        Route::post('/remove-withdrawal-requests/{uuid}', 'WithDrawlRequestController@removeWithdrawalRequests');
        Route::get('/list-spreadsheets/{uuid}', 'WithDrawalRequestSheetController@index');
        Route::post('/download-spreadsheet/{uuid}', 'WithDrawalRequestSheetController@downloadSpreadSheet');
        Route::post('/pendent-withdrawal-request/{uuid}', 'AdmController@pendentWithdrawalRequest');
        Route::post('/approve-withdrawal-request/{uuid}', 'AdmController@approveWithdrawalRequest');
        Route::post('/approve-withdrawal-request-btc/{uuid}', 'PaymentBtc@approveWithdrawalBtc');
        Route::get('/verify-existing-spreadsheet/{filename}', 'WithDrawlRequestController@verifySpreadSheetInFolder');
        Route::post('/withdrawal-report/{uuid}', 'AdmController@withdrawalReport');
        Route::post('/adm/withdrawal-for-crypto/{uuid}', 'WithDrawlRequestController@admWithdrawalByCrypto');

        Route::post('/document-blocked/{uuid}', 'DocumentBlockedController@create');
        Route::put('/document-blocked/{uuid}', 'DocumentBlockedController@update');
        Route::put('/document-blocked-status/{uuid}', 'DocumentBlockedController@status');
        Route::get('/document-blocked/{uuid}', 'DocumentBlockedController@index');

        //REGISTRATION_REQUEST @RegistrationRequestController
        Route::post('/search-registration-request/{uuid}', 'RegistrationRequestController@search');
        Route::put('/update-registration-request/{uuid}', 'RegistrationRequestController@admUpdate');

        //ACCOUNT_STATEMENT @AccountStatementController
        Route::post('/search-statement/{uuid}', 'AccountStatementController@search');
        Route::put('/update-statement/{uuid}', 'AccountStatementController@admUpdate');

        //USER @UserController
        Route::post('/search-user/{uuid}', 'UserController@search');
        Route::put('/update-user/{uuid}', 'UserController@admUpdate');

        //DAILY_BONUS_SCORE @DailyBonusScoreController
        Route::post('/daily-bonus-score', 'DailyBonusScoreController@dailyBonusScore');
        Route::post('/report-insert-bonus-score', 'DailyBonusScoreController@reportInsertBonusScore');
        Route::get('/get-daily-bonus-score-list/{uuid}', 'DailyBonusScoreController@getDailyBonusScoreList');

        //DAILY_CASHBACK @DailyCashBackController
        Route::post('/daily-cashback', 'DailyCashBackController@insertCashBack');
        Route::get('/get-cashback-list/{uuid}', 'DailyCashBackController@getDailyCashBackList');
        Route::post('/report-insert-cashback', 'DailyCashBackController@reportInsertCashback');
        Route::post('/adm/get-cashback/{uuid}', 'AdmController@getCashBackList');

        //USER @UserController
        Route::post('/search-config/{uuid}', 'ConfigController@search');
        Route::put('/adm/update-preferential-sponsor/{uuid}', 'ConfigController@admChangePreferentialSponsor');

        //ADM @AdmBackController
        Route::get('/get-adm-privileges/{uuid}', 'AdmController@getAdmPrivileges');
        Route::get('/get-adm-menu/{uuid}', 'AdmController@getAdmMenu');

        //RegistrationRequestAdm
        Route::post('/adm/registration-request', 'RegistrationRequestController@admRegistrationRequest');

        //ADM @AdmController
        Route::post('/adm/blocked-cashback/{uuid}', 'AdmController@blockedCashback');

        //Config @ConfigController
        Route::put('/update-config/{uuid}', 'ConfigController@admUpdate');

        //DASHBOARD
        Route::post('/adm/get-user-account-information/{uuid}', 'AdmController@getUserAccountInformationAll');
        Route::post('/adm/get-binary-chart-list/{uuid}', 'AdmController@getBinaryChartList');

        //MY ACCOUNT
        Route::post('/adm/get-user-bank/{uuid}', 'AdmController@getUserBank');
        Route::post('/adm/get-preferential-bank/{uuid}', 'AdmController@getPreferentialBank');
        Route::post('/adm/get-user-data/{uuid}', 'AdmController@getUserData');
        Route::post('/adm/get-user-wallet/{uuid}', 'AdmController@getUserWallet');
        Route::post('/adm/get-preferential-wallet/{uuid}', 'AdmController@getPreferentialUserWallet');

        //STORE
        Route::post('/adm/get-product-list/{uuid}', 'AdmController@getProductList');
        Route::post('/adm/get-order-item-list/{uuid}', 'AdmController@getOrderItemList');

        //NETWORK
        Route::post('/adm/get-network-list/{uuid}', 'AdmController@getNetworkList');
        Route::post('/adm/get-sponsors-list/{uuid}', 'AdmController@getSponsorsList');
        Route::post('/adm/get-last-network-leg/{uuid}', 'AdmController@getLastNetworkLeg');
        Route::post('/adm/get-network-list-by-nickname/{uuid}', 'AdmController@getNetworkListByNickname');
        Route::post('/adm/get-upline-network/{uuid}', 'AdmController@getUplineNetwork');

        //FINANCE
        Route::post('/adm/get-statement-list/{uuid}', 'AdmController@getStatementList');
        Route::post('/adm/order-tracking/{uuid}', 'AdmController@orderTracking');
        Route::post('/adm/order-tracking-item/{uuid}', 'AdmController@orderTrackingItem');
        Route::post('/adm/get-transfer-log/{uuid}', 'AdmController@getTransferLog');

        //REPORTS
        Route::post('/adm/user-account-score-list/{uuid}', 'AdmController@UserAccountScoreList');
        Route::post('/adm/vs-user-account-score-list/{uuid}', 'AdmController@vsUserAccountScoreList');
        Route::post('/adm/get-payment-order-list/{uuid}', 'AdmController@getPaymentOrderList');
        Route::post('/adm/export-payment-order-list/{uuid}', 'AdmController@exportPaymentOrderList');
        Route::get('/adm/get-transfer-nickname/{uuid}', 'AdmController@getTransferUserAccount');

        //USER ACCOUNT
        Route::post('/adm/search-user-account/{uuid}', 'AdmController@searchUserAccount');
        Route::put('/adm/update-nickname/{uuid}', 'AdmController@updateNickname');
    });
});


//LOGIN @JWTAuthenticateController
Route::post('/login', 'JWTAuthenticateController@login');
Route::post('/quotecryptotransaction', 'IndividualRouteController@quotecryptotransaction');
//rota de teste
Route::post('/rotateste', 'IndividualRouteController@rotateste');

//JWTAuthenticate @JWTAuthenticateController
Route::get('/refresh-token', 'JWTAuthenticateController@refreshToken');
Route::get('/refresh-adm-token', 'JWTAuthenticateController@refreshAdmToken');

/*ROTAS INDIVIDUAIS @IndividualRouteController*/
Route::post('/existing-document', 'IndividualRouteController@existingDocument'); //VERIFICA SE O DOCUMENTO ESTA DISPONIVEL
Route::post('/existing-email', 'IndividualRouteController@existingEmail'); //VERIFICA SE O EMAIL ESTA DISPONIVEL
Route::post('/existing-nickname', 'IndividualRouteController@existingNickname'); //VERIFICA SE O NICKNAME ESTA DISPONIVEL
Route::post('/existing-sponsor', 'IndividualRouteController@existingSponsor'); //VERIFICA SE O PATROCINADOR EXISTE
Route::post('/existing-sponsor-id', 'IndividualRouteController@existingSponsorId'); //VERIFICA SE O ID DO PATROCINADOR EXISTE
Route::get('/maintenance-system/{system}', 'IndividualRouteController@maintenanceSystem'); //VERIFICA SE O SISTEMA ESTA EM MANUTENÇÃO
Route::post('/registered-user', 'IndividualRouteController@registeredUser'); //VERIFICA SE O USUARIO ESTA REGISTRADO
Route::get('/awaiting-payment/{id}', 'IndividualRouteController@awaitingPayment'); //VERIFICA SE EXISTE PAGAMENTO PENDENTE
Route::get('/get-product-list/{id}', 'IndividualRouteController@getProductList'); //RETORNA A LISTA DE PRODUTOS DO USUARIO
Route::get('/existing-registration-request/{id}', 'IndividualRouteController@existingRegistrationRequest'); //VERIFICA SE DETERMINADO REGISTRATION REQUEST EXUSTE
Route::get('/existing-user-account-id/{id}', 'IndividualRouteController@existingUserAccountId'); //VERIFICA SE DETERMINADA USER ACCOUNT EXISTE
Route::post('/existing-user', 'IndividualRouteController@existingUser');
Route::post('/support-form-contact', 'IndividualRouteController@sendSupportForm');
Route::get('/get-id-by-nickname/{nickname}', 'UserAccountController@getIdByNickname');

Route::get('/get-preferential-sponsor', 'ConfigController@getPreferentialSponsor');
Route::get('/status-termo/{id}', 'IndividualRouteController@getStatusTermo');

Route::get('/genres', 'GenresController@index');
Route::get('/genres/{id}', 'GenresController@show');

Route::post('/verify-auth-token', 'IndividualRouteController@authTokenVerify');

/**ADM**/
Route::post('/recovery-adm-password', 'RecoveryPasswordController@sendRecoveryAdminPassword');
Route::get('/verify-adm-token/{token}', 'RecoveryPasswordController@verifyAdmToken');
Route::get('/verify-new-user-email/{token}', 'AdmController@verifyNewUserEmail');

Route::group(['middleware' => ['maintenance']], function (){

    //REGISTRATION_REQUESTS @RegistrationRequestController
    Route::post('/registration-requests', 'RegistrationRequestController@store');
    Route::get('/registration-requests/validate/{token}', 'RegistrationRequestController@validateAccount');
    Route::get('/registration-requests/resend/{email}', 'RegistrationRequestController@resendEmail');
    Route::post('/pending-requests', 'RegistrationRequestController@pendingConfirmationRequests');

    //RECOVERY_PASSWORD @RecoveryPasswordController
    Route::post('/recovery-password', 'RecoveryPasswordController@sendRecoveryPassword');
    Route::get('/recovery-password/{token}', 'RecoveryPasswordController@verifyRecoveryPassword');
    Route::post('/verify-token', 'RecoveryPasswordController@verifyToken');
    Route::put('/recovery-password/{id}', 'RecoveryPasswordController@setRecoveryPassword');

    Route::post('/recovery-financial-password', 'RecoveryPasswordController@sendRecoveryFinancialPassword');
    Route::get('/recovery-financial-password/{token}', 'RecoveryPasswordController@verifyRecoveryFinancialPassword');
    Route::put('/recovery-financial-password/{id}', 'RecoveryPasswordController@setRecoveryFinancialPassword');

    //Redirects de validação de token
    Route::get('/user-account-request/accept/{token}', 'UserAccountController@validateOtherAccountRequest');
    Route::get('/new-withdrawl-request/{withdrawalMethod}/{token}', 'WithDrawlRequestController@withDrawlRequest');
    Route::get('/user/user-wallet/{token}', 'UserWalletController@walletRequest');

    //SCHOOL INTEGRATION
    Route::get('/auto-login-school/{token}', 'IndividualRouteController@autologinSchool');

    //UserAccount @UserAccountController
    Route::post('/new-cashback-week', 'UserAccountController@newCashBackWeek');
    Route::get('/get-cashback-week-validate-list/{id}', 'UserAccountController@getCashBackWeekValidateList');
    Route::post('/user-account', 'UserAccountController@store');

    Route::group(['middleware' => ['jwt.auth']], function (){

        //USER_ACCOUNT @UserAccountController
        Route::post('/clear-career-path', 'UserAccountController@clearCareerPath');

        Route::post('/get-network-list', 'UserAccountController@getNetWorkList');
        Route::post('/get-network-list-by-nickname', 'UserAccountController@getNetworkListByNickname');
        Route::post('/get-statement-list', 'UserAccountController@getEstatementList');
        Route::get('/get-cashback-week-list/{id}', 'UserAccountController@getCashBackWeekList');

        Route::post('/nickname-for-external-client', 'UserAccountController@newUserAccountForExternalClient');

        Route::post('/get-binary-chart-list', 'UserAccountController@getBinaryChartList');
        Route::post('/get-last-network-leg', 'UserAccountController@getLastNetworkLeg');
        Route::get('/blocked-cashback-list/{id}', 'UserAccountController@blockedCashBackList');
        Route::get('/user-account-information/{id}', 'UserAccountController@getUserAccountInformation');
        Route::get('/get-order-item-list/{id}', 'UserAccountController@getOrderItemList');
        Route::get('/get-user-account-information-all/{id}', 'UserAccountController@getUserAccountInformationAll');
        Route::get('get-user-account-plan/{id}', 'UserAccountController@getUserAccountPlan');
        Route::post('/user-account/set-profile-image/{id}', 'UserAccountController@setUserAccountProfileImage');
        Route::post('/user-account/aceite-carteira', 'UserAccountController@setUserAccountCarteira');
        Route::get('/user-account/get-profile-image/{id}', 'UserAccountController@getUserAccountProfileImage');
        Route::delete('/user-account/remove-profile-image/{id}', 'UserAccountController@removeUserAccountProfileImage');
        Route::post('/user-account-request', 'UserAccountController@otherAccountRequest');
        Route::get('/get-sponsors-list/{id}', 'UserAccountController@getSponsorsList');
        Route::post('/up-preferential-side', 'UserAccountController@upPreferentialSide');
        Route::get('/get-nickname-by-id/{id}', 'UserAccountController@getNicknameById');
        Route::post('/user-account-score-list/{id}', 'UserAccountController@userAccountScoreList');
        Route::post('/top-five-list/{userid}', 'UserAccountController@topFiveList');
        Route::post('/vs-user-account-score-list/{id}', 'UserAccountController@vsUserAccountScoreList');
        Route::post('/get-user-by-nickname/{uuid}', 'UserAccountController@getUserDataByNickName');
        Route::post('/transfer-user-account/{uuid}', 'UserAccountController@transferUserAccount');

        //USER @UserController
        Route::get('/user', 'UserController@index');
        Route::put('/user/{id}', 'UserController@update');
        Route::put('/user/career-path/{id}', 'UserController@careerPath');
        Route::put('/user/change-password/{id}', 'UserController@changePassword');
        Route::post('/insert-cotacao/{uuid}', 'UserController@insertCotacao');
        Route::get('/user/user-account/{id}', 'UserController@getUserAccount');
        Route::get('/user/user-information/{id}', 'UserController@getUserInformation');
        Route::get('/user/user-bank/{id}', 'UserController@getUserBank');
        Route::post('/user/user-bank', 'UserBankController@store');
        Route::get('/user/get-preferential-bank/{id}', 'UserBankController@getPreferentialBank');
        Route::put('/user/user-bank/set-description/{id}', 'UserBankController@setDescription');
        Route::put('/user/user-bank/set-preferential-bank/{id}', 'UserBankController@setPreferentialBank');
        Route::put('/user/user-bank/change-status/{id}', 'UserBankController@changeStatus');
        Route::get('/user/user-wallet-list/{id}', 'UserController@getUserWallet');
        Route::post('/user/user-wallet', 'UserWalletController@store2');
        Route::get('/user/get-preferential-wallet/{id}', 'UserWalletController@getPreferentialWallet');
        Route::put('/user/user-wallet/set-description/{id}', 'UserWalletController@setDescription');
        Route::put('/user/user-wallet/set-preferential-wallet/{id}', 'UserWalletController@setPreferentialWallet');
        Route::put('/user/user-wallet/change-status/{id}', 'UserWalletController@changeStatus');
        Route::put('/user/set-preferential-user-account/{id}', 'UserController@setPreferentialUserAccount');
        Route::post('/user/set-profile-image/{id}', 'UserController@setUserProfileImage');
        Route::get('/user/get-profile-image/{id}', 'UserController@getUserProfileImage');
        Route::delete('/user/remove-profile-image/{id}', 'UserController@removeUserProfileImage');
        Route::put('/withdrawal-tuddo-pay', 'UserController@withdrawalByToddoPay');

        //PRODUCT @ProductController
        Route::get('/product-list/{userAccountId}', 'ProductController@productList');
        Route::post('/product/set-product-image/{id}', 'ProductController@setProductImage');
        Route::get('/product/get-product-image/{id}', 'ProductController@getProductImage');
        Route::delete('/product/remove-product-image/{id}', 'ProductController@removeProductImage');

        //PRODUCT_PRICE @ProductPriceController
        Route::get('/product-price/{id}', 'ProductPriceController@show');

        //ORDER_ITEM @OrderItemController
        Route::post('/order-item', 'OrderItemController@store');
        Route::post('/del-order-item-open', 'OrderItemController@delOrderItemOpen');
        Route::post('/add-payment-voucher/{id}', 'OrderItemController@addPaymentVoucher');
        Route::post('/crypto-payment/{id}', 'OrderItemController@cryptoPayment');
        Route::delete('/remove-voucher/{id}', 'OrderItemController@removeVoucher');
        Route::get('/clear-payment-method/{id}', 'OrderItemController@clearPaymentMethod');
        Route::post('/get-transfer', 'OrderItemController@getTransfer');
        Route::post('/criptocoin-payment', 'SquadiPayController@store');
        Route::get('/clear-criptocoin-payment/{id}', 'SquadiPayController@clearCryptoOrder');

        //Payment em Crypto
        Route::post('/createbitcoinwallet', 'IndividualRouteController@createaddressbtc');
        Route::get('/createwalletusdt/{order}/{type}', 'IndividualRouteController@createaddressusdt');
        Route::get('/createaddressusdttrc20/{order}/{type}', 'IndividualRouteController@createaddressusdttrc20');
        //Route::get('/internalactive/{order}/{type}', 'IndividualRouteController@internalactive');
        //Route::post('/createbitcoinaddress', 'PaymentBitCoreControler2@createBitcoinAddress');

        //ORDER_TRACKING @OrderTrackingController
        Route::get('/order-tracking/{id}', 'OrderTrackingController@show');

        //ORDER_TRACKING_ITEM @OrderTrackingItemController
        Route::post('/order-tracking-item', 'OrderTrackingItemController@show');

        //WITHDRAWL_METHOD @WithdrawlMethodController
        Route::get('/withdrawl-methods', 'WithdrawlMethodController@index');
        Route::get('/enable-withdrawl-screen/{id}', 'WithdrawlMethodController@enableWithdrawlScreen');
        Route::post('/new-withdrawl-request', 'WithDrawlRequestController@requestWithDrawal');
        Route::post('transfer-account-balance', 'TransferBalanceAccount@transferBalanceAccount');
        Route::get('/remove-all-exports-files/{uuid}', 'WithDrawlRequestController@removeAllExports');

        //BANK @BankController
        Route::get('/bank', 'BankController@index');
        Route::get('/bank/{id}', 'BankController@show');

        //TYPE_BANK_ACCOUNT @TypeBankAccountController
        Route::get('/type-bank-account', 'TypeBankAccountController@index');

        //TYPE_DOCUMENT @TypeDocumentController
        Route::get('/type-document', 'TypeDocumentController@index');

        //User @TypeDocumentController
        Route::get('/type-document', 'TypeDocumentController@index');

        //DigitalPlatform
        Route::get('/digital-platforms', 'DigitalPlatformController@index');

        //BOLETO @BoletoController
        Route::post('/boleto-create', 'BoletoController@create');
        Route::delete('/boleto-cancel/{uuid}', 'BoletoController@cancel');
        Route::post('/boleto/generate-voucher', 'BoletoController@generateVoucher');

        //CryptoCurrency CryptoCurrencyController
        Route::get('/cryptocurrency', 'CryptoCurrencyController@index');
        //Quotation Crypto
        Route::get('/quotation', 'QuotationController@index');
    });
});

/** CALL BACKs **/
Route::post('/boleto-callback-u4c', 'BoletoController@callbackU4C'); //DEIXAR FORA DA AUTENTICAÇÃO JWT
Route::post('/spreadsheet-callback-u4c', 'WithDrawlRequestController@callbackSpreadSheet'); //DEIXAR FORA DA AUTENTICAÇÃO JWT
//Route::post('/payments/notification/squadipay', 'SquadiPayController@callback'); //DEIXAR FORA DA AUTENTICAÇÃO JWT
//Route::post('/force-approve-payment', 'BoletoController@forceApprovePayment'); //SOMENTE DURANTE A FASE DE TESTES

Route::post("/notification-crypto-test", 'IndividualRouteController@txt');
Route::get("/remove-notification-crypto", 'IndividualRouteController@Removetxt');
