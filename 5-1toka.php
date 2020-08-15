<?php
//ここでmysqlに関する初期設定を行う

//データベースにログイン
$dsn = "データベース名";
$user = "ユーザ名";
$password = 'パスワード名';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


//テーブルを作成
$sql = "CREATE TABLE IF NOT EXISTS tb_bbs"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "pass TEXT"
	.");";
$stmt = $pdo->query($sql);
    
?>

<?php

//編集用データ格納変数　空だったらなんもなくなる、追記モード
$editNumber = "";
$editName = "";
$editComment = "";

//書き込み用データ変数
$filecount = "count.txt";

?>

<?php

//送信内容によって処理が分かれる　編集が押されたあとすぐの動作
if(!empty($_POST["editok"]) && !empty($_POST["pass_edit"])) {

    //編集番号からデータを求める
    $sql = 'SELECT * FROM tb_bbs';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
        
        if($_POST["edit"] == $row['id'] && $_POST["pass_edit"] == $row["pass"]){
            $editNumber = $row["id"];
            $editName = $row["name"];
            $editComment = $row["comment"];
            break;
        }
	
	}

//送信ボタンを押したとき
}else if (!empty($_POST["name"] ) && !empty($_POST["comment"]) &&  !empty($_POST["pass"]) && !empty($_POST["normal"])){

    $date = date("Y/m/d H:i:s");

    //編集モード
    if($_POST["edit_post"]) {

        $sql = 'SELECT * FROM tb_bbs';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();

        $flag = false;

        foreach ($results as $row){

            //もしidが編集番号に一致　かつ　passが入力されたパスワードに一致　したらflag をtrueにする
            $passw = $row['pass'];
            $num = $row["id"];

            //echo "password : " . $passw . " num : " . $num . " edit_post : " . $_POST["edit_post"] . " post_pass : " . $_POST["pass"] . "<br>";

            if($num == $_POST["edit_post"] && $passw == $_POST["pass"]) $flag = true;
       
        }
   
        if ($flag){
    
            $id = $_POST["edit_post"]; 
	        $name = $_POST["name"];
            $comment = $_POST["comment"];
            $date = date("Y/m/d H:i:s");

	        $sql = 'UPDATE tb_bbs SET name=:name,comment=:comment,date=:date WHERE id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

        }

    }else{

    //追記モード
    $sql = $pdo -> prepare("INSERT INTO tb_bbs (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	$name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass = $_POST["pass"];
	$sql -> execute();

    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>はじめての掲示板</title>
</head>
<body>
    名前
    
    <form action="5-1toka.php" method="post">
    
    <!-- hidden 属性で編集する番号を渡す 更新先にデータを送りたいとき使う-->
    <input type="hidden" name="edit_post" value="<?php echo $editNumber; ?>"></input>
    <input type="text" name="name" value="<?php echo $editName; ?>"></input>
    <!-- hidden属性は、$_POST[""]で value属性で指定した値を取得する
         text属性は、テキスト入力フォームで入力されたテキストを$_POST[""]で取得
                     このとき、valueは初期値　-->
    <br>

    コメントの入力 <br>
    <form action="5-1toka.php" method="post">
   <?php /*valueが初期値になる*/ ?>
    <input type="text" name="comment" value=<?php echo $editComment; ?>></input>
    <br>

    パスワード <br>
    <form  action="5-1toka.php" method="post">
    <input type="text" name="pass"></input>
    <input type="submit" name="normal" value="送信"></input>
    <br><br>

    削除対象番号　　　　　パスワード <br>
    <form  action="5-1toka.php" method="post">
    <input type="number" name="bye"></input>

    <form  action="5-1toka.php" method="post">
    <input type="text" name="pass_bye"></input>
    <input type="submit" value="削除"></input>
    
    <br><br>

    編集対象番号　　　　　パスワード <br>
    <form  action="5-1toka.php" method="post">
    <input type="number" name="edit"></input>

    <form  action="5-1toka.php" method="post">
    <input type="text" name="pass_edit"></input>
    <input type="submit" name="editok" value="編集"></input>
    <br><br>
    
    </form></form></form></form></form>
    
<?php
    
if(!empty($_POST["bye"]) && !empty($_POST["pass_bye"])){

    $sql = 'SELECT * FROM tb_bbs';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();

    $flag = false;

    foreach ($results as $row){
        //passが入力されたパスワードに一致　したらflag をtrueにする
        $passw = $row['pass'];
        $n = $row["id"];
         //echo "password : " . $passw . " num : " . $num . " edit_post : " . $_POST["edit_post"] . " post_pass : " . $_POST["pass"] . "<br>";
        if($n == $_POST["bye"] && $passw == $_POST["pass_bye"]) $flag = true;
    }

    if ($flag){
        $delete = $_POST["bye"];
        $id = $delete;
	    $sql = 'delete from tb_bbs where id=:id';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt->execute();
    }
}

$sql = 'SELECT * FROM tb_bbs';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();

foreach ($results as $row){
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['date'].'<br>';
	echo "<hr>";
    }

 ?>
    
</body>
</html>
