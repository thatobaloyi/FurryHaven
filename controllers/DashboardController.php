<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once __DIR__ . '/../core/functions.php'; // For redirectTo, isLoggedIn
require_once __DIR__ . '/../controllers/AnalyticsController.php'; // For redirectTo, isLoggedIn
require_once __DIR__ . '/../controllers/AnimalController.php'; // For redirectTo, isLoggedIn
require_once __DIR__ . '/../controllers/MedicalProcedureController.php'; // For redirectTo, isLoggedIn

class DashboardController
{
    private $analyticsController;
    private $animalController;
    private $medicalProcedureController;

    public function __construct()
    {
        $this->analyticsController = new AnalyticsController();
        $this->animalController = new AnimalController();
        $this->medicalProcedureController = new MedicalProcedureController();
    }

    public function index()
    {
        if (!isLoggedIn()) {
            redirectTo("../login.php");
        }
        
        $userRole = $_SESSION['user_role'];
        $dashboardFile = '';
        switch ($userRole) {
            case 'Admin':
            case 'Vet':
            case 'Volunteer':
                [$result, $search, $filter] = $this->animalController->index();
                $totalAnimals = $this->analyticsController->getTotalAnimals();
                $adopted = $this->analyticsController->getAdoptedAnimals();  
                $available = $this->analyticsController->getAvailableAnimals();
                $monthlyIntakes = $this->analyticsController->getMonthlyIntakes();
                $monthlyAdoptions = $this->analyticsController->getMonthlyAdoptions();
                $healthy  = $this->analyticsController->getHealthyAnimals();
                $inTreatment = $this->analyticsController->getInTreatmentAnimals();
                $dashboardFile = __DIR__ . '/../dashboard2.php';
                break;
            case 'Guest':
                
                $dashboardFile = __DIR__ . '/../homepage.php';
                break;
            // case 'Volunteer':

            //     $dashboardFile = __DIR__ . '/../dashboard2.php';
            //     break;
            // Add other roles here as separate cases
            default:
                // Handle cases where the role is invalid or not found
                redirectTo("/login");
                return; // Stop execution
        }

        if (file_exists($dashboardFile)) {
            require_once $dashboardFile;
        } else {
            // This case should ideally never be reached with the switch statement
            echo "Dashboard not found!";
            redirectTo("/login");
        }
    }

}