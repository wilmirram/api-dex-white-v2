<?php


namespace App\Utils;


class SOAP
{
    private $address = "https://apphom.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl";
    private $config = array(
                            "trace" => 1,
                            "exception" => 0,
                            "cache_wsdl" => WSDL_CACHE_MEMORY
                           );
    private $client = null;
    private $usuario = "sigep";
    private $senha = "n5f9t8";
    private $cdAdm = "17000190";
    private $contrato = "9992157880";
    private $cartao = "0067599079";

    public function __construct()
    {
        $client = new \SoapClient($this->address, $this->config);
        $this->client = $client;
    }

    public function consultaCEP($cep)
    {
        $cep  = $this->client->consultaCEP(['cep' => $cep]);
        return $cep;
    }

    public function verificaDisponibilidadeServico($cepOrigem, $cepDestino)
    {
        $disponibilidade  = $this->client->verificaDisponibilidadeServico([
                                                                            'codAdministrativo' => $this->cdAdm,
                                                                            'numeroServico' => '40215',
                                                                            'cepOrigem' => $cepOrigem,
                                                                            'cepDestino' => $cepDestino,
                                                                            'usuario' => $this->usuario,
                                                                            'senha' => $this->senha,
                                                                          ]);
        if(strlen($disponibilidade->return) <= 2){
            return true;
        }else{
            return false;
        }
    }

    public function getStatusCartaoPostagem()
    {
        $status  = $this->client->getStatusCartaoPostagem([
                                                        'numeroCartaoPostagem' => $this->cartao,
                                                        'usuario' => $this->usuario,
                                                        'senha' => $this->senha
                                                      ]);
        if($status->return == 'Normal'){
            return true;
        }else{
            return false;
        }
    }
}
