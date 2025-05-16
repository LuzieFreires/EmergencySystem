<?php
class SMS {
    private $recipient;
    private $message;
    private $sent_at;

    public function __construct($recipient, $message) {
        $this->recipient = $recipient;
        $this->message = $message;
        $this->sent_at = date('Y-m-d H:i:s');
    }

    public function send() {
        // Implement SMS sending logic here
        return true;
    }
}