<?php
class Alert {
    private $id;
    private $type;
    private $message;
    private $severity;
    private $created_at;
    private $status;

    public function __construct($type, $message, $severity) {
        $this->type = $type;
        $this->message = $message;
        $this->severity = $severity;
        $this->created_at = date('Y-m-d H:i:s');
        $this->status = 'active';
    }

    public function broadcast() {
        // Implement alert broadcasting logic
        return true;
    }
}