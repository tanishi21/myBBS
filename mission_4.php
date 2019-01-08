<?php  // 変数定義

// 全体
$name = $_POST["name"];
$comment = $_POST["comment"];

// 削除用変数、
$del_index = $_POST["del_index"];

// submit
$toukou_submit = $_POST["toukou_submit"];
$del_submit = $_POST["del_submit"];
$update_submit = $_POST["update_submit"];

// MySQL編集用
$update_name = $_POST["update_name"];
$update_comment = $_POST["update_comment"];
$update_index = $_POST["update_index"];

// 未入力時のエラーメッセージ
$error_name = "※名前を入力してください<br>";
$error_comment = "※コメントを入力してください<br>";
$error_both = "※名前とコメントを入力してください<br>";
$error_edit = "※編集せずに最初のページに戻りました<br>";

// パスワード
$input_password = $_POST["input_password"];
$correct_password = "mission";//正しいパスワード

$main_message = ""; //最上部に表示するメッセージ（エラーや挨拶など、page_flag内で変更する）	
$error_message = ""; //空の変数。page_flag内で適したエラーを代入する

// 未入力時のエラーメッセージ
$error_name = "※名前を入力してください<br>";
$error_comment = "※コメントを入力してください<br>";
$error_both = "※名前とコメントを入力してください<br>";
$error_edit = "※編集せずに最初のページに戻りました<br>";
$error_pass = "※パスワードが違います";
$error_empty_pass = "※パスワードを入力してください";

$del_data = $_POST["del_data"]; //全データ削除ボタン
$del_table = $_POST["del_table"];

// データベースに接続
$dsn = "database";
$host = "host";
$username = "user";
$pass = "password";
$pdo = new PDO($dsn , $username , $pass , array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS mission4_bbs"
."("
."id INT NOT NULL ," //AUTO INCREMENT
."name char(32),"
."comment TEXT,"
."submittime TEXT,"
."PRIMARY KEY(id)"
.");";
$stmt = $pdo->query($sql); 
// var_dump($stmt);
// echo "<hr>";

// $sql = "SHOW CREATE TABLE mission_test"; //作ったテーブルの中身を確認
// $results = $pdo->query($sql); 
// foreach ($results as $row) {
// 	echo $row[1]."<br>";
// }
// echo "<hr>";
?>

<!DOCTYPE html>
<html>
<head>
	<title>mission4</title>
	<meta charset="utf-8">
</head>
<body>
<?php
$submittime = date("Y/m/d H:i:s"); //投稿時刻
// echo "現在時刻は".$submittime."<br>";
// page_flag & switch（投稿・削除・編集・編集後・データ削除）

// パスワードが入力されて、かつ正しい場合
if(isset($input_password) && $input_password == $correct_password){
	$main_message =  "correct_passwordです";
	if (isset($toukou_submit)) {
		$page_flag = "toukou";
	}elseif (isset($del_submit)) {
		$page_flag = "delete";
	}elseif (isset($update_submit)) {
		$page_flag = "update";
	}elseif(isset($del_data)){
		$page_flag = "del_data";
	}elseif(isset($del_table)){
		$page_flag = "del_table";
	}else{
		$main_message = "※エラーが発生しました";
	}
}elseif (isset($input_password) && $input_password != $correct_password) {
	$main_message = $error_pass; //パスワードエラー//else{$main_message = $error_pass;}
}else{
	$main_message = $error_empty_pass;
}

// ########################   page_flagで分岐   ###################
switch ($page_flag) {

case 'toukou': //INSERTで追加
	$sql = $pdo->prepare("INSERT INTO mission4_bbs (id , name , comment , submittime) VALUES (NULL , :name , :comment , :submittime)");
	$sql->bindParam(":name" , $name , PDO::PARAM_STR);
	$sql->bindParam(":comment" , $comment , PDO::PARAM_STR);
	$sql->bindParam(":submittime" , $submittime , PDO::PARAM_STR);
	$sql->execute(); //prepareでSQL文を準備し、executeで実行
	// $sql->execute(array(":name" => $name , ":comment" => $comment , ":submittime" => $submittime));
	$main_message = "新しく".$name." ".$comment." ".$submittime."と投稿しました";
	break;
	
case "delete":

	$sql = $pdo->prepare("DELETE FROM mission4_bbs WHERE id = :id");
	// $stmt = $pdo->prepare($sql);
	$sql->bindParam(":id" , $del_index , PDO::PARAM_INT);
	$sql->execute();
	$main_message = $del_index."の投稿を削除しました";
	break;

case "update":
	// $sql = ;
	$sql = $pdo->prepare("UPDATE mission4_bbs SET name = :name , comment = :comment , submittime = :submittime WHERE id = :id"); //準備して
	$sql->bindParam(":name" , $update_name , PDO::PARAM_STR);
	$sql->bindParam(":comment" , $update_comment , PDO::PARAM_STR);
	$sql->bindParam(":submittime" , $submittime , PDO::PARAM_STR);
	$sql->bindParam(":id" , $update_index , PDO::PARAM_INT);

	$sql->execute();
	$main_message = $update_index."の投稿を更新しました";
	// $stmt->execute(array(":name" => $update_name , ":comment" => $update_comment , ":submittime" => $submittime , ":id" => $update_index));	
	break;

case "del_data":
	$sql = "DELETE FROM mission4_bbs"; //テーブル内の全データ削除
	// $pdo -> query($sql);
	// $sql = "delete from mission4_bbs";
	$stmt = $pdo->query($sql);
	$main_message = "テーブル内のデータを削除しました";
	break;

case "del_table":
	$sql = "DROP TABLE IF EXISTS mission4_bbs";
	$stmt = $pdo->query($sql);
	$main_message = "テーブルが削除されました";
	break;

default:
	$page_flag = "default";
	$main_message = "掲示板へようこそ！";
	break;
}
// メインメッセージとエラーメッセージを表示
echo "<h2>".$main_message."</h2>";
echo "<h2>".$error_message."</h2>";
// echo "page_flag：".$page_flag;
echo "<h3>パスワードは「mission」</h3>";
?>

<!-- HTML表示部分 -->

<p>-------------------------------------------------------</p>
<!-- 投稿か編集かで属性、タイトルを変更 -->
 	<h3>投稿フォーム</h3>

	<form action="test_mission4.php" method="post"> 
		<p>名前：<input type="text" name="name" placeholder="名前"></p>
		<p>コメント：<input type="text" name="comment" placeholder="コメント"></p>
		<p>パスワード：<input type='password' name='input_password'></p>
	<input type="submit" name="toukou_submit" value="投稿">
	</form>
<p>-------------------------------------------------------</p>

	<h3>削除フォーム</h3>
	<p>※削除番号は半角数字</p>

	<form action="test_mission4.php" method="post">
		<p>削除数字：<input type="text" name="del_index" placeholder="削除したい数字"></p>
		<p>パスワード：<input type="password" name="input_password"></p>
		<input type="submit" name="del_submit" value="削除する">
	</form>
<p>-------------------------------------------------------</p>

	<h3>編集フォーム</h3>
	<p>※編集番号は半角数字</p>

	<form action="test_mission4.php" method="post">
		<p>編集番号：<input type="text" name="update_index"></p>
		<p>編集する名前：<input type="text" name="update_name"></p>
		<p>編集するコメント：<input type="text" name="update_comment"></p>
		<p>パスワード：<input type="password" name="input_password"></p>
		<p><input type="submit" name="update_submit" value="編集"></p>	
	</form>
<p>-------------------------------------------------------</p>

	<h3>全データ削除、テーブル削除</h3>
	<form action="test_mission4.php" method="post">
		<p>パスワード：<input type="password" name="input_password"></p>	
		<p><input type="submit" name="del_data" value="全データを削除"></p>
		<p><input type="submit" name="del_table" value="テーブルを削除"></p>
	</form>
	
<p>-------------------------------------------------------</p>
<h3>投稿履歴</h3>
<?php 
// 履歴表示

$sql = "SELECT * FROM mission4_bbs ORDER BY id"; //SELECTで取得
$stmt = $pdo->query($sql); //実行？？
$results = $stmt->fetchAll(); //全部取得
foreach ($results as $row) {
	echo "<p>";
	echo $row["id"]." ";
	echo $row["name"]." ";
	echo $row["comment"]." ";
	echo $row["submittime"];
	echo "</p>";
}
// var_dump($results); //確認用
 ?>
 </body>
 </html>