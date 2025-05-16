<?php
require_once 'User.php';

class Resident extends User {
    private $address;
    private $contact_number;
    private $emergency_contact;

    public function getAddress() { return $this->address; }
    public function getContactNumber() { return $this->contact_number; }
    public function getEmergencyContact() { return $this->emergency_contact; }

    public function setAddress($address) { $this->address = $address; }
    public function setContactNumber($number) { $this->contact_number = $number; }
    public function setEmergencyContact($contact) { $this->emergency_contact = $contact; }
}