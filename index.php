<?php
function h($str){
	return htmlspecialchars($str,ENT_QUOTES,'utf-8');
}

session_start();

$name = (string)filter_input(INPUT_POST,'name');
$text = (string)filter_input(INPUT_POST,'text');
$token = (string)filter_input(INPUT_POST,'token');

// $fp = fopen('data.csv','a+b');
// if($_SERVER['REQUEST_METHOD'] === 'POST' && sha1(session_id())=== $token){
// 	//ファイル書き込み前に排他ロック
// 	flock($fp,LOCK_EX);
// 	//ファイル書き込み
// 	fputcsv($fp,[$name,$text]);
// 	//ポインタを先頭に
// 	rewind($fp);
// }
// //共有ロックを実行or切り替え
// flock($fp,LOCK_SH);
// while($row = fgetcsv($fp)){
// 	$rows[] = $row;
// }
// //ファイル操作が終了したのでロック解除
// flock($fp,LOCK_UN);
// fclose($fp);

//json
//cモードは任意の位置から書き込み可能
$fp = fopen('data.json','c+b');
//jsonファイルの中身を全て読み込む（配列として）
$rows = (array)json_decode(stream_get_contents($fp),true);
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	//ファイルから読み込んだ変数に今回POSTされた分を追加
	$rows[]=['name'=>$_POST['name'],'text'=>$_POST['text']];
	rewind($fp);
	//ファイル先頭からファイルに書き込み
	fwrite($fp,json_encode($rows,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
	//今回書き込んだ分より前に書き込まれていた分を削除
	ftruncate($fp,ftell($fp));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>掲示板</title>
</head>
<body>
<h1>掲示板</h1>
<section>
	<h2>新規投稿</h2>
	<form action="" method="post">
		名前：<input type="text" name="name" value=""><br>
		本文：<input type="text" name="text" value=""><br>
		<input type="submit" value="投稿">
		<input type="hidden" name="token" value="<?=h(sha1(session_id())) ?>">
	</form>
</section>
<section>
	<h2>投稿一覧</h2>
	<?php if(!empty($rows)): ?>
		<ul>
	<?php foreach ($rows as $row): ?>
			<li><?=$row['name']?>(<?=$row['text']?>)</li>
	<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p>投稿はまだありません</p>
	<?php endif; ?>
</section>
</body>
</html>