<?php

include_once '../app.php';


jrpc('API');


class API{
    function DB(string $sql, $param = [], $return = null){
        return db($sql, $param, $return);
    }


    function FileSave(string $filename, string $data){
        $ym   = date('Ym');
        $dir  = UPLOAD_DIR . $ym;
        $ext  = pathinfo($filename, PATHINFO_EXTENSION);
        $file = $ext ? uniqid() . ".$ext" : uniqid();

        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        file_put_contents("$dir/$file", $data);

        return UPLOAD_URL."$ym/$file";
    }


    function FileDelete(string $path){
        if(str_contains($path, '..')){
            throw new Exception('不正なパス');
        }
        unlink(UPLOAD_DIR.$path);
    }


    function FileList(string $path){
        $dir = UPLOAD_DIR.$path;

        if(str_contains($path, '..')){
            throw new Exception('不正なパス');
        }
        if(!is_dir($dir)){
            throw new Exception('ディレクトリが存在しません');
        }

        return array_values(array_filter(scandir($dir), fn($file)=>is_file("$dir/$file") and !str_starts_with($file,'.')));
    }


    function DirList(string $path = './'){
        $dir = UPLOAD_DIR.$path;

        if(str_contains($path, '..')){
            throw new Exception('不正なパス');
        }
        if(!is_dir($dir)){
            throw new Exception('ディレクトリが存在しません');
        }

        return array_values(array_filter(scandir($dir), fn($file)=>is_dir("$dir/$file") and !str_starts_with($file,'.')));
    }
}


