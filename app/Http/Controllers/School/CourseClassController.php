<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Adm;
use App\Models\School\Course;
use App\Models\School\CourseClass;
use App\Models\School\CourseClassUser;
use App\Models\User;
use App\Models\UserAccount;
use App\Utils\FileHandler;
use App\Utils\Message;
use App\Utils\SqlHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseClassController extends Controller
{
    private $class;

    public function __construct(CourseClass $class)
    {
        $this->class = $class;
    }

    public function index($courseId)
    {
        $classes = $this->class->where('COURSE_ID', $courseId)->get();
        foreach ($classes as $key => $class){
            $classes[$key]['photo'] = $this->getClassImage($class) ?: null;
            $classes[$key]['attachments'] = $this->getClassAttachments($class) ?: null;
        }
        return (new Message())->defaultMessage(1, 200, $classes);
    }

    public function getClasses($courseId)
    {
        $classes = $this->class->where('COURSE_ID', $courseId)->where('ACTIVE', 1)->get();
        foreach ($classes as $key => $class){
            $classes[$key]['photo'] = $this->getClassImage($class) ?: null;
        }
        return (new Message())->defaultMessage(1, 200, $classes);
    }

    public function getStudentClasses(Request $request)
    {
        Validator::make($request->all(), [
            'P_USER_ID' => 'required',
            'P_USER_ACCOUNT_ID' => 'required',
            'P_COURSE_ID' => 'required'
        ])->validate();
        $db = env('DB_DATABASE_SCHOOL');

        $classes = DB::connection('mysql_school')->select("
            SELECT CCU.ID AS COURSE_CLASS_USER_ID,
                   CCU.COURSE_ID,
                   CCU.COURSE_CLASS_ID,
                   CCU.USER_ID,
                   CCU.USER_ACCOUNT_ID,
                   CCU.DT_START,
                   CCU.DT_END,
                   CCU.STATUS_ID,
                   CCU.ACTIVE,
                   CCU.DT_REGISTER,
                   CC.*
                   FROM {$db}.COURSE_CLASS_USER CCU
                   JOIN {$db}.COURSE_CLASS CC
                   ON CCU.COURSE_ID = CC.COURSE_ID
                   AND CCU.COURSE_CLASS_ID = CC.ID
                   WHERE CCU.USER_ID = {$request->P_USER_ID}
                   AND CCU.USER_ACCOUNT_ID = (IF( {$request->P_USER_ACCOUNT_ID} = 0, NULL,{$request->P_USER_ACCOUNT_ID}))
                   AND CCU.COURSE_ID = {$request->P_COURSE_ID}
                   ORDER BY CCU.COURSE_ID, CC.SEQ"
        );

        if (count($classes)){
            foreach ($classes as $key => $class){
                $classes[$key]->photo = $this->getClassImage($class) ?: null;
                $classes[$key]->attachments = $this->getClassAttachments($class) ?: null;
            }
        }

        return (new Message())->defaultMessage(1, 200, $classes);
    }

    public function show($id)
    {
        $class = $this->class->find($id);

        if (! $class){
            return (new Message())->defaultMessage(17, 404);
        }

        $class['photo'] = $this->getClassImage($class) ?: null;
        $class['attachments'] = $this->getClassAttachments($class) ?: null;

        return (new Message())->defaultMessage(1, 200, $class);
    }

    public function store($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required',
            'COURSE_ID' => 'required',
            'SEQ' => 'required',
            'DESCRIPTION' => 'required',
            'URL' => 'required',
            'DURATION_TIME' => 'required'
        ])->validate();

        $data = $request->all();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $data['ADM_ID'] = $adm->ID;
        $data['ACTIVE'] = 1;

        $seqClass = $this->class->where('COURSE_ID', $request->COURSE_ID)->where('SEQ', $request->SEQ)->first();

        if ($seqClass){
            return response()->json(
                ['ERROR' => 'SEQUENCIA JA CADASTRADA NESSE CURSO, VERIFIQUE A ORDEM DAS AULAS E TENTE NOVAMENTE'],
                400);
        }

        $class = $this->class->create($data);

        if (! $class){
            return (new Message())->defaultMessage(17, 404);
        }
        return (new Message())->defaultMessage(1, 200, $class);
    }

    public function update($uuid, Request $request)
    {
        Validator::make($request->all(), [
            'NAME' => 'required',
            'COURSE_ID' => 'required',
            'DESCRIPTION' => 'required',
            'URL' => 'required',
            'SEQ' => 'required',
            'DURATION_TIME' => 'required',
            'ID' => 'required'
        ])->validate();

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $class = $this->class->find($request->ID);

        if (! $class){
            return (new Message())->defaultMessage(17, 404);
        }

        $affected = DB::connection('mysql_school')->table('COURSE_CLASS')
            ->where('ID', $class->ID)
            ->update([
                'NAME' => $request->NAME,
                'COURSE_ID' => $request->COURSE_ID,
                'URL' => $request->URL,
                'SEQ' => $request->SEQ,
                'DURATION_TIME' => $request->DURATION_TIME,
                'DESCRIPTION' => $request->DESCRIPTION,
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

        $class = $this->class->find($request->ID);

        if (! $class){
            return (new Message())->defaultMessage(17, 404);
        }

        $adm = Adm::where('UUID', $uuid)->first();
        if(! $adm) return (new Message())->defaultMessage(27, 404);

        $affected = DB::connection('mysql_school')->table('COURSE_CLASS')
            ->where('ID', $class->ID)
            ->update([
                'ACTIVE' => ! $class->ACTIVE,
                'ADM_ID' => $adm->ID,
            ]);

        if (! $affected)  return response()->json(['ERROR' => ['DATA' => 'TRY AGAIN OR CONTACT SUPPORT']], 404);

        return (new Message())->defaultMessage(1, 200);
    }

    public function setPicture(Request $request)
    {
        Validator::make($request->all(), [
            'IMAGE' => 'required',
            'COURSE_CLASS_ID' => 'required'
        ])->validate();

        $class = $this->class->find($request->COURSE_CLASS_ID);

        if (! $class){
            return (new Message())->defaultMessage(20, 404);
        }

        $files = Storage::disk('public')->files("school/courses/{$class->COURSE_ID}/class/{$class->ID}/photo/");

        if (count($files)){
            foreach ($files as $img){
                Storage::disk('public')->delete($img);
            }
        }

        $size = 10;
        $seed = time();
        $rand = substr(sha1($seed), 40 - min($size,40));

        $file = (new FileHandler())->write($request->IMAGE, "school/courses/{$class->COURSE_ID}/class/{$class->ID}/photo/", $rand);

        return (new Message())->defaultMessage(1, 200);
    }

    public function removePicture($id, Request $request)
    {
        Validator::make($request->all(), [
            'file_name' => 'required'
        ])->validate();

        $class = $this->class->find($id);

        if (! $class){
            return (new Message())->defaultMessage(20, 404);
        }

        if (Storage::disk('public')->exists("school/courses/{$class->COURSE_ID}/class/{$class->ID}/photo/$request->file_name")) {
            Storage::disk('public')->delete("school/courses/{$class->COURSE_ID}/class/{$class->ID}/photo/$request->file_name");
            return (new Message())->defaultMessage(1, 200);
        } else {
            return response()->json(['ERROR' => 'IMAGE NOT FOUND'], 404);
        }
    }

    private function getClassImage($class)
    {
        $image = [];
        $files = Storage::disk('public')->files("school/courses/{$class->COURSE_ID}/class/{$class->ID}/photo/");
        if (count($files)){
            foreach ($files as $key => $value){
                $file = str_replace("school/courses/{$class->COURSE_ID}/class/{$class->ID}/photo/", '', $value);
                $image[] = [
                    "URL" => env('APP_URL')."/storage/school/courses/{$class->COURSE_ID}/class/{$class->ID}/photo/$file",
                    "FILE_NAME" => $file
                ];
            }
        }else{
            $image = false;
        }

        return $image;
    }

    public function setAttachment(Request $request)
    {
        Validator::make($request->all(), [
            'ATTACHMENT' => 'required',
            'COURSE_CLASS_ID' => 'required',
            'FILE_NAME' => 'required'
        ])->validate();

        $class = $this->class->find($request->COURSE_CLASS_ID);

        if (! $class){
            return (new Message())->defaultMessage(20, 404);
        }

        $file = (new FileHandler())->write($request->ATTACHMENT, "school/courses/{$class->COURSE_ID}/class/{$class->ID}/attachment/", $request->FILE_NAME);

        return (new Message())->defaultMessage(1, 200);
    }


    public function removeAttachment($id, Request $request)
    {
        Validator::make($request->all(), [
            'file_name' => 'required'
        ])->validate();

        $class = $this->class->find($id);

        if (! $class){
            return (new Message())->defaultMessage(20, 404);
        }

        if (Storage::disk('public')->exists("school/courses/{$class->COURSE_ID}/class/{$class->ID}/attachment/$request->file_name")) {
            Storage::disk('public')->delete("school/courses/{$class->COURSE_ID}/class/{$class->ID}/attachment/$request->file_name");
            return (new Message())->defaultMessage(1, 200);
        } else {
            return response()->json(['ERROR' => 'IMAGE NOT FOUND'], 404);
        }
    }

    private function getClassAttachments($class)
    {
        $image = [];
        $files = Storage::disk('public')->files("school/courses/{$class->COURSE_ID}/class/{$class->ID}/attachment/");
        if (count($files)){
            foreach ($files as $key => $value){
                $file = str_replace("school/courses/{$class->COURSE_ID}/class/{$class->ID}/attachment/", '', $value);
                $image[$key] = [
                    "URL" => env('APP_URL')."/storage/school/courses/{$class->COURSE_ID}/class/{$class->ID}/attachment/$file",
                    "FILE_NAME" => $file
                ];
            }
        }else{
            $image = false;
        }

        return $image;
    }

    public function startClass(Request $request)
    {
        Validator::make($request->all(), [
            'P_COURSE_CLASS_USER_ID' => 'required'
        ])->validate();

        $ccu = CourseClassUser::find($request->P_COURSE_CLASS_USER_ID);

        if (! $ccu) {
            return (new Message())->defaultMessage(17, 404);
        }

        if ($ccu->DT_START && $ccu->STATUS_ID == 1){
            return response()->json(['SUCCESS' => 'THIS CLASS ALREADY STARTED'], 202);
        }

        try {
            DB::connection('mysql_school')->beginTransaction();
            $query = "SELECT ID, STATUS_ID FROM COURSE_USER WHERE COURSE_ID = {$ccu->COURSE_ID} AND USER_ID = {$ccu->USER_ID}";

            if ($ccu->USER_ACCOUNT_ID){
                $query .= " AND USER_ACCOUNT_ID = {$ccu->USER_ACCOUNT_ID}";
            }

            $courseUser = DB::connection('mysql_school')->select($query);

            if (count($courseUser)){
                $courseUser = $courseUser[0];
                if ($courseUser->STATUS_ID == 3){
                    DB::connection('mysql_school')->select("UPDATE COURSE_USER SET STATUS_ID = 1 WHERE ID = {$courseUser->ID}");
                }

                DB::connection('mysql_school')->select("UPDATE COURSE_USER SET COURSE_CLASS_USER_ID = {$ccu->ID} WHERE ID = {$courseUser->ID}");
            }

            if (! $ccu->DT_START){
                DB::connection('mysql_school')->select("UPDATE COURSE_CLASS_USER SET STATUS_ID = 1, DT_START = NOW() WHERE ID = {$ccu->ID}");
            }

            DB::connection('mysql_school')->commit();

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            DB::connection('mysql_school')->rollBack();
            return response()->json(['ERROR' => 'ERROR STARTING THE CLASS, TRY AGAIN'], 400);
        }
    }

    public function finishClass(Request $request)
    {
        Validator::make($request->all(), [
            'P_COURSE_CLASS_USER_ID' => 'required'
        ])->validate();

        $ccu = CourseClassUser::find($request->P_COURSE_CLASS_USER_ID);

        if (! $ccu) {
            return (new Message())->defaultMessage(17, 404);
        }

        if ($ccu->DT_END && $ccu->STATUS_ID == 2){
            return response()->json(['SUCCESS' => 'THIS CLASS ALREADY FINISHED'], 202);
        }

        if ($ccu->STATUS_ID != 1){
            return response()->json(['ERROR' => 'YOU CANNOT FINISH A CLASS THATs NOT STARTED'], 400);
        }

        try {
            DB::connection('mysql_school')->beginTransaction();

            DB::connection('mysql_school')->select("UPDATE COURSE_CLASS_USER SET STATUS_ID = 2, DT_END = NOW() WHERE ID = {$ccu->ID}");

            $query = "SELECT count(ID) as number FROM COURSE_CLASS_USER WHERE COURSE_ID = {$ccu->COURSE_ID} AND USER_ID = {$ccu->USER_ID}";

            if ($ccu->USER_ACCOUNT_ID){
                $query .= " AND USER_ACCOUNT_ID = {$ccu->USER_ACCOUNT_ID}";
            }

            $qnt = DB::connection('mysql_school')->select($query);

            $query .= " AND STATUS_ID = 2";

            $total = DB::connection('mysql_school')->select($query);

            if (count($qnt)) {
                $qnt = $qnt[0]->number;
            }else{
                $qnt = 0;
            }

            if (count($total)) {
                $total = $total[0]->number;
            }else{
                $total = 0;
            }

            if ($qnt == $total){
                $query = "SELECT ID, STATUS_ID FROM COURSE_USER WHERE COURSE_ID = {$ccu->COURSE_ID} AND USER_ID = {$ccu->USER_ID}";

                if ($ccu->USER_ACCOUNT_ID){
                    $query .= " AND USER_ACCOUNT_ID = {$ccu->USER_ACCOUNT_ID}";
                }

                $courseUser = DB::connection('mysql_school')->select($query);
                if (count($courseUser)){
                    $courseUser = $courseUser[0];
                    if ($courseUser->STATUS_ID == 1){
                        DB::connection('mysql_school')->select("UPDATE COURSE_USER SET STATUS_ID = 2, DT_END = NOW() WHERE ID = {$courseUser->ID}");
                    }
                }
            }

            DB::connection('mysql_school')->commit();

            return (new Message())->defaultMessage(1, 200);
        }catch (\Exception $e){
            DB::connection('mysql_school')->rollBack();
            return response()->json(['ERROR' => 'ERROR STARTING THE CLASS, TRY AGAIN'], 400);
        }
    }
}
