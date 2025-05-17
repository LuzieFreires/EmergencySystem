<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../classes/Database.php';

class Responder {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect(); 
    }

    public function updateStatus($responderId, $status) {
        $validStatuses = ['available', 'on_duty', 'unavailable'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Invalid status');
        }

        $query = "UPDATE responders SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$status, $responderId]);

        return $stmt->rowCount() > 0;
    }

    public function getActiveResponders() {
        $query = "SELECT r.*, u.name, u.phone 
                  FROM responders r
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.status IN ('available', 'on_duty')
                  ORDER BY r.status ASC, r.updated_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateLocation($responderId, $lat, $lng) {
        $query = "UPDATE responders 
                  SET last_location_lat = ?, last_location_lng = ?, updated_at = NOW() 
                  WHERE id = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$lat, $lng, $responderId]);

        return $stmt->rowCount() > 0;
    }

    public function respondToEmergency($responderId, $emergencyId) {
        try {
            $this->db->beginTransaction();

            // Update responder status
            $this->updateStatus($responderId, 'on_duty');

            // Create response record
            $query = "INSERT INTO emergency_responses 
                      (emergency_id, responder_id, status, created_at) 
                      VALUES (?, ?, 'responding', NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$emergencyId, $responderId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception('Failed to respond to emergency');
        }
    }

    public function completeEmergencyResponse($responseId) {
        try {
            $this->db->beginTransaction();

            // Update response status
            $query = "UPDATE emergency_responses 
                      SET status = 'completed', completed_at = NOW() 
                      WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$responseId]);

            // Get responder ID
            $query = "SELECT responder_id FROM emergency_responses WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$responseId]);
            $responder = $stmt->fetch(PDO::FETCH_ASSOC);

            // Update responder status back to available
            if ($responder) {
                $this->updateStatus($responder['responder_id'], 'available');
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception('Failed to complete emergency response');
        }
    }

    public function getResponderStats($responderId) {
        $query = "SELECT 
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_responses,
                    COUNT(CASE WHEN status = 'responding' THEN 1 END) as active_responses,
                    AVG(TIMESTAMPDIFF(MINUTE, created_at, IFNULL(completed_at, NOW()))) as avg_response_time
                  FROM emergency_responses
                  WHERE responder_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$responderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getResponderInfo($responderId) {
        $query = "SELECT ri.*, r.specialization, r.contact_number, r.status
                  FROM responder_info ri
                  JOIN responders r ON ri.responder_id = r.id
                  WHERE ri.responder_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$responderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMedicalConditions($responderId) {
        $query = "SELECT * FROM responder_medical_conditions 
                  WHERE responder_id = ? ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$responderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateResponderInfo($responderId, $data) {
        try {
            $this->db->beginTransaction();

            $query = "UPDATE responder_info SET 
                      date_of_birth = :dob,
                      blood_type = :blood_type,
                      height = :height,
                      weight = :weight,
                      certification_number = :cert_num,
                      certification_expiry = :cert_expiry,
                      years_of_experience = :experience,
                      department = :department,
                      shift_preference = :shift,
                      updated_at = NOW()
                      WHERE responder_id = :responder_id";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':dob' => $data['date_of_birth'],
                ':blood_type' => $data['blood_type'],
                ':height' => $data['height'],
                ':weight' => $data['weight'],
                ':cert_num' => $data['certification_number'],
                ':cert_expiry' => $data['certification_expiry'],
                ':experience' => $data['years_of_experience'],
                ':department' => $data['department'],
                ':shift' => $data['shift_preference'],
                ':responder_id' => $responderId
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new Exception('Failed to update responder information');
        }
    }

    public function addMedicalCondition($responderId, $data) {
        $query = "INSERT INTO responder_medical_conditions 
                  (responder_id, condition_name, diagnosis_date, severity, status, treatment_plan, notes)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $responderId,
            $data['condition_name'],
            $data['diagnosis_date'],
            $data['severity'],
            $data['status'],
            $data['treatment_plan'],
            $data['notes']
        ]);
    }

    public function getFitnessAssessments($responderId) {
        $query = "SELECT * FROM responder_fitness_assessments 
                  WHERE responder_id = ? ORDER BY assessment_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$responderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCertifications($responderId) {
        $query = "SELECT * FROM responder_certifications 
                  WHERE responder_id = ? ORDER BY expiry_date ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$responderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkCertificationStatus($responderId) {
        $query = "SELECT certification_name, expiry_date, status 
                  FROM responder_certifications 
                  WHERE responder_id = ? AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$responderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}