<?php
    class AnswerWord {
        private $word_id;
        private $session_id;
        private $result;
        private $input_date;

        // 回答の初期化
        public function __construct($word_id, $session_id, $result, $input_date) {
            $this->word_id = $word_id;
            $this->session_id = $session_id;
            $this->result = $result;
            $this->input_date = $input_date;
        }

        public function getWordId() { return $this->word_id; }
        public function getSessionId() { return $this->session_id; }
        public function getResult() { return $this->result; }
        public function getInputDate() { return $this->input_date; }
    }
?>