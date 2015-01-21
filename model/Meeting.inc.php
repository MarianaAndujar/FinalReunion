<?php
	class Meeting{
		private $id;
		private $subject;
		private $description;
		private $location;
		private $duration;
		private $id_user;

		public function getId(){
			return $this->id;
		}
		
		public function getSubject(){
			return $this->subject;
		}
		
		public function getDescription(){
			return $this->description;
		}
		
		public function getLocation(){
			return $this->location;
		}
		
		public function getDuration(){
			return $this->duration;
		}
	}
?>