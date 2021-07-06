<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title><?=$タイトル?></title>
  <?=$追加ヘッダ?>
</head>
<body>


<?=$本文?>

<script>
document.onkeydown = function (event){
    if(event.ctrlKey && event.which == 13){ //Ctrl+Enter
        event.preventDefault();
        window.open('./admin/?edit=<?=urlencode($タイトル)?>', '_blank')
    }
}
</script>
</body>
</html>