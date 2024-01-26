<?php

use App\Utils\FileHandler;
use App\Utils\Message;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::post('/pay-mercadopago','MercadoPagoController@index');

Route::post('/ipn-mercadopago', 'MercadoPagoController@ipn');

Route::get('/test-mercadopago' , 'MercadoPagoController@index');

Route::get('/paypal/{id}', 'PayPalController@create');

Route::get('/market', function (){
    if(!session()->has('auth')){
        return redirect()->route('doc.login');
    }
    return view('market');
})->name('doc.market');

Route::get('/doc', function (){
    if(!session()->has('auth')){
        return redirect()->route('doc.login');
    }
   return view('welcome');
})->name('office.doc');

Route::get('/adm', function (){
    if(!session()->has('auth')){
        return redirect()->route('doc.login');
    }
   return view('adm');
})->name('adm.doc');

Route::get('/store', function (){
    if(!session()->has('auth')){
        return redirect()->route('doc.login');
    }
    return view('vs');
})->name('vs.doc');

Route::get('/store/product-control', function (){
    if(!session()->has('auth')){
        return redirect()->route('doc.login');
    }
    return view('product-control');
})->name('vs.product-control');

Route::post('/store/product-control', function (\Illuminate\Http\Request $request){
    if(!session()->has('auth')){
        return redirect()->route('doc.login');
    }
    Validator::make($request->all(), [
        'PRODUCT_IMAGE' => 'required',
        'PRODUCT_ID' => 'required'
    ])->validate();

    $product = \App\Models\VS\Product::find($request->PRODUCT_ID);
    if ($product) {
        $size = 10;
        $seed = time();
        $rand = substr(sha1($seed), 40 - min($size,40));
        $file = (new FileHandler())->writeFile($request->PRODUCT_IMAGE, 'product', $request->PRODUCT_ID, $rand);
        return redirect()->back();
    }else {
        return redirect()->back();
    }
})->name('vs.save-product-image');

Route::post('/store/search-product', function (\Illuminate\Http\Request $request){
    if(!session()->has('auth')){
        return redirect()->route('doc.login');
    }

    Validator::make($request->all(), [
        'PRODUCT_ID' => 'required'
    ])->validate();

    $product = \App\Models\VS\Product::find($request->PRODUCT_ID);
    if ($product) {
        $files = Storage::disk('public')->files("products/{$product->ID}");
        $images = array();
        foreach ($files as $key => $value){
            $file = explode('products/', $value);
            $name = explode('/', $file[1]);
            $prodName = explode('.', $name[1]);
            $images[$prodName[0]] =  (new FileHandler())->getFile($value);
        }
        return view('vs-product', compact('product', 'images'));
    }else {
        return redirect()->back();
    }
})->name('vs.search-product');

Route::post('/store/remove-image', function (\Illuminate\Http\Request $request){
    if(Storage::disk('public')->exists("products/{$request->id}/$request->filename.jpeg")) {
        $filename = $request->filename . ".jpeg";
    }elseif (Storage::disk('public')->exists("products/{$request->id}/$request->filename.png")) {
        $filename = $request->filename . ".png";
    }elseif(Storage::disk('public')->exists("products/{$request->id}/$request->filename.jpg")) {
        $filename = $request->filename . ".jpg";
    }elseif(Storage::disk('public')->exists("products/{$request->id}/$request->filename.pdf")) {
        $filename = $request->filename . ".pdf";
    }elseif (Storage::disk('public')->exists("products/{$request->id}/$request->filename.webp")){
        $filename = $request->filename . ".webp";
    }else{
        return redirect()->back();
    }
    Storage::disk('public')->delete("products/{$request->id}/$filename");
    return redirect()->back();
})->name('vs.remove-image');

Route::get('/', function (){
    return view('login');
})->name('doc.login');

Route::post('/login', function (\Illuminate\Http\Request $request){
    if($request->password == '120905'){
        session()->put('auth', true);
        return redirect()->route('office.doc');
    }else{
        return redirect()->back();
    }
})->name('doc.auth');

Route::get('/logout', function (){
    if (session()->has('auth')){
        session()->remove('auth');
    }
    return redirect()->route('doc.login');
})->name('doc.logout');

Route::get('credit-card/{id}', function ($id){
    if (session()->has('auth')){
        session()->remove('auth');
    }
    $order = \App\Models\VS\Order::find($id);
    $price = $order->NET_PRICE;
    $publicKey = \App\Utils\CreditCard::public_key;
    return view('creditcard', compact('id', 'price', 'publicKey'));
});

Route::get('teste', function (){

});

Route::get('/test', 'TestController@show');
