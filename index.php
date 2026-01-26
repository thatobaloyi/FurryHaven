<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Adjust paths relative to this file's location (public/)


require_once './core/functions.php';
require_once './config/databaseconnection.php';
require_once './models/User.php';
// ... all your require_once statements ...
require_once './controllers/AuthController.php';
require_once './controllers/AnimalController.php';
require_once './controllers/DonationsController.php';
require_once './controllers/UserController.php';
require_once './controllers/DashboardController.php';
require_once './controllers/CrueltyReportController.php';
require_once './controllers/AdoptionController.php';

require_once './controllers/KennelController.php';
require_once './controllers/CageController.php';
require_once './controllers/CampaignController.php';
require_once './controllers/ApplicationsController.php';
require_once './controllers/MedicalProcedureController.php';
require_once './controllers/BoardingPaymentController.php';
require_once './controllers/BoardingAnimalController.php';
require_once './controllers/BoardingController.php';

// A simple router function
function route($uri, $routes)
{
    // Your existing routing logic from your original index file
    if (isset($routes[$uri])) {
        $controller_name = $routes[$uri][0];
        $method_name = $routes[$uri][1];
        if (class_exists($controller_name)) {
            $controller = new $controller_name();
            if (method_exists($controller, $method_name)) {
                $controller->$method_name($_SERVER['REQUEST_METHOD']);
            } else {
                error_404("Action '{$method_name}' not found for controller '{$controller_name}'.");
            }
        } else {
            error_404("Controller '{$controller_name}' not found.");
        }
    } else {
        error_404("Page not found.");
    }
}

// Start output buffering to capture the controller's output
ob_start();

// Your route definitions
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route_key = ltrim($request_uri, '/');
if (substr($route_key, -1) === '/') {
    $route_key = rtrim($route_key, '/');
}

$routes = [
    'medical'                                  => ['MedicalProcedureController', 'index'],
    'medical/submit'                                  => ['MedicalProcedureController', 'addMedicalProcedure'],
    'medical/update'                                  => ['MedicalProcedureController', 'updateMedicalProcedure'],
    'medical/updatebatch'                                  => ['MedicalProcedureController', 'updateMedicalProcedureBatch'],
    'medical/delete'                                  => ['MedicalProcedureController', 'deleteMedicalProcedure'],
    ''                                  => ['DashboardController', 'index'],
    'kennels'                                  => ['KennelController', 'index'],
    'dashboard'                                  => ['DashboardController', 'index'],
    'application/submit'                => ['ApplicationsController', 'processApplication'],
    'applications'                => ['ApplicationsController', 'index'],
    'application/approve'                => ['ApplicationsController', 'approveApplication'],
    'application/reject'                => ['ApplicationsController', 'rejectApplication'],
    'login'                             => ['AuthController', 'showLoginForm'],
    'login-process'                     => ['AuthController', 'processLogin'],
    'logout'                            => ['AuthController', 'logout'],
    'register'                  => ["AnimalController", "showRegistrationForm"],
    'animals'                  => ["AnimalController", "showAnimals"],
    'animals/details'                  => ["AnimalController", "showAnimalDetails"],
    'animals/update'                  => ["AnimalController", "showUpdateForm"],
    'animals/filterSearch'                  => ["AnimalController", "index"],
    'animals/process-update'                  => ["AnimalController", "processUpdate"],
    'animals/process-registration'      => ['AnimalController', 'processRegistration'],
    'animals/delete'                  => ["AnimalController", "animalDelete"],
    'animals/softdelete'                  => ["AnimalController", "animalSoftDelete"],
    'animals/restore'      => ['AnimalController', 'animalRestore'],
    'donate'                            => ['DonationsController', 'showDonationForm'],
    'donate/submit'           => ['DonationsController', 'processDonation'],
    'user/create'  => ['UserController', 'index'],
    'user/reset-password'  => ['UserController', 'resetPassword'],
    'user/process_create'                          => ['UserController', 'createUser'],
    'cruelty'   => ['CrueltyReportController', 'showCrueltyReportForm'],
    'cruelties'   => ['CrueltyReportController', 'index'],
    'cruelty/softdelete'   => ['CrueltyReportController', 'softDeleteCrueltyReport'],
    'cruelty/delete'   => ['CrueltyReportController', ' deleteCrueltyReport'],
    'cruelty/submit'   => ['CrueltyReportController', 'processCrueltyReport'],
    'cruelty/restore'   => ['CrueltyReportController', 'crueltyRestore'],
    'adopt'   => ['AdoptionController', 'showAdoptionForm'],
    'adopt/submit'   => ['AdoptionController', 'processAdoption'],
    'volunteering' => ['VolunteerController', 'showRegistrationForm'],
    'volunteering/submit' => ['VolunteerController', 'processRegistration'],
    'kennel/create' => ['KennelController', 'showCreateForm'],
    'kennel/process_create' => ['KennelController', 'processCreate'],
    'cage/create'   => ['CageController', 'showCreateForm'],
    'cage/process_create'   => ['CageController', 'processCreate'],
    'campaign/create'   => ['CampaignController', 'showCreateForm'],
    'campaigns'   => ['CampaignController', 'index'],
    'campaign/softdelete'   => ['CampaignController', 'softDeleteCampaign'],
    'campaign/delete'   => ['CampaignController', 'deleteCampaign'],
    'campaign/process_create'   => ['CampaignController', 'processCreate'],
    'boarding/payment/process' => ['BoardingPaymentController', 'process'],
    'boarding_animal/process_create' =>    ['BoardingAnimalController', 'processCreate'],
    'boarding_animal/process_update' =>    ['BoardingAnimalController', 'processUpdate'],
    'boarding_animal/process_delete' =>    ['BoardingAnimalController', 'processDelete'],
    'userAnimals'                      => ['BoardingAnimalController', 'showAllAnimals'],
    'boarding/checkin' => ['BoardingController', 'checkIn'],
    'boarding/checkout' => ['BoardingController', 'checkOut'],
    'medical/proceduresForCalendar' => ['MedicalProcedureController', 'getProceduresForCalendar'],
    'medical/proceduresForDay' => ['MedicalProcedureController', 'getProceduresForDay'],
    // 'dashboard'                         => ['DashboardController', 'index'],

];

// Execute the routing logic
route($route_key, $routes);

// Get the captured content from the buffer
$mainContent = ob_get_clean();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php echo $_SESSION['user_role']?> Dashboard</title>
    <link rel="stylesheet" href="style2.css">
</head>

<body>
        <div>
            <?php
            require_once './notification.php';
            // The captured content from the controller is inserted here
            echo $mainContent;
            ?>
        </div>
    </div>
</body>

</html>

<?php
function error_404($message = "Page not found.")
{
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "<p>{$message}</p>";
    exit();
}

?>

