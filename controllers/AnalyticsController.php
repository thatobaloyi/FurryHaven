<?php

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include_once __DIR__ . '/../models/Animal.php';

class AnalyticsController
{
    private $animalModel;

    public function __construct()
    {
        $this->animalModel = new Animal();
    }

    public function getTotalAnimals()
    {
        return $this->animalModel->getTotalAnimals();
    }

    public function getAdoptedAnimals()
    {
        return $this->animalModel->getAdoptedAnimals();
    }

    public function getAvailableAnimals()
    {
        return $this->animalModel->getAvailableAnimals();
    }

    public function getMonthlyIntakes()
    {
        return $this->animalModel->getMonthlyIntakes();
    }

    public function getMonthlyAdoptions()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');
        return $this->animalModel->getMonthlyAdoptions($currentMonth, $currentYear);
    }

    public function getHealthyAnimals()
    {
        return $this->animalModel->getHealthyAnimals();
    }

    public function getInTreatmentAnimals()
    {
        return $this->animalModel->getInTreatmentAnimals();
    }
}
