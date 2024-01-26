<!DOCTYPE html>
<html>
<head>


    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title></title>

    {{--<link href="https://fonts.googleapis.com/css?family=Dancing+Script:400,700" rel="stylesheet">--}}
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">



    <style>
        @font-face {
            font-family: 'Lobster Two';
            src: url({{public_path('/fonts/lobster/LobsterTwo-Bold.ttf')}}) format('truetype'),
            url({{public_path('/fonts/lobster/LobsterTwo-BoldItalic.ttf')}}) format('truetype'),
            url({{public_path('/fonts/lobster/LobsterTwo-Italic.ttf')}}) format('truetype'),
            url({{public_path('/fonts/lobster/LobsterTwo-Regular.ttf')}}) format('truetype'),

        }

        body, h1, h2, h3, h4, span, div {
            /*font-family: 'Dancing Script', cursive;*/
            font-family: 'Lobster Two', cursive;

        }

        body {
            background-image: );
            background-repeat: no-repeat; background-position: center;
        }

        .main-border {
            border: 20px solid darkred;
        }

        .row {
            position: relative;
        }

        /*.main-border .row{*/
        /*height: 800px;*/
        /*}*/
        .main-border .row h1 {
            font-size: 80px;
        }

        .banner {
            position: absolute;
            left: 0;
            right: 0;
            margin: auto;
        }

        .badge-img {
            right: 0;
            top: 0;
        }

        .logo {
            left: 40%;
            position: absolute;
            bottom: 22%;
            right: 0;
            display: inline-block;
            margin: auto;
        }

        /*.container-fluid {*/
        /*width: 1200px;*/
        /*height: 855px;*/
        /*}*/

        .wrapper {
            position: absolute;
            left: 0;
            top: 50%;
            right: 0;
            margin: auto;
        }

        .text-block {
            position: absolute;
            right: 0;
            margin: auto;
            top: 40%;
            left: 0;
            text-align: center;
        }

        .text-block p {
            line-height: 1;
            margin-top: 30px;
            font-size: 30px;
            opacity: 0.9;
        }

        .font-weight-bold {
            font-weight: bolder;
        }
    </style>
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script src="https://sdk.mercadopago.com/js/v2"></script>

</head>
<body >
<div class="container-fluid px-0" style="height: 500px;">
    <div style="position: relative;text-align: center"
         class="row h-100 justify-content-center text-center position-relative m-0">
        <img class="logo" src="">
        <div class="mercadopago-label">

      </div>
    </div>
</div>
</body>
<script>
// Adicione as credenciais do SDK
const mp = new MercadoPago('TEST-1302cff9-a3d5-4291-b81a-5ddf85b8a4c0', {
          locale: 'pt-BR'
    });

    // Inicialize o checkout
    mp.checkout({
        preference: {
            id: 'mercadopago-button'
        },
        render: {
              container: '.mercadopago-label', // Indique o nome da class onde será exibido o botão de pagamento
              label: 'Pagar', // Muda o texto do botão de pagamento (opcional)
        }
    });
</script>
</html>
