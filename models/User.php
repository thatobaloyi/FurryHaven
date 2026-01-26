<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class User
{
    private $table = 'users';
    private $conn; 
    private $username;
    private $preferred_name;
    private $password;
    private $FirstName;
    private $LastName;
    private $email;
    private $phone;
    private $userRole;
    private $addedBy;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // Getters
    public function getUsername()
    {
        return $this->username;
    }
    public function getPreferredName()
    {
        return $this->preferred_name;
    }
    public function getPassword()
    {
        return $this->password;
    }

    public function getFirstName()
    {
        return $this->FirstName;
    }

    public function getLastName()
    {
        return $this->LastName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getUserRole()
    {
        return $this->userRole;
    }

    public function getAddedBy()
    {
        return $this->addedBy;
    }

    // Setters
    public function setUsername($username)
    {
        $this->username = $username;
    }
    public function setPreferredName($preferred_name)
    {
        $this->preferred_name = $preferred_name;
    }
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setFirstName($FirstName)
    {
        $this->FirstName = $FirstName;
    }

    public function setLastName($LastName)
    {
        $this->LastName = $LastName;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setUserRole($userRole)
    {
        $this->userRole = $userRole;
    }

    public function setAddedBy($addedBy)
    {
        $this->addedBy = $addedBy;
    }

    public function getTable(){
        return $this->table;
    }
       // CRUD Methods

    // Create
    public function addUser()
    {
        global $conn;
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO $this->table (username, password, FirstName, preferredName, LastName, email, phone, userRole, addedBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $this->username, $hashed_password, $this->FirstName, $this->preferred_name, $this->LastName, $this->email, $this->phone, $this->userRole, $this->addedBy);
        if ($stmt->execute()) {
            return true;
        } else {
            return $stmt->error; // Return the error message
        }
    }

    public function verifyPassword($password){
        return password_verify($password, $this->password);
    }

    // Read
    public function getUserById($id)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM $this->table WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


    public function findByUsername($username)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM $this->table WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findByRole($role)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM $this->table WHERE userRole = ?");
        $stmt->bind_param("s", $role);
        
        if(!$stmt->execute()){
            throw new Exception($stmt->error);
        }
        return $stmt->get_result();
    }

    public function resetPassword($password, $username){
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE $this->table SET password = ? WHERE username = ?");
        $stmt -> bind_param("ss", $hashedPassword, $username);
        return $stmt->execute();
    }

    // Update
    public function updateUser($id)
    {
        $stmt = $this->conn->prepare("UPDATE Users SET username = ?, FirstName = ?, preferredName = ?, LastName = ?, email = ?, phone = ?, userRole = ?, addedBy = ?,  WHERE username = ?");
        $stmt->bind_param("ssssssss", $this->username, $this->FirstName, $this->preferred_name, $this->LastName, $this->email, $this->phone, $this->userRole, $this->addedBy, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateUserRole($id, $role)
    {
        $stmt = $this->conn->prepare("UPDATE Users SET userRole = ? WHERE username = ?");
        $stmt->bind_param("ss", $role, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateProfile($username, $firstName, $lastName, $preferredName, $email, $phone, $path)
    {
        $stmt = $this->conn->prepare("UPDATE users SET FirstName = ?, LastName = ?, preferredName = ?, email = ?, phone = ?, profilePicturePath = ? WHERE username = ?");
        $stmt->bind_param("sssssss", $firstName, $lastName, $preferredName, $email, $phone,$path, $username);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getVets()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE userRole = 'Vet'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Delete
    public function deleteUser($id)
    {
        global $conn;
        $stmt = $conn->prepare("DELETE FROM $this->table WHERE id = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(DonorID, 5) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'USR-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}





