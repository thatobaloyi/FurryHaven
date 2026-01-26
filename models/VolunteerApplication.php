<?php

include_once __DIR__ . '/../config/databaseconnection.php';

class VolunteerApplication
{
    private $db;
    private $table = 'volunteerapplication';

    // Attributes
    private $volAppID;
    private $username;
    private $applicationDate;
    private $age;
    private $applicantSkills;
    private $whyVolunteering;
    private $criminalConvictions;
    private $criminalConvictionAffidavit;
    private $certifiedID;
    private $contactableReference1;
    private $contactableReference2;
    private $indemnityForm;
    private $authorityTosearchForm;
    private $status;
    private $isDeleted;
    private $screenedBy;
    private $approvedBy;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    // Setters
    public function setVolAppID($volAppID)
    {
        $this->volAppID = $volAppID;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setApplicationDate($applicationDate)
    {
        $this->applicationDate = $applicationDate;
    }

    public function setAge($age)
    {
        $this->age = $age;
    }

    public function setApplicantSkills($applicantSkills)
    {
        $this->applicantSkills = $applicantSkills;
    }

    public function setWhyVolunteering($whyVolunteering)
    {
        $this->whyVolunteering = $whyVolunteering;
    }

    public function setCriminalConvictions($criminalConvictions)
    {
        $this->criminalConvictions = $criminalConvictions;
    }

    public function setCriminalConvictionAffidavit($criminalConvictionAffidavit)
    {
        $this->criminalConvictionAffidavit = $criminalConvictionAffidavit;
    }

    public function setCertifiedID($certifiedID)
    {
        $this->certifiedID = $certifiedID;
    }

    public function setContactableReference1($contactableReference1)
    {
        $this->contactableReference1 = $contactableReference1;
    }

    public function setContactableReference2($contactableReference2)
    {
        $this->contactableReference2 = $contactableReference2;
    }

    public function setIndemnityForm($indemnityForm)
    {
        $this->indemnityForm = $indemnityForm;
    }

    public function setAuthorityToSearchForm($authorityTosearchForm)
    {
        $this->authorityTosearchForm = $authorityTosearchForm;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    public function setScreenedBy($screenedBy)
    {
        $this->screenedBy = $screenedBy;
    }

    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    // Getters
    public function getVolAppID()
    {
        return $this->volAppID;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getApplicationDate()
    {
        return $this->applicationDate;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getApplicantSkills()
    {
        return $this->applicantSkills;
    }

    public function getWhyVolunteering()
    {
        return $this->whyVolunteering;
    }

    public function getCriminalConvictions()
    {
        return $this->criminalConvictions;
    }

    public function getCriminalConvictionAffidavit()
    {
        return $this->criminalConvictionAffidavit;
    }

    public function getCertifiedID()
    {
        return $this->certifiedID;
    }

    public function getContactableReference1()
    {
        return $this->contactableReference1;
    }

    public function getContactableReference2()
    {
        return $this->contactableReference2;
    }

    public function getIndemnityForm()
    {
        return $this->indemnityForm;
    }

    public function getAuthorityToSearchForm()
    {
        return $this->authorityTosearchForm;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    public function getScreenedBy()
    {
        return $this->screenedBy;
    }

    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    // CRUD Methods
    public function create()
    {
        $query = "INSERT INTO " . $this->table . " (volAppID, username, applicationDate, age, applicantSkills, whyVolunteering, criminalConvictions, criminalConvictionAffidavit, certifiedID, contactableReference1, contactableReference2, indemnityForm, authorityTosearchForm, status, isDeleted, screenedBy, approvedBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sssisssssssssssis", $this->volAppID, $this->username, $this->applicationDate, $this->age, $this->applicantSkills, $this->whyVolunteering, $this->criminalConvictions, $this->criminalConvictionAffidavit, $this->certifiedID, $this->contactableReference1, $this->contactableReference2, $this->indemnityForm, $this->authorityTosearchForm, $this->status, $this->isDeleted, $this->screenedBy, $this->approvedBy);
        return $stmt->execute();
    }


    public function findByUsername($username)
    {
        $query = "SELECT * FROM $this->table WHERE username = ? and isDeleted = 0";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateStatus($id)
    {
        $stmt = $this->db->prepare("UPDATE $this->table SET Status = ? WHERE volAppID = ?");
        $stmt->bind_param("ss", $this->status, $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function generateID()
    {
        $query = "SELECT MAX(CAST(SUBSTRING(volAppID, 8) AS UNSIGNED)) as max_id FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_id = ($result['max_id']) ? $result['max_id'] + 1 : 1;
        return 'VOLAPP-' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }
}
