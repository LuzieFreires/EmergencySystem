<?php
class Validator {
    private $errors = [];

    public function validateUsername($username) {
        if(empty($username)) {
            $this->errors['username'] = 'Username is required';
        } elseif(strlen($username) < 3) {
            $this->errors['username'] = 'Username must be at least 3 characters';
        }
    }

    public function validateEmail($email) {
        if(empty($email)) {
            $this->errors['email'] = 'Email is required';
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format';
        }
    }

    public function validatePassword($password) {
        if(empty($password)) {
            $this->errors['password'] = 'Password is required';
        } elseif(strlen($password) < 6) {
            $this->errors['password'] = 'Password must be at least 6 characters';
        }
    }

    public function validateEmergency($type, $location, $description) {
        if(empty($type)) {
            $this->errors['type'] = 'Emergency type is required';
        }
        if(empty($location)) {
            $this->errors['location'] = 'Location is required';
        }
        if(empty($description)) {
            $this->errors['description'] = 'Description is required';
        }
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }
}