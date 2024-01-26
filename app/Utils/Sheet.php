<?php

namespace App\Utils;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\File;

abstract class Sheet
{
    protected $mimeType;
    protected $file;
    protected $filename;
    protected $readAble;
    protected $inArray;

    public function __construct(UploadedFile $spreadSheet)
    {
        $fileOptions = explode('.', $spreadSheet->getClientOriginalName());
        $this->filename = $fileOptions[0];
        $this->mimeType = ucfirst($fileOptions[1]);
        $this->file = $spreadSheet;
        $this->loadSheet();
    }

    public abstract function render($param = null);

    private function loadSheet()
    {
        $reader = IOFactory::createReader($this->mimeType);
        $spreadsheet = $reader->load($this->file);
        $this->readAble = $spreadsheet;
    }

    public function readAble()
    {
        $this->readAble->getActiveSheet();
        return $this->readAble;
    }

    public function array()
    {
        $sheet = $this->readAble->getActiveSheet();
        $this->inArray = $sheet->toArray();
        return $this->inArray;
    }

    public function writeFile($path, $filename)
    {
        $filename = $filename.'.'.strtolower($this->mimeType);

        $dir = $_SERVER['DOCUMENT_ROOT'].'/storage/'.$path.'/';

        if (!file_exists($dir)){
            File::makeDirectory($dir);
        }

        $writer = IOFactory::createWriter($this->readAble, $this->mimeType);
        $writer->save($dir.$filename);
        if (file_exists($dir.$filename)) return true;
        return false;
    }

    public function removeFile($path, $filename)
    {
        if(!Storage::disk('public')->exists($path.'/'.$filename.'.'.$this->mimeType)) return true;
        if(!Storage::disk('public')->delete($path.'/'.$filename.'.'.$this->mimeType)) return false;
        return true;
    }

    abstract protected function verifyIntegrity();
}
