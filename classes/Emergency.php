<?php
require_once 'Database.php';

class Emergency {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getActiveEmergencies() {
        $query = "SELECT e.*, r.name as resident_name, r.phone 
                  FROM emergencies e 
                  JOIN residents r ON e.resident_id = r.id 
                  WHERE e.status = 'active' 
                  ORDER BY e.created_at DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmergencyDetails($emergencyId) {
        $query = "SELECT e.*, r.name as resident_name, r.phone, r.address 
                  FROM emergencies e 
                  JOIN residents r ON e.resident_id = r.id 
                  WHERE e.id = ?";
        return $this->db->query($query, [$emergencyId])->fetch(PDO::FETCH_ASSOC);
    }

    public function assignResponder($emergencyId, $responderId) {
        $query = "UPDATE emergencies 
                  SET responder_id = ?, status = 'in_progress', updated_at = NOW() 
                  WHERE id = ? AND (status = 'active' OR status = 'pending')";
        return $this->db->query($query, [$responderId, $emergencyId])->rowCount() > 0;
    }

    public function closeEmergency($emergencyId, $responderId) {
        $query = "UPDATE emergencies 
                  SET status = 'closed', closed_at = NOW(), updated_at = NOW() 
                  WHERE id = ? AND responder_id = ?";
        return $this->db->query($query, [$emergencyId, $responderId])->rowCount() > 0;
    }
}