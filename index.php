<?php
function h($str){
	return htmlspecialchars($str,ENT_QUOTES,'utf-8');
}
$name = (string)filter_input(INPUT_POST,'name');
$text = (string)filter_input(INPUT_POST,'text');
$token = (string)filter_input(INPUT_POST,'token');

$fp = fopen('data.csv','a+b');
if($_SERVER['REQUEST_METHOD'] === 'POST' && sha1(session_id())=== $token){
	//ファイル書き込み前に排他ロック
	flock($fp,LOCK_EX);
	fputcsv($fp,[$name,$text]);
	//ポインタを先頭に
	rewind($fp);
}
//共有ロックを実行or切り替え
flock($fp,LOCK_SH);
while($row = fgetcsv($fp)){
	$rows[] = $row;
}
flock($fp,LOCK_UN);
fclose($fp);
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
			<li><?=$row[1]?>(<?=$row[0]?>)</li>
	<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<p>投稿はまだありません</p>
	<?php endif; ?>
</section>
</body>
</html>