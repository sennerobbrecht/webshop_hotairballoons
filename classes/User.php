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
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    public function register($email, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

        $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); 
        }
        return false;
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add this method to allow password updates
    public function updatePassword($email, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]);

        $query = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();  // Return true if update is successful, false otherwise
    }
    public function getUserIdByEmail($email)
    {
        // Use $this->conn instead of $this->db
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            return $user['id'];
        } else {
            return null; // Return null if no user found
        }
    }
    

}
?>




