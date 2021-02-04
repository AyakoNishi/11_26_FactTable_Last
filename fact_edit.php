<?php
session_start();
include("functions.php");
check_session_id();

// DB接続情報
$pdo = connect_to_db();

$id = $_GET['id'];

// データ取得SQL作成
// $sql = 'SELECT id, kind_cd, coffee_cd, brend_cd, hot_cd, count_c FROM cafe_table WHERE id=:id';
$sql = 'SELECT id, kind_cd, 
(SELECT kind_table.kind_nm FROM cafe_table, kind_table WHERE cafe_table.id=:id AND cafe_table.kind_cd=kind_table.kind_cd) as kind_nm,
coffee_cd, 
(SELECT coffee_table.coffee_nm FROM cafe_table, coffee_table WHERE cafe_table.id=:id AND cafe_table.coffee_cd=coffee_table.coffee_cd) as coffee_nm,
brend_cd, 
(SELECT brend_table.brend_nm FROM cafe_table, brend_table WHERE cafe_table.id=:id AND cafe_table.brend_cd=brend_table.brend_cd) as brend_nm,
hot_cd, 
(SELECT hot_table.hot_nm FROM cafe_table, hot_table WHERE cafe_table.id=:id AND cafe_table.hot_cd=hot_table.hot_cd) as hot_nm,
count_c FROM cafe_table WHERE id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

// fetch()で1レコード取得できる．
if ($status == false) {
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  $record_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// master_read($pdo);
//SQL kind-table
$output_k = kind_table_access($pdo);

//SQL coffee-table
$output_c = coffee_table_access($pdo);

//SQL brend-table
$output_b = brend_table_access($pdo);

//SQL hot-table
$output_h = hot_table_access($pdo);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>カフェ売り上げ実績（ファクトテーブル実践）</title>
</head>

<body>
  <form action="fact_update.php" method="POST">
    <div class="head-title">
      <h1>カフェ売り上げ実績（ファクトテーブル実践）</h1>
    </div>
    <div class="button_area">
      <button><a href="fact_input.php">入力画面</a></button>
      <button><a href="login/fact_logout.php">ログアウト</a></button>
    </div>
    <div class="tab">
      <ul class="tab_menu">
        <li class="current"><a href="fact_read.php">実績表示</a></li>
        <li><a href="fact_input.php">実績入力</a></li>
        <li><a href="fact_mast_edit.php">マスタ修正</a></li>
        <li><a href="fact_sum.php">集計</a></li>
      </ul>
    </div>

    <h2> 修正中・・・</h2>
    <div>
      <label for="kind_list" class="textfield_label">種類:</label>
      <input type="text" name="kind_cd" value="<?= $record_edit["kind_cd"] ?>">
      <?= $record_edit["kind_cd"] ?>：<?= $record_edit["kind_nm"] ?>
    </div>
    <div>
      <label for="coffee_list" class="textfield_label">メニュー:</label>
      <input type="text" name="coffee_cd" value="<?= $record_edit["coffee_cd"] ?>">
      <?= $record_edit["coffee_cd"] ?>：<?= $record_edit["coffee_nm"] ?>
    </div>
    <div>
      <label for="brend_list" class="textfield_label">アイテム:</label>
      <input type="text" name="brend_cd" value="<?= $record_edit["brend_cd"] ?>">
      <?= $record_edit["brend_cd"] ?>：<?= $record_edit["brend_nm"] ?>
    </div>
    <div>
      <label for="hot_list" class="textfield_label">ICE/HOT:</label>
      <input type="text" name="hot_cd" value="<?= $record_edit["hot_cd"] ?>">
      <?= $record_edit["hot_cd"] ?>：<?= $record_edit["hot_nm"] ?>
    </div>
    <div>
      <label for="count_c_list" class="textfield_label">数量:</label>
      <input type="text" name="count_c" value="<?= $record_edit["count_c"] ?>">
      <?= $record_edit["count_c"] ?>
    </div>
    <div>
      <input type="hidden" name="id" value="<?= $record_edit['id'] ?>">
    </div>
    <div>
      <button>送信</button>
    </div>
  </form>

  <div class="master">
    <div class="kind_t">
      <table>
        <thead>
          <tr>
            <th colspan="2">種類</th>
          </tr>
        </thead>
        <tbody>
          <?= $output_k ?>
        </tbody>
      </table>
    </div>
    <div class="coffee_t">
      <table>
        <thead>
          <tr>
            <th colspan="2">メニュー名</th>
          </tr>
        </thead>
        <tbody>
          <?= $output_c ?>
        </tbody>
      </table>
    </div>
    <div class="brend_t">
      <table>
        <thead>
          <tr>
            <th colspan="2">アイテム名</th>
            <th>単価</th>
          </tr>
        </thead>
        <tbody>
          <?= $output_b ?>
        </tbody>
      </table>
    </div>
    <div class="hot_t">
      <table>
        <thead>
          <tr>
            <th colspan="2">HOT/ICE</th>
          </tr>
        </thead>
        <tbody>
          <?= $output_h ?>
        </tbody>
      </table>
    </div>
  </div>

</body>

</html>