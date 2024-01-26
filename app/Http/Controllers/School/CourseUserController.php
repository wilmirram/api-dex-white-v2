<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School\Course;
use App\Models\School\CourseUser;
use App\Models\User;
use App\Utils\Message;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseUserController extends Controller
{
    private $cu;

    public function __construct(CourseUser $cu)
    {
        $this->cu = $cu;
    }

    public function showCourses(Request $request)
    {
        Validator::make($request->all(), [
            'USER_ID' => 'required',
            'USER_ACCOUNT_ID' => 'required'
        ])->validate();

        $user = User::find($request->USER_ID);

        if (! $user){
            return (new Message())->defaultMessage(17, 404);
        }

        $query = "SELECT CU.*
                  FROM COURSE_USER CU
                  WHERE CU.USER_ID = {$request->USER_ID}";

        if ($request->USER_ACCOUNT_ID != 0){
            $query .= " AND CU.USER_ACCOUNT_ID = {$request->USER_ACCOUNT_ID}";
        }

        $this->getPercentage(2, 475, 1844);

        $result = DB::connection('mysql_school')->select($query);

        $courses = [];

        foreach ($result as $key => $course){
            $courseValue = Course::find($course->COURSE_ID);
            $course->NAME = $courseValue->NAME;
            $course->DESCRIPTION = $courseValue->DESCRIPTION;
            $course->photo =  app(CourseController::class)->getCourseImage($course->COURSE_ID) ?: null;
            $course->percentage = $this->getPercentage($course->COURSE_ID, $request->USER_ID, $request->USER_ACCOUNT_ID);
            array_push($courses, $course);
        }

        return (new Message())->defaultMessage(1, 200, $courses);

    }

    public function generateCertificate(Request $request)
    {
        Validator::make($request->all(), [
            'COURSE_USER_ID' => 'required'
        ])->validate();

        $cu = $this->cu->find($request->COURSE_USER_ID);

        if (! $cu){
            return (new Message())->defaultMessage(17, 404);
        }

        if ($cu->STATUS_ID != 2){
            return response()->json(['ERROR' => 'THIS COURSE HAS NOT YET BEEN COMPLETED'], 400);
        }

        $data = [];

        $user = User::find($cu->USER_ID);
        $course = Course::find($cu->COURSE_ID);

        $data['name'] = $user->NAME;
        $data['course_name'] = $course->NAME;

        $dateCreate = date_create($cu->DT_END);
        $data['date'] = date_format($dateCreate,"d/m/Y");;

        $certificateFile = file_get_contents(public_path('/school/certificates/certificate2.jpg'));
        $certificate = base64_encode($certificateFile);
        $certificate = 'data:image/jpg;base64,' . $certificate;
        $data['certificate'] = $certificate;

        //$logoFile = file_get_contents(public_path('/school/logos/1634151809-logoschoolbranco.png'));
        $logoFile = file_get_contents(public_path('/school/logos/logo-black-text.png'));
        $logo = base64_encode($logoFile);
        $logo = 'data:image/png;base64,' . $logo;
        $data['logo'] = $logo;

        $pdf = PDF::loadView('school.certificate2', compact('data'))->setPaper('a4', 'landscape');
        return $pdf->download($user->NAME.' - '.$course->NAME.'.pdf');
    }

    private function getPercentage($courseId, $userId, $userAccountId = 0)
    {
        try {
            $query = "SELECT ID, STATUS_ID FROM COURSE_CLASS_USER WHERE COURSE_ID = {$courseId} AND USER_ID = {$userId}";

            if ($userAccountId != 0){
                $query .= " AND USER_ACCOUNT_ID = {$userAccountId}";
            }

            $result = DB::connection('mysql_school')->select($query);

            $total = count($result);

            $watched = array_filter($result, function($value){
               if ($value->STATUS_ID == 2){
                   return $value;
               }
            });

            $watched = count($watched);

            return ($watched * 100) / $total;
        }catch (\Exception $exception){
            return 0;
        }
    }
}
