<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Market</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
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

        <div class="container mt-4 mb-4">
            <h1 class="text-center">Market</h1>
            <hr>
            <div id="cadastro">
                <h4 class="mb-3 mt-2">Cadastro </h4>
                <div class="mt-1" id="accordion">
                    <div class="card">
                        <div class="card-header ac-bg-warning" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    <span class="badge badge-pill badge-warning bdg">POST</span>
                                    <span class="ml-4 ac-title">/market/registration-request	</span>
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua o cadastro previo de um usuario e envia um email de confirmação</span>
                                </button>
                                <span class="float-right mt-2"> <i class="fas fa-key"></i></span>
                            </h5>
                        </div>
                        <div id="collapse1" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">
                                <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_MARKET_URL')}}/registration-request</p>
                                <h4>Campos: </h4>
                                <p class="text-center"><strong>Enviar todos os campos como string</strong></p>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        NAME
                                    </li>
                                    <li class="list-group-item">
                                        EMAIL
                                    </li>
                                    <li class="list-group-item">
                                        PASSWORD
                                    </li>
                                    <li class="list-group-item">
                                        TYPE_DOCUMENT_ID
                                    </li>
                                    <li class="list-group-item">
                                        DOCUMENT
                                    </li>
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
                                <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                                    <span class="badge badge-pill badge-success bdg">GET</span>
                                    <span class="ml-4 ac-title">/registration-requests/resend/{email}</span>
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Reenvia o email para validar os dados do usuário</span>
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
            <div CLASS="mt-4" id="login">
                <h4>LOGIN  </h4>
                <div class="mt-4" id="accordion">
                    <div class="card">
                        <div class="card-header ac-bg-warning" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                                    <span class="badge badge-pill badge-warning bdg">POST</span>
                                    <span class="ml-4 ac-title">/login</span>
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Valida o EMAIL e SENHA do Usuário e retorna um token valido</span>
                                </button>
                                <!--<span class="float-right mt-2"> <i class="fas fa-key"></i></span>-->
                            </h5>
                        </div>

                        <div id="collapse3" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">
                                <p CLASS="text-center"> <i class="fas fa-link"></i> {{env('BACK_URL')}}/login</p>
                                <h4>Campos: </h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">EMAIL</li>
                                    <li class="list-group-item">PASSWORD</li>
                                    <li class="list-group-item">P_IP </li>
                                    <li class="list-group-item">P_SYSTEM_ID <span class="ml-3">(FIXO = 1)</span></li>
                                </ul>
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
            <div id="recovery-password">
                <h4 class="mb-3 mt-2">RECOVERY PASSWORD </h4>
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
                        <div class="card-header ac-bg-info" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse7" aria-expanded="true" aria-controls="collapse7">
                                    <span class="badge badge-pill badge-info bdg">PUT</span>
                                    <span class="ml-4 ac-title">/recovery-password/{id}	</span>
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua a atualização de senha do usuário</span>
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
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Verifica se o token é valido</span>
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
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Efetua a atualização de senha financeira do usuário</span>
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
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Envia o email para recuperar a senha financeira do usuario</span>
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
            <div id="user">
                <h4 class="mb-3 mt-2">USER</h4>
                <div class="mt-1" id="accordion">
                    <div class="card">
                        <div class="card-header ac-bg-info" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse24" aria-expanded="true" aria-controls="collapse24">
                                    <span class="badge badge-pill badge-info bdg">PUT</span>
                                    <span class="ml-4 ac-title">/user/{id}	</span>
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Atualiza os dados do usuário</span>
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
                        <div class="card-header ac-bg-success" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse54" aria-expanded="true" aria-controls="collapse54">
                                    <span class="badge badge-pill badge-success bdg">GET</span>
                                    <span class="ml-4 ac-title">/user/{id}	</span>
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os dados do usuario</span>
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
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna os generos cadastrados</span>
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
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna um genero em especifico</span>
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
                                <button class="btn btn-link buttom-hover" data-toggle="collapse" data-target="#collapse65" aria-expanded="true" aria-controls="collapse65">
                                    <span class="badge badge-pill badge-success bdg">GET</span>
                                    <span class="ml-4 ac-title">/user/get-profile-image/{id}	</span>
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Retorna a imagem de perfil daquele USER</span>
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
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Adiciona uma imagem de perfil para aquele USER</span>
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
                                    <span class="ml-5 ac-description"><i class="fas fa-arrow-right"></i> Remove a imagem de perfil daquele USER</span>
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
            </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
</body>
</html>
