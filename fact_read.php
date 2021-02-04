<?php
// session_start();
include("functions.php");
// check_session_id();

// DB接続情報
$pdo = connect_to_db();

// $is_admin = check_session_id_ippan($pdo);
// // var_dump($output_k);
// // exit();
// // // 管理ユーザでない
// if ($is_admin != 1) {
//   echo "<p>管理者以外の閲覧は出来ません。</p>";
//   header('Location: login/fact_login.php'); // ログイン画面へ移動
// };


//SQL Fact-table
$sql = 'SELECT E.id, A.kind_cd, A.kind_nm, B.coffee_cd, B.coffee_nm, 
C.brend_cd, C.brend_nm, 
D.hot_nm, C.brend_price as unit_price, 
E.count_c, E.count_c *  C.brend_price as price , E.memo
FROM cafe_table E, kind_table A, coffee_table B, brend_table C, hot_table D 
WHERE (E.kind_cd = A.kind_cd) 
AND (E.coffee_cd = B.coffee_cd) 
AND (E.brend_cd = C.brend_cd) 
AND (E.hot_cd = D.hot_cd) 
ORDER BY E.id';
$stmt = $pdo->prepare($sql);
$status = $stmt->execute();

if ($status == false) {
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $output = "";
  foreach ($result as $record) {
    $output .= "<tr>";
    $output .= "<td>{$record["kind_cd"]}</td>";
    $output .= "<td>{$record["kind_nm"]}</td>";
    $output .= "<td>{$record["coffee_cd"]}</td>";
    $output .= "<td>{$record["coffee_nm"]}</td>";
    $output .= "<td>{$record["brend_cd"]}</td>";
    $output .= "<td>{$record["brend_nm"]}</td>";
    $output .= "<td>{$record["hot_nm"]}</td>";
    $output .= "<td>{$record["unit_price"]}</td>";
    $output .= "<td>{$record["count_c"]}</td>";
    $output .= "<td>{$record["price"]}</td>";
    $output .= "<td>{$record["memo"]}</td>";
    // edit deleteリンクを追加
    $output .= "<td><a href='fact_edit.php?id={$record["id"]}'>更新</a></td>";
    $output .= "<td><a href='fact_delete.php?id={$record["id"]}'>削除</a></td>";
    $output .= "</tr>";
  }
  unset($record);
}

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
  <title>カフェ売り上げ実績</title>
</head>

<body>
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

  <div class="fact_t">
    <table>
      <thead>
        <tr>
          <th colspan="2">種類</th>
          <th colspan="2">メニュー</th>
          <th colspan="2">アイテム名</th>
          <th>HOT/ICE</th>
          <th>単価</th>
          <th>数量</th>
          <th>金額</th>
          <th>備考</th>
          <th colspan="2"></th>
          <!-- <th></th> -->
        </tr>
      </thead>
      <tbody>
        <!-- <input type="radio" name="line_cd"> -->
        <?= $output ?>
      </tbody>
    </table>
  </div>
  <div class="master">
    <div class="kind_t">
      <table>
        <thead>
          <tr>
            <!-- <th>カテゴリID</th> -->
            <th colspan="2">種類</th>
            <!-- <th colspan="2"></th> -->
            <!-- <th></th> -->
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
            <!-- <th>メニューID</th> -->
            <th colspan="2">メニュー名</th>
            <!-- <th colspan="2"></th> -->
            <!-- <th></th> -->
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
            <!-- <th>アイテムID</th> -->
            <th colspan="2">アイテム名</th>
            <th>単価</th>
            <!-- <th colspan="2"></th> -->
            <!-- <th></th> -->
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
            <!-- <th></th> -->
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