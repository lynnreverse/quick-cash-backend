<?php

    class Job {
        public $id;
        public $category;
        public $title;
        public $description;
        public $payment;

        public function __construct($id, $category, $title, $description, $payment){
            $this->id = $id;
            $this->category = $category;
            $this->title = $title;
            $this->description = $description;
            $this->payment = $payment;
        }
    }

?>