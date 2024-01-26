<?php

namespace App\Http\Controllers\VS;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VS\Correios;
use App\Models\VS\Delivery;
use App\Models\VS\Product;
use App\Models\VS\ProductPrice;
use App\Models\VS\Supplier;
use App\Utils\JwtValidation;
use App\Utils\Message;
use App\Utils\TrackPackage;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\Promise\all;

class CorreiosController extends Controller
{

    private $correios;

    public function __construct(Correios $correios)
    {
        $this->correios = $correios;
    }

    public function calculateShippingPrice(Request $request)
    {
        Validator::make($request->all(), [
            'DESTINO' => 'required',
            'PRODUCT_LIST' => 'required'
        ])->validate();
        $result = $this->getProductSpecs($request->PRODUCT_LIST);

        $res = [];
        $shipping_price = 0;

        foreach ($result as $key => $supplier){
           $prdList = $supplier['PRODUCT_LIST'];
           unset($supplier['PRODUCT_LIST']);
           $destino = $request->DESTINO;
           $cepOrigin = (DB::select("SELECT ZIP_CODE FROM VS_SUPPLIER WHERE ID = {$key}"))[0]->ZIP_CODE;
           $total = $supplier['cm3'];
           $peso = $supplier['p'];
           $supplierData = Supplier::find($key);
           $manufacture_days = $supplier['days'];
           unset($supplier['days']);
           $raizCubica = round(pow($total, 1/3), 2);

            $result = [
                'comprimento' =>  0,
                'altura' => 0,
                'largura' => 0,
                'peso' => 0
            ];


            if($raizCubica < 16){
                $result['comprimento'] = 16;
            }else{
                $result['comprimento'] = $raizCubica;
            }
            if($raizCubica < 11){
                $result['largura'] = 11;
            }else{
                $result['largura'] = $raizCubica;
            }
            if($raizCubica < 2){
                $result['altura'] = 2;
            }else{
                $result['altura'] = $raizCubica;
            }
            if($peso < 0.3){
                $result['peso'] = 0.3;
            }else{
                $result['peso'] = $peso;
            }

            if ($total == 0 && $peso == 0) {
                $result['comprimento'] = 0;
                $result['largura'] = 0;
                $result['peso'] = 0;
                $result['altura'] = 0;
            }

            //dd($result);

            if($peso >= 30 && $raizCubica >= 105){
                $result = $this->maxWeightAndRootCubic($result, $peso, $raizCubica, $destino, $manufacture_days, $cepOrigin, $supplierData);
            }elseif($peso >= 30){
                $result = $this->maxWeight($result, $peso, $raizCubica, $destino, $manufacture_days, $cepOrigin, $supplierData);
            }elseif($raizCubica >= 105){
                $result = $this->maxRootCubic($result, $peso, $raizCubica, $destino, $manufacture_days, $cepOrigin, $supplierData);
            }else{
                if($supplierData->IS_DISTRIBUTION_CENTER){
                    $senha = $supplierData->POST_OFFICE_PASSWORD ? $supplierData->POST_OFFICE_PASSWORD : 'n';
                    $codigo = $supplierData->POST_OFFICE_CONTRACT ? $supplierData->POST_OFFICE_CONTRACT : 'n';
                    if (! $supplierData->POST_OFFICE_PASSWORD){
                        $this->correios->setCodigoServico('04510', 'pac');
                        $this->correios->setCodigoServico('04014', 'sedex');
                    }
                }else{
                    $senha = $this->correios->getSenha();
                    $codigo = $this->correios->getCodigo();
                }

                //dd("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");

                if ($supplierData->PREFERENTIAL_TYPE_SHIPPING_ID == 2){
                    $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                    $type = 2;
                }elseif ($supplierData->PREFERENTIAL_TYPE_SHIPPING_ID == 3){
                    $this->correios->setCodigoServico('04782', 'sedex');
                    $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                    $type = 3;
                }else{
                    $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                    $type = 1;
                }

                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $valor = $array['cServico']['Valor'];

                if ((float) $valor == 0){
                    $type =  1;
                    $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                    $xml = simplexml_load_string($response->body());
                    $json = json_encode($xml);
                    $array = json_decode($json,TRUE);
                    $prazo = (int)$array['cServico']['PrazoEntrega'];
                    $valor = $array['cServico']['Valor'];
                }

                if ((float) $valor == 0){
                    $type =  2;
                    $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                    $xml = simplexml_load_string($response->body());
                    $json = json_encode($xml);
                    $array = json_decode($json,TRUE);
                    $prazo = (int)$array['cServico']['PrazoEntrega'];
                    $valor = $array['cServico']['Valor'];
                }
                if ((float) $valor == 0){
                    $type = 3;
                    $this->correios->setCodigoServico('04782', 'sedex');
                    $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                    $xml = simplexml_load_string($response->body());
                    $json = json_encode($xml);
                    $array = json_decode($json,TRUE);
                    $prazo = (int)$array['cServico']['PrazoEntrega'];
                    $valor = $array['cServico']['Valor'];
                }
                $valor = str_replace(',', '.', $valor);
                $now = date('Y-m-d');
                $prazo = $prazo+$manufacture_days;
                $result = [
                    'SHIPPING_PRICE' => (float)$valor,
                    'DT_ESTIMATED_DELIVERY' => (date('Y-m-d', strtotime("+" . $prazo . " days",strtotime($now))))
                    ];
                /*$result = [
                    'SHIPPING_PRICE' => (float)$valor,
                    'DT_ESTIMATED_DELIVERY' => (date('Y-m-d', strtotime("+" . $prazo . " days",strtotime($now)))),
                    'SUPPLIER_ID' => $key,
                    'PRODUCT_LIST' => $prdList
                ];
                array_push($res, $result);*/
            }
            $supplier = DB::select("select ID, FANTASY_NAME FROM VS_SUPPLIER WHERE ID = {$key}");
            $result += [
                'TYPE_SHIPPING_ID' => $type,
                'SUPPLIER_ID' => $supplier[0]->ID,
                'SUPPLIER_NAME' => $supplier[0]->FANTASY_NAME,
                'PRODUCT_LIST' => $prdList
            ];
            $shipping_price += $result['SHIPPING_PRICE'];
            array_push($res, $result);
        }
        //dd($shipping_price);
        $result = ['SHIPPING_PRICE' => $shipping_price, 'ORDER_ITEMS' => $res];

        foreach ($result['ORDER_ITEMS'] as $key =>  $item){
            $supplier = Supplier::find($item['SUPPLIER_ID']);
            $result['ORDER_ITEMS'][$key]['DISTRIBUTION_CENTER_VS_SUPPLIER_ID'] = $supplier->DISTRIBUTION_CENTER_VS_SUPPLIER_ID;
            //$supplier = DB::select("CALL SP_GET_VS_PRODUCT_LIST_BY_FILTER('{".'"'.'VS_SUPPLIER_ID'.'"'.':'.$item['SUPPLIER_ID']."}')");
            $countPrice = 0;
            $countWeight = 0;
            foreach ($item['PRODUCT_LIST'] as $keyProd => $prod){
                $price = DB::select("CALL SP_GET_VS_PRODUCT_LIST_BY_FILTER('{".'"'.'ID'.'"'.':'.$prod['ID']."}')");
                $price = (float) $price[0]->SALE_PRICE_DISCOUNT * $prod['UNITS'];
                $countPrice += $price;

                $weight = Product::find($prod['ID']);
                $weight = (float) $weight->WEIGHT * $prod['UNITS'];
                $countWeight += $weight;
            }
            $result['ORDER_ITEMS'][$key]['PRICE'] = $countPrice;
            $result['ORDER_ITEMS'][$key]['WEIGHT'] = $countWeight;
        }

        if ($request->header('Authorization')){
            $token = (new JwtValidation())->getPayload($request);
            $user = User::find($token->uid);
            if (strlen($user->STATE) == 2){
                $result = Correios::freeShipping($user, $result);
            }else{
                foreach ($result['ORDER_ITEMS'] as $key =>  $item){
                    $result['ORDER_ITEMS'][$key]['FREE_SHIPPING'] = 0;
                }
            }
        }else{
            foreach ($result['ORDER_ITEMS'] as $key =>  $item){
                $result['ORDER_ITEMS'][$key]['FREE_SHIPPING'] = 0;
            }
        }

        return $result;
    }

    public function maxWeightAndRootCubic($result, $peso, $raizCubica, $destino, $manufacture_days, $cepOrigin, $supplierData)
    {
        unset($result);
        $result[0] = [
            'comprimento' => $raizCubica,
            'altura' => $raizCubica,
            'largura' => $raizCubica,
            'peso' => $peso
        ];

        if(($peso/30) > ($raizCubica/60)){
            $result[0]['peso'] = 29;

            $resultPeso = $peso-29;
            $count = 1;
            while ($resultPeso > 0){
                if($resultPeso < 30){
                    $result[$count]['peso'] = $resultPeso;
                    $resultPeso -= $resultPeso;
                }else{
                    $result[$count]['peso'] = 29;
                    $resultPeso -= 29;
                    $count++;
                }
            }
            $resultCount = count($result);
            for ($i=0; $i< $resultCount;$i++) {
                if(($raizCubica * $result[$i]['peso']) / $peso < 16){
                    $result[$i]['comprimento'] = 16;
                }else{
                    $result[$i]['comprimento'] = ($raizCubica * $result[$i]['peso']) / $peso;
                    $result[$i]['comprimento'] = number_format((float)$result[$i]['comprimento'], 2, '.', '');
                }
                if(($raizCubica * $result[$i]['peso']) / $peso < 11){
                    $result[$i]['largura'] = 11;
                }else{
                    $result[$i]['largura'] = ($raizCubica * $result[$i]['peso']) / $peso;
                    $result[$i]['largura'] = number_format((float)$result[$i]['largura'], 2, '.', '');
                }
                if(($raizCubica * $result[$i]['peso']) / $peso < 2){
                    $result[$i]['altura'] = 2;
                }else{
                    $result[$i]['altura'] = ($raizCubica * $result[$i]['peso']) / $peso;
                    $result[$i]['altura'] = number_format((float)$result[$i]['altura'], 2, '.', '');
                }
                if($result[$i]['peso'] < 0.3){
                    $result[$i]['peso'] = 0.3;
                }else{
                    $result[$i]['peso'] = number_format((float)$result[$i]['peso'], 2, '.', '');
                }
            }
        }else{
            $result[0]['comprimento'] = 66  ;
            $result[0]['altura'] = 66;
            $result[0]['largura'] = 66;

            $resultRaiz = $raizCubica-66;
            $count = 1;

            while ($resultRaiz > 0){
                if($resultRaiz < 105){
                    $result[$count]['comprimento'] = $resultRaiz;
                    $result[$count]['altura'] = $resultRaiz;
                    $result[$count]['largura'] = $resultRaiz;
                    $resultRaiz -= $resultRaiz;
                }else{
                    $result[$count]['comprimento'] = 66;
                    $result[$count]['altura'] = 66;
                    $result[$count]['largura'] = 66;
                    $resultRaiz -= 66;
                    $count++;
                }
            }
            $resultCount = count($result);
            for ($i=0; $i< $resultCount;$i++){
                if($result[$i]['comprimento'] > 66){
                    $rest = $result[$i]['comprimento']-66;
                    $result[$i]['comprimento'] = 66;
                    $result[$i]['altura'] = 66;
                    $result[$i]['largura'] = 66;
                    if(($peso*66)/$raizCubica > 0.3){
                        $result[$i]['peso'] = ($peso*66)/$raizCubica;
                        $result[$i]['peso'] = number_format((float)$result[$i]['peso'], 2, '.', '');
                    }else{
                        $result[$i]['peso'] = 0.3;
                    }
                    if($rest > 16) {
                        $result[$i+1]['comprimento'] = $rest;
                        $result[$i+1]['comprimento'] = number_format((float)$result[$i+1]['comprimento'], 1, '.', '');
                    }else{
                        $result[$i+1]['comprimento'] = 16;
                    }
                    if($rest > 11){
                        $result[$i+1]['largura'] = $rest;
                        $result[$i+1]['largura'] = number_format((float)$result[$i+1]['largura'], 1, '.', '');
                    }else{
                        $result[$i+1]['largura'] = 11;
                    }
                    if($rest > 2){
                        $result[$i+1]['altura'] = $rest;
                        $result[$i+1]['altura'] = number_format((float)$result[$i+1]['altura'], 1, '.', '');
                    }else{
                        $result[$i+1]['altura'] = 2;
                    }
                    $resultCount++;
                }else{
                    if(($peso*$result[$i]['comprimento'])/$raizCubica > 0.3){
                        $result[$i]['peso'] = ($peso*$result[$i]['comprimento'])/$raizCubica;
                        $result[$i]['peso'] = number_format((float)$result[$i]['peso'], 2, '.', '');
                    }else{
                        $result[$i]['peso'] = 0.3;
                    }
                }
            }
        }
        $value = 0;
        $prazo = 0;
        $newResultCount = count($result);
        for ($i = 0; $i < $newResultCount  ; $i++){
            if($supplierData->IS_DISTRIBUTION_CENTER){
                $senha = $supplierData->POST_OFFICE_PASSWORD ? $supplierData->POST_OFFICE_PASSWORD : 'n';
                $codigo = $supplierData->POST_OFFICE_CONTRACT ? $supplierData->POST_OFFICE_CONTRACT : 'n';
                if (! $supplierData->POST_OFFICE_PASSWORD){
                    $this->correios->setCodigoServico('04510', 'pac');
                    $this->correios->setCodigoServico('04014', 'sedex');
                }
            }else{
                $senha = $this->correios->getSenha();
                $codigo = $this->correios->getCodigo();
            }

            //dd("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");

            if ($supplierData->PREFERENTIAL_TYPE_SHIPPING_ID == 2){
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $type = 2;
            }elseif ($supplierData->PREFERENTIAL_TYPE_SHIPPING_ID == 3){
                $this->correios->setCodigoServico('04782', 'sedex');
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $type = 3;
            }else{
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $type = 1;
            }

            $xml = simplexml_load_string($response->body());
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            $prazo = (int)$array['cServico']['PrazoEntrega'];
            $prazo = $prazo+$manufacture_days;
            $value += (float)$array['cServico']['Valor'];

            if ((float) $value == 0){
                $type = 1;
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result[$i]['peso']}&nCdFormato=1&nVlComprimento={$result[$i]['comprimento']}&nVlAltura={$result[$i]['altura']}&nVlLargura={$result[$i]['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $value = $array['cServico']['Valor'];
            }

            if ((float) $value == 0){
                $type = 2;
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result[$i]['peso']}&nCdFormato=1&nVlComprimento={$result[$i]['comprimento']}&nVlAltura={$result[$i]['altura']}&nVlLargura={$result[$i]['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $value = $array['cServico']['Valor'];
            }
            if ((float) $value == 0){
                $type = 3;
                $this->correios->setCodigoServico('04782', 'sedex');
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $value = $array['cServico']['Valor'];
            }
            //echo (float)$array['cServico']['Valor']." - "."$value". " - ". "$i <br>";
        }
        $now = date('Y-m-d');
        return $result = ['TYPE_SHIPPING_ID' => $type, 'SHIPPING_PRICE' => $value, 'DT_ESTIMATED_DELIVERY' => (date('Y-m-d', strtotime("+" . $prazo . " days",strtotime($now))))];
    }

    private function maxWeight($result, $peso, $raizCubica, $destino, $manufacture_days, $cepOrigin, $supplierData)
    {
        $rComprimento = $result['comprimento'];
        $rLargura = $result['largura'];
        $rAltura = $result['altura'];
        unset($result);
        $result[0] = [
            'comprimento' => $rComprimento,
            'altura' => $rLargura,
            'largura' => $rAltura,
            'peso' => $peso
        ];
        if($peso >= 30){
            $result[0]['peso'] = 29;

            $resultPeso = $peso-29;
            $count = 1;
            while ($resultPeso > 0){
                if($resultPeso < 30){
                    $result[$count]['peso'] = $resultPeso;
                    $resultPeso -= $resultPeso;
                }else{
                    $result[$count]['peso'] = 29;
                    $resultPeso -= 29;
                    $count++;
                }
            }
        }
        $count = count($result);
        for ($i = 0; $i<$count; $i++){
            if(($raizCubica * $result[$i]['peso']) / $peso < 16){
                $result[$i]['comprimento'] = 16;
            }else{
                $result[$i]['comprimento'] = ($raizCubica * $result[$i]['peso']) / $peso;
                $result[$i]['comprimento'] = number_format((float)$result[$i]['comprimento'], 2, '.', '');
            }
            if(($raizCubica * $result[$i]['peso']) / $peso < 11){
                $result[$i]['largura'] = 11;
            }else{
                $result[$i]['largura'] = ($raizCubica * $result[$i]['peso']) / $peso;
                $result[$i]['largura'] = number_format((float)$result[$i]['largura'], 2, '.', '');
            }
            if(($raizCubica * $result[$i]['peso']) / $peso < 2){
                $result[$i]['altura'] = 2;
            }else{
                $result[$i]['altura'] = ($raizCubica * $result[$i]['peso']) / $peso;
                $result[$i]['altura'] = number_format((float)$result[$i]['altura'], 2, '.', '');
            }
            if($result[$i]['peso'] < 0.3){
                $result[$i]['peso'] = 0.3;
            }else{
                $result[$i]['peso'] = number_format((float)$result[$i]['peso'], 2, '.', '');
            }
        }
        $value = 0;
        $prazo = 0;
        for ($i = 0; $i < $count; $i++){
            if($supplierData->IS_DISTRIBUTION_CENTER){
                $senha = $supplierData->POST_OFFICE_PASSWORD ? $supplierData->POST_OFFICE_PASSWORD : 'n';
                $codigo = $supplierData->POST_OFFICE_CONTRACT ? $supplierData->POST_OFFICE_CONTRACT : 'n';
                if (! $supplierData->POST_OFFICE_PASSWORD){
                    $this->correios->setCodigoServico('04510', 'pac');
                    $this->correios->setCodigoServico('04014', 'sedex');
                }
            }else{
                $senha = $this->correios->getSenha();
                $codigo = $this->correios->getCodigo();
            }

            if ($supplierData->PREFERENTIAL_TYPE_SHIPPING_ID == 2){
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $type = 2;
            }elseif ($supplierData->PREFERENTIAL_TYPE_SHIPPING_ID == 3){
                $this->correios->setCodigoServico('04782', 'sedex');
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $type = 3;
            }else{
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $type = 1;
            }

            $xml = simplexml_load_string($response->body());
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            $prazo = (int)$array['cServico']['PrazoEntrega'];
            $prazo = $prazo+$manufacture_days;
            $value += (float)$array['cServico']['Valor'];

            if ((float) $value == 0){
                $type = 1;
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result[$i]['peso']}&nCdFormato=1&nVlComprimento={$result[$i]['comprimento']}&nVlAltura={$result[$i]['altura']}&nVlLargura={$result[$i]['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $value = $array['cServico']['Valor'];
            }

            if ((float) $value == 0){
                $type = 2;
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result[$i]['peso']}&nCdFormato=1&nVlComprimento={$result[$i]['comprimento']}&nVlAltura={$result[$i]['altura']}&nVlLargura={$result[$i]['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $value = $array['cServico']['Valor'];
            }

            if ((float) $value == 0){
                $type = 3;
                $this->correios->setCodigoServico('04782', 'sedex');
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $value = $array['cServico']['Valor'];
            }
            //echo (float)$array['cServico']['Valor']." - "."$value". " - ". "$i <br>";
        }
        $now = date('Y-m-d');
        return $result = ['TYPE_SHIPPING_ID' => $type, 'SHIPPING_PRICE' => $value, 'DT_ESTIMATED_DELIVERY' => (date('Y-m-d', strtotime("+" . $prazo . " days",strtotime($now))))];
    }

    private function maxRootCubic($result, $peso, $raizCubica, $destino, $manufacture_days, $cepOrigin, $supplierData)
    {
        $rComprimento = $result['comprimento'];
        $rLargura = $result['largura'];
        $rAltura = $result['altura'];
        unset($result);
        $result[0] = [
            'comprimento' => $rComprimento,
            'altura' => $rLargura,
            'largura' => $rAltura,
            'peso' => $peso
        ];
        if($raizCubica >= 105){
            $result[0]['comprimento'] = 66;
            $result[0]['altura'] = 66;
            $result[0]['largura'] = 66;

            $resultRaiz = $raizCubica-66;
            $count = 1;
            while ($resultRaiz > 0){
                if($resultRaiz < 105){
                    $result[$count]['comprimento'] = $resultRaiz;
                    $result[$count]['altura'] = $resultRaiz;
                    $result[$count]['largura'] = $resultRaiz;
                    $resultRaiz -= $resultRaiz;
                }else{
                    $result[$count]['comprimento'] = 66;
                    $result[$count]['altura'] = 66;
                    $result[$count]['largura'] = 66;
                    $resultRaiz -= 66;
                    $count++;
                }
            }
        }
        $count = count($result);
        for ($i = 0; $i<$count; $i++){
            if($result[$i]['comprimento'] > 66){
                $rest = $result[$i]['comprimento']-66;
                $result[$i]['comprimento'] = 66;
                $result[$i]['altura'] = 66;
                $result[$i]['largura'] = 66;
                if(($peso*66)/$raizCubica > 0.3){
                    $result[$i]['peso'] = ($peso*66)/$raizCubica;
                    $result[$i]['peso'] = number_format((float)$result[$i]['peso'], 2, '.', '');
                }else{
                    $result[$i]['peso'] = 0.3;
                }
                if($rest > 16) {
                    $result[$i+1]['comprimento'] = $rest;
                    $result[$i+1]['comprimento'] = number_format((float)$result[$i+1]['comprimento'], 1, '.', '');
                }else{
                    $result[$i+1]['comprimento'] = 16;
                }
                if($rest > 11){
                    $result[$i+1]['largura'] = $rest;
                    $result[$i+1]['largura'] = number_format((float)$result[$i+1]['largura'], 1, '.', '');
                }else{
                    $result[$i+1]['largura'] = 11;
                }
                if($rest > 2){
                    $result[$i+1]['altura'] = $rest;
                    $result[$i+1]['altura'] = number_format((float)$result[$i+1]['altura'], 1, '.', '');
                }else{
                    $result[$i+1]['altura'] = 2;
                }
                $resultCount++;
            }else{
                if(($peso*$result[$i]['comprimento'])/$raizCubica > 0.3){
                    $result[$i]['peso'] = ($peso*$result[$i]['comprimento'])/$raizCubica;
                    $result[$i]['peso'] = number_format((float)$result[$i]['peso'], 2, '.', '');
                }else{
                    $result[$i]['peso'] = 0.3;
                }
            }
        }
        $value = 0;
        $prazo = 0;

        for ($i = 0; $i < $count; $i++){
            if($supplierData->IS_DISTRIBUTION_CENTER){
                $senha = $supplierData->POST_OFFICE_PASSWORD ? $supplierData->POST_OFFICE_PASSWORD : 'n';
                $codigo = $supplierData->POST_OFFICE_CONTRACT ? $supplierData->POST_OFFICE_CONTRACT : 'n';
                if (! $supplierData->POST_OFFICE_PASSWORD){
                    $this->correios->setCodigoServico('04510', 'pac');
                    $this->correios->setCodigoServico('04014', 'sedex');
                }
            }else{
                $senha = $this->correios->getSenha();
                $codigo = $this->correios->getCodigo();
            }
            if ($supplierData->PREFERENTIAL_TYPE_SHIPPING_ID == 2){
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $type = 2;
            }elseif ($supplierData->PREFERENTIAL_TYPE_SHIPPING_ID == 3){
                $this->correios->setCodigoServico('04782', 'sedex');
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $type = 3;
            }else{
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $type = 1;
            }

            $xml = simplexml_load_string($response->body());
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            $prazo = (int)$array['cServico']['PrazoEntrega'];
            $prazo = $prazo+$manufacture_days;
            $value += (float)$array['cServico']['Valor'];

            if ((float) $value == 0){
                $type = 1;
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result[$i]['peso']}&nCdFormato=1&nVlComprimento={$result[$i]['comprimento']}&nVlAltura={$result[$i]['altura']}&nVlLargura={$result[$i]['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('pac')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $value = $array['cServico']['Valor'];
            }

            if ((float) $value == 0){
                $type = 2;
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result[$i]['peso']}&nCdFormato=1&nVlComprimento={$result[$i]['comprimento']}&nVlAltura={$result[$i]['altura']}&nVlLargura={$result[$i]['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $value = $array['cServico']['Valor'];
            }

            if ((float) $value == 0){
                $type = 3;
                $this->correios->setCodigoServico('04782', 'sedex');
                $response = Http::get("http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$codigo}&sDsSenha={$senha}&sCepOrigem={$cepOrigin}&sCepDestino={$destino}&nVlPeso={$result['peso']}&nCdFormato=1&nVlComprimento={$result['comprimento']}&nVlAltura={$result['altura']}&nVlLargura={$result['largura']}&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico={$this->correios->getCodigoServico('sedex')}&nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3");
                $xml = simplexml_load_string($response->body());
                $json = json_encode($xml);
                $array = json_decode($json,TRUE);
                $prazo = (int)$array['cServico']['PrazoEntrega'];
                $value = $array['cServico']['Valor'];
            }
            //echo (float)$array['cServico']['Valor']." - "."$value". " - ". "$i". " - ". ".".$result[$i]['peso']."<br>";
        }
        //dd($value);
        $now = date('Y-m-d');
        return $result = ['TYPE_SHIPPING_ID' => $type, 'SHIPPING_PRICE' => $value, 'DT_ESTIMATED_DELIVERY' => (date('Y-m-d', strtotime("+" . $prazo . " days",strtotime($now))))];
    }

    private function getProductSpecs($productList)
    {
        $cm3 = 0;
        $peso = 0;
        $manufacture_days = 0;
        $zipCodes = [];
        $prdList = [];
        foreach ($productList as $key => $product){
            $prod = Product::find($product['ID']);
            $prodPrice = ProductPrice::where('VS_PRODUCT_ID', $prod->ID)->get();
            $prodPrice = $prodPrice[count($prodPrice) - 1];
            $price = $prodPrice ? $prodPrice->FREE_SHIPPING : 0;
            $zipCodeResult = $prod->findSupplierCode();
            if(!array_key_exists($zipCodeResult['id'], $zipCodes)){
                $cm3 = 0;
                $peso = 0;
                $manufacture_days = 0;
            }

            if ($price == 1) {
                if(!array_key_exists($zipCodeResult['id'], $zipCodes)) {
                    $zipCodes[$zipCodeResult['id']] = [
                        'cm3' => 0,
                        'p' => 0,
                        'days' => 0
                    ];
                }
            }else{
                $zipCodes[$zipCodeResult['id']] = [
                    'cm3' => $cm3 += ($prod->HEIGHT*$prod->WIDTH*$prod->LENGTH)*$product['UNITS'],
                    'p' => $peso += $prod->WEIGHT*$product['UNITS'],
                ];

                if($prod->DAYS_MANUFACTURE > $manufacture_days){
                    $manufacture_days = $prod->DAYS_MANUFACTURE;
                }
                $zipCodes[$zipCodeResult['id']] += [
                    'days' => $manufacture_days
                ];
            }

            if(!array_key_exists($zipCodeResult['id'], $prdList)) $prdList += [$zipCodeResult['id'] => []];
            array_push($prdList[$zipCodeResult['id']], $product);
        }

        foreach ($zipCodes as $key => $zip){
            $zipCodes[$key] += ['PRODUCT_LIST' => $prdList[$key]];
        }

        return $zipCodes;
    }

    function trackPackage($trackingCode)
    {
        $response = new TrackPackage();
        $isValid = Delivery::where('TRACKING_CODE', $trackingCode)->first();

        if (!$isValid) return (new Message())->defaultMessage(17, 404);
        $package = $response->trackPackage($trackingCode);
        if($package){
            return (new Message())->defaultMessage(1, 200, $package);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
