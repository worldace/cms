<?php

include_once './app.php';

define('TITLE', $_GET['title'] ?? '');


if(TITLE){
    $data = db('select * from マスター where タイトル = ?', TITLE);
}

print isset($data) ? new template('template/content.html', $data[0]) : new template('template/index.html');
