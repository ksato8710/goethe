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
    aaa
    <?php
    $result = $mysqli->query("SELECT input_date, result, word_id FROM `answer_word` GROUP BY input_date, result");
    $date_results = array();
    while ($row = $result->fetch_assoc()) {
        $date = date('Y-m-d', strtotime($row['input_date']));
        $word = new Word();
        $word->load($row['word_id']);
        if (!isset($date_results[$date])) {
            $date_results[$date] = array(
                'correct' => array(),
                'incorrect' => array()
            );
        }
        if ($row['result'] == 0) {
            array_push($date_results[$date]['correct'], $word->english);
        } else {
            array_push($date_results[$date]['incorrect'], $word->english);
        }
    }

    // カレンダー形式で表示
    foreach ($date_results as $date => $results) {
        echo $date . ": 正解数=" . $results['correct'] . ", 不正解数=" . $results['incorrect'] . "<br>";

        $result = $mysqli->query("SELECT wordlist.japanese FROM `answer_word` INNER JOIN wordlist ON answer_word.word_id = wordlist.word_id where input_date like '$date%' and result = 0 ");
        echo "正解した単語: ";
        while ($row = $result->fetch_assoc()) {
            echo $row['japanese'] . " ";
        }
        echo "<br>";

        $result = $mysqli->query("SELECT wordlist.japanese FROM `answer_word` INNER JOIN wordlist ON answer_word.word_id = wordlist.word_id where input_date like '$date%' and result = 1 ");
        echo "不正解だった単語: ";
        while ($row = $result->fetch_assoc()) {
            echo $row['japanese'] . " ";
        }
        echo "<br><br>";
    }
    $mysqli->close();
    ?>

    <a href="home_md.php">問題に戻る</a>

</body>]