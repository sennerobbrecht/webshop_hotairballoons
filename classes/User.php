<?php
class User
{
    private $conn;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function canLogin($email, $password)
    {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                return true;
            }
        }
        return false;
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function emailExists($email)
    {
        $query = "SELECT COUNT(*) FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count > 0;
    }

    public function updateUser($currentEmail, $newEmail, $newPassword = null)
    {
        $query = "UPDATE users SET email = ?, password = COALESCE(?, password) WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $newEmail, $newPassword, $currentEmail);
        return $stmt->execute();
    }
}
?>


