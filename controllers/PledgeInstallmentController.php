<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/PledgeInstallments.php';

class PledgeInstallmentController
{
    private $installmentModel;

    public function __construct()
    {
        $this->installmentModel = new PledgeInstallments();
    }

    // Create installments for a recurring pledge
    public function createInstallments($pledgeId, $startDate, $frequency, $installmentCount, $amountPerInstallment)
    {
        $intervalSpec = ($frequency === 'Monthly') ? 'P1M' : 'P3M';
        $dueDate = new DateTime($startDate);
        $nextIdNumber = $this->installmentModel->getNextIdNumber();

        for ($i = 0; $i < $installmentCount; $i++) {
            $installmentId = 'INSTALLMENT-' . str_pad($nextIdNumber + $i, 5, '0', STR_PAD_LEFT);
            $this->installmentModel->setInstallmentId($installmentId);
            $this->installmentModel->setPledgeId($pledgeId);
            $this->installmentModel->setDueDate($dueDate->format('Y-m-d'));
            $this->installmentModel->setAmountDue($amountPerInstallment);
            $this->installmentModel->setStatus('Pending');
            $this->installmentModel->setIsDeleted(0);
            if (!$this->installmentModel->create()) {
                throw new Exception('Failed to create pledge installment with ID: ' . $installmentId);
            }
            $dueDate->add(new DateInterval($intervalSpec));
        }
    }

    // Fetch all installments for a pledge
    public function getInstallmentsByPledge($pledgeId)
    {
        return $this->installmentModel->findByPledgeId($pledgeId);
    }

    // Get all installments ordered by overdue status
    public function getAllInstallments() {
        return $this->installmentModel->findAllOrderedByOverdue();
    }

    // (Optional) API endpoint to get installments for a pledge
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pledgeId'])) {
            $pledgeId = $_POST['pledgeId'];
            $result = $this->getInstallmentsByPledge($pledgeId);
            $installments = [];
            while ($row = $result->fetch_assoc()) {
                $installments[] = $row;
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'installments' => $installments]);
            exit;
        }
    }
}

// Uncomment below if you want to use this controller as an endpoint
$controller = new PledgeInstallmentController();
$controller->handleRequest();
?>