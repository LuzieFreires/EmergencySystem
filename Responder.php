<?php

require_once 'User.php';

class Responder extends User {
    private $specialization;
    private $contact_number;
    private $status; // available, busy, offline

    public function __construct() {
        parent::__construct();
        $this->status = 'available';
    }

    public function getSpecialization() { return $this->specialization; }
    public function getContactNumber() { return $this->contact_number; }
    public function getStatus() { return $this->status; }

    public function setSpecialization($spec) { $this->specialization = $spec; }
    public function setContactNumber($number) { $this->contact_number = $number; }
    public function setStatus($status) { $this->status = $status; }
}