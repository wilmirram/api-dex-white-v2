<?php


namespace App\Utils;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileHandler
{
    public $filename;
    public $path;
    public $ext;
    public $file;

    function getFile($name)
    {
        $path = Storage::disk('public')->path($name);
        $file = file_get_contents($path);
        $file = base64_encode($file);
        $ext = explode('.', $name);
        $ext = $ext[1];
        if($ext === 'pdf'){
            $file = 'data:application/' . $ext . ';base64,' . $file;
        }else{
            $file = 'data:image/' . $ext . ';base64,' . $file;
        }
        return $file;
    }

    function writeFile($file, $type, $id, $flag = null)
    {
        $this->file = $file;
        $file = file_get_contents(str_replace(' ', '+', $this->file));
        $parts = explode(',', $this->file);
        $ext = explode(';', explode('/', $parts[0])[1])[0];
        if($flag != null){
            $filename = $flag . '.' . $ext;
        }else{
            $filename = $type . '-' . $id . '.' . $ext;
        }
        $img = base64_decode($parts[1]);
        if($flag != null){
            Storage::disk('public')->put("products/{$id}/".$filename, $img);
        }else{
            Storage::disk('public')->put($filename, $img);
        }
        return $filename;
    }

    public function write($file, $path, $filename)
    {
        $this->file = $file;
        $file = file_get_contents(str_replace(' ', '+', $this->file));
        $parts = explode(',', $this->file);
        $ext = explode(';', explode('/', $parts[0])[1])[0];

        $filename = $path.$filename.'.'.$ext;
        $img = base64_decode($parts[1]);
        if(Storage::disk('public')->put($filename, $img)) return $filename;
        return false;
    }

    function removeFile($filename)
    {
        $voucher = $filename;
        if(Storage::disk('public')->exists($voucher)){
            Storage::disk('public')->delete($voucher);
            return true;
        }else{
            return false;
        }
    }
}
