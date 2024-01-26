<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Controle de Produtos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand ml-4" href="{{route('vs.product-control')}}">Product Control - STORE</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
        </ul>
        <form class="form-inline my-2 my-lg-0" method="POST" action="{{route('vs.search-product')}}">
            @csrf
            <label for="exampleFormControlInput1">Search Product:</label>
            <input class="form-control mr-sm-2 ml-4" name="PRODUCT_ID" type="search" placeholder="Type ID" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Find</button>
        </form>
    </div>
</nav>
<div class="container mt-4">
    <form method="POST" action="{{route('vs.save-product-image')}}">
        @csrf
        <div class="form-group">
            <label for="exampleFormControlInput1">Product ID</label>
            <input type="text" class="form-control" id="exampleFormControlInput1" name="PRODUCT_ID">
        </div>
        <div class="form-group">
            <label for="exampleFormControlTextarea1">Product Base64 Image</label>
            <textarea class="form-control" name="PRODUCT_IMAGE" id="exampleFormControlTextarea1" rows="6"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
</body>
</html>
