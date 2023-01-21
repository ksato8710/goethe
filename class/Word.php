<?php
    class Word {
        private $word_id;
        private $english;
        private $japanese;
        private $correct_time;
        private $failure_time;

        // 単語マスターの初期化
        public function __construct($word_id, $english, $japanese) {
            $this->word_id = $word_id;
            $this->english = $english;
            $this->japanese = $japanese;
        }

        public function getWordId() { return $this->word_id; }
        public function getJapanese() { return $this->japanese; }
        public function getEnglish() { return $this->english; }
        public function getCorrectTime() { return $this->correct_time; }
        public function getFailureTime() { return $this->failure_time; }

        public function setCorrectTime($correct_time) { $this->correct_time=$correct_time; }
        public function setFailureTime($failure_time) { $this->failure_time=$failure_time; }
    }
?>
