<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <?php require_once 'config/db.php'; ?>
    <?php date_default_timezone_set('Asia/Tokyo'); ?>
    <?php require_once 'class/Word.php'; ?>
    <?php require_once 'class/AnswerWord.php'; ?>
    <link rel="stylesheet" href="main.css?1000">
</head>
<body>
<?php
$result = $mysqli->query("SELECT input_date, result FROM `answer_word` GROUP BY input_date, result");

$date_results = array();
while ($row = $result->fetch_assoc()) {
    $date = date('Y-m-d', strtotime($row['input_date']));
    if (!isset($date_results[$date])) {
        $date_results[$date] = array(
            'correct' => 0,
            'incorrect' => 0
        );
    }
    if ($row['result'] == 0) {
        $date_results[$date]['correct']++;
    } else {
        $date_results[$date]['incorrect']++;
    }
}

// カレンダー形式で表示
foreach ($date_results as $date => $results) {
    echo $date . ": 正解数=" . $results['correct'] . ", 不正解数=" . $results['incorrect'] . "<br>";
}

$mysqli->close();
?>

<a href="home_md.php">問題に戻る</a>

</body>