<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Utils\Message;
use Illuminate\Http\Request;

class GenresController extends Controller
{

    private $genre;

    public function __construct(Genre $genre)
    {
        $this->genre = $genre;
    }

    public function index()
    {
        return (new Message())->defaultMessage(1, 200, $this->genre->all());
    }

    public function show($id)
    {
        $genre = $this->genre->find($id);
        if($genre){
            return (new Message())->defaultMessage(1, 200, $genre);
        }else{
            return (new Message())->defaultMessage(17, 404);
        }
    }
}
