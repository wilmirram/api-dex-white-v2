<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação ADM</title>
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
        <a class="navbar-brand scroll" href="#home"><span class="t-color">NÃO SEI O NOME</span></a>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
        </div>
        <div class="float-right">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown  font-weight-bolder ml-4" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="t-color">Seções</span> <i class="t-color fas fa-map"></i>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="scroll dropdown-item" href="#autenticacao">AUTENTICAÇÃO</a>
                        <a class="scroll dropdown-item" href="#pagamento">PAGAMENTO</a>
                        <a class="scroll dropdown-item" href="#relatorios">RELATORIOS</a>
                        <a class="scroll dropdown-item" href="#bonus">BONUS</a>
                        <a class="scroll dropdown-item" href="#cashback">CASHBACK</a>
                        <a class="scroll dropdown-item" href="#user">USER</a>
                        <a class="scroll dropdown-item" href="#userAccount">USER ACCOUNT</a>
                        <a class="scroll dropdown-item" href="#banco">BANCO</a>
                        <a class="scroll dropdown-item" href="#documento">DOCUMENTO</a>
                        <a class="scroll dropdown-item" href="#produto">PRODUTO</a>
                        <a class="scroll dropdown-item" href="#permissoes">PERMISSOES</a>
                        <a class="scroll dropdown-item" href="#boletos">BOLETOS</a>
                        <a class="scroll dropdown-item" href="#rr">REGISTRATION REQUEST</a>
                        <a class="scroll dropdown-item" href="#special-access">ACESSO ESPECIAL</a>
                        <a class="scroll dropdown-item" href="#access">NIVEIS DE ACESSO</a>
                        <a class="scroll dropdown-item" href="#type_object">OBJETO</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="jumbotron jumbotron-fluid mt-3 " id="home">
        <div class="container text-center">
            <h1 class="display-4 t-color">NÃO SEI O NOME - API (ADM) TESTE</h1>
            <p class="lead mt-4">Documentação para consumo da API <i class="far fa-file-alt"></i></p>
            <a href="{{route('office.doc')}}" class="btn btn-outline-danger float-left">Rotas Office</a>
            <a href="{{route('vs.doc')}}" class="btn btn-outline-danger ml-4 float-left">Rotas STORE</a>
            @if(session()->has('auth'))
                <a href="{{route('doc.market')}}" class="btn btn-outline-danger float-right">Market</a>
            @endif
        </div>
    </div>

    <div id="sponsored">
        <h4 class="mb-3 mt-2">CONTAS PATROCINADAS </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1003" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/approve-sponsored-account/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Approva uma conta patrocinada
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse1003" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/approve-sponsored-account/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                P_ORDER_ITEM_ID
                            </li>
                            <li class="list-group-item">
                                P_USER_ACCOUNT_ID
                            </li>
                            <li class="list-group-item">
                                P_EXPIRATION_DATE (ex: 2020-03-22)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
    </div>
    <hr>

    <div id="holidays">
        <h4 class="mb-3 mt-2">FERIADOS </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3001" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/holidays	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os feriados
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse3001" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/holidays</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3002" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/holidays/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna um feriado
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse3002" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/holidays/{id}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3003" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/holidays	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cadastra um novo feriado
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse3003" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/holidays</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                DT_HOLIDAY
                            </li>
                            <li class="list-group-item">
                                DESCRIPTION
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-info" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3004" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/holidays/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza um feriado
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse3004" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/holidays/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                DT_HOLIDAY (OPCIONAL)
                            </li>
                            <li class="list-group-item">
                                DESCRIPTION (OPCIONAL)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
    </div>
    <hr>
    <div id="users">
        <h4 class="mb-3 mt-2">USUARIOS ADMINSTRATIVOS </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse96748513" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/new-adm/{uuid}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cadastra um novo usuario administrativo
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse96748513" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/new-adm/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">NAME</li>
                            <li class="list-group-item">EMAIL</li>
                            <li class="list-group-item">PASSWORD</li>
                            <li class="list-group-item">ACCESS_LEVEL_ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse9674851" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/adm-user-list/{uuid}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os ADMs
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse9674851" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/adm-user-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> status - <strong>active</strong> ou <strong>inactive</strong> (OPCIONAL, Ex: {{env('BACK_URL')}}/adm/access-level/{uuid}?status=active)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-info" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse967451" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/adm/change-status-adm-user/{uuid}/{id}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza o status do adm
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse967451" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/adm/change-status-adm-user/{uuid}/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-info" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse96745441" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/change-access-level/{uuid}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza o nivel de acesso do adm
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse96745441" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/change-access-level/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">ADM_ID</li>
                            <li class="list-group-item">ACCESS_LEVEL_ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
    </div>
    <hr>
    <div id="autenticacao">
        <h4 class="mb-3 mt-2">AUTENTICAÇÃO </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse10567" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/refresh-adm-token		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Renova o token do adm
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse10567" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/refresh-adm-token</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">

                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-info" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse459456" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/new-adm-password/{uuid}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Altera a senha do adminstrador
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse459456" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/new-adm-password/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">OLD_PASSWORD</li>
                            <li class="list-group-item">NEW_PASSWORD</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse24597" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/recovery-adm-password		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Envia uma senha randomica para o email do administrador
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse24597" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/recovery-adm-password</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">EMAIL</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
    </div>
    <hr>
    <div id="pagamento">
        <h4 class="mb-3 mt-2">PAGAMENTO </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/approve-payment		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Aprova o pagamento de um pedido em aberto
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse1" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/approve-payment</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_ADM_ID</li>
                            <li class="list-group-item">P_ORDER_ITEM_ID</li>
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                            <li class="list-group-item">P_PAYMENT_METHOD_ID</li>
                            <li class="list-group-item">P_AMOUNT_RECEIVED</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse846314" aria-expanded="true" aria-controls="collapse846314">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/withdrawal-for-crypto/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Aprova a solicitação de Transferência realizada por Cypto
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse846314" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/withdrawal-for-crypto/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_WITHDRAWAL_REQUEST_ID</li>
                            <li class="list-group-item">P_STATUS (Ex: 'paid' | 'reversal')</li>
                            <li class="list-group-item">P_CRYPTO_CURRENCY_ID</li>
                            <li class="list-group-item">P_AMOUNT</li>
                            <li class="list-group-item">P_SATOSHI</li>
                            <li class="list-group-item">P_HASH_TRANSACTION_URL</li>
                            <li class="list-group-item">P_CRYPTO_QUOTE_USD</li>
                            <li class="list-group-item">P_WALLET</li>
                            <li class="list-group-item">P_DT_TRANSACTION</li>
                            <li class="list-group-item">P_DESCRIPTION</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse8463154" aria-expanded="true" aria-controls="collapse8463154">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/payment-report/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os pagamentos realizados
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse8463154" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/payment-report/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_DT_PAYMENT_START</li>
                            <li class="list-group-item">P_DT_PAYMENT_END</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse84631" aria-expanded="true" aria-controls="collapse84631">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/payment-report-xls/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os pagamentos realizados em XLS
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse84631" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/payment-report-xls/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_DT_PAYMENT_START</li>
                            <li class="list-group-item">P_DT_PAYMENT_END</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
    </div>
    <hr>
    <div id="relatorios">
        <h4 class="mb-3 mt-2">RELATORIOS </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse2651" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/search-transfer/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Relatorio de pagamentos realizados por transferencia
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse2651" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/search-transfer/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
                        </ul>
                        <hr>
                        <h5>Instruções para enviar as requisições:</h5>
                        <p>Pode pesquisar por todos os campos da tabela :</p>

                        <p><strong>Parametros disponiveis</strong></p>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">NICKNAME</li>
                            <li class="list-group-item">DOCUMENT</li>
                            <li class="list-group-item"> NAME</li>
                            <li class="list-group-item">SOCIAL_REASON</li>
                            <li class="list-group-item">DT_REGISTER_START</li>
                            <li class="list-group-item">DT_REGISTER_END</li>
                            <li class="list-group-item">DT_TRANSFER_START</li>
                            <li class="list-group-item">DT_TRANSFER_END</li>
                            <li class="list-group-item">DIGITAL_PLATFORM_ID</li>
                            <li class="list-group-item">HASH</li>
                            <li class="list-group-item">ID_TRANSFER</li>
                            <li class="list-group-item">TRANSFER_TO</li>
                            <li class="list-group-item">AMOUNT</li>
                            <li class="list-group-item">USER_ACCOUNT_ID</li>
                            <li class="list-group-item">VS_ORDER_ID</li>
                            <li class="list-group-item">ORDER_ITEM_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/search-order-item/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para primeiros testes de relatorios com JSON com JSON
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse2" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/search-order-item/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
                        </ul>
                        <hr>
                        <h5>Instruções para enviar as requisições:</h5>
                        <p>Pode pesquisar por todos os campos da tabela :</p>

                        <p><strong>Parametros disponiveis</strong></p>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">ID</li>
                            <li class="list-group-item">USER_ACCOUNT_ID</li>
                            <li class="list-group-item"> SEQ</li>
                            <li class="list-group-item">PRODUCT_ID</li>
                            <li class="list-group-item">PRODUCT_PRICE_ID</li>
                            <li class="list-group-item">GLOSS_PRICE</li>
                            <li class="list-group-item">FEE</li>
                            <li class="list-group-item">DOCUMENT</li>
                            <li class="list-group-item">DISCOUNT</li>
                            <li class="list-group-item">NET_PRICE</li>
                            <li class="list-group-item">UPGRADE</li>
                            <li class="list-group-item">CURRENT_ORDER_TRACKING_ID</li>
                            <li class="list-group-item">PRODUCT_SCORE</li>
                            <li class="list-group-item">LAUNCHED_SCORE</li>
                            <li class="list-group-item">PAYMENT_METHOD_ID</li>
                            <li class="list-group-item">PAYMENT_VOUCHER</li>
                            <li class="list-group-item">STATUS_ORDER_ID</li>
                            <li class="list-group-item">REFERENCE</li>
                            <li class="list-group-item">ACTIVE</li>
                            <li class="list-group-item">ADM_ID</li>
                            <li class="list-group-item">DT_REGISTER</li>
                            <li class="list-group-item">DT_LAST_UPDATE</li>
                            <li class="list-group-item">DT_LAST_UPDATE_ADM</li>
                            <li class="list-group-item">DT_PAYMENT_VOUCHER</li>
                            <li class="list-group-item">DT_ORDER_ITEM_EXPIRATION</li>
                        </ul>
                        <p class="mt-4">
                        Todos os campos de referencia normalmente final  ( _ID ) pode passar
                        um paramento numério ou um array:
                        </p>
                        <p>
                        <strong>"PAYMENT_METHOD_ID": 1</strong>
                        ou
                        <strong>"PAYMENT_METHOD_ID":[1,2,3,4,5]</strong>
                        </p>

                        <p>
                        Para os campos de data,
                        </p>

                        <p>DT_REGISTER</p>
                        <p>DT_LAST_UPDATE</p>
                        <p>DT_LAST_UPDATE_ADM</p>
                        <p>DT_PAYMENT_VOUCHER</p>
                        <p>DT_ORDER_ITEM_EXPIRATION</p>

                        <p>Usar o seguinte padrão acrescentar _START E _END após o atributo.</p>
                        <p><strong>DT_REFERENCE_START</strong></p>
                        <p><strong>DT_REFERENCE_END</strong></p>


                        <p>Exemplo:</p>


                        <p>DT_REGISTER_START</p>
                        <p>DT_REGISTER_END</p>


                        <p>Caso caso seja para uma data específica colocar em ambos o mesmo parâmetro</p>

                        <p>"DT_REGISTER_START": "2020-01-15" , "DT_REGISTER_END": "2020-01-15"</p>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse2080" aria-expanded="true" aria-controls="collapse2080">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/export-order-item/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Exporta uma planilha Excel do relatorio Order-Item
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse2080" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/export-order-item/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Enviar os mesmos paramentros enviados para a rota: <strong>{{env('BACK_URL')}}/search-order-item/{uuid}</strong></li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/search-withdrawal-request/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para primeiros testes de relatorios com JSON
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse3" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/search-withdrawal-request/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
                        </ul>
                        <hr>
                        <h5>Instruções para enviar as requisições:</h5>
                        <p>Pode pesquisar por todos os campos da tabela :</p>

                        <p><strong>Parametros disponiveis</strong></p>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">ID</li>
                            <li class="list-group-item">USER_ACCOUNT_ID</li>
                            <li class="list-group-item"> SEQ</li>
                            <li class="list-group-item">GLOSS_AMOUNT</li>
                            <li class="list-group-item">FEE_AMOUNT</li>
                            <li class="list-group-item">NET_AMOUNT</li>
                            <li class="list-group-item">WITHDRAWAL_STATUS_ID</li>
                            <li class="list-group-item">WITHDRAWAL_METHOD_ID</li>
                            <li class="list-group-item">USER_BANK_ID</li>
                            <li class="list-group-item">BANK_ID</li>
                            <li class="list-group-item">TYPE_BANK_ACCOUNT_ID</li>
                            <li class="list-group-item">AGENCY</li>
                            <li class="list-group-item">CURRENCT_ACCOUNT</li>
                            <li class="list-group-item">OPERATION</li>
                            <li class="list-group-item">USER_WALLET_ID</li>
                            <li class="list-group-item">CRYPTO_CURRENCY_ID</li>
                            <li class="list-group-item">ADDRESS</li>
                            <li class="list-group-item">FINANCE_CATEGORY_ID</li>
                            <li class="list-group-item">TOKEN</li>
                            <li class="list-group-item">REFERENCE</li>
                            <li class="list-group-item">NOTE</li>
                            <li class="list-group-item">ADM_ID</li>
                            <li class="list-group-item">DT_REGISTER</li>
                            <li class="list-group-item">DT_DEPOSIT</li>
                            <li class="list-group-item">DT_LAST_UPDATE</li>
                            <li class="list-group-item">DT_LAST_UPDATE_ADM</li>
                        </ul>
                        <p class="mt-4">
                            Todos os campos de referencia normalmente final  ( _ID ) pode passar
                            um paramento numério ou um array:
                        </p>
                        <p>
                            <strong>"FINANCE_CATEGORY_ID": 1</strong>
                            ou
                            <strong>"FINANCE_CATEGORY_ID":[1,2,3,4,5]</strong>
                        </p>

                        <p>
                            Para os campos de data,
                        </p>

                        <p>DT_REGISTER</p>
                        <p>DT_DEPOSIT</p>
                        <p>DT_LAST_UPDATE</p>
                        <p>DT_LAST_UPDATE_ADM</p>

                        <p>Usar o seguinte padrão acrescentar _START E _END após o atributo.</p>

                        <p><strong>DT_DEPOSIT_START  </strong></p>
                        <p><strong>DT_DEPOSIT_END</strong></p>


                        <p>Exemplo:</p>


                        <p>DT_REGISTER_START</p>
                        <p>DT_REGISTER_END</p>


                        <p>Caso caso seja para uma data específica colocar em ambos o mesmo parâmetro</p>

                        <p>"DT_REGISTER_START": "2020-01-15" , "DT_REGISTER_END": "2020-01-15"</p>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse54548946" aria-expanded="true" aria-controls="collapse54548946">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/withdrawal-report/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os log de registro de saques
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse54548946" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/withdrawal-report/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">WITHDRAWALL_REQUEST_ID</li>
                            <li class="list-group-item">NICKNAME</li>
                            <li class="list-group-item">NAME</li>
                            <li class="list-group-item">DOCUMENT</li>
                            <li class="list-group-item">STATUS</li>
                            <li class="list-group-item">SOCIAL_REASON</li>
                            <li class="list-group-item">DT_REGISTER_START</li>
                            <li class="list-group-item">DT_REGISTER_AND</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/search-statement/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para primeiros testes de relatorios com JSON
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse4" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/search-statement/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
                        </ul>
                        <hr>
                        <h5>Instruções para enviar as requisições:</h5>
                        <p>Pode pesquisar por todos os campos da tabela :</p>

                        <p><strong>Parametros disponiveis</strong></p>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">ID</li>
                            <li class="list-group-item">USER_ACCOUNT_ID</li>
                            <li class="list-group-item"> SEQ</li>
                            <li class="list-group-item">FINANCE_CATEGORY_ID </li>
                            <li class="list-group-item">AMOUNT  </li>
                            <li class="list-group-item">REF_ORDER_TRACKING_ID </li>
                            <li class="list-group-item">REF_ORDER_TRACKING_ITEM_ID </li>
                            <li class="list-group-item">REF_PAYMENT_ORDER_ID </li>
                            <li class="list-group-item">REF_WITHDRAWAL_REQUEST_ID </li>
                            <li class="list-group-item">WITHDRAWAL_STATUS_ID </li>
                            <li class="list-group-item">ACTIVE </li>
                            <li class="list-group-item">ADM_ID </li>
                            <li class="list-group-item">DT_LAST_UPDATE_ADM </li>
                        </ul>
                        <p class="mt-4">
                            Todos os campos de referencia normalmente final  ( _ID ) pode passar
                            um paramento numério ou um array:
                        </p>
                        <p>
                            <strong>"REF_ORDER_TRACKING_ID": 1</strong>
                            ou
                            <strong>"REF_ORDER_TRACKING_ID":[1,2,3,4,5]</strong>
                        </p>

                        <p>
                            Para os campos de data,
                        </p>

                        <p>DT_REGISTER</p>
                        <p>DT_REFERENCE</p>

                        <p>Usar o seguinte padrão acrescentar _START E _END após o atributo.</p>

                        <p><strong>DT_REFERENCE_START  </strong></p>
                        <p><strong>DT_REFERENCE_END</strong></p>


                        <p>Exemplo:</p>


                        <p>DT_REGISTER_START</p>
                        <p>DT_REGISTER_END</p>


                        <p>Caso caso seja para uma data específica colocar em ambos o mesmo parâmetro</p>

                        <p>"DT_REGISTER_START": "2020-01-15" , "DT_REGISTER_END": "2020-01-15"</p>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->

        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse207" aria-expanded="true" aria-controls="collapse207">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/search-user/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para primeiros testes de relatorios com JSON
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse207" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/search-user/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">A ADICIONAR</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse208" aria-expanded="true" aria-controls="collapse208">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/search-registration-request/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para primeiros testes de relatorios com JSON
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse208" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/search-registration-request/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">A ADICIONAR</li>
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
                            <span class="ml-4 ac-title">/search-config/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para primeiros testes de relatorios com JSON
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse209" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/search-config/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">A ADICIONAR</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse2099" aria-expanded="true" aria-controls="collapse2099">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/search-user-account/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os NICKNAMES do USUARIO baseado no documento, nome, email ou nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse2099" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/search-user-account/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">DOCUMENT</li>
                            <li class="list-group-item">EMAIL</li>
                            <li class="list-group-item">NAME</li>
                            <li class="list-group-item">NICKNAME</li>
                        </ul>
                        <p class="text-center">Pelo menos um paramentro acima deve ser enviado</p>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse619" aria-expanded="true" aria-controls="collapse619">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-payment-order-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista de Pagamento das ordens
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse619" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-payment-order-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> DATE_START</li>
                            <li class="list-group-item"> DATE_END</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse6019" aria-expanded="true" aria-controls="collapse6019">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/export-payment-order-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista de Pagamento das ordens em formato excel (xlsx)
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse6019" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/export-payment-order-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> DATE_START</li>
                            <li class="list-group-item"> DATE_END</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse620" aria-expanded="true" aria-controls="collapse620">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/get-transfer-nickname/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista dos NICKNAMES que foram transferidos
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse620" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-transfer-nickname/{uuid}</p>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1000" aria-expanded="true" aria-controls="collapse1000">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/prepare-withdrawal-spread-report/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Prepara o relatorio de saque a ser enviado para a U4C
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse1000" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/prepare-withdrawal-spread-report/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">WITHDRAWAL_STATUS_ID (Fixo: 1)</li>
                            <li class="list-group-item">WITHDRAWAL_METHOD_ID (Fixo: 3)</li>
                            <li class="list-group-item">DIGITAL_PLATFORM_ID (Fixo: 1)</li>
                            <li class="list-group-item">DT_REGISTER_START (Ex: 2020-09-01) | OPCIONAL</li>
                            <li class="list-group-item">DT_REGISTER_END (Ex: 2020-09-30) | OPCIONAL</li>
                            <li class="list-group-item">NICKNAME | OPCIONAL</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1001" aria-expanded="true" aria-controls="collapse1001">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/withdrawal-spreadsheet-export	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cria e exporta a planilha em XLSX
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse1001" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/withdrawal-spreadsheet-export</p>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3001" aria-expanded="true" aria-controls="collapse3001">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/remove-withdrawal-requests/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Remove determinados pedidos de saque da geração da planilha
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse3001" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/remove-withdrawal-requests/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">WITHDRAWAL_REQUEST_ID (Ex: "WITHDRAWAL_REQUEST_ID": [5,7,11] - Tambem pode ser enviado individual = "WITHDRAWAL_REQUEST_ID": [1])</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse21159" aria-expanded="true" aria-controls="collapse21159">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/pendent-withdrawal-request/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista os saques pendentes
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse21159" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/pendent-withdrawal-request/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">WITHDRAWAL_STATUS_ID (Fixo: "1")</li>
                            <li class="list-group-item">NICKNAME</li>
                            <li class="list-group-item">DOCUMENT</li>
                            <li class="list-group-item">DT_REGISTER_START</li>
                            <li class="list-group-item">DT_REGISTER_END</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse56874" aria-expanded="true" aria-controls="collapse56874">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/approve-withdrawal-request/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Aprova manualmente um pedido de saque
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse56874" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/approve-withdrawal-request/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">WITHDRAWAL_REQUEST_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1004" aria-expanded="true" aria-controls="collapse1004">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/list-spreadsheets/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista as planilhas ja criadas
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse1004" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/list-spreadsheets/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">dtStart (Ex: 2020-09-01) | OPCIONAIS</li>
                            <li class="list-group-item">dtEnd (Ex: 2020-09-30) | OPCIONAIS</li>
                            <hr>
                            <li class="list-group-item text-center">{{env('BACK_URL')}}/withdrawal-spreadsheet-export?<strong>dtStart=2020-09-01&dtEnd=2020-09-30</strong></li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1063" aria-expanded="true" aria-controls="collapse1063">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/verify-existing-spreadsheet/{filename}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Verifica se determinada planilha existe dentro da pasta
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse1063" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/verify-existing-spreadsheet/{filename}</p>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1005" aria-expanded="true" aria-controls="collapse1005">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/download-spreadsheet/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Faz o download de determinada planilha
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse1005" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/download-spreadsheet/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> FILENAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse906" aria-expanded="true" aria-controls="collapse906">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/spreadsheet-callback-u4c</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Callback de notificação de saque
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse906" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/spreadsheet-callback-u4c</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> documentNumber</li>
                            <li class="list-group-item"> transactionDate</li>
                            <li class="list-group-item"> amount</li>
                            <li class="list-group-item"> status</li>
                            <li class="list-group-item"> transaction_id</li>
                            <li class="list-group-item"> externalId</li>
                        </ul>
                        <p class="text-right">token: <strong>{{env('CALLBACK_TOKEN')}}</strong></p>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="updates">
        <h4 class="mb-3 mt-2">UPDATES </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-info" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse302" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/update-order-item/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para atualização de dados (ORDER_ITEM) usado pelo ADM
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse302" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/update-order-item/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse301" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/update-withdrawal-request/{uuid}	</span>
                            <span class="ml-3 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para atualização de dados (WITHDRAWAL_REQUEST) usado pelo ADM
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse301" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/update-withdrawal-request/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse300" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/update-registration-request/{uuid}	</span>
                            <span class="ml-3 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para atualização de dados (REGISTRATION_REQUEST) usado pelo ADM
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse300" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/update-registration-request/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse304" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/update-statement/{uuid}	</span>
                            <span class="ml-3 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para atualização de dados (ACCOUNT_STATEMENT) usado pelo ADM
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse304" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/update-statement/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse306" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/update-user/{uuid}	</span>
                            <span class="ml-3 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para atualização de dados (USER) usado pelo ADM
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse306" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/update-user/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse307" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/update-config/{uuid}	</span>
                            <span class="ml-3 ac-description"><i class="fas fa-arrow-right"></i> Rota implementada para atualização de dados (CONFIG) usado pelo ADM
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse307" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/update-config/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (ADM)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse30700" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/update-preferential-sponsor/{uuid}	</span>
                            <span class="ml-3 ac-description"><i class="fas fa-arrow-right"></i> Atualiza o patrocinador preferencial
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse30700" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/update-preferential-sponsor/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse307007" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/update-nickname/{uuid}	</span>
                            <span class="ml-3 ac-description"><i class="fas fa-arrow-right"></i> Rota para atualizar o NICKNAME do usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse3077" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/update-nickname/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_USER_ID</li>
                            <li class="list-group-item">P_USER_ACCOUNT_ID</li>
                            <li class="list-group-item">P_NICKNAME_OLD</li>
                            <li class="list-group-item">P_NICKNAME_NEW</li>
                            <li class="list-group-item">P_NOTE</li>
                            <li class="list-group-item">P_SYSTEM_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse5454952" aria-expanded="true" aria-controls="collapse2">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/new-user-email/{uuid}	</span>
                            <span class="ml-3 ac-description"><i class="fas fa-arrow-right"></i> Rota para atualizar o email de um determinado usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse5454952" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/new-user-email/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_USER_ID</li>
                            <li class="list-group-item">P_OLD_EMAIL</li>
                            <li class="list-group-item">P_NEW_EMAIL</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="bonus">
        <h4 class="mb-3 mt-2">BONUS</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse52" aria-expanded="true" aria-controls="collapse52">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/daily-bonus-score</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Insere o bonus diario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse52" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/daily-bonus-score</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> P_DT_REFERENCE</li>
                            <li class="list-group-item"> P_ADM_UUID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse800" aria-expanded="true" aria-controls="collapse800">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/report-insert-bonus-score</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Resume o bonus a ser lançado
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse800" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/report-insert-bonus-score</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> P_DT_REFERENCE</li>
                            <li class="list-group-item"> P_ADM_UUID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse210" aria-expanded="true" aria-controls="collapse210">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-daily-bonus-score-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista os binarios lançados
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse210" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-daily-bonus-score-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> uuid (Adm)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse620" aria-expanded="true" aria-controls="collapse620">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/blocked-cashback/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista de cashback bloqueado de um usuario baseado no nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse620" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/blocked-cashback/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> nickname</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse2491" aria-expanded="true" aria-controls="collapse2491">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-cashback/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista de cashback lançada em determinada data
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse2491" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/get-cashback/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> DATE (Ex: 2020-10-21)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="cashback">
        <h4 class="mb-3 mt-2">CASHBACK</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse53" aria-expanded="true" aria-controls="collapse53">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/daily-cashback</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Insere o cashback diario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse53" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/daily-cashback</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> P_DT_REFERENCE</li>
                            <li class="list-group-item"> P_ADM_UUID</li>
                            <li class="list-group-item"> P_TICKET</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse801" aria-expanded="true" aria-controls="collapse801">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/report-insert-cashback</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Resume o cashback a ser lançado
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse801" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/report-insert-cashback</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> P_DT_REFERENCE</li>
                            <li class="list-group-item"> P_ADM_UUID</li>
                            <li class="list-group-item"> P_TICKET</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse209" aria-expanded="true" aria-controls="collapse209">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-cashback-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista o cashback lançado
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse209" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/get-cashback-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> uuid (Adm)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="user">
        <h4 class="mb-3 mt-2">USER</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse201" aria-expanded="true" aria-controls="collapse201">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/user</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista com os IDs e Nomes de todos os usuarios
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse201" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user</p>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse489856" aria-expanded="true" aria-controls="collapse489856">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/find-nickname-by-user/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os nicknames de um usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse489856" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/find-nickname-by-user/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> VALUE (Pode ser enviado o email, documento ou nome do usuario)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse4898599" aria-expanded="true" aria-controls="collapse4898599">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-user-log-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o log de modificações de um determinado usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse4898599" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/get-user-log-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> P_USER_ID </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="userAccount">
        <h4 class="mb-3 mt-2">USER ACCOUNT</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse202" aria-expanded="true" aria-controls="collapse202">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/user-account</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista com os IDs e NICKNAMES de todos os usuarios
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse202" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/user</p>
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
    <div id="orderstatus">
        <h4 class="mb-3 mt-2">STATUS ORDEM</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3025" aria-expanded="true" aria-controls="collapse3025">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/status-order</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os status de ordem disponiveis
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse3025" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/adm/status-order</p>
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
    <div id="banco">
        <h4 class="mb-3 mt-2">BANCO</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse203" aria-expanded="true" aria-controls="collapse203">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/bank</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista com todos os bancos
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse203" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/bank</p>
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
    <div id="documento">
        <h4 class="mb-3 mt-2">DOCUMENTO</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse205" aria-expanded="true" aria-controls="collapse205">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/type-document</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista com todos os tipos de documentos
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse205" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/type-document</p>
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
    <div id="produto">
        <h4 class="mb-3 mt-2">PRODUTO</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse206" aria-expanded="true" aria-controls="collapse206">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/product</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a lista com todos os produtos
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse206" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/product</p>
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
    <div id="access">
        <h4 class="mb-3 mt-2">ACCESS LEVEL</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse89751" aria-expanded="true" aria-controls="collapse89751">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/access-level/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os niveis de acesso
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse89751" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/access-level/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> status - <strong>active</strong> ou <strong>inactive</strong> (OPCIONAL, Ex: {{env('BACK_URL')}}/adm/access-level/{uuid}?status=active)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse897512" aria-expanded="true" aria-controls="collapse897512">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/access-level/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cadastra um novo nivel de acesso
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse897512" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/access-level/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> DESCRIPTION</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse897513" aria-expanded="true" aria-controls="collapse897513">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/access-level/{uuid}/{id}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza os dados de um nivel de acesso
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse897513" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/access-level/{uuid}/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> DESCRIPTION</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse897514" aria-expanded="true" aria-controls="collapse897514">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/change-access-level/{uuid}/{id}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Altera o status de um nivel de acesso
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse897514" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/change-access-level/{uuid}/{id}</p>
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
    <div id="permissoes">
        <h4 class="mb-3 mt-2">PERMISSOES</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse20147" aria-expanded="true" aria-controls="collapse20147">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-privileges/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os privelegios disponiveis de determinado nivel
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse20147" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-privileges/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> P_ACCESS_LEVEL_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse20148" aria-expanded="true" aria-controls="collapse20148">
                            <span class="badge badge-pill badge-info bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/update-privileges/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza os dados de um determinado nivel de privilegio
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse20148" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/update-privileges/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> P_OBJECT_ID</li>
                            <li class="list-group-item"> P_TYPE_OBJECT_ID</li>
                            <li class="list-group-item"> P_OBJECT_ACCESS_LEVEL_ID</li>
                            <li class="list-group-item"> P_ACCESS_LEVEL_ID</li>
                            <li class="list-group-item"> P_ACTIVE</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse207" aria-expanded="true" aria-controls="collapse207">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-adm-privileges/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os privelegios do administrador
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse207" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/get-adm-privileges/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> uuid (ADM)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse2007" aria-expanded="true" aria-controls="collapse2007">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/get-adm-menu/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os menus do administrador
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse2007" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/get-adm-menu/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> uuid (ADM)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse8965" aria-expanded="true" aria-controls="collapse8965">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/permissions/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos as permissões
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse8965" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/permissions/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> status - <strong>active</strong> ou <strong>inactive</strong> (OPCIONAL, Ex: {{env('BACK_URL')}}/adm/permissions/{uuid}?status=active)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse8964" aria-expanded="true" aria-controls="collapse8964">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/permissions/{id}/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna uma permissão pelo seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse8964" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/permissions/{id}/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> id (Permissao)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse8963" aria-expanded="true" aria-controls="collapse8963">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/permissions/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Insere uma nova permissão
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse8963" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/permissions/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NAME</li>
                            <li class="list-group-item"> DESCRIPTION</li>
                            <li class="list-group-item"> NAME_PT_BRL</li>
                            <li class="list-group-item"> MENU</li>
                            <li class="list-group-item"> TYPE_OBJECT_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse8962" aria-expanded="true" aria-controls="collapse8962">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/permissions/{id}/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza os dados de uma permissão
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse8962" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/permissions/{id}/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NAME (OPCIONAL)</li>
                            <li class="list-group-item"> DESCRIPTION (OPCIONAL)</li>
                            <li class="list-group-item"> NAME_PT_BRL (OPCIONAL)</li>
                            <li class="list-group-item"> MENU (OPCIONAL)</li>
                            <li class="list-group-item"> TYPE_OBJECT_ID (OPCIONAL)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse8961" aria-expanded="true" aria-controls="collapse8961">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/active-or-inactive-permission/{id}/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Inativa ou ativa uma permissão
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse8961" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/active-or-inactive-permission/{id}/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">

                        </ul>
                        <p class="text-center"><strong>Se a permissão estiver inativa ela será ativada ou o inverso5</strong></p>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="type_object">
        <h4 class="mb-3 mt-2">TYPE OBJECT</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse9000" aria-expanded="true" aria-controls="collapse9000">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/type-object/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os tipos de objetos
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse9000" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/type-object/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> status - <strong>active</strong> ou <strong>inactive</strong> (OPCIONAL, Ex: {{env('BACK_URL')}}/adm/type-object/{uuid}?status=active)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse9001" aria-expanded="true" aria-controls="collapse9001">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/type-object/{id}/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna um objeto pelo seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse9001" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/type-object/{id}/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> id (typeObject)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse9002" aria-expanded="true" aria-controls="collapse9002">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/type-object/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Insere um novo objeto
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse9002" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/type-object/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> DESCRIPTION</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse9004" aria-expanded="true" aria-controls="collapse9004">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/type-object/{id}/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza o nome de um objeto
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse9004" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/type-object/{id}/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> DESCRIPTION </li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse9005" aria-expanded="true" aria-controls="collapse9005">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/active-or-inactive-type-object/{id}/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Inativa ou ativa uma permissão
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse9005" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/active-or-inactive-type-object/{id}/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">

                        </ul>
                        <p class="text-center"><strong>Se o objeto estiver inativo ele será ativado ou o inverso</strong></p>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="boletos">
        <h4 class="mb-3 mt-2">BOLETOS</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse400" aria-expanded="true" aria-controls="collapse400">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/boleto/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os boletos
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse400" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/boleto/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> uuid (ADM)</li>
                            <li class="list-group-item"> DT_REGISTER_START (Ex: 2020-09-17)</li>
                            <li class="list-group-item"> DT_REGISTER_END (Ex: 2020-09-20)</li>
                            <li class="list-group-item"> DOCUMENT</li>
                            <li class="list-group-item"> BILLET_DIGITABLE_LINE</li>
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse401" aria-expanded="true" aria-controls="collapse401">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/boleto-details/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os detalhes de um boleto especifico
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse401" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/boleto-details/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> uuid (ADM)</li>
                            <li class="list-group-item"> id (Boleto)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse402" aria-expanded="true" aria-controls="collapse402">
                            <span class="badge badge-pill badge-danger bdg">DELETE</span>
                            <span class="ml-4 ac-title">/boleto-cancel/{id}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua o cancelamento de um boleto especifico
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse402" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/boleto-cancel/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> id (UserAccount)</li>
                            <li class="list-group-item"> digitableline (Boleto)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse403" aria-expanded="true" aria-controls="collapse403">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/boleto-create</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Gera um novo boleto
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse403" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/boleto-create</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> ORDER_ITEM_ID</li>
                            <li class="list-group-item"> P_DIGITAL_PLATFORM_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse404" aria-expanded="true" aria-controls="collapse404">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/boleto-callback-u4c</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Callback de notificação de pagamento do boleto
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse404" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/boleto-callback-u4c</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> id</li>
                            <li class="list-group-item"> externalId</li>
                        </ul>
                        <p class="text-right">token: <strong>{{env('CALLBACK_TOKEN')}}</strong></p>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse405" aria-expanded="true" aria-controls="collapse405">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/force-approve-payment</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza o status do boleto para pago
                    </span>
                        </button>
                        <!-- <span class="float-right mt-2"> <i class="fas fa-key"></i></span> -->
                    </h5>
                </div>

                <div id="collapse405" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/force-approve-payment</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> digitableLine</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="rr">
        <h4 class="mb-3 mt-2">REGISTRATION REQUEST</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse415" aria-expanded="true" aria-controls="collapse415">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/registration-request</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cria um Registration Request para casos de venda de Nicknames
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse415" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/registration-request</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> P_EMAIL</li>
                            <li class="list-group-item"> P_NAME</li>
                            <li class="list-group-item"> P_ADM_UUID</li>
                            <li class="list-group-item"> P_TYPE_DOCUMENT_ID</li>
                            <li class="list-group-item"> P_DOCUMENT</li>
                            <li class="list-group-item"> P_TRANSFER_USER_ACCOUNT_ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="wc">
        <h4 class="mb-3 mt-2">CONFIGURAÇÕES DE SAQUE</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse5214" aria-expanded="true" aria-controls="collapse415">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/adm/get-withdrawal-configs/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista as configurações de saque a serem exibidas no painel ADM
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse5214" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-withdrawal-configs/{uuid}</p>
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
                <div class="card-header ac-bg-info" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse521441" aria-expanded="true" aria-controls="collapse415">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/adm/update-withdrawal-configs/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza as configurações de saque
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse521441" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/update-withdrawal-configs/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> WITHDRAWAL_AMOUNT_MIN (OPCIONAL)</li>
                            <li class="list-group-item"> WITHDRAWAL_AMOUNT_MAX (OPCIONAL)</li>
                            <li class="list-group-item"> WITHDRAWAL_AMOUNT_CRYPTO_MIN (OPCIONAL)</li>
                            <li class="list-group-item"> WITHDRAWAL_AMOUNT_CRYPTO_MAX (OPCIONAL)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
        <!--END-->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse52144" aria-expanded="true" aria-controls="collapse415">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/get-withdrawal-limits</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna as configurações de saque a serem exibidas ao usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse52144" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/get-withdrawal-limits</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> P_WITHDRAWAL_METHOD_ID</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
    <hr>
    <div id="special-access">
        <h4 class="mb-3 mt-2">ACESSO ESPECIAL</h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse600" aria-expanded="true" aria-controls="collapse600">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-user-account-information/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (DASHBOARD) Retorna todos os dados do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse600" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-user-account-information/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> nickname</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse601" aria-expanded="true" aria-controls="collapse601">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-binary-chart-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (DASHBOARD) Retorna o grafico binario do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse601" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-binary-chart-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
                            <li class="list-group-item"> P_DAYS</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse602" aria-expanded="true" aria-controls="collapse602">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-user-bank/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (MY ACCOUNT) Retorna os bancos do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse602" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-user-bank/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse603" aria-expanded="true" aria-controls="collapse603">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-preferential-bank/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (MY ACCOUNT) Retorna o banco preferencial do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse603" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-preferential-bank/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse604" aria-expanded="true" aria-controls="collapse604">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-user-data/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (MY ACCOUNT) Retorna os dados de cadastro do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse604" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-user-data/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse605" aria-expanded="true" aria-controls="collapse605">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-user-wallet/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (MY ACCOUNT) Retorna as carteiras do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse605" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-user-wallet/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse606" aria-expanded="true" aria-controls="collapse606">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-preferential-wallet/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (MY ACCOUNT) Retorna a carteira preferencial do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse606" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-preferential-wallet/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse607" aria-expanded="true" aria-controls="collapse607">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-product-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (STORE) Retorna a a lista de produtos do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse607" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-product-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse608" aria-expanded="true" aria-controls="collapse608">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-order-item-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (STORE) Retorna a lista de pedidos do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse608" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-order-item-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse644" aria-expanded="true" aria-controls="collapse644">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/order-tracking-item/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (STORE) Retorna os detalhes da lista de pedidos do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse644" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/order-tracking-item/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
                            <li class="list-group-item"> P_ORDER_TRACKING_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse609" aria-expanded="true" aria-controls="collapse609">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-network-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (NETWORK) Retorna a arvore binaria do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse609" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-network-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
                            <li class="list-group-item"> P_REF_USER_ACCOUNT_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse7134" aria-expanded="true" aria-controls="collapse7134">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-network-list-by-nickname/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (NETWORK) Retorna a Arvore de um usuario que pertece a rede pelo nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse7134" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-network-list-by-nickname/{uuid}</p>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse614" aria-expanded="true" aria-controls="collapse614">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-last-network-leg/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (NETWORK) Lista o usuario abaixo de determinada perna baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse614" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-last-network-leg/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
                            <li class="list-group-item"> P_SIDE</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse610" aria-expanded="true" aria-controls="collapse610">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-sponsors-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (NETWORK) Retorna a lista de patrocinadoresdo usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse610" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-sponsors-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse6100" aria-expanded="true" aria-controls="collapse6100">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-upline-network/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (NETWORK) Retorna toda a rede que está acima do usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse6100" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-upline-network/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse611" aria-expanded="true" aria-controls="collapse611">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-statement-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (FINANCIAL) Retorna os extratos do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse611" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-statement-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
                            <li class="list-group-item"> P_DATE (Ex: 2020-07-27)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse612" aria-expanded="true" aria-controls="collapse612">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/order-tracking/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (FINANCIAL) Retorna os pedidos do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse612" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/order-tracking/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse6147" aria-expanded="true" aria-controls="collapse6147">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/order-tracking-item/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (FINANCIAL) Retorna os detalhes de um pedido do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse6147" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/order-tracking-item/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
                            <li class="list-group-item"> P_ORDER_TRACKING_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse6120" aria-expanded="true" aria-controls="collapse6120">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/get-transfer-log/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (FINANCIAL) Retorna o log de erros das transferencias por hash
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse6120" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/get-transfer-log/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
                            <li class="list-group-item"> HASH</li>
                            <li class="list-group-item"> DOCUMENT</li>
                            <li class="list-group-item"> DT_REGISTER_START</li>
                            <li class="list-group-item"> DT_REGISTER_END</li>
                            <li class="list-group-item"> NAME</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse613" aria-expanded="true" aria-controls="collapse613">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/user-account-score-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (REPORTS) Lista o score do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse613" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/user-account-score-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
                            <li class="list-group-item"> DATE</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse54792" aria-expanded="true" aria-controls="collapse54792">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/adm/vs-user-account-score-list/{uuid}</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> (REPORTS) Lista o score da loja do usuario baseado em seu nickname
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse54792" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i>
                            {{env('BACK_URL')}}/adm/vs-user-account-score-list/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"> NICKNAME</li>
                            <li class="list-group-item"> DATE</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--END-->
    </div>
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
