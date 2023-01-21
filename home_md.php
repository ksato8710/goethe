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
    <link rel="stylesheet" href="main.css?1000">
</head>
<body>        
    <?php // POST受信時の処理
        $answered_word_id = $_POST['answer_word_id']; 
        $answered_japanese = $_POST['answer_japanese']; 
        $answered_result = $_POST['answer_result'];  // 0 : あたり, 1 : まちがい
        $answered_session_id = 1; // TODO : 暫定の処置
        $answered_date =  date('Y-m-d H:i:s');
        // 成功した記載　INSERT INTO `answer_word` (`word_id`, `session_id`, `result`, `input_date`, `memo`) VALUES ('1', '1', '1', '2022-06-23 04:36:02', NULL)
        try {
            $insert_sql = "INSERT INTO `answer_word` (`word_id`, `session_id`, `result`, `input_date`, `memo`) 
                VALUES ('" . $answered_word_id . "', '" . $answered_session_id . "', '" . $answered_result . "', '" . $answered_date . "', NULL);" ;
            // echo $insert_sql;
            $mysqli->query($insert_sql);
            // echo "DB INSERT DONE.";
        } catch(Exception $e) {
            echo "DB ERROR.";
            print_r($e);
        }
    ?> 

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
                $answered_word = new AnswerWord($row["word_id"], $row["session_id"], $row["result"] , $row["input_date"]);
                array_push($answered_words, $answered_word);

                // Wordに回答結果を記録
                foreach($words as $word) {
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
    
<audio id="correct_sound" src="correct_sound.mp3"></audio>
<audio id="failure_sound" src="failure_sound.mp3"></audio>

<div id="modal_success">
    <div class="modal-content">
        <div class="modal-header">
            <h1>正解！ 🎉</h1>
            <span class="modalClose">×</span>
        </div>
        <div class="modal-body">
            <p>よく分かったね!</p>
            <p>この調子でがんばろう ☺️</p>
        </div>
        <div class="modal-footer">
            <form method="post" action="">
                <input type="hidden" name="answer_word_id" value=<?php echo $correct_word->getWordId(); ?> >
                <input type="hidden" name="answer_japanese" value=<?php echo $correct_word->getJapanese(); ?> >
                <input type="hidden" name="answer_result" value=0 >
                <input type="submit" value="つぎの問題" class="button_submit">
            </form>
        </div>
    </div>
</div>

<div id="modal_failure">
    <div class="modal-content">
        <div class="modal-header">
            <h1>ざんねん、まちがってるよ</h1>
            <span class="modalClose">×</span>
        </div>
        <div class="modal-body">
            <p>まだまだがんばろう！☺️</p>
            <p>わからないことは辞書（じしょ）でしらべてみよう</p>
            <?php echo '答え: ' . $correct_word->getJapanese(); ?>
        </div>
        <div class="modal-footer">
            <form method="post" action="">
                <input type="hidden" name="answer_word_id" value=<?php echo $correct_word->getWordId(); ?> >
                <input type="hidden" name="answer_japanese" value=<?php echo $correct_word->getJapanese(); ?> >
                <input type="hidden" name="answer_result" value=1 >
                <input type="submit" value="つぎの問題"  class="button_submit">
            </form>
        </div>
    </div>
</div>

<div class="container">

    <!-- <div class="greeting">
        Hello Kaede. This is your wordlist
    </div> -->

    <div class="main_question">
        <?php echo $correct_word->getEnglish(); ?>
        <button id="speakButton" onclick="speachSynthesis('<?php echo $correct_word->getEnglish(); ?>')">Speak</button>
    </div>    

        <div class="main_contents">
            <button class="mdc-button mdc-button--raised button" onclick="modalOpen('button1');">
                <?php echo $choise_word1->getJapanese(); ?>
            </button> 
            <button id="speakButton" class="mdc-button mdc-button__label sub_button" onclick="speachSynthesisJp('<?php echo $choise_word1->getJapanese(); ?>')">Speak</button>

            <button class="mdc-button mdc-button--raised button" onclick="modalOpen('button2');">
                <?php echo $choise_word2->getJapanese(); ?>
            </button> 
            <button id="speakButton"  class="mdc-button mdc-button__label sub_button" onclick="speachSynthesisJp('<?php echo $choise_word2->getJapanese(); ?>')">Speak</button>

            <button class="mdc-button mdc-button--raised button" onclick="modalOpen('button3');">
                <?php echo $choise_word3->getJapanese(); ?>
            </button> 
            <button id="speakButton"  class="mdc-button mdc-button__label sub_button" onclick="speachSynthesisJp('<?php echo $choise_word3->getJapanese(); ?>')">Speak</button>

            <button class="mdc-button mdc-button--raised button" onclick="modalOpen('button4');">
                <?php echo $choise_word4->getJapanese(); ?>
            </button> 
            <button id="speakButton"  class="mdc-button mdc-button__label sub_button" onclick="speachSynthesisJp('<?php echo $choise_word4->getJapanese(); ?>')">Speak</button>


            <a href="record.php">成績を見る 🎉</a>
            <a href="calendar.php">カレンダーを見る 🎉</a>


        </div>
    </div>
    
    <script language="javascript" type="text/javascript">

        function OnButtonClickBad() {
            alert('ざんねん！ちがいます。');
            location.reload();
        }

        function modalOpen(button_num) {
            var correctSound = document.getElementById("correct_sound");
            var failureSound = document.getElementById("failure_sound");
            // phpで正誤チェックして、適切なダイアログを表示
            switch(button_num) {
                case 'button1':
                    if ('<?php echo $correct_word_japanese ?>' == '<?php echo $choise_word1->getJapanese() ?>') {
                        modal_success.style.display = 'block';
                        correctSound.play();
                    } else {
                        modal_failure.style.display = 'block';
                        failureSound.play();
                    }
                break;
                case 'button2':
                    if ('<?php echo $correct_word_japanese ?>' == '<?php echo $choise_word2->getJapanese() ?>') {
                        modal_success.style.display = 'block';
                        correctSound.play();
                    } else {
                        modal_failure.style.display = 'block';
                        failureSound.play();
                    }
                break;
                case 'button3':
                    if ('<?php echo $correct_word_japanese ?>' == '<?php echo $choise_word3->getJapanese() ?>') {
                        modal_success.style.display = 'block';
                        correctSound.play();
                    } else {
                        modal_failure.style.display = 'block';
                        failureSound.play();
                    }
                break;
                case 'button4':
                    if ('<?php echo $correct_word_japanese ?>' == '<?php echo $choise_word4->getJapanese() ?>') {
                        modal_success.style.display = 'block';
                        correctSound.play();
                    } else {
                        modal_failure.style.display = 'block';
                        failureSound.play();
                    }
                break;
            }
        }
        
        function modalClose() {
            modal_success.style.display = 'none';
            modal_failure.style.display = 'none';
            location.reload();
        }

        function outsideClose(e) {
            if (e.target == modal) {
                modal_success.style.display = 'none';
                modal_failure.style.display = 'none';
                location.reload();
            }
        }

        const modal_success = document.getElementById('modal_success');
        const modal_failure = document.getElementById('modal_failure');
        const buttonClose0 = document.getElementsByClassName('modalClose')[0];
        const buttonClose1 = document.getElementsByClassName('modalClose')[1];

        buttonClose0.addEventListener('click', modalClose);
        buttonClose1.addEventListener('click', modalClose);
        modal_success.addEventListener('click', outsideClose);
        modal_failure.addEventListener('click', outsideClose);

        function speachSynthesis(word){
            const msg = new SpeechSynthesisUtterance(word);
            msg.lang = 'en-US'
            window.speechSynthesis.speak(msg);
        }
        
        function speachSynthesisJp(word){
            const msg = new SpeechSynthesisUtterance(word);
            msg.lang = 'jp'
            window.speechSynthesis.speak(msg);
        }
        

    </script>

</body>
