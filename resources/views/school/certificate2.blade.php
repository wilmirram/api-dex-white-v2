<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>{{$data['course_name']}}</title>

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
            background-image: url(<?php echo $data['certificate'] ?>);
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
</head>
<body >
<div class="container-fluid px-0" style="height: 500px;">
    <div style="position: relative;text-align: center"
         class="row h-100 justify-content-center text-center position-relative m-0">
        <img class="logo" src="{{$data['logo']}}">

        <div class="col-12 text-block align-self-center">
            <p class="text-center mb-0" style="font-size: 24px">This is to certify that <span class="font-weight-bold" style="font-family: DejaVu Sans;">{{$data['name']}}</span> successfully completed
            </p>
            <p style="word-wrap: break-word;white-space: nowrap; font-size: 24px"><span class="font-weight-bold" style="font-family: DejaVu Sans;">{{$data['course_name']}}</span>
            </p>
            <p  style="word-wrap: break-word;white-space: nowrap; font-size: 24px">on <span class="font-weight-bold" style="font-family: DejaVu Sans;">WHITE CLUB</span> online course on <span class="font-weight-bold">{{$data['date']}}</span></p>
        </div>
    </div>
</div>
</body>
</html>
