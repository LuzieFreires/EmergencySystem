<?php
require_once '../classes/Database.php';

class Emergency {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createEmergency(array $data) {
        $required = ['type', 'location', 'latitude', 'longitude', 'status', 'severity'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }
        if (!is_numeric($data['latitude']) || !is_numeric($data['longitude'])) {
            throw new Exception('Invalid coordinates');
        }

        $sql = "INSERT INTO emergencies (type, location, description, latitude, longitude, status, severity, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $params = [
            $data['type'],
            $data['location'],
            $data['description'] ?? '',
            $data['latitude'],
            $data['longitude'],
            $data['status'],
            $data['severity']
        ];
        return $this->db->execute($sql, $params);
    }

    public function getActiveEmergencies() {
        $query = "SELECT e.*, r.name AS resident_name 
                  FROM emergencies e 
                  LEFT JOIN residents r ON e.reported_by = r.user_id 
                  WHERE e.status IN ('pending', 'in_progress') 
                  ORDER BY e.created_at DESC";
        return $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmergencyDetails($id) {
        $sql = "SELECT e.*, r.name AS resident_name 
                FROM emergencies e 
                LEFT JOIN residents r ON e.reported_by = r.user_id 
                WHERE e.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateEmergency($id, array $data) {
        $sql = "UPDATE emergencies SET status = ?, responder_id = ?, updated_at = NOW() WHERE id = ?";
        $params = [
            $data['status'],
            $data['responder_id'] ?? null,
            $id
        ];
        return $this->db->execute($sql, $params);
    }

    public function closeEmergency($id, $responderId = null) {
        $sql = "UPDATE emergencies SET status = 'resolved', updated_at = NOW()";
        $params = [];
        if ($responderId !== null) {
            $sql .= ", responder_id = ?";
            $params[] = $responderId;
        }
        $sql .= " WHERE id = ?";
        $params[] = $id;

        return $this->db->execute($sql, $params);
    }

    // Simplified methods to fetch counts or lists can be added similarly as needed...

}
