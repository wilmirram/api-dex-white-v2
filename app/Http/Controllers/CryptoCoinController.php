<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\VS\Order;
use App\Utils\CryptoCoins;
use App\Utils\FileHandler;
use App\Utils\Message;
use CURLFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CryptoCoinController extends Controller
{
    private $crypto;

    public function __construct(CryptoCoins $cryptoCoins)
    {
        $this->crypto = $cryptoCoins;
    }

    public function importCsv(Request $request)
    {
        Validator::make($request->all(), [
            'csv' => 'required'
        ])->validate();

        $csv = Storage::disk('public')->putFileAs('temp_crypt_csv', $request->csv, 'file.csv');
        $response = $this->crypto->importCsv($csv);

        if(! $response){
            return (new Message())->defaultMessage(17, 400);
        }

        return (new Message())->defaultMessage(1, 200, "Importado com sucesso");

    }

    public function addWallet(Request $request)
    {
        Validator::make($request->all(), [
            'idPedido' => 'required',
            'valor' => 'required',
            'idCliente' => 'required',
            'exchange_id' => 'required'
        ])->validate();

        $order = OrderItem::find($request->idPedido);

        if (! $order){
            return (new Message())->defaultMessage(17, 400);
        }

        $response = $this->crypto->addWallet($request->idPedido, $request->valor, $request->idCliente, $request->exchange_id);

        if(! $response){
            return (new Message())->defaultMessage(17, 400);
        }

        return (new Message())->defaultMessage(1, 200, $response['data']);
    }

    public function queryWallets(Request $request)
    {
        $wallets = $this->crypto->query($request->has('enderecoCarteira') ? $request->enderecoCarteira : null, $request->has('data') ? $request->data : null, $request->has('pedido') ? $request->pedido : null);

        if (! $wallets){
            return (new Message())->defaultMessage(17, 400);
        }

        return (new Message())->defaultMessage(1, 200, $wallets['data']);
    }

    public function listWallets()
    {
        $wallets = $this->crypto->listWallets();

        return (new Message())->defaultMessage(1, 200, $wallets['data']['registros']);
    }

    public function addReceipt(Request $request)
    {
        Validator::make($request->all(), [
            'receipt' => 'required',
            'wallet' => 'required'
        ])->validate();
        $receipt = $request->receipt;
        $wallet = $request->wallet;

        $receipt = (new FileHandler())->write($receipt, 'temp_crypt_receipt/', $wallet);

        $ext = explode('.', $receipt);
        $ext = $ext[count($ext) - 1];

        $result = $this->crypto->addReceipt($wallet, $ext);

        if (! $result){
            return (new Message())->defaultMessage(17, 400);
        }
        return (new Message())->defaultMessage(1, 200, $result['data']);
    }
}
