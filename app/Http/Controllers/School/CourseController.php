<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\School\Course;
use App\Models\School\CoursePrice;
use App\Utils\FileHandler;
use App\Utils\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    private $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function index(Request $request)
    {
        $existed = [];

        if ($request->has('USER_ID')) {
            $query = "SELECT COURSE_ID AS ID FROM COURSE_USER WHERE USER_ID = {$request->USER_ID}";

            if ($request->has('USER_ACCOUNT_ID')) {
                $query .= " AND USER_ACCOUNT_ID = {$request->USER_ACCOUNT_ID}";
            }

            $result = DB::connection('mysql_school')->select($query);
            if(count($result)) {
               foreach ($result as $res) {
                   array_push($existed, $res->ID);
               }
            }
        }

        $courses = $this->course->whereNotIn('ID', $existed)->get();

        foreach ($courses as $course){
            $price = CoursePrice::where('COURSE_ID', $course->ID)->first();
            $course['PRICE'] = $price ? $price->PRICE : 0;
            $course['PRICE_ID'] = $price ? $price->ID : "null";
            $course['photo'] = $this->getCourseImage($course->ID) ?: null;
        }

        return (new Message())->defaultMessage(1, 200, $courses);
    }

    public function show($id)
    {
        $course = $this->course->find($id);

        if (! $course){
            return (new Message())->defaultMessage(17, 404);
        }

        $course['photo'] = $this->getCourseImage($course->ID) ?: null;

        return (new Message())->defaultMessage(1, 200, $course);
    }

    public function store($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required',
            'DESCRIPTION' => 'required',
        ])->validate();

        $data = $request->all();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $data['ADM_ID'] = $adm->ID;
        $data['ACTIVE'] = 1;

        if ($request->has('ISSUES_CERTIFICATE')){
            $data['ISSUES_CERTIFICATE'] = $request->ISSUES_CERTIFICATE;
        }

        $course = $this->course->create($data);

        if (! $course){
            return (new Message())->defaultMessage(17, 404);
        }
        return (new Message())->defaultMessage(1, 200, $course);
    }

    public function update($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required',
            'DESCRIPTION' => 'required',
            'ID' => 'required',
            'ISSUES_CERTIFICATE' => 'required'
        ])->validate();

        $course = $this->course->find($request->ID);

        if (! $course){
            return (new Message())->defaultMessage(17, 404);
        }

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $affected = DB::connection('mysql_school')->table('COURSE')
            ->where('ID', $course->ID)
            ->update([
                'NAME' => $request->NAME,
                'DESCRIPTION' => $request->DESCRIPTION,
                'ISSUES_CERTIFICATE' => $request->ISSUES_CERTIFICATE,
                'ADM_ID' => $adm->ID,
            ]);

        if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

        return (new Message())->defaultMessage(1, 200);
    }

    public function changeStatus($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'ID' => 'required'
        ])->validate();

        $course = $this->course->find($request->ID);

        if (! $course){
            return (new Message())->defaultMessage(17, 404);
        }

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $affected = DB::connection('mysql_school')->table('COURSE')
            ->where('ID', $course->ID)
            ->update([
                'ACTIVE' => ! $course->ACTIVE,
                'ADM_ID' => $adm->ID,
            ]);

        if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

        return (new Message())->defaultMessage(1, 200);
    }

    public function setPicture(Request $request)
    {
        Validator::make($request->all(), [
            'IMAGE' => 'required',
            'COURSE_ID' => 'required'
        ])->validate();

        $course = $this->course->find($request->COURSE_ID);

        if (! $course){
            return (new Message())->defaultMessage(20, 404);
        }

        $file = (new FileHandler())->write($request->IMAGE, "school/courses/{$request->COURSE_ID}/", $request->COURSE_ID);

        return (new Message())->defaultMessage(1, 200);
    }

    public function removePicture($id, Request $request)
    {
        Validator::make($request->all(), [
            'file_name' => 'required'
        ])->validate();

        $course = $this->course->find($id);

        if (! $course){
            return (new Message())->defaultMessage(20, 404);
        }

        if (Storage::disk('public')->exists("school/courses/{$course->ID}/$request->file_name")) {
            Storage::disk('public')->delete("school/courses/{$course->ID}/$request->file_name");
            return (new Message())->defaultMessage(1, 200);
        } else {
            return response()->json(['ERROR' => 'IMAGE NOT FOUND'], 404);
        }
    }

    public function getCourseImage($id)
    {
        $image = [];
        $files = Storage::disk('public')->files("school/courses/{$id}/");
        if (count($files)){
            foreach ($files as $key => $value){
                $file = str_replace("school/courses/{$id}/", '', $value);
                $image = [
                    "URL" => env('APP_URL')."/storage/school/courses/{$id}/$file",
                    "FILE_NAME" => $file
                ];
            }
        }else{
            $image = false;
        }

        return $image;
    }
}
