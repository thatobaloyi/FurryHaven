<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../models/Applications.php';
include_once __DIR__ . '/../config/databaseconnection.php';
include_once __DIR__ . '/../models/Adoption.php';
include_once __DIR__ . '/../models/Foster.php';
include_once __DIR__ . '/../models/User.php';
include_once __DIR__ . '/../models/AnimalApplication.php';
include_once __DIR__ . '/../models/VolunteerApplication.php';
include_once __DIR__ . '/../config/databaseconnection.php';
include_once __DIR__ . '/../core/functions.php';
include_once __DIR__ . '/./NotificationController.php';

class ApplicationsController
{
    private $application;
    private $notification;

    public function __construct()
    {
        $this->application = new Applications();
        $this->notification = new NotificationController();
    }

    public function index()
    {
        include __DIR__ . '/../applications2.php';
    }

    public function processApplication()
    {
        global $conn;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['applicationType'])) {
                switch ($_POST['applicationType']) {
                    case 'volunteering':
                        include_once __DIR__ . '/../models/VolunteerApplication.php';
                        $volunteerApplication = new VolunteerApplication();

                        $username = sanitizeInput($_SESSION['guest_username']);
                        $applicationDate = date('Y-m-d H:i:s');
                        $age = isset($_POST['age']) ? sanitizeInput($_POST['age']) : null;
                        $applicantSkills = isset($_POST['applicantSkills']) ? sanitizeInput($_POST['applicantSkills']) : null;
                        $whyVolunteering = isset($_POST['whyVolunteering']) ? sanitizeInput($_POST['whyVolunteering']) : null;
                        $criminalConvictions = isset($_POST['conviction']) ? sanitizeInput($_POST['conviction']) : null;

                        $uploadDir = __DIR__ . '/../images/volunteer_applications/';

                        $criminalConvictionAffidavit_path = null;
                        if (isset($_FILES['criminalConviction']) && $_FILES['criminalConviction']['error'] == UPLOAD_ERR_OK) {
                            $criminalConvictionAffidavit_tmp = $_FILES['criminalConviction']['tmp_name'];
                            $criminalConvictionAffidavit_name = uniqid() . '-' . basename($_FILES['criminalConviction']['name']);
                            $criminalConvictionAffidavit_path = $uploadDir . $criminalConvictionAffidavit_name;
                            move_uploaded_file($criminalConvictionAffidavit_tmp, $criminalConvictionAffidavit_path);
                        }

                        $certifiedID_path = null;
                        if (isset($_FILES['certifiedID']) && $_FILES['certifiedID']['error'] == UPLOAD_ERR_OK) {
                            $certifiedID_tmp = $_FILES['certifiedID']['tmp_name'];
                            $certifiedID_name = uniqid() . '-' . basename($_FILES['certifiedID']['name']);
                            $certifiedID_path = $uploadDir . $certifiedID_name;
                            move_uploaded_file($certifiedID_tmp, $certifiedID_path);
                        }

                        $indemnityForm_path = null;
                        if (isset($_FILES['indemnityForm']) && $_FILES['indemnityForm']['error'] == UPLOAD_ERR_OK) {
                            $indemnityForm_tmp = $_FILES['indemnityForm']['tmp_name'];
                            $indemnityForm_name = uniqid() . '-' . basename($_FILES['indemnityForm']['name']);
                            $indemnityForm_path = $uploadDir . $indemnityForm_name;
                            move_uploaded_file($indemnityForm_tmp, $indemnityForm_path);
                        }

                        $authorityTosearchForm_path = null;
                        if (isset($_FILES['authorityTosearchForm']) && $_FILES['authorityTosearchForm']['error'] == UPLOAD_ERR_OK) {
                            $authorityTosearchForm_tmp = $_FILES['authorityTosearchForm']['tmp_name'];
                            $authorityTosearchForm_name = uniqid() . '-' . basename($_FILES['authorityTosearchForm']['name']);
                            $authorityTosearchForm_path = $uploadDir . $authorityTosearchForm_name;
                            move_uploaded_file($authorityTosearchForm_tmp, $authorityTosearchForm_path);
                        }

                        if ($username) {
                            $volunteerApplication->setVolAppID($volunteerApplication->generateID());
                            $volunteerApplication->setUsername($username);
                            $volunteerApplication->setApplicationDate($applicationDate);
                            $volunteerApplication->setAge($age);
                            $volunteerApplication->setApplicantSkills($applicantSkills);
                            $volunteerApplication->setWhyVolunteering($whyVolunteering);
                            $volunteerApplication->setCriminalConvictions($criminalConvictions);
                            $volunteerApplication->setCriminalConvictionAffidavit($criminalConvictionAffidavit_name);
                            $volunteerApplication->setCertifiedID($certifiedID_name);
                            $volunteerApplication->setContactableReference1(null);
                            $volunteerApplication->setContactableReference2(null);
                            $volunteerApplication->setIndemnityForm($indemnityForm_name);
                            $volunteerApplication->setAuthorityToSearchForm($authorityTosearchForm_name);
                            $volunteerApplication->setStatus('Pending');
                            $volunteerApplication->setIsDeleted(0);
                            $volunteerApplication->setScreenedBy(null);
                            $volunteerApplication->setApprovedBy(null);

                            if ($volunteerApplication->create()) {
                                $_SESSION['notification'] = [
                                    'message' => "Application Submitted!",
                                    'type' => 'success'
                                ];
                                redirectTo('../volunteering.php');
                            } else {
                                $_SESSION['notification'] = [
                                    'message' => "Application Not Submitted!",
                                    'type' => 'error'
                                ];
                                redirectTo('../volunteering.php');
                            }
                        } else {
                            $_SESSION['notification'] = [
                                'message' => "Please Fill in all the required Fields.",
                                'type' => 'error'
                            ];
                            redirectTo('../volunteering.php');
                        }
                        break;

                    case "Foster":
                        echo "dum dun nin";
                        include_once __DIR__ . '/../models/AnimalApplication.php';
                        $animalApplication = new AnimalApplication();
                        $username = isset($_SESSION['username']) ? sanitizeInput($_SESSION['username']) : null;
                        $animalID = isset($_POST['Animal_ID']) ? sanitizeInput($_POST['Animal_ID']) : null;

                        $foundApplication  = $animalApplication->findOne($username, $animalID);

                        if ($foundApplication) {
                            $_SESSION['notification'] = [
                                'message' => "You have already submitted a foster application for this animal",
                                'type' => 'error'
                            ];
                            redirectTo("../animaldatabase2.php");
                        }

                        $fosterDuration = isset($_POST['fosterDuration']) ? sanitizeInput($_POST['fosterDuration']) : null;
                        $applicationDate = date('Y-m-d');
                        $IDnumber = isset($_POST['IDnumber']) ? sanitizeInput($_POST['IDnumber']) : null;
                        $passportNumber = isset($_POST['passportNumber']) ? sanitizeInput($_POST['passportNumber']) : null;
                        $age = isset($_POST['age']) ? sanitizeInput($_POST['age']) : null;
                        $city = isset($_POST['city']) ? sanitizeInput($_POST['city']) : null;
                        $province = isset($_POST['province']) ? sanitizeInput($_POST['province']) : null;
                        $postalCode = isset($_POST['postalCode']) ? sanitizeInput($_POST['postalCode']) : null;
                        $country = isset($_POST['country']) ? sanitizeInput($_POST['country']) : null;
                        $housingType = isset($_POST['housingType']) ? sanitizeInput($_POST['housingType']) : null;
                        $homeOwnershipStatus = isset($_POST['homeOwnershipStatus']) ? sanitizeInput($_POST['homeOwnershipStatus']) : null;
                        $permissionFromLandlord = isset($_POST['permissionFromLandlord']) ? sanitizeInput($_POST['permissionFromLandlord']) : null;
                        $hasFencedYard = isset($_POST['hasFencedYard']) ? sanitizeInput($_POST['hasFencedYard']) : null;
                        $allergicHousehold = isset($_POST['allergicHousehold']) ? sanitizeInput($_POST['allergicHousehold']) : null;
                        $hasOtherPets = isset($_POST['hasOtherPets']) ? sanitizeInput($_POST['hasOtherPets']) : null;
                        $numberOfPets = isset($_POST['numberOfPets']) && !empty($_POST['numberOfPets']) ? sanitizeInput($_POST['numberOfPets']) : null;
                        $whyFosterOrAdopt = isset($_POST['whyFoster']) ? sanitizeInput($_POST['whyFoster']) : null;
                        $applicationType = isset($_POST['applicationType']) ? sanitizeInput($_POST['applicationType']) : null;


                        if ($username && $animalID) {
                            $animalApplication->setAnimalAppID($animalApplication->generateID());
                            $animalApplication->setUsername($username);
                            $animalApplication->setApplicationDate($applicationDate);
                            $animalApplication->setIDnumber($IDnumber);
                            $animalApplication->setFosterDuration($fosterDuration);
                            $animalApplication->setPassportNumber($passportNumber);
                            $animalApplication->setAge($age);
                            $animalApplication->setCity($city);
                            $animalApplication->setProvince($province);
                            $animalApplication->setPostalCode($postalCode);
                            $animalApplication->setCountry($country);
                            $animalApplication->setHousingType($housingType);
                            $animalApplication->setHomeOwnershipStatus($homeOwnershipStatus);
                            $animalApplication->setPermissionFromLandlord($permissionFromLandlord);
                            $animalApplication->setHasFencedYard($hasFencedYard);
                            $animalApplication->setAllergicHousehold($allergicHousehold);
                            $animalApplication->setHasOtherPets($hasOtherPets);
                            $animalApplication->setNumberOfPets($numberOfPets);
                            $animalApplication->setWhyFosterOrAdopt($whyFosterOrAdopt);
                            $animalApplication->setHouseInspectionDate(null);
                            $animalApplication->setHouseInspectionNotes(null);
                            $animalApplication->setHouseInspectionStatus('Pending');
                            $animalApplication->setScreeningNotes(null);
                            $animalApplication->setApplicationStatus('Pending');
                            $animalApplication->setIsDeleted(0);
                            $animalApplication->setApplicationType($applicationType);
                            $animalApplication->setAnimalID($animalID);
                            $animalApplication->setInspectedBy(null);

                            if ($animalApplication->create()) {
                                $_SESSION['notification'] = [
                                    'message' => "Application Submitted!",
                                    'type' => 'success'
                                ];
                                redirectTo('../adopt.php');
                            } else {
                                $_SESSION['notification'] = [
                                    'message' => "Application Not Submitted!",
                                    'type' => 'error'
                                ];
                            }
                        } else {
                            echo "missing fields";
                        }
                        break;
                    case "Adoption":
                        include_once __DIR__ . '/../models/AnimalApplication.php';
                        $animalApplication = new AnimalApplication();
                        $username = isset($_SESSION['username']) ? sanitizeInput($_SESSION['username']) : null;
                        $animalID = isset($_POST['Animal_ID']) ? sanitizeInput($_POST['Animal_ID']) : null;
                        $foundApplication  = $animalApplication->findOne($username, $animalID);

                        if ($foundApplication) {
                            $_SESSION['notification'] = [
                                'message' => "You have already submitted an adoption application for this animal",
                                'type' => 'error'
                            ];
                            redirectTo("../adoptable.php");
                        }


                        $applicationDate = date('Y-m-d');
                        $IDnumber = isset($_POST['IDnumber']) ? sanitizeInput($_POST['IDnumber']) : null;
                        $passportNumber = isset($_POST['passportNumber']) ? sanitizeInput($_POST['passportNumber']) : null;
                        $age = isset($_POST['age']) ? sanitizeInput($_POST['age']) : null;
                        $city = isset($_POST['city']) ? sanitizeInput($_POST['city']) : null;
                        $province = isset($_POST['province']) ? sanitizeInput($_POST['province']) : null;
                        $postalCode = isset($_POST['postalCode']) ? sanitizeInput($_POST['postalCode']) : null;
                        $country = isset($_POST['country']) ? sanitizeInput($_POST['country']) : null;
                        $housingType = isset($_POST['housingType']) ? sanitizeInput($_POST['housingType']) : null;
                        $homeOwnershipStatus = isset($_POST['homeOwnershipStatus']) ? sanitizeInput($_POST['homeOwnershipStatus']) : null;
                        $permissionFromLandlord = isset($_POST['permissionFromLandlord']) ? sanitizeInput($_POST['permissionFromLandlord']) : null;
                        $hasFencedYard = isset($_POST['hasFencedYard']) ? sanitizeInput($_POST['hasFencedYard']) : null;
                        $allergicHousehold = isset($_POST['allergicHousehold']) ? sanitizeInput($_POST['allergicHousehold']) : null;
                        $hasOtherPets = isset($_POST['hasOtherPets']) ? sanitizeInput($_POST['hasOtherPets']) : null;
                        $numberOfPets = (isset($_POST['numberOfPets']) && !empty($_POST['numberOfPets'])) ? sanitizeInput($_POST['numberOfPets']) : null;
                        $whyFosterOrAdopt = isset($_POST['whyAdopt']) ? sanitizeInput($_POST['whyAdopt']) : null;
                        $applicationType = isset($_POST['applicationType']) ? sanitizeInput($_POST['applicationType']) : null;



                        if ($username && $animalID) {
                            $animalApplication->setAnimalAppID($animalApplication->generateID());
                            $animalApplication->setUsername($username);
                            $animalApplication->setApplicationDate($applicationDate);
                            $animalApplication->setIDnumber($IDnumber);

                            $animalApplication->setPassportNumber($passportNumber);
                            $animalApplication->setAge($age);
                            $animalApplication->setCity($city);
                            $animalApplication->setProvince($province);
                            $animalApplication->setPostalCode($postalCode);
                            $animalApplication->setCountry($country);
                            $animalApplication->setHousingType($housingType);
                            $animalApplication->setHomeOwnershipStatus($homeOwnershipStatus);
                            $animalApplication->setPermissionFromLandlord($permissionFromLandlord);
                            $animalApplication->setHasFencedYard($hasFencedYard);
                            $animalApplication->setAllergicHousehold($allergicHousehold);
                            $animalApplication->setHasOtherPets($hasOtherPets);
                            $animalApplication->setNumberOfPets($numberOfPets);
                            $animalApplication->setWhyFosterOrAdopt($whyFosterOrAdopt);
                            $animalApplication->setHouseInspectionDate(null);
                            $animalApplication->setHouseInspectionNotes(null);
                            $animalApplication->setHouseInspectionStatus('Pending');
                            $animalApplication->setScreeningNotes(null);
                            $animalApplication->setApplicationStatus('Pending');
                            $animalApplication->setIsDeleted(0);
                            $animalApplication->setApplicationType($applicationType);
                            $animalApplication->setAnimalID($animalID);
                            $animalApplication->setInspectedBy(null);

                            if ($animalApplication->create()) {
                                $_SESSION['notification'] = [
                                    'message' => "Application Submitted!",
                                    'type' => 'success'
                                ];
                                redirectTo('../adopt.php');
                            } else {
                                $_SESSION['notification'] = [
                                    'message' => "Application Not Submitted!",
                                    'type' => 'error'
                                ];
                            }
                        } else {
                            $_SESSION['notification'] = [
                                'message' => "You have to be logged in and have clicked on the 'Adopt Me' button for the Animal",
                                'type' => 'error'
                            ];
                        }
                        break;
                }
            }
        }
    }


    public function approveApplication()
    {
        global $conn;
        if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['type'])) {
            switch ($_POST['type']) {
                case "volunteer":
                    $volunteerApplication = new VolunteerApplication();
                    $user = new User();
                    $volunteerApplication->setStatus("Accepted");
                    $conn->begin_transaction();
                    try {
                        if ($volunteerApplication->updateStatus($_POST['volAppID'])) {
                            $found = $user->findByUsername($_POST['username']);
                            if ($found && $found['userRole'] === "Guest") {
                                if ($user->updateUserRole($found['username'], "Volunteer")) {
                                    $this->notification->sendStatusUpdateNotification($found['username'], $_POST['type']);
                                    $_SESSION['notification'] = [
                                        'message' => "Application Status Updated!",
                                        'type' => 'success'
                                    ];
                                    $conn->commit();
                                    redirectTo("../applications2.php");
                                } else {
                                    throw new Exception("Failed to Update the User Role for $_POST[username]");
                                }
                            } else {
                                throw new Exception("Only Guest Users Can Be Promoted to Volunteers!");
                            }
                        } else {
                            throw new Exception("Volunteer Application Status not updated for Application $_POST[volAppID]");
                        }
                    } catch (Exception $e) {
                        $_SESSION['notification'] = [
                            'message' => $e->getMessage(),
                            'type' => 'error'
                        ];
                        $conn->rollback();
                        redirectTo("../applications2.php");
                    }
                    break;
                case "animal": //the form needs to send information here.
                    // I also have to add a transaction.
                    $animalApplication = new AnimalApplication();
                    $animalApplication->setStatus("Accepted");

                    $conn->begin_transaction(); // begin the transaction since I have quite a lot of creates.

                    try {
                        if ($animalApplication->updateStatus($_POST['animalAppID'])) {
                            switch ($_POST["animalAppType"]) {
                                case "Adoption":
                                    $adoption = new Adoption();
                                    $foster = new Foster();
                                    $fosterResult = $foster->getFosterByAnimalId($_POST["animalID"]);
                                    $adoptionResult = $adoption->findOneByAnimalID($_POST["animalID"]);
                                    //var_dump($_POST);
                                    // var_dump($fosterResult);
                                    // var_dump($adoptionResult);
                                    if (($adoptionResult->num_rows > 0) || ($fosterResult->num_rows > 0)) {
                                        throw new Exception("Animal has already been adopted/fostered.");
                                    } else {
                                        $adoption->setId($adoption->generateID()); // set the id of the adoption
                                        $adoption->setAnimalId($_POST["animalID"]);
                                        $adoption->setAdopterId($_POST["ApplicantID"]);
                                        $adoption->setAdoptionDate(date("Y-m-d H:i:s"));
                                        $adoption->setApprovedBy($_SESSION["admin_username"]);

                                        if (!$adoption->create()) {
                                            throw new Exception("Adoption Cannot Be Created!");
                                        } else {
                                            $_SESSION['notification'] = [
                                                'message' => "Adoption Application Status Updated Successfully",
                                                'type' => 'success'
                                            ];
                                            $conn->commit();
                                            redirectTo("../applications2.php");
                                        }
                                    }
                                case "Foster":
                                    $foster = new Foster();
                                    $adoption = new Adoption();
                                    if ($foster->getFosterByAnimalId($_POST["animalID"]) || $adoption->findOneByAnimalID($_POST["animalID"])) {
                                        throw new Exception("Animal has already been fostered/adopted.");
                                    } else {
                                        $foster->setFosterID($foster->generateID()); // set the id of the foster
                                        $foster->setAnimalId($_POST["animalID"]);
                                        $foster->setFosterer($_POST["ApplicantID"]);
                                        $foster->setDuration(sanitizeInput($_POST['duration']));
                                        $foster->setApprovedBy($_SESSION["admin_username"]);

                                        if (!$foster->createFoster()) {
                                            throw new Exception("Foster Cannot Be Created!");
                                        } else {
                                            $_SESSION['notification'] = [
                                                'message' => "Foster Application Status Updated Successfully",
                                                'type' => 'success'
                                            ];
                                            $conn->commit();
                                            redirectTo("../applications2.php");
                                        }
                                    }
                            }
                        } else {
                            throw new Exception("Application Status Not Updated!");
                        }
                    } catch (Exception $e) {
                        $_SESSION['notification'] = [
                            'message' => $e->getMessage(),
                            'type' => 'error'
                        ];
                        $conn->rollback();
                        redirectTo("../applications2.php");
                    }
                    break;
                case "boarding":
                    $boardingApplication = new Boarding();
                    $boardingApplication->setStatus("Accepted");
                    if ($boardingApplication->updateStatus($_GET['id'])) {
                        echo "application status updated";
                    } else {
                        "status approved";
                    }
                    break;

                default:
                    echo "error";
            }
        }
    }


    public function rejectApplication()
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['type'])) {
            switch ($_POST['type']) {
                case "volunteer":
                    $volunteerApplication = new VolunteerApplication();
                    $volunteerApplication->setStatus("Rejected");
                    if ($volunteerApplication->updateStatus($_POST['volAppID'])) {
                        $_SESSION['notification'] = [
                            'message' => "Application Status Updated!",
                            'type' => 'success'
                        ];
                        redirectTo("../applications2.php");
                    } else {
                        $_SESSION['notification'] = [
                            'message' => "Application Status Not Updated!",
                            'type' => 'error'
                        ];
                        redirectTo("../applications2.php");
                    }
                    break;

                case "animal":
                    $animalApplication = new AnimalApplication();
                    $animalApplication->setStatus("Rejected");
                    if ($animalApplication->updateStatus($_POST['animalID'])) {
                        $_SESSION['notification'] = [
                            'message' => "Application Status Updated!",
                            'type' => 'success'
                        ];
                        redirectTo("../applications2.php");
                    } else {
                        $_SESSION['notification'] = [
                            'message' => "Application Status Not Updated!",
                            'type' => 'error'
                        ];
                        redirectTo("../applications2.php");
                    }
                    break;


                case "boarding":
                    $boardingApplication = new Boarding();
                    $boardingApplication->setStatus("Rejected");
                    if ($boardingApplication->updateStatus($_GET['id'])) {
                        $_SESSION['notification'] = [
                            'message' => "Application Status Updated!",
                            'type' => 'success'
                        ];
                        redirectTo("../applications2.php");
                    } else {
                        $_SESSION['notification'] = [
                            'message' => "Application Status Not Updated!",
                            'type' => 'error'
                        ];
                        redirectTo("../applications2.php");
                    }
                    break;

                default:
                    echo "error";
            }
        }
    }
}


$application = new ApplicationsController();


if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'submit':
            $application->processApplication();
            break;
        case 'approve':
            $application->approveApplication();
            break;
        case 'reject':
            $application->rejectApplication();
            break;
    }
}
