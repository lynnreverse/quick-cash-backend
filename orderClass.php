<?php
    class Order{
        public $id;
        public $user_id;
        public $job_id;
        public $date_taken;

        public $job;

        public function __construct($id, $user_id, $job_id, $date_taken){
            $this->id = $id;
            $this->user_id = $user_id;
            $this->job_id = $job_id;
            $this->date_taken = $date_taken;
        }
    }
?>