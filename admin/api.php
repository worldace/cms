<?php

include_once '../app.php';


jrpc('api');


class api{
    function db(string $sql, $param = []){
        $sqlite = new PDO('sqlite:./test.db');
        $handle = $sqlite->prepare($sql);
        $result = $handle->execute((array)$param);

        return preg_match('/^select/i', $sql) ? $handle->fetchAll(PDO::FETCH_OBJ) : $result;
    }


    function file_save(string $path, string $data){
        ['dirname'=>$dir, 'basename'=>$name] = pathinfo(UPLOAD_DIR.$path);

        if(str_contains($path, '..')){
            throw new Exception('不正なパス');
        }
        if(!is_dir($dir)){
            mkdir($dir, 0777, true);
        }

        file_put_contents("$dir/$name", $data);

        return UPLOAD_URL.$path;
    }


    function file_list(string $path){
        $dir = UPLOAD_DIR.$path;

        if(str_contains($path, '..')){
            throw new Exception('不正なパス');
        }
        if(!is_dir($dir)){
            throw new Exception('ディレクトリが存在しません');
        }

        return array_values(array_filter(scandir($dir), fn($file)=>is_file("$dir/$file") and !str_starts_with($file,'.')));
    }


    function dir_list(string $path = './'){
        $dir = UPLOAD_DIR.$path;

        if(str_contains($path, '..')){
            throw new Exception('不正なパス');
        }
        if(!is_dir($dir)){
            throw new Exception('ディレクトリが存在しません');
        }

        return array_values(array_filter(scandir($dir), fn($file)=>is_dir("$dir/$file") and !str_starts_with($file,'.')));
    }


    function file_delete(string $path){
        if(str_contains($path, '..')){
            throw new Exception('不正なパス');
        }
        unlink(UPLOAD_DIR.$path);
    }
}


