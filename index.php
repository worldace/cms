<?php

include_once './app.php';

define('TITLE', $_GET['title'] ?? '');


if(TITLE){
    $data = db('select * from マスター where タイトル = ?', TITLE, 'object');
    $data->本文 = preg_replace_callback('/【(.+)】/', fn($m)=>sprintf('<a href="./%s">%s</a>', urlencode($m[1]),$m[1]), $data->本文);
}

print isset($data) ? new template('template/content.html', $data) : new template('template/index.html');
