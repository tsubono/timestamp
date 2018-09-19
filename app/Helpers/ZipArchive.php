<?php
/**
 * Created by PhpStorm.
 * User: mkkn
 * Date: 15/07/03
 * Time: 15:48
 */

namespace App\Helpers;

class ZipArchive {

    /** @type resource  */
    protected $fp;

    /** @type \ZipArchive  */
    protected $zip;

    public $fpCache = [];

    function __construct()
    {
        $zip = new \ZipArchive();
        $this->fp = tmpfile();
        $path = stream_get_meta_data($this->fp)["uri"];
        if($zip->open($path,\ZipArchive::CREATE)){
            $this->zip = $zip;
        }else{
            throw new \Exception("fail to open zip archive");
        }
    }

    public function addFile($filePath,$fileName){
        $this->zip->addFile($filePath,$fileName);
    }

    public function addExcel(\PHPExcel $excel,$fileName){
        $fp = tmpfile();
        $path = stream_get_meta_data($fp)["uri"];

        // tmpリソースにexcelを書き出し
        $writer = \PHPExcel_IOFactory::createWriter($excel,'Excel5');
        $writer->save($path);

        $this->fpCache[] = $fp; // 参照カウント保持のため、キャッシュに格納
        $this->zip->addFile($path,$fileName);
    }

    public function output(){
        $path = stream_get_meta_data($this->fp)["uri"];
        $this->zip->close();
        return file_get_contents($path);

    }
    public function getPath(){
        $path = stream_get_meta_data($this->fp)["uri"];
        $this->zip->close();
        return $path;
    }
}