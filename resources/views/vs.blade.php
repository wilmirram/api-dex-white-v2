<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação Store</title>
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
                        <a class="scroll dropdown-item" href="#produtos">PRODUTOS</a>
                        <a class="scroll dropdown-item" href="#ordem">ORDEM</a>
                        <a class="scroll dropdown-item" href="#address">ENDEREÇOS</a>
                        <a class="scroll dropdown-item" href="#category">CATEGORIA</a>
                        <a class="scroll dropdown-item" href="#sub-category">SUB CATEGORIA</a>
                        <a class="scroll dropdown-item" href="#measurement">UNIDADES DE MEDIDA</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="jumbotron jumbotron-fluid mt-3 " id="home">
        <div class="container text-center">
            <h1 class="display-4 t-color">NÃO SEI O NOME - VIRTUAL STORE</h1>
            <p class="lead mt-4">Documentação para consumo da API da Loja Virtual<i class="far fa-file-alt"></i></p>
            <a href="{{route('office.doc')}}" class="btn btn-outline-danger float-left">Rotas Office</a>
            <a href="{{route('adm.doc')}}" class="btn btn-outline-danger ml-4 float-left">Rotas ADM</a>
            <a href="{{route('vs.product-control')}}" class="btn btn-outline-danger ml-4 float-left">Controle de imagens</a>
            @if(session()->has('auth'))
                <a href="{{route('doc.market')}}" class="btn btn-outline-danger float-right">Market</a>
            @endif
        </div>
    </div>

    <div id="shipping">
        <h4 class="mb-3 mt-2">FRETE </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse100" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/shipping-calculate	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Calcula o frete dos produtos no carrinho
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse100" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/shipping-calculate</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                DESTINO
                            </li>
                            <li class="list-group-item">
                                PRODUCT_LIST (ID, UNITS)
                            </li>
                            <li class="list-group-item">
                                ZIP_CODE
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div id="supplier">
        <h4 class="mb-3 mt-2">FORNECEDOR </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3001" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/suppliers	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os fornecedores
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse3001" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/store/suppliers</p>
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
                            <span class="ml-4 ac-title">/store/supplier/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna um fornecedore
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse3002" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/supplier/{id}</p>
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
                            <span class="ml-4 ac-title">/store/supplier	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cadastra um novo fornecedor
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse3003" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/supplier</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                SOCIAL_REASON
                            </li>
                            <li class="list-group-item">
                                FANTASY_NAME
                            </li>
                            <li class="list-group-item">
                                REPRESENTATIVE
                            </li>
                            <li class="list-group-item">
                                DDI
                            </li>
                            <li class="list-group-item">
                                PHONE
                            </li>
                            <li class="list-group-item">
                                ZIP_CODE
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
                            <span class="ml-4 ac-title">/store/supplier/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza um fornecedor
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse3004" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/supplier/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                SOCIAL_REASON (OPCIONAL)
                            </li>
                            <li class="list-group-item">
                                FANTASY_NAME (OPCIONAL)
                            </li>
                            <li class="list-group-item">
                                REPRESENTATIVE (OPCIONAL)
                            </li>
                            <li class="list-group-item">
                                DDI (OPCIONAL)
                            </li>
                            <li class="list-group-item">
                                PHONE (OPCIONAL)
                            </li>
                            <li class="list-group-item">
                                ZIP_CODE (OPCIONAL)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
    </div>
    <hr>
    <div id="produtos">
        <h4 class="mb-3 mt-2">PRODUTOS </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/products/{userAccountId}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista todos os produtos da loja
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse1" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/products/{userAccountId}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse6898456" aria-expanded="true" aria-controls="collapse6898456">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/get-product-list		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista os produtos da loja para selects
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse6898456" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/get-product-list</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse10000" aria-expanded="true" aria-controls="collapse10000">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/get-product-image-name-list/{id}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista o nome e a url de todos os produtos baseado no ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse10000" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/get-product-image-name-list/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1000" aria-expanded="true" aria-controls="collapse1000">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/get-product-image-list/{id}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista todas as imagens do produto baseado em seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse1000" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/get-product-image-list/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1001" aria-expanded="true" aria-controls="collapse1001">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/set-product-image/{id}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona uma nova imagem ao produto
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse1001" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/set-product-image/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                id (Product)
                            </li>
                            <li class="list-group-item">
                                PRODUCT_IMAGE (base64)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-danger" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1002" aria-expanded="true" aria-controls="collapse1002">
                            <span class="badge badge-pill badge-danger bdg">DELETE</span>
                            <span class="ml-4 ac-title">/store/remove-product-image/{id}/{filename}		</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Remove uma imagem do produto
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>
                <div id="collapse1002" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/remove-product-image/{id}/{filename}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                id (Product)
                            </li>
                            <li class="list-group-item">
                                filename (nomedoarquivo.ext)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div id="ordem">
        <h4 class="mb-3 mt-2">ORDEM </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3421" aria-expanded="true" aria-controls="collapse3421">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/api/delivery-status	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista os status de entrega
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse3421" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/delivery-status</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse614" aria-expanded="true" aria-controls="collapse614">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/confirm-delivery/{admUuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Confirma o envio de uma ordem
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse614" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/confirm-delivery/{admUuid}</p>
                        <h4>Campos: </h4>
                        <p class="text-center"><strong>Enviar todos os valores como string</strong></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                ID (VS_ORDER)
                            </li>
                            <li class="list-group-item">
                                TRACKING_CODE
                            </li>
                            <li class="list-group-item">
                                DT_SHIPPING
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse613" aria-expanded="true" aria-controls="collapse613">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/search-order/{admUuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Busca as ordens da loja
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse613" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/search-order/{admUuid}</p>
                        <h4>Campos: </h4>
                        <p class="text-center"><strong>Enviar todos os valores como string</strong></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                ID (VS_ORDER)
                            </li>
                            <li class="list-group-item">
                                USER_ACCOUNT_ID
                            </li>
                            <li class="list-group-item">
                                DOCUMENT
                            </li>
                            <li class="list-group-item">
                                DT_REGISTER_START
                            </li>
                            <li class="list-group-item">
                                DT_REGISTER_END
                            </li>
                            <li class="list-group-item">
                                DELIVERY_STATUS_ID (Padrão: [1])
                            </li>
                            <li class="list-group-item">
                                TRACKING_CODE
                            </li>
                            <li class="list-group-item">
                                DT_SHIPPING
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse16" aria-expanded="true" aria-controls="collapse16">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/order	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Realiza uma nova ordem
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse16" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/order</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                DT_ESTIMATED_DELIVERY
                            </li>
                            <li class="list-group-item">
                                User_Account_ID
                            </li>
                            <li class="list-group-item">
                                Shipping_Price
                            </li>
                            <li class="list-group-item">
                                Delivery_Address (0 = Default ou id do endereço na tabela USER ADDRESS)
                            </li>
                            <li class="list-group-item">
                                Product_List
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse160" aria-expanded="true" aria-controls="collapse160">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/order-store	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Gera o boleto para determinada ordem
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse160" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/order-store</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                VS_ORDER_ID
                            </li>
                            <li class="list-group-item">
                                P_DIGITAL_PLATFORM_ID
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse615" aria-expanded="true" aria-controls="collapse615">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/order-get-transfer	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua o pagamento de uma ordem usando uma hash de transferencia
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse615" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/order-get-transfer</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                hash
                            </li>
                            <li class="list-group-item">
                                vs_order_id
                            </li>
                            <li class="list-group-item">
                                digital_platform_id
                            </li>
                            <li class="list-group-item">
                                user_account_id
                            </li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse616" aria-expanded="true" aria-controls="collapse616">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/order-add-payment-voucher/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona um comprovante de pagamento a ordem
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse616" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/order-add-payment-voucher/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                PAYMENT_VOUCHER (base64)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-danger" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse15795" aria-expanded="true" aria-controls="collapse15795">
                            <span class="badge badge-pill badge-danger bdg">DELETE</span>
                            <span class="ml-4 ac-title">/store/order-boleto-cancel/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cancela um boleto de uma ordem no eccomerce
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse15795" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/order-boleto-cancel/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                digitableLine
                            </li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse32649" aria-expanded="true" aria-controls="collapse32649">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/search-vs-order/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Lista as ordens do eccomerce
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse32649" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/search-vs-order/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_VS_ORDER_ID</li>
                            <li class="list-group-item">USER_ACCOUNT_ID</li>
                            <li class="list-group-item">DOCUMENT</li>
                            <li class="list-group-item">PAYMENT_METHOD_ID</li>
                            <li class="list-group-item">DT_REGISTER_START</li>
                            <li class="list-group-item">DT_REGISTER_END</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1579" aria-expanded="true" aria-controls="collapse1579">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/approve-vs-order/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Aprova uma ordem no eccomerce
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse1579" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/approve-vs-order/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">P_VS_ORDER_ID</li>
                            <li class="list-group-item">NICKNAME</li>
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
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse617" aria-expanded="true" aria-controls="collapse617">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/order-get-payment-voucher/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna o comprovante de pagamento a ordem
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse617" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/order-get-payment-voucher/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-danger" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse619" aria-expanded="true" aria-controls="collapse619">
                            <span class="badge badge-pill badge-danger bdg">DELETE</span>
                            <span class="ml-4 ac-title">/store/order-remove-payment-voucher/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Remove o comprovante de pagamento a ordem
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse619" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/order-remove-payment-voucher/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                            </li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse618" aria-expanded="true" aria-controls="collapse618">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/cancel-order	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cancela uma ordem caso a mesma esteja aguardando pagamento
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse618" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/cancel-order</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">USER_ACCOUNT_ID</li>
                            <li class="list-group-item">VS_ORDER_ID</li>
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
                            <span class="ml-4 ac-title">/store/payment-report/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os pagamentos realizados na loja
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse8463154" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/payment-report/{uuid}</p>
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
                            <span class="ml-4 ac-title">/store/payment-report-xls/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os pagamentos realizados na loja em XLS
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse84631" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/payment-report-xls/{uuid}</p>
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
    <div id="address">
        <h4 class="mb-3 mt-2">ENDEREÇOS </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse20" aria-expanded="true" aria-controls="collapse20">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/user-address	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cadastra um novo endereço para o usuario
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse20" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/user-address</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">USER_ID</li>
                            <li class="list-group-item">COUNTRY_ID</li>
                            <li class="list-group-item">ZIP_CODE</li>
                            <li class="list-group-item">ADDRESS</li>
                            <li class="list-group-item">NUMBER</li>
                            <li class="list-group-item">NEIGHBORHOOD</li>
                            <li class="list-group-item">CITY</li>
                            <li class="list-group-item">STATE</li>
                            <li class="list-group-item">COMPLEMENT | OPCIONAL</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse21" aria-expanded="true" aria-controls="collapse21">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/store/user-address/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza os dados de um endereço pelo seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse21" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/user-address/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">USER_ID | OPCIONAL</li>
                            <li class="list-group-item">COUNTRY_ID | OPCIONAL</li>
                            <li class="list-group-item">ZIP_CODE | OPCIONAL</li>
                            <li class="list-group-item">ADDRESS | OPCIONAL</li>
                            <li class="list-group-item">NUMBER | OPCIONAL</li>
                            <li class="list-group-item">NEIGHBORHOOD | OPCIONAL</li>
                            <li class="list-group-item">CITY | OPCIONAL</li>
                            <li class="list-group-item">STATE | OPCIONAL</li>
                            <li class="list-group-item">COMPLEMENT | OPCIONAL</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-danger" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse22" aria-expanded="true" aria-controls="collapse22">
                            <span class="badge badge-pill badge-danger bdg">DELETE</span>
                            <span class="ml-4 ac-title">/store/user-address/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Remove um endereço pelo seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse22" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/user-address/{id}</p>
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
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse23" aria-expanded="true" aria-controls="collapse23">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/user-address/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todos os endereços de um usuario pelo id da sua User Account
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse23" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/user-address/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
    </div>
    <hr>
    <div id="category">
        <h4 class="mb-3 mt-2">CATEGORIAS </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse24" aria-expanded="true" aria-controls="collapse24">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/categories	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todas as categorias
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse24" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/categories</p>
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
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse25" aria-expanded="true" aria-controls="collapse25">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/category/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna uma determinada categoria baseada no seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse25" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/category/{id}</p>
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
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse26" aria-expanded="true" aria-controls="collapse26">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/category/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cadastra uma nova categoria
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse26" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/category/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (Adm)</li>
                            <li class="list-group-item">DESCRIPTION</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse27" aria-expanded="true" aria-controls="collapse27">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/store/category/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza os dados de uma categoria baseada em seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse27" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/category/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">UUID (Adm)</li>
                            <li class="list-group-item">DESCRIPTION</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
    </div>
    <hr>
    <div id="sub-category">
        <h4 class="mb-3 mt-2">SUB CATEGORIAS </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse28" aria-expanded="true" aria-controls="collapse28">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/sub-categories	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todas as sub categorias
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse28" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/sub-categories</p>
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
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse29" aria-expanded="true" aria-controls="collapse29">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/sub-category/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna uma determinada sub categoria baseada no seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse29" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/sub-category/{id}</p>
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
                <div class="card-header ac-bg-warning" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse30" aria-expanded="true" aria-controls="collapse30">
                            <span class="badge badge-pill badge-warning bdg">POST</span>
                            <span class="ml-4 ac-title">/store/sub-category/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cadastra uma nova sub categoria
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse30" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/sub-category/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (Adm)</li>
                            <li class="list-group-item">DESCRIPTION</li>
                            <li class="list-group-item">VS_CATEGORY_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse31" aria-expanded="true" aria-controls="collapse31">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/store/sub-category/{id}/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza os dados de uma determinada sub categoria baseada em seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse31" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/sub-category/{id}/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (Adm)</li>
                            <li class="list-group-item">id (SubCategory)</li>
                            <li class="list-group-item">DESCRIPTION</li>
                            <li class="list-group-item">VS_CATEGORY_ID</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse32" aria-expanded="true" aria-controls="collapse32">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/store/sub-category/status/{id}/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza o status de uma determinada sub categoria baseada em seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse32" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/sub-category/status/{id}/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (Adm)</li>
                            <li class="list-group-item">id (SubCategory)</li>
                            <li class="list-group-item">STATUS</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
    </div>
    <hr>
    <div id="measurement">
        <h4 class="mb-3 mt-2">UNIDADES DE MEDIDA </h4>
        <div class="mt-1" id="accordion">
            <div class="card">
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse33" aria-expanded="true" aria-controls="collapse33">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/measurement	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna todas as unidades de medida
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse33" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/measurement</p>
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
                <div class="card-header ac-bg-success" id="headingOne">
                    <h5 class="mb-0">
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse36" aria-expanded="true" aria-controls="collapse36">
                            <span class="badge badge-pill badge-success bdg">GET</span>
                            <span class="ml-4 ac-title">/store/measurement/{id}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna determinada unidade de medida baseada em seu ID
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse36" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/measurement/{id}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">id (Measurement)</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse34" aria-expanded="true" aria-controls="collapse34">
                            <span class="badge badge-pill badge-warning bdg">PSOT</span>
                            <span class="ml-4 ac-title">/store/measurement/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Cadastra uma nova unidade de medida
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse34" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/measurement/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (Adm)</li>
                            <li class="list-group-item">DESCRIPTION</li>
                            <li class="list-group-item">SYMBOL</li>
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
                        <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse35" aria-expanded="true" aria-controls="collapse35">
                            <span class="badge badge-pill badge-info bdg">PUT</span>
                            <span class="ml-4 ac-title">/store/measurement/{id}/{uuid}	</span>
                            <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza os dados de uma unidade de medida
                    </span>
                        </button>
                        <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                    </h5>
                </div>

                <div id="collapse35" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_STORE_URL')}}/measurement/{id}/{uuid}</p>
                        <h4>Campos: </h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">uuid (Adm)</li>
                            <li class="list-group-item">id (Measurement)</li>
                            <li class="list-group-item">DESCRIPTION</li>
                            <li class="list-group-item">SYMBOL</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- END -->
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
