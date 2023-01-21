<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <?php require_once 'config/db.php'; ?>
    <?php require_once 'config/timezone.php'; ?>
    <?php require_once 'class/Word.php'; ?>
    <?php require_once 'class/AnswerWord.php'; ?>
    <link href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css" rel="stylesheet">
    <script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="main.css">

<body>
    <a href="home_md.php">問題に戻る</a>

    <?php // WordListの初期化
    $sql = "SELECT * FROM wordlist";
    $words = array();

    if ($result = $mysqli->query($sql)) {
        // 連想配列を取得 TODO:数が増えると非効率
        while ($row = $result->fetch_assoc()) {
            $word = new Word($row["word_id"], $row["english"], $row["japanese"]);
            array_push($words, $word);
        }
        // 結果セットを閉じる
        $result->close();
    }

    // 配列からランダムなキーを選択肢分出力
    $word_keys = array_rand($words, 4);
    $choise_word1 = $words[$word_keys[0]];
    $choise_word2 = $words[$word_keys[1]];
    $choise_word3 = $words[$word_keys[2]];
    $choise_word4 = $words[$word_keys[3]];

    //print_r($choise_word1);

    shuffle($word_keys);

    $correct_word = $words[$word_keys[0]];

    $correct_word_japanese = $correct_word->getJapanese();
    $answer_word_japanese;
    ?>

    <?php // AnswerdWordの初期化。Wordに回答結果も拡張
    $sql = "SELECT * FROM answer_word";
    $answered_words = array();

    if ($result = $mysqli->query($sql)) {
        // 連想配列を取得 TODO:数が増えると非効率
        while ($row = $result->fetch_assoc()) {
            $answered_word = new AnswerWord($row["word_id"], $row["session_id"], $row["result"], $row["input_date"]);
            array_push($answered_words, $answered_word);

            // Wordに回答結果を記録
            foreach ($words as $word) {
                if ($word->getWordId() == $answered_word->getWordId()) {
                    // echo "match. ". $word->getEnglish() . "</br>";
                    if (!$answered_word->getResult()) {
                        $word->setCorrectTime($word->getCorrectTime() + 1);
                        // echo "correctTimeUp. ". $word->getEnglish() . ". and correctTime : " . $word->getCorrectTime() . "</br>";
                    } else {
                        $word->setFailureTime($word->getFailureTime() + 1);
                    }
                }
            }
        }
        // 結果セットを閉じる
        $result->close();
    }
    ?>


<ul class="mdc-deprecated-list">
  <li class="mdc-deprecated-list-item" tabindex="0">
    <span class="mdc-deprecated-list-item__ripple"></span>
    <span class="mdc-deprecated-list-item__text">　<?php echo '英検3級の全単語数： ' . count($words); ?>　</span>
  </li>
  <li class="mdc-deprecated-list-item">
    <span class="mdc-deprecated-list-item__ripple"></span>
    <span class="mdc-deprecated-list-item__text">　 <?php echo '回答した単語数： ' . count($answered_words); ?>　</span>
  </li>
  <li class="mdc-deprecated-list-item">
    <span class="mdc-deprecated-list-item__ripple"></span>
    <span class="mdc-deprecated-list-item__text">　 <?php echo '残りの単語数： ' .( count($words) - count($answered_words)); ?>　</span>
  </li>
</ul>


    
   

        <div class="result_contents">
            <div class="title">
                　Correct Words 🎉
            </div>
            <div class="result_area">
                <table>
                    <tr>
                        <th>English </th>
                        <th>Japanese </th>
                        <th>できた数 </th>
                    </tr>
                    <?php ?>
                    <?php foreach ($words as $word) {
                        if ($word->getCorrectTime() > 0) { ?>
                            <tr>
                                <td><?= $word->getEnglish() ?></td>
                                <td><?= $word->getJapanese() ?></td>
                                <td><?= $word->getCorrectTime() ?></td>
                        <?php }
                    } ?>
                            </tr>
                </table>
            </div>

            <div class="title">
                　まだおぼえていない Words
            </div>
            <div class="result_area">
                <table>
                    <tr>
                        <th>Engilsh </th>
                        <th>Japanese </th>
                        <th>まちがった数 </th>
                    </tr>
                    <?php ?>
                    <?php foreach ($words as $word) {
                        if ($word->getCorrectTime() == 0) { ?>
                            <tr>
                                <td><?= $word->getEnglish() ?></td>
                                <td><?= $word->getJapanese() ?></td>
                                <td><?= $word->getFailureTime() ?></td>
                        <?php }
                    } ?>
                            </tr>
                </table>
            </div>

        </div>
</body>