<?
class Auth {
    private $db;
    private $table = 'users';

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($username, $password, $email) {
        try {
            $query = "INSERT INTO " . $this->table . " (username, password, email) VALUES (:username, :password, :email)";
            $stmt = $this->db->prepare($query);

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);

            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function login($username, $password) {
        try {
            $query = "SELECT id, username, password FROM " . $this->table . " WHERE username = :username";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($password, $row['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    return true;
                }
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']);
    }
}