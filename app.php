<?php

const URL        = 'http://127.0.0.1/cms/';
const PATH       = __DIR__.'/';
const DB_FILE    = PATH.'admin/test.db';
const UPLOAD_DIR = PATH.'upload/';
const UPLOAD_URL = URL.'upload/';
const ID = ['admin'=>'neko'];


function db(string $sql, $param = [], $return = null){
    $sqlite = new PDO('sqlite:'.DB_FILE);
    $handle = $sqlite->prepare($sql);
    $result = $handle->execute((array)$param);

    return match($return){
        'table'  => $handle->fetchAll(PDO::FETCH_OBJ),
        'array'  => $handle->fetchAll(PDO::FETCH_COLUMN),
        'object' => $handle->fetch(PDO::FETCH_OBJ),
        'var'    => $handle->fetchColumn(),
        default  => $result,
    };
}


function jrpc(string $class) :void{
    try{
        $json = json_decode(file_get_contents('php://input'));

        if(preg_match('/^__/', $json->fn)){
            throw new Exception('不正なアクセスです。');
        }

        foreach($json->base64 as $i){
            $json->args[$i] = base64_decode($json->args[$i]);
        }

        $result = [new $class, $json->fn](...$json->args);

        header('Content-Type: application/json; charset=UTF-8');
        print json_encode($result);
    }
    catch(Throwable $e){
        header('Content-Type: text/plain; charset=UTF-8');
        print $e->getMessage();
    }
}


class template{
    private $tempLate;

    function __construct($template, $assign = null){
        $this->tempLate = $template;
        if($assign){
            foreach($assign as $k => $v) $this->$k = $v;
        }
    }

    function __toString(){
        extract((array)$this);
        ob_start();
        include $this->cache();
        return ob_get_clean();
    }

    function cache(){
        $cache = "$this->tempLate.php";
        $mtime = filemtime($this->tempLate);

        if($mtime and $mtime !== @filemtime($cache)){
            file_put_contents($cache, $this->compile(), LOCK_EX);
            touch($cache, $mtime);
        }
        return $cache;
    }

    function compile(){
        $re = ['/(\{\{)(.+?)\}\}/s', '/<(if|elseif|else|foreach|\/if|\/foreach)( code="(.+?)")*>/s'];
        return preg_replace_callback($re, 'self::callback', file_get_contents($this->tempLate));
    }

    private static function callback($m){
        switch($m[1]){
            case 'if'      : return "<?php if($m[3]){ ?>";
            case 'elseif'  : return "<?php } elseif($m[3]){ ?>";
            case 'else'    : return "<?php } else{ ?>";
            case 'foreach' : return "<?php foreach($m[3]){ ?>";
            case '/if'     : return '<?php } ?>';
            case '/foreach': return '<?php } ?>';
            default        : return '<?='.trim($m[2]).'?>';
        }
    }
}