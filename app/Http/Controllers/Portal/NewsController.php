<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\Portal\News;
use App\Utils\FileHandler;
use App\Utils\MassiveJsonConverter;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\Promise\all;

class NewsController extends Controller
{

    private $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        if (count($data)){
            $json = "'".json_encode($data)."'";
        }else{
            $json = 'NULL';
        }

        $result = [];

        $news = DB::connection('mysql_portal')->select("CALL SP_GET_NEWS_LIST_BY_FILTER({$json})");
        foreach ($news as $new){
            $imageList = $this->getImageList($new->ID);
            $new->IMAGE_LIST = $imageList ? $imageList : null;
            array_push($result, $new);
        }
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function getNews($uuid)
    {
        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $news = $this->news->all();
        $result = [];
        foreach ($news as $new){
            $imageList = $this->getImageList($new->ID);
            $new->IMAGE_LIST = $imageList ? $imageList : null;
            array_push($result, $new);
        }
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function indexEspelho(Request $request)
    {
        $data = $request->all();
        if (count($data)){
            $json = "'".json_encode($data)."'";
        }else{
            $json = 'NULL';
        }

        dd("CALL SP_GET_NEWS_LIST_BY_FILTER({$json})");
    }

    public function store(Request $request, $uuid)
    {
        Validator::make($request->all(), [
            'TITLE' => 'required',
            'SUMMARY' => 'required',
            'DESCRIPTION' => 'required',
            'IS_HIGHLIGHT' => 'required',
            'CATEGORY_NEWS_ID' => 'required',
            'TITLE_SEO' => 'required',
            'METATAGS' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $news = $this->news->create([
            'TITLE' => $request->TITLE,
            'SUMMARY' => $request->SUMMARY,
            'DESCRIPTION' => $request->DESCRIPTION,
            'IS_HIGHLIGHT' => $request->IS_HIGHLIGHT,
            'CATEGORY_NEWS_ID' => $request->CATEGORY_NEWS_ID,
            'TITLE_SEO' => $request->TITLE_SEO,
            'METATAGS' => $request->METATAGS,
            'ADM_ID' => $adm->ID
        ]);

        if (! $news) return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 400);

        return (new Message())->defaultMessage(1, 200, $news);
    }

    public function update(Request $request, $uuid)
    {
        Validator::make($request->all(), [
            'TITLE' => 'required',
            'SUMMARY' => 'required',
            'DESCRIPTION' => 'required',
            'IS_HIGHLIGHT' => 'required',
            'CATEGORY_NEWS_ID' => 'required',
            'TITLE_SEO' => 'required',
            'METATAGS' => 'required',
            'ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $news = $this->news->find($request->ID);
        if (! $news) return response()->json(['ERROR' => ['DATA' => 'NEWS NOT FOUND']], 404);

        try {

            $affected = DB::connection('mysql_portal')->table('NEWS')
                ->where('ID', $request->ID)
                ->update(
                    [
                        'TITLE' => $request->TITLE,
                        'SUMMARY' => $request->SUMMARY,
                        'DESCRIPTION' => $request->DESCRIPTION,
                        'IS_HIGHLIGHT' => (string) $request->IS_HIGHLIGHT,
                        'CATEGORY_NEWS_ID' => $request->CATEGORY_NEWS_ID,
                        'TITLE_SEO' => $request->TITLE_SEO,
                        'METATAGS' => $request->METATAGS,
                        'ADM_ID' => (string) $adm->ID
                    ]
                );

            if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
                DB::connection('mysql_portal')
                    ->select("UPDATE NEWS SET IS_HIGHLIGHT = 0
                                        WHERE ACTIVE
                                        AND ID NOT IN (
                                            SELECT ID FROM (
                                                SELECT ID FROM NEWS
                                                     WHERE IS_HIGHLIGHT
                                                     ORDER BY  DT_LAST_UPDATE DESC
                                                     LIMIT 3 )
                                                RES)"
                    );
            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
        }
    }

    public function status(Request $request, $uuid)
    {
        Validator::make($request->all(), [
            'ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $news = $this->news->find($request->ID);
        if (! $news) return response()->json(['ERROR' => ['DATA' => 'NEWS NOT FOUND']], 404);

        try {
            $status = ! $news->ACTIVE;
            $affected = DB::connection('mysql_portal')->table('NEWS')
                ->where('ID', $request->ID)
                ->update(
                    [
                        'ACTIVE' => $status,
                        'ADM_ID' => (string) $adm->ID
                    ]
                );

            if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
        }
    }

    public function setImages($id, Request $request)
    {
        Validator::make($request->all(), [
            'NEWS_IMAGE_ARRAY' => 'required'
        ])->validate();
        $news = $this->news->find($id);
        if ($news) {
            foreach ($request->NEWS_IMAGE_ARRAY as $key => $image){
                $size = 10;
                $seed = time();
                $rand = substr(sha1($seed), 40 - min($size,40));
                $file = (new FileHandler())->write($image, "news/{$news->ID}/", $key.'-'.$rand);
            }
            return (new Message())->defaultMessage(1, 200);
        }else {
            return (new Message())->defaultMessage(18, 404);
        }
    }

    private function getImageList($id)
    {
        $files = Storage::disk('public')->files("news/{$id}");
        $images = array();
        foreach ($files as $key => $value){
            $file = str_replace("news/{$id}/", '', $value);
            $images[$key] = [
                "URL" => env('APP_URL')."/storage/news/{$id}/$file",
                "FILE_NAME" => $file
            ];
        }
        return $images;
    }

    public function getNewsImageList($id)
    {
        $news = $this->news->find($id);
        if ($news) {
            $images = $this->getImageList($id);
            return (new Message())->defaultMessage(1, 200, $images);
        }else {
            return response()->json(['ERROR' => ['DATA' => 'NEWS NOT FOUND']], 404);
        }
    }

    public function removeImage($id, Request $request)
    {
        Validator::make($request->all(), [
            'file_name' => 'required'
        ])->validate();

        $news = $this->news->find($id);
        if ($news) {

            if (Storage::disk('public')->exists("news/{$id}/$request->file_name")) {
                Storage::disk('public')->delete("news/{$id}/$request->file_name");
                return (new Message())->defaultMessage(1, 200);
            } else {
                return response()->json(['ERROR' => 'IMAGE NOT FOUND'], 404);
            }
        }else {
            return response()->json(['ERROR' => ['DATA' => 'NEWS NOT FOUND']], 404);
        }
    }
}
