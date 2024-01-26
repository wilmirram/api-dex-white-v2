<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/35505cabf9.js" crossorigin="anonymous"></script>
    <style>
        body{
            background-color: #f8f9fa;
        }
        .bdg{
            font-size: 18px;
        }
        .ac-bg-success{
            background-color: #00FF7F;
        }

        .ac-bg-warning{
            background-color: #F0E68C;
        }
        .ac-bg-info{
            background-color: #7FFFD4;
        }
        .ac-bg-danger{
            background-color: #FF6347;
        }
        .ac-title{
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            color: black;
        }
        .ac-description{
            color: black;
            font-size: 14px;
            font-weight: bold;
        }
        .buttom-hover:hover{
            text-decoration: none;
        }

        .t-color{
            color: #dc3545;
        }
    </style>
</head>
<body>
<div class="container mb-5">

    <nav class="navbar sticky-top navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand scroll" href="#home"><span class="t-color">NÃO SEI O NOME - API</span></a>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
        </div>
        <div class="float-right">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown  font-weight-bolder ml-4" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="t-color">Seções</span> <i class="t-color fas fa-map"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="scroll dropdown-item" href="#login">LOGIN</a>
                        <a class="scroll dropdown-item" href="#registration-requests">REGISTRATION REQUEST</a>
                        <a class="scroll dropdown-item" href="#recovery-password">RECOVERY PASSOWORD</a>
                        <a class="scroll dropdown-item" href="#individual-routes">ROTAS INDIVIDUAIS</a>
                        <a class="scroll dropdown-item" href="#user-account">USER ACCOUNT</a>
                        <a class="scroll dropdown-item" href="#user">USER</a>
                        <a class="scroll dropdown-item" href="#product">PRODUTO</a>
                        <a class="scroll dropdown-item" href="#ORDER">ORDEM</a>
                        <a class="scroll dropdown-item" href="#pagamento">PAGAMENTO</a>
                        <a class="scroll dropdown-item" href="#SAQUE">SAQUE</a>
                        <a class="scroll dropdown-item" href="#bonus">BONUS</a>
                        <a class="scroll dropdown-item" href="#cashback">CASHBACK</a>
                        <a class="scroll dropdown-item" href="#PAISES">PAISES</a>
                        <a class="scroll dropdown-item" href="#BANK">BANCO</a>
                        <a class="scroll dropdown-item" href="#WALLET">CARTEIRA</a>
                        <a class="scroll dropdown-item" href="#cripto">CRIPTOMOEDAS</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="jumbotron jumbotron-fluid mt-3 " id="home">
        <div class="container text-center">
            <h1 class="display-4 t-color">NÃO SEI O NOME - API</h1>
            <p class="lead mt-4">Documentação para consumo da API <i class="far fa-file-alt"></i></p>
            <a href="{{route('adm.doc')}}" class="btn btn-outline-danger float-left">Rotas ADM</a>
            <a href="{{route('vs.doc')}}" class="btn btn-outline-danger ml-4 float-left">Rotas STORE</a>
            @if(session()->has('auth'))
                <a href="{{route('doc.market')}}" class="btn btn-outline-danger float-right">Market</a>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mt-4">
            <h2 class="mb-4">Informações importantes: </h2>
            <ul class="list-group">
                <li class="list-group-item"> As rotas listadas abaixo são referentes aos testes de integração entre Front e Back End.</li>
                <li class="list-group-item">Estão listados em cada rota quais parâmetros  e campos que devem ser enviados.</li>
                <li class="list-group-item">Qualquer erro ou bug deve ser reportado a Ramires para que sejam posteriormente corrigidos.</li>
                <li class="list-group-item">Os campos devem ser enviados da mesma maneira que estão descritos na tabela abaixo.</li>
                <li class="list-group-item">Todo o historico de atualização das rotas ficará listado nessa pagina.</li>
                <li class="list-group-item">As rotas que possuem a chave são protegidas por autenticação de token JWT.</li>
            </ul>
        </div>
        <!--
        <div class="col-md-6 mt-4">
            <h2 class="mb-4">Histórico de atualizações: </h2>
            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2107">
                21/07/2020
            </button>
            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2207">
                22/07/2020
            </button>
            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2307">
                23/07/2020
            </button>
            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2407">
                24/07/2020
            </button>
            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2707">
                27/07/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2807">
                28/07/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2907">
                29/07/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m3007">
                30/07/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m0308">
                03/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m0408">
                04/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m0508">
                05/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m0608">
                06/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m1008">
                10/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m1108">
                11/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m1308">
                13/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m1408">
                14/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m1808">
                18/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2108">
                21/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2408">
                24/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2708">
                27/08/2020
            </button>

            <button type="button" class="btn btn-outline-danger mt-2" data-toggle="modal" data-target="#m2808">
                28/08/2020
            </button>

        </div>
        -->
    </div>
    <!-- MODAIS -->

    <!-- 28/08/2020 -->
    <div class="modal fade" id="m2808" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">28/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionado rotas para controle das carteiras
                    </p>
                    <p>
                        Adicionada rotas para controle das contas bancarias
                    </p>
                    <p>
                        Adicionada a rota que verifica se o usuario esta cadastrado
                    </p>
                    <hr>
                    <p>/user/user-bank</p>
                    <p>/user/user-bank/set-description/{id}</p>
                    <p>/user/user-bank/set-preferential-bank/{id}</p>
                    <p>/user/get-preferential-bank/{id}</p>
                    <p>/user/user-bank/change-status/{id}</p>
                    <br>
                    <p>/user/user-wallet</p>
                    <p>/user/user-wallet/set-description/{id}</p>
                    <p>/user/user-wallet/set-preferential-wallet/{id}</p>
                    <p>/user/get-preferential-wallet/{id}</p>
                    <p>/user/user-wallet/change-status/{id}</p>
                    <br>
                    <p>/existing-user</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 27/08/2020 -->
    <div class="modal fade" id="m2708" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">27/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionado o envio de emails para hotmail (Emails da Microsoft)
                    </p>
                    <p>
                        Adicionada a rota para relatorios de Extrato (ADM)
                    </p>
                    <p>
                        Adicionada a pagina para <a href="{{route('adm.doc')}}">rotas adminstrativas</a>
                    </p>
                    <hr>
                </div>
            </div>
        </div>
    </div>

    <!-- 24/08/2020 -->
    <div class="modal fade" id="m2408" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">24/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Corrigido alguns problemas referentes ao acesso das rotas protegidas pela validação de segurança
                    </p>
                    <p>
                        Feito o refatoramento em algumas funções para facilitar a manutenabilidade
                    </p>
                    <p>
                        Adicionada a rota para relatorios de saque
                    </p>
                    <p>
                        Adicionada a rota para buscar o nickname pelo ID
                    </p>
                    <p>
                        Feita algumas alterções no cadastro de novas User Accounts permitindo o cadastro interno de terceiros
                    </p>
                    <hr>
                    <p>/search-withdrawal-request/{uuid}</p>
                    <p>/get-nickname-by-id/{id}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 21/08/2020 -->
    <div class="modal fade" id="m2108" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">21/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Unificado as rotas:
                    </p>
                    <p>
                        <strong>/user-account-information/userAccountId</strong>
                    </p>
                    <p>
                        <strong>/user/user-account/userAccountId</strong>
                    </p>
                    <p>
                        <strong>/get-cashback-week-validate-list/userAccountId</strong>
                    </p>
                    <p>
                        Em uma unica rota:
                    </p>
                    <p>
                        <strong>/get-user-account-information-all/{id}</strong>
                    </p>
                    <p>
                        Adicionado a rota /api/registration-requests a opção de não enviar a senha. Caso Isso acontecça será gerado uma senha automatica
                    </p>
                    <p>
                        Implementado o metodo de segurança que impede que um usuario acesse dados de outro usuario que não pertença a ele.
                        (Valida se o usuario que gerou o token é o mesmo da rota acessada)
                    </p>
                    <hr>
                    <p>/get-user-account-information-all/{id}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 18/08/2020 -->
    <div class="modal fade" id="m1808" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">18/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionado o upload, remoção e recuperação da foto dos produtos
                    </p>
                    <hr>
                    <p>/product/set-prodcut-image/{id}</p>
                    <p>/product/get-prodcut-image/{id}</p>
                    <p>/product/remove-prodcut-image/{id}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 14/08/2020 -->
    <div class="modal fade" id="m1408" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">14/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionado o upload, remoção e recuperação da foto de perfil do USER
                    </p>
                    <p>
                        Adicionado o upload, remoção e recuperação da foto de perfil da USER ACCOUNT
                    </p>
                    <hr>
                    <p>/user/set-profile-image/{id}</p>
                    <p>/user/get-profile-image/{id}</p>
                    <p>/user/remove-profile-image/{id}</p>
                    <p>/user-account/set-profile-image/{id}</p>
                    <p>/user-account/get-profile-image/{id}</p>
                    <p>/user-account/remove-profile-image/{id}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 13/08/2020 -->
    <div class="modal fade" id="m1308" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">13/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionado o upload, remoção e recuperação do comprovante de pagamento (VOUCHER)
                    </p>
                    <hr>
                    <p>
                        /remove-voucher/{id}
                    </p>
                    <p>
                        /get-voucher/{id}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 11/08/2020 -->
    <div class="modal fade" id="m1108" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">11/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionado o link completo de cada rota para facilitar o uso da documentação
                    </p>
                    <p>
                       Adicionada nova rota realizar login (Fase de testes)
                    </p>

                    <hr>
                    <p>
                        /login-test
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 10/08/2020 -->
    <div class="modal fade" id="m1008" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">10/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Implementado o novo layout da documentação, com navegação entre os grupos de rotas
                    </p>
                    <p>
                        Implementado a nova logica de segurança do token, com criptografica interna dos dados e assinatura do token com chave privada
                    </p>
                    <p>
                        Feita a alteração na chave de segurança do JWT para uma nova e mais segura
                    </p>
                    <p>
                        Padronizado a logica de criptografica e descriptografia dos tokens, impedindo a alteração fora da API
                    </p>
                    <p>
                        Implantado a nova rota para exibir os preços dos produtos
                    </p>
                    <p>
                        Implantado a nova rota para exibir a carteira do usuario
                    </p>
                    <p>
                        Implantado a rota que retorna o id do user account usando como parametro o nickname
                    </p>
                    <hr>
                    <p>
                        /product-price/{id}
                    </p>
                    <p>
                        /user/user-wallet/{id}
                    </p>
                    <p>
                        /get-id-by-nickname/{nickname}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 21/07/2020 -->
    <div class="modal fade" id="m2107" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">21/07/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Inserida as primeiras 32 rotas para serem integradas ao front end;
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 22/07/2020 -->
    <div class="modal fade" id="m2207" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">22/07/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionadas 2 novas rotas para serem integradas ao front end;
                    </p>
                    <p>
                        Adicionado a Flag para direcionar o usuario apos o login;
                    </p>
                    <p>
                        Feita alteração no Registration Request para receber o nome;
                    </p>
                    <p>
                        /order-tracking/{id}
                    </p>
                    <p>
                        /order-tracking-item
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 23/07/2020 -->
    <div class="modal fade" id="m2307" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">23/07/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionadas 2 novas rotas para serem integradas ao front end;
                    </p>
                    <p>
                        /add-payment-voucher/{id}
                    </p>
                    <p>
                        /user/set-preferential-user-account/{id}
                    </p>
                    <p>
                        Feita alteração no Registration Request adicionando o campo SYSTEM como DEFAULT e alterando o UUID pelo Nickname;
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 24/07/2020 -->
    <div class="modal fade" id="m2407" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">24/07/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Feita a alteração na rota /existing-nickname
                    </p>
                    <p>
                        Agora não é mais necessario enviar o Registration Request ID como parametro
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 27/07/2020 -->
    <div class="modal fade" id="m2707" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">27/07/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionada nova rota para verificar os extratos;
                    </p>
                    <p>
                        Adicionado a rota para verificar as semanas pendentes de cashback
                    </p>
                    <p>
                        Adicionado a rota para mostrar o historico de validações de cashback do usuario
                    </p>
                    <p>
                        Adicionado a rota para inserir (validar) o cashback da semana
                    </p>
                    <p>
                        Adicionado a rota mostrar o grafico binario no dashboard
                    </p>
                    <p>
                        Criada a rota para mostrar os usuarios cadastrados abaixo de determinada perna do usuario
                    </p>
                    <hr>
                    <p>
                        /get-statement-id
                    </p>
                    <p>
                        /get-cashback-week-validate-list/{id}
                    </p>
                    <p>
                        /get-cashback-week-list/{id}
                    </p>
                    <p>
                        /new-cashback-week
                    </p>
                    <p>
                        /get-binary-chart-list
                    </p>
                    <p>
                        /get-last-network-leg
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 28/07/2020 -->
    <div class="modal fade" id="m2807" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">28/07/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionada nova rota para listar o cashback bloqueado;
                    </p>
                    <hr>
                    <p>
                        /performance-diary/{id}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 29/07/2020 -->
    <div class="modal fade" id="m2907" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">29/07/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionada a rota para exibir os metodos de pagamento
                    </p>
                    <p>
                        Adicionada a rota para exibir os metodos de recebimento
                    </p>
                    <p>
                        Adicionada a rota para verificar se a tela de pagamento pode ou não ser exibida
                    </p>
                    <hr>
                    <hr>
                    <p>
                        /payment-methods
                    </p>
                    <p>
                        /withdrawl-methods
                    </p>
                    <p>
                        /enable-withdrawl-screen/{id}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 30/07/2020 -->
    <div class="modal fade" id="m3007" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">30/07/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionada a rota para exibir os dados da user account no dashboard
                    </p>
                    <hr>
                    <p>
                        /user-account-information/{id}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 03/08/2020 -->
    <div class="modal fade" id="m0308" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">03/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionada a rota para verificar se o token de recuperação de senha é valido
                    </p>
                    <p>
                        Adicionada a rota para efetuar a solicitação de saque
                    </p>
                    <hr>
                    <p>
                        /verify-token
                    </p>
                    <p>
                        /new-withdrawl-request
                    </p>

                </div>
            </div>
        </div>
    </div>

    <!-- 04/08/2020 -->
    <div class="modal fade" id="m0408" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">04/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionada a rota para inicio dos testes de geração de planilhas
                    </p>
                    <hr>
                    <p>
                        /users/export
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 05/08/2020 -->
    <div class="modal fade" id="m0508" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">05/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionada a rota para inserção dos pontos binarios diarios
                    </p>
                    <p>
                        Adicionada a rota para inserção do cash back diario
                    </p>
                    <hr>
                    <p>
                        /daily-bonus-score
                    </p>
                    <p>
                        /daily-cashback
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 06/08/2020 -->
    <div class="modal fade" id="m0608" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">06/08/2020</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Adicionada a rota exibição dos dados do usuario (Tela te atualização dos dados - PERFIL)
                    </p>
                    <p>
                        Correção na rota de atualização dos dados
                    </p>
                    <p>
                        Adicionada a rota para listagem dos paises
                    </p>
                    <hr>
                    <p>
                        /user/{id}
                    </p>
                    <p>
                        /country
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAIS -->

    <hr>

    <span class="mt-4"><strong>Link para a API: </strong><a href="{{env('BACK_URL')}}">{{env('BACK_URL')}}</a></span>

    <h2 class="mb-4 mt-4">Routes:</h2>

    <hr>
    <div CLASS="mt-4" id="login">
        <h4>LOGIN  <span class="badge badge-dark float-right mr-3">2</span></h4>
        <div class="mt-4" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/login</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Valida o EMAIL e SENHA do Usuário e retorna um token valido</span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse1" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/login</p>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Campos: </h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">EMAIL</li>
                                    <li class="list-group-item">PASSWORD</li>
                                    <li class="list-group-item">P_IP</li>
                                    <li class="list-group-item">P_SYSTEM_ID</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4>Retorno: </h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Token</li>
                                    <li class="list-group-item">Verified_Data_User (0 = false & 1 = True)</li>
                                    <li class="list-group-item">User</li>
                                    <li class="list-group-item">UserAccounts</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse131" aria-expanded="true" aria-controls="collapse131">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/refresh-token</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Gera um novo token</span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse131" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/refresh-token</p>
                        <div class="row">
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="registration-requests">
        <h4 class="mb-3 mt-2">REGISTRATION REQUEST <span class="badge badge-dark float-right mr-3">3</span></h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/registration-requests</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cria um novo registration request e envia um email para validar os dados do usuário</span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse2" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/registration-requests</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_NAME</li>
                            <li class="list-group-item">P_EMAIL</li>
                            <li class="list-group-item">P_SPONSOR_NICKNAME</li>
                            <li class="list-group-item">P_NICKNAME</li>
                            <li class="list-group-item">P_PASSWORD</li>
                            <li class="list-group-item">P_TYPE_DOCUMENT_ID</li>
                            <li class="list-group-item">P_DOCUMENT</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse2015" aria-expanded="true" aria-controls="collapsecollapse2015">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/pending-requests</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os Registration Requests pendentes dos usuarios no qual sou patrocinador</span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse2015" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/pending-requests</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">SPONSOR_ID (UserAccountId do usuario logado)</li>
                            <li class="list-group-item">VERIFIED_EMAIL (Fixo: 0)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/registration-requests/validate/{token}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Valida o TOKEN e caso esteja valido, cria a USER e a USER ACCOUNT
                    </span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse3" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/registration-requests/validate/{token}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">token</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/registration-requests/resend/{email}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Reenvia o email para validar os dados do usuário
                    </span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse4" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/registration-requests/resend/{email}</p>

                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">email</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="recovery-password">
        <h4 class="mb-3 mt-2">RECOVERY PASSWORD <span class="badge badge-dark float-right mr-3">4</span></h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse5" aria-expanded="true" aria-controls="collapse5">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/recovery-password</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Envia um email para o usuário recuperar sua senha
                    </span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse5" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/recovery-password</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">EMAIL</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse6" aria-expanded="true" aria-controls="collapse6">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/recovery-password/{token}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Valida o Token para recuperação de senha
                    </span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse6" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/recovery-password/{token}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Token</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-info" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse7" aria-expanded="true" aria-controls="collapse7">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/recovery-password/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua a atualização de senha do usuário
                    </span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse7" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/recovery-password/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">PASSWORD</li>
                            <li class="list-group-item">EMAIL</li>
                            <li class="list-group-item">ID (User)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse48" aria-expanded="true" aria-controls="collapse48">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/verify-token	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Verifica se o token é valido
                    </span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse48" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/verify-token</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">token</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-info" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse7004" aria-expanded="true" aria-controls="collapse7004">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/recovery-financial-password/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua a atualização de senha financeira do usuário
                    </span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse7004" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/recovery-financial-password/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">PASSWORD</li>
                            <li class="list-group-item">EMAIL</li>
                            <li class="list-group-item">token</li>
                            <li class="list-group-item">ID (User)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse7005" aria-expanded="true" aria-controls="collapse7005">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/recovery-financial-password	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Envia o email para recuperar a senha financeira do usuario
                    </span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse7005" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/recovery-financial-password</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">EMAIL</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <HR>
    <div id="user-account">
        <h4 class="mb-3 mt-2">USER ACCOUNT <span class="badge badge-dark float-right mr-3">18</span></h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse73" aria-expanded="true" aria-controls="collapse73">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-user-account-information-all/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Todos os dados para o dashboard com exeção do grafico binario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse73" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-user-account-information-all/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse733741" aria-expanded="true" aria-controls="collapse733741">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-preferential-sponsor	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o patrocinador preferencial
                    </span>
                        </button>
                        <!-- <span class="float-right mt-2"> <i class="fas fa-key"></i></span> -->
                    </h5>
                </div>

                <div id="collapse733741" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-preferential-sponsor</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse8" aria-expanded="true" aria-controls="collapse8">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/user-account	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cria um novo User Account
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse8" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user-account</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_EMAIL</li>
                            <li class="list-group-item">P_PASSWORD</li>
                            <li class="list-group-item">P_SPONSOR_ID</li>
                            <li class="list-group-item">P_SIDE</li>
                            <li class="list-group-item">P_NICKNAME</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse100" aria-expanded="true" aria-controls="collapse100">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/user-account-request	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Solicita a criação de uma nova user account para terceiros
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse100" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user-account-request</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_EMAIL</li>
                            <li class="list-group-item">P_PASSWORD</li>
                            <li class="list-group-item">P_OWNER_EMAIL</li>
                            <li class="list-group-item">P_SPONSOR_ID</li>
                            <li class="list-group-item">P_SIDE</li>
                            <li class="list-group-item">P_NICKNAME</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse10" aria-expanded="true" aria-controls="collapse10">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-sponsors-list/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista de usuários nos quais sou patrocinador
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse10" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-sponsors-list/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">ID (UserAccount)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse11" aria-expanded="true" aria-controls="collapse11">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/up-preferential-side	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Altera o lado prefencial do usuário
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse11" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/up-preferential-side</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                            <li class="list-group-item">P_SIDE</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse51" aria-expanded="true" aria-controls="collapse51">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-id-by-nickname/{nickname}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o ID da User Account
                    </span>
                        </button>
                        <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                    </h5>
                </div>

                <div id="collapse51" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-id-by-nickname/{nickname}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            nickname
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse9" aria-expanded="true" aria-controls="collapse9">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/get-network-list	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a Arvore do usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse9" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-network-list</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                            <li class="list-group-item">P_REF_USER_ACCOUNT_ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="card">
            <div class="card-header ac-bg-warning" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse9064" aria-expanded="true" aria-controls="collapse9064">
                        <span class="badge badge-pill badge-warning bdg">POST</span>
                        <span class="ml-4 ac-title">/get-network-list-by-nickname	</span>
                        <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a Arvore de um usuario que pertece a usa rede pelo nickname
                    </span>
                    </button>
                    <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                </h5>
            </div>

            <div id="collapse9064" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="card-body">
                    <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-network-list-by-nickname</p>
                    <h4>Campos: </h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"> USER_ACCOUNT_ID (Autenticado)</li>
                        <li class="list-group-item"> P_REF_NICKNAME</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse37" aria-expanded="true" aria-controls="collapse37">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/get-statement-list	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o extrato do usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse37" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-statement-list</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                            <li class="list-group-item">P_DATE (Ex: 2020-07-27)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse39" aria-expanded="true" aria-controls="collapse39">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-cashback-week-validate-list/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista a(s) semana(s) para validação para o lançamento de cashback
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse39" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-cashback-week-validate-list/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse40" aria-expanded="true" aria-controls="collapse40">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-cashback-week-list/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista todas as validações semanais da conta do  usuário
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse40" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-cashback-week-list/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse41" aria-expanded="true" aria-controls="collapse41">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/new-cashback-week	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Valida(insere) a semana para o Lançamento de cashback
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse41" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/new-cashback-week</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                            <li class="list-group-item">P_CASHBACK_WEEK_ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse42" aria-expanded="true" aria-controls="collapse42">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/get-binary-chart-list	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Consulta para montar o gráfico de binário no dashboard
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse42" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-binary-chart-list</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                            <li class="list-group-item">P_DAYS</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse43" aria-expanded="true" aria-controls="collapse43">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/get-last-network-leg	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista o usuario abaixo de determinada perna
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse43" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-last-network-leg</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                            <li class="list-group-item">P_SIDE</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse44" aria-expanded="true" aria-controls="collapse44">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/performance-diary/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista de cashback bloqueado do usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse44" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/performance-diary{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse38" aria-expanded="true" aria-controls="collapse38">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/user-account-information/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retona os dados de determinado User Account - Dashboard
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse38" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user-account-information/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse59" aria-expanded="true" aria-controls="collapse59">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-order-item-list/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os pedidos daquela USER ACCOUNT
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse59" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-order-item-list/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse62" aria-expanded="true" aria-controls="collapse62">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/user-account/get-profile-image/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a imagem de perfil daquela USER ACCOUNT
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse62" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user-account/get-profile-image/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse63" aria-expanded="true" aria-controls="collapse63">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/user-account/set-profile-image/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona uma imagem de perfil para aquela USER ACCOUNT
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse63" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user-account/set-profile-image/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                            <li class="list-group-item">PROFILE_IMAGE</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-danger" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse64" aria-expanded="true" aria-controls="collapse64">
                            <span class="badge badge-pill badge-danger bdg">DELETE</span>
                            <span class="ml-4 ac-title">/user-account/remove-profile-image/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Remove a imagem de perfil daquela USER ACCOUNT
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse64" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user-account/remove-profile-image/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse78" aria-expanded="true" aria-controls="collapse78">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-nickname-by-id/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o nickname do usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse78" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-nickname-by-id/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse209" aria-expanded="true" aria-controls="collapse209">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/user-account-score-list/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista de pontos completa do Usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse209" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user-account-score-list/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                            <li class="list-group-item">DATE</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse2654556" aria-expanded="true" aria-controls="collapse2654556">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/vs-user-account-score-list/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista de pontos completa da loja para o Usuario
                        </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse2654556" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/vs-user-account-score-list/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (UserAccount)</li>
                            <li class="list-group-item">DATE</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse709" aria-expanded="true" aria-controls="collapse709">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/get-user-by-nickname/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os dados do Usuario baseado no seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse709" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-user-by-nickname/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
                            <li class="list-group-item">nickname</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse708" aria-expanded="true" aria-controls="collapse708">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/transfer-user-account/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Transfere uma UserAccount para outro Usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse708" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/transfer-user-account/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                            <li class="list-group-item">P_USER_ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <div class="container">
        <HR>
        <div id="user">
            <h4 class="mb-3 mt-2">USER <span class="badge badge-dark float-right mr-3">9</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse24" aria-expanded="true" aria-controls="collapse24">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/user/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza os dados do usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse24" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse101" aria-expanded="true" aria-controls="collapse101">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/user/change-password/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza a senha do usuario (Financeira ou de acesso)
                    </span>
                            </button>
                            <span class="float-right mt-2/withdrawl-methods"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse101" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/change-password/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserAccount)</li>
                                <li class="list-group-item">PASSWORD</li>
                                <li class="list-group-item">OLD_PASSWORD</li>
                                <li class="list-group-item">TYPE (1 = PASSWORD && 2 = FINANCIAL_PASSWORD)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse54" aria-expanded="true" aria-controls="collapse54">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/user/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os dados do usuario
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse54" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse645621" aria-expanded="true" aria-controls="collapse645621">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/genres	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os generos cadastrados
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse645621" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/genres</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse64562" aria-expanded="true" aria-controls="collapse64562">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/genres/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna um genero em especifico
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse64562" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/genres/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse25" aria-expanded="true" aria-controls="collapse25">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/user/user-account/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna as Users Accounts daquele usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse25" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-account/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse26" aria-expanded="true" aria-controls="collapse26">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/user/user-information/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todas as informações daquele usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse26" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-information/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse35" aria-expanded="true" aria-controls="collapse35">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/user/set-preferential-user-account/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona o USER ACCOUNT preferencial de determinado USER
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse35" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/set-preferential-user-account/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                                <li class="list-group-item">PREFERENTIAL_USER_ACCOUNT_ID</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse50" aria-expanded="true" aria-controls="collapse50">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/users/export</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os users em uma planilha do Excel
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse50" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/users/export</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse65" aria-expanded="true" aria-controls="collapse65">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/user/get-profile-image/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a imagem de perfil daquele USER
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse65" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/get-profile-image/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse66" aria-expanded="true" aria-controls="collapse66">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/user/set-profile-image/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona uma imagem de perfil para aquele USER
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse66" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/set-profile-image/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                                <li class="list-group-item">PROFILE_IMAGE</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-danger" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse67" aria-expanded="true" aria-controls="collapse67">
                                <span class="badge badge-pill badge-danger bdg">DELETE</span>
                                <span class="ml-4 ac-title">/user/remove-profile-image/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Remove a imagem de perfil daquele USER
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse67" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/remove-profile-image/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse367" aria-expanded="true" aria-controls="collapse367">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/auto-login-school/{token}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua o login na plataforma de cursos
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse367" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/auto-login-school/{token}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">token -> base64 (id|token|userAccountId) -> id do usuario autenticado concatenado com uma pipe | e o token de autenticação e outra pipe | userAccountId </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse368" aria-expanded="true" aria-controls="collapse368">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/withdrawal-tuddo-pay	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Altera o status do TODDO PAY
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse368" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/withdrawal-tuddo-pay</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"> toddoPay (1 ou 0) </li>
                                <li class="list-group-item"> user_id </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </div>
        <hr>
        <div id="individual-routes">
            <h4 class="mb-3 mt-2">ROTAS INDIVIDUAIS <span class="badge badge-dark float-right mr-3">11</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse12" aria-expanded="true" aria-controls="collapse12">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/existing-document	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna se aquele documento está cadastrado
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse12" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/existing-document</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">document</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse94" aria-expanded="true" aria-controls="collapse94">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/existing-user	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna se aquele usuario está cadastrado
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse94" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/existing-user</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">P_EMAIL</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse13" aria-expanded="true" aria-controls="collapse13">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/existing-email	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna se aquele email está cadastrado
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse13" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/existing-email</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">email</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse14" aria-expanded="true" aria-controls="collapse14">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/existing-nickname	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna se aquele nickname está cadastrado
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse14" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">nickname</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse15" aria-expanded="true" aria-controls="collapse15">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/existing-sponsor	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna se aquele uuid existe
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse15" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/existing-sponsor</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">uuid</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse16" aria-expanded="true" aria-controls="collapse16">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/existing-sponsor-id	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna se aquele patrocinador esta cadastrado
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse16" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/existing-sponsor-id</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse17" aria-expanded="true" aria-controls="collapse17">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/maintenance-system/{system}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna se o sistema esta em manutenção
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse17" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/maintenance-system{system}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">system</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse18" aria-expanded="true" aria-controls="collapse18">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/registered-user	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Verifica se o usuário esta registrado
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse18" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/registered-user</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">email</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse19" aria-expanded="true" aria-controls="collapse19">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/awaiting-payment/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Verifica se aquele usuário possui algum pagamento pendente
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse19" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/awaiting-payment</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserAccount)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse20" aria-expanded="true" aria-controls="collapse20">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/get-product-list/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista de produtos daquele usuário
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse20" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-product-list/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserAccount)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse21" aria-expanded="true" aria-controls="collapse21">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/existing-registration-request/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Verifica se existe uma registration request com aquele ID
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse21" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/existing-registration-request/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (RegistrationRequest)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse22" aria-expanded="true" aria-controls="collapse22">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/existing-user-account-id/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Verifica se existe uma user account com aquele ID
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse22" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/existing-user-account-id/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserAccount)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse5648894" aria-expanded="true" aria-controls="collapse5648894">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/support-form-contact	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Envia um email para o contato do suporte com copia para o cliente
                    </span>
                            </button>
                            <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                        </h5>
                    </div>

                    <div id="collapse5648894" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/support-form-contact</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">nickname</li>
                                <li class="list-group-item">reason</li>
                                <li class="list-group-item">data</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div id="product">
            <h4 class="mb-3 mt-2">PRODUTO <span class="badge badge-dark float-right mr-3">5</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse29" aria-expanded="true" aria-controls="collapse29">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/product-list/{userAccountId}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os produtos daquele usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse29" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/product-list/{userAccountId}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">userAccountId</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse57" aria-expanded="true" aria-controls="collapse57">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/product-price/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o preço de determinado produto
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse57" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/product-price/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (Product)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse68" aria-expanded="true" aria-controls="collapse68">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/product/set-product-image/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona a imagem de determinado produto
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse68" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/product/set-product-image/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (Product)</li>
                                <li class="list-group-item">PRODUCT_IMAGE</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse69" aria-expanded="true" aria-controls="collapse69">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/product/get-product-image/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a imagem de determinado produto
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse69" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/product/get-product-image/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (Product)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-danger" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse70" aria-expanded="true" aria-controls="collapse70">
                                <span class="badge badge-pill badge-danger bdg">DELETE</span>
                                <span class="ml-4 ac-title">/product/remove-product-image/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Remove a imagem de determinado produto
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse70" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/product/remove-product-image/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (Product)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </div>
        <HR>
        <DIV id="ORDER">
            <h4 class="mb-3 mt-2">ORDEM <span class="badge badge-dark float-right mr-3">4</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse30" aria-expanded="true" aria-controls="collapse30">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/order-item		</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua o pedido de um novo produto
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse30" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/order-item</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                                <li class="list-group-item">P_PRODUCT_ID</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse31" aria-expanded="true" aria-controls="collapse31">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/del-order-item-open		</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua o cancelamento de um pedido em aberto
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse31" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/del-order-item-open</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                                <li class="list-group-item">P_ORDER_ITEM_ID</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse33" aria-expanded="true" aria-controls="collapse33">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/order-tracking-item		</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os detalhes de um pedido do usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse33" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/order-tracking-item</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                                <li class="list-group-item">P_ORDER_TRACKING_ID</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse34" aria-expanded="true" aria-controls="collapse34">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/order-tracking/{id}		</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os pedidos do usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse34" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/order-tracking/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserAccount)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse457" aria-expanded="true" aria-controls="collapse457">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/get-transfer	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua o pagamento da ordem a partir do Hash de transferencia
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse457" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-transfer</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">hash</li>
                                <li class="list-group-item">user_account_id</li>
                                <li class="list-group-item">order_item_id</li>
                                <li class="list-group-item">digital_platform_id</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </DIV>
        <hr>
        <div id="pagamento">
            <h4 class="mb-3 mt-2">PAGAMENTO <span class="badge badge-dark float-right mr-3">5</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse36" aria-expanded="true" aria-controls="collapse36">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/add-payment-voucher/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona o comprovante de pagamento
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse36" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/add-payment-voucher/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (OrderItem)</li>
                                <li class="list-group-item">PAYMENT_VOUCHER</li>
                                <li class="list-group-item">PAYMENT_METHOD_ID (Usar caso seja uma transferencia direta)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse60" aria-expanded="true" aria-controls="collapse60">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/get-voucher/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o link para acessar o PAYMENT VOUCHER
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse60" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-voucher/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (OrderItem)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-danger" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse61" aria-expanded="true" aria-controls="collapse61">
                                <span class="badge badge-pill badge-danger bdg">DELETE</span>
                                <span class="ml-4 ac-title">/remove-voucher/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Remove o PAYMENT VOUCHER
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse61" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/remove-voucher/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (OrderItem)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse45" aria-expanded="true" aria-controls="collapse45">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/payment-methods	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista com todos os metodos de pagamento
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse45" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/payment-methods</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Sem parametros</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse6000" aria-expanded="true" aria-controls="collapse6000">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/clear-payment-method/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o link para acessar o PAYMENT VOUCHER
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse6000" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/clear-payment-method/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (OrderItem)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </div>
        <HR>
        <div id="cripto">
            <h4 class="mb-3 mt-2">CRIPTOMOEDAS</h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse41544" aria-expanded="true" aria-controls="collapse41544">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/criptocoin-payment	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona o comprovante de pagamento
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse41544" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/criptocoin-payment</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">ORDER_ITEM_ID</li>
                                <li class="list-group-item">PAYMENT_METHOD_ID</li>
                                <li class="list-group-item">P_CRYPTO_CURRENCY_ID</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse4144" aria-expanded="true" aria-controls="collapse4144">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/clear-criptocoin-payment/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Libera para alteração do metodo de pagamento ou geração de uma nova carteira
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse4144" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/clear-criptocoin-payment/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (OrderItem)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse5654981" aria-expanded="true" aria-controls="collapse5654981">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/payments/notification/squadipay	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> CALLBACK para a api das criptomoedas
                    </span>
                            </button>

                        </h5>
                    </div>
                    <div id="collapse5654981" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/payments/notification/squadipay</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1564894" aria-expanded="true" aria-controls="collapse1564894">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/cryptocurrency	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista as criptomoedas
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse1564894" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/cryptocurrency</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </div>
        <hr>
        <div id="SAQUE">
            <h4 class="mb-3 mt-2">SAQUE <span class="badge badge-dark float-right mr-3">4</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse46" aria-expanded="true" aria-controls="collapse46">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/withdrawl-methods	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista com todos os metodos de recebimento
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse46" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/withdrawl-methods</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Sem parametros</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse47" aria-expanded="true" aria-controls="collapse47">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/enable-withdrawl-screen/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Verifica se a tela de saque pode ser exibida para aquela User Account
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse47" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/enable-withdrawl-screen/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserAccount)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse49" aria-expanded="true" aria-controls="collapse49">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/new-withdrawl-request	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua uma nova solicitação de Transferência
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse49" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/new-withdrawl-request</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                                <li class="list-group-item">P_WITHDRAWAL_METHOD_ID</li>
                                <li class="list-group-item">P_REFERENCE_ID</li>
                                <li class="list-group-item">P_AMOUNT</li>
                                <li class="list-group-item">P_FINANCIAL_PASSWORD</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </div>
        <HR>
        <div id="PAISES">
            <h4 class="mb-3 mt-2">PAISES <span class="badge badge-dark float-right mr-3">1</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse55" aria-expanded="true" aria-controls="collapse55">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/country	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista todos os paises
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse55" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/country</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Sem parametros</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </div>
        <hr>
        <div id="BANK">
            <h4 class="mb-3 mt-2">BANCO <span class="badge badge-dark float-right mr-3">2</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse75" aria-expanded="true" aria-controls="collapse75">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/bank	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os bancos cadastrados no sistema
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse75" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/bank</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Sem parametros</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse76" aria-expanded="true" aria-controls="collapse76">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/bank/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os dados de determinado bank
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse76" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/bank</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (Bank)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse27" aria-expanded="true" aria-controls="collapse27">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/user/user-bank/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna as contas bancarias daquele usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse27" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-bank/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse62" aria-expanded="true" aria-controls="collapse62">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/user/user-bank	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona uma nova conta bancaria ao usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse62" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-bank</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">USER_ID</li>
                                <li class="list-group-item">BANK_ID</li>
                                <li class="list-group-item">TYPE_BANK_ACCOUNT_ID</li>
                                <li class="list-group-item">AGENCY</li>
                                <li class="list-group-item">CURRENT_ACCOUNT</li>
                                <li class="list-group-item">OPERATION</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse82" aria-expanded="true" aria-controls="collapse82">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/user/user-bank/set-description/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona uma nova descrição ou apelido a conta bancaria do usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse82" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-bank/set-description/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserBank)</li>
                                <li class="list-group-item">DESCRIPTION</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse83" aria-expanded="true" aria-controls="collapse83">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/user/user-bank/set-preferential-bank/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Seta determinado banco como preferencial do usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse83" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-bank/set-preferential-bank/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                                <li class="list-group-item">PREFERENTIAL_BANK</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse84" aria-expanded="true" aria-controls="collapse84">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/user/user-bank/change-status/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Inativa / Ativa determinado banco do usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse84" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-bank/change-status/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserBank)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse85" aria-expanded="true" aria-controls="collapse85">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/user/get-preferential-bank/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o banco preferencial do usuario
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse85" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/get-preferential-bank/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </div>
        <hr>
        <div id="WALLET">
            <h4 class="mb-3 mt-2">CARTEIRA <span class="badge badge-dark float-right mr-3">2</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse56" aria-expanded="true" aria-controls="collapse56">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/user/user-wallet/{id}</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todas as carterias cadastradas do usuario
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse56" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-wallet/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                id
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-warning" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse61" aria-expanded="true" aria-controls="collapse61">
                                <span class="badge badge-pill badge-warning bdg">POST</span>
                                <span class="ml-4 ac-title">/user/user-wallet</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona um wallet ao usuario
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse61" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-wallet</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">USER_ID</li>
                                <li class="list-group-item">CRYPTO_CURRENCY_ID</li>
                                <li class="list-group-item">ADDRESS</li>
                                <li class="list-group-item">DESCRIPTION</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse90" aria-expanded="true" aria-controls="collapse90">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/user/user-wallet/set-description/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona uma nova descrição ou apelido a carteira do usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse90" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-wallet/set-description/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserWallet)</li>
                                <li class="list-group-item">DESCRIPTION</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse91" aria-expanded="true" aria-controls="collapse91">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/user/user-wallet/set-preferential-wallet/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Seta determinada carteira como preferencial do usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse91" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-wallet/set-preferential-wallet/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                                <li class="list-group-item">PREFERENTIAL_WALLET</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-info" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse92" aria-expanded="true" aria-controls="collapse92">
                                <span class="badge badge-pill badge-info bdg">PUT</span>
                                <span class="ml-4 ac-title">/user/user-wallet/change-status/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Inativa / Ativa determinada carteira do usuário
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse92" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/user-wallet/change-status/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (UserWallet)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse93" aria-expanded="true" aria-controls="collapse93">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/user/get-preferential-wallet/{id}	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a conta preferencial do usuario
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse93" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user/get-preferential-wallet/{id}</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">id (User)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </div>
        <HR>
        <div id="plataformas">
            <h4 class="mb-3 mt-2">PLATAFORMAS DIGITAIS <span class="badge badge-dark float-right mr-3">4</span></h4>
            <div class="mt-1" id="accordion">
                <div class="card">
                    <div class="card-header ac-bg-success" id="headingOne">
                        <h5 class="mb-0">
                            <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse460" aria-expanded="true" aria-controls="collapse460">
                                <span class="badge badge-pill badge-success bdg">GET</span>
                                <span class="ml-4 ac-title">/digital-platforms	</span>
                                <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista com todas as plataformas digitais
                    </span>
                            </button>
                            <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                        </h5>
                    </div>

                    <div id="collapse460" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                        <div class="card-body">
                            <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/digital-platforms</p>
                            <h4>Campos: </h4>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Sem parametros</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!--END-->
        </div>
    </div>
<hr>
<p class="font-weight-bolder text-center">Todos os direitos reservados</p>

<script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
<!-- SCRIPT DE ROLAGEM SUAVE  -->
<script>
    jQuery(document).ready(function($) {
        $(".scroll").click(function(event){
            event.preventDefault();
            $('html,body').animate({scrollTop:$(this.hash).offset().top}, 600);
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

</body>
</html>
