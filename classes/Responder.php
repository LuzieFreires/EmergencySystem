<?php
require_once 'Database.php';

class Responder {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function updateStatus($responderId, $status) {
        $validStatuses = ['available', 'on_duty', 'unavailable'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Invalid status');
        }

        $query = "UPDATE responders SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->query($query, [$status, $responderId])->rowCount() > 0;
    }

    public function getActiveResponders() {
        $query = "SELECT id, name, status, last_location_lat, last_location_lng 
                  FROM responders 
                  WHERE status IN ('available', 'on_duty')";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateLocation($responderId, $lat, $lng) {
        $query = "UPDATE responders 
                  SET last_location_lat = ?, last_location_lng = ?, updated_at = NOW() 
                  WHERE id = ?";
        return $this->db->query($query, [$lat, $lng, $responderId])->rowCount() > 0;
    }
}