<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Portal\AdvertisingLocation;
use App\Models\Portal\Announcement;
use App\Utils\FileHandler;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    private $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function index()
    {
        $announcements = $this->announcement->all();
        $result = [];
        foreach ($announcements as $announcement){
            $imageList = $this->getImageList($announcement->ID);
            $announcement->IMAGE_LIST = $imageList ? $imageList : null;
            array_push($result, $announcement);
        }
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function show($id)
    {
        $result = $this->announcement->find($id);
        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }

        $imageList = $this->getImageList($result->ID);

        $result->IMAGE_LIST = $imageList ? $imageList : null;

        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'ADVERTISER_ID' => 'required',
            'ADVERTISING_LOCATION_ID' => 'required',
            'ADM_ID' => 'required',
            'DT_EXPIRATION' => 'required',
            'TITLE' => 'required',
            'DESCRIPTION' => 'required',
            'URL' => 'required'
        ])->validate();

        $result = $this->announcement->create($request->all());

        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }
        return (new Message())->defaultMessage(1, 200, $result);
    }

    public function update($id, Request $request)
    {
        $result = $this->announcement->find($id);
        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }

        $data = $request->all();

        try {
            $affected = DB::connection('mysql_portal')->table('ANNOUNCEMENT')
                ->where('ID', $result->ID)
                ->update($data);

            if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

            $result = $this->announcement->find($id);

            return (new Message())->defaultMessage(1, 200, $result);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
        }
    }

    public function changeStatus($id)
    {
        $result = $this->announcement->find($id);
        if (!$result){
            return (new Message())->defaultMessage(17, 404);
        }

        $status = ! $result->ACTIVE;

        try {
            $affected = DB::connection('mysql_portal')->table('ANNOUNCEMENT')
                ->where('ID', $result->ID)
                ->update(['ACTIVE' => $status]);

            if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);
        }
    }

    public function setImages($id, Request $request)
    {
        $announcement = $this->announcement->find($id);
        if ($announcement) {
            if ($request->has('ANNOUNCEMENT_IMAGE_ARRAY')){
                if ($request->ANNOUNCEMENT_IMAGE_ARRAY != 0){
                    foreach ($request->ANNOUNCEMENT_IMAGE_ARRAY as $key => $image){
                        $size = 10;
                        $seed = time();
                        $rand = substr(sha1($seed), 40 - min($size,40));
                        $file = (new FileHandler())->write($image, "announcement/{$announcement->ID}/", $key.'-'.$rand);
                    }
                }
            }

            if ($request->has('ANNOUNCEMENT_IMAGE_ARRAY_MOBILE')){
                if ($request->ANNOUNCEMENT_IMAGE_ARRAY_MOBILE != 0){
                    foreach ($request->ANNOUNCEMENT_IMAGE_ARRAY_MOBILE as $key => $image){
                        $size = 10;
                        $seed = time();
                        $rand = substr(sha1($seed), 40 - min($size,40));
                        $file = (new FileHandler())->write($image, "announcement/{$announcement->ID}/mobile/", $key.'-'.$rand);
                    }
                }
            }
            return (new Message())->defaultMessage(1, 200);
        }else {
            return (new Message())->defaultMessage(18, 404);
        }
    }

    public function removeImage($id, Request $request)
    {
        Validator::make($request->all(), [
            'file_name' => 'required'
        ])->validate();

        $news = $this->announcement->find($id);
        if ($news) {

            if (Storage::disk('public')->exists("announcement/{$id}/$request->file_name")) {
                Storage::disk('public')->delete("announcement/{$id}/$request->file_name");
                return (new Message())->defaultMessage(1, 200);
            } else {
                return response()->json(['ERROR' => 'IMAGE NOT FOUND'], 404);
            }
        }else {
            return response()->json(['ERROR' => ['DATA' => 'NEWS NOT FOUND']], 404);
        }
    }

    private function getImageList($id)
    {
        $images = array();
        $files = Storage::disk('public')->files("announcement/{$id}");
        if (count($files)){
            foreach ($files as $key => $value){
                $file = str_replace("announcement/{$id}/", '', $value);
                $images[$key] = [
                    "URL" => env('APP_URL')."/storage/announcement/{$id}/$file",
                    "FILE_NAME" => $file
                ];
            }
        }

        $mobile = Storage::disk('public')->files("announcement/{$id}/mobile");
        if (count($mobile)){
            foreach ($mobile as $key => $value){
                $file = str_replace("announcement/{$id}/mobile/", '', $value);
                $images['MOBILE'][$key] = [
                    "URL" => env('APP_URL')."/storage/announcement/{$id}/mobile/$file",
                    "FILE_NAME" => $file
                ];
            }
        }else{
            $images['MOBILE'] = null;
        }

        return $images;
    }

    public function typePerson()
    {
        $result = DB::select("SELECT ID, DESCRIPTION FROM TYPE_PERSON WHERE ACTIVE = 1");
        return (new Message())->defaultMessage(1, 200, $result);
    }
}
