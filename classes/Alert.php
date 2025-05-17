<?php
class Alert {
    private $id;
    private $type;
    private $message;
    private $severity;
    private $created_at;
    private $status;
    private $db;

    public function __construct($type, $message, $severity) {
        $this->type = $type;
        $this->message = $message;
        $this->severity = $severity;
        $this->created_at = date('Y-m-d H:i:s');
        $this->status = 'active';
        $this->db = new Database();
    }

    public function save() {
        try {
            $sql = "INSERT INTO alerts (type, message, severity, created_at, status) 
                    VALUES (?, ?, ?, ?, ?)";
            $params = [
                $this->type,
                $this->message,
                $this->severity,
                $this->created_at,
                $this->status
            ];
            return $this->db->execute($sql, $params);
        } catch (Exception $e) {
            error_log('Alert save error: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function getHistory($limit = 10) {
        $db = new Database();
        $sql = "SELECT * FROM alerts ORDER BY created_at DESC LIMIT ?";
        return $db->query($sql, [$limit])->fetchAll(PDO::FETCH_ASSOC);
    }
}