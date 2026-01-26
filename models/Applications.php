<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class Applications
{
    private $table = 'applications';
    private $conn;

    private $applicationsID;
    private $applicantFirstName;
    private $applicantLastName;
    private $applicantPhone;
    private $applicantEmail;
    private $applicantSkills;
    private $certID;
    private $certAffidavit;
    private $certCriminalConvictions;
    private $idemnityForm;
    private $references;
    private $cv;
    private $coverLetter;
    private $animalID;
    private $applicationStatus;
    private $applicationType;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // Getters
    public function getApplicationsID()
    {
        return $this->applicationsID;
    }

    public function getApplicantFirstName()
    {
        return $this->applicantFirstName;
    }

    public function getApplicantLastName()
    {
        return $this->applicantLastName;
    }

    public function getApplicantPhone()
    {
        return $this->applicantPhone;
    }

    public function getApplicantEmail()
    {
        return $this->applicantEmail;
    }

    public function getApplicantSkills()
    {
        return $this->applicantSkills;
    }

    public function getCertID()
    {
        return $this->certID;
    }

    public function getCertAffidavit()
    {
        return $this->certAffidavit;
    }

    public function getCertCriminalConvictions()
    {
        return $this->certCriminalConvictions;
    }

    public function getIdemnityForm()
    {
        return $this->idemnityForm;
    }

    public function getReferences()
    {
        return $this->references;
    }

    public function getCv()
    {
        return $this->cv;
    }

    public function getCoverLetter()
    {
        return $this->coverLetter;
    }

    public function getApplicationStatus()
    {
        return $this->applicationStatus;
    }

    public function getApplicationType()
    {
        return $this->applicationType;
    }

    // Setters
    public function setApplicationsID($applicationsID)
    {
        $this->applicationsID = $applicationsID;
    }

    public function setApplicantFirstName($applicantFirstName)
    {
        $this->applicantFirstName = $applicantFirstName;
    }

    public function setApplicantLastName($applicantLastName)
    {
        $this->applicantLastName = $applicantLastName;
    }

    public function setApplicantPhone($applicantPhone)
    {
        $this->applicantPhone = $applicantPhone;
    }

    public function setAnimalID($animalID){
        $this->animalID = $animalID;
    }
    public function setApplicantEmail($applicantEmail)
    {
        $this->applicantEmail = $applicantEmail;
    }

    public function setApplicantSkills($applicantSkills)
    {
        $this->applicantSkills = $applicantSkills;
    }

    public function setCertID($certID)
    {
        $this->certID = $certID;
    }

    public function setCertAffidavit($certAffidavit)
    {
        $this->certAffidavit = $certAffidavit;
    }

    public function setCertCriminalConvictions($certCriminalConvictions)
    {
        $this->certCriminalConvictions = $certCriminalConvictions;
    }

    public function setIdemnityForm($idemnityForm)
    {
        $this->idemnityForm = $idemnityForm;
    }

    public function setReferences($references)
    {
        $this->references = $references;
    }

    public function setCv($cv)
    {
        $this->cv = $cv;
    }

    public function setCoverLetter($coverLetter)
    {
        $this->coverLetter = $coverLetter;
    }

    public function setApplicationStatus($applicationStatus)
    {
        $this->applicationStatus = $applicationStatus;
    }

    public function setApplicationType($applicationType)
    {
        $this->applicationType = $applicationType;
    }

    // CRUD Methods

    // Create
    public function createApplication()
    {
        $query = "INSERT INTO " . $this->table . " (applicationsID, applicantFirstName, applicantLastName, applicantPhone, applicantEmail, applicantSkills, certID, certAffidavit, certCriminalConvictions, idemnityForm, `references`, cv, coverLetter, applicationStatus, applicationType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssssssssssss", $this->applicationsID, $this->applicantFirstName, $this->applicantLastName, $this->applicantPhone, $this->applicantEmail, $this->applicantSkills, $this->certID, $this->certAffidavit, $this->certCriminalConvictions, $this->idemnityForm, $this->references, $this->cv, $this->coverLetter, $this->applicationStatus, $this->applicationType);

        if ($stmt->execute()) {
            $this->applicationsID = $this->conn->insert_id;
            return true;
        }
        return false;
    }

    // Read
    public function getApplicationById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE applicationsID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getApplicationByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE applicationsID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update
    public function updateApplication()
    {
        $query = "UPDATE " . $this->table . " SET applicantFirstName = ?, applicantLastName = ?, applicantPhone = ?, applicantEmail = ?, applicantSkills = ?, certID = ?, certAffidavit = ?, certCriminalConvictions = ?, idemnityForm = ?, `references` = ?, cv = ?, coverLetter = ?, applicationStatus = ?, applicationType = ? WHERE applicationsID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssssssssssi", $this->applicantFirstName, $this->applicantLastName, $this->applicantPhone, $this->applicantEmail, $this->applicantSkills, $this->certID, $this->certAffidavit, $this->certCriminalConvictions, $this->idemnityForm, $this->references, $this->cv, $this->coverLetter, $this->applicationStatus, $this->applicationType, $this->applicationsID);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete
    public function deleteApplication($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE applicationsID = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(applicationsID, 5) AS UNSIGNED)) as max_id FROM $this->table";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'APP-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}