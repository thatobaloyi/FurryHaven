<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../models/Pledges.php';
require_once '../models/PledgeInstallments.php';
require_once '../controllers/DonationsController.php'; // For createDonationFromSource
require_once '../config/databaseconnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'markPaid') {
    global $conn;
    $type = $_POST['type'];
    $id = $_POST['id'];

    $conn->begin_transaction();

    try {
        if ($type === 'pledge') {
            $pledgeModel = new Pledges();
            $pledgeData = $pledgeModel->findOne($id);
            if (!$pledgeData || $pledgeData['Status'] !== 'Pending') {
                throw new Exception('Pledge not found or is not pending.');
            }

            // Mark pledge as paid
            $pledgeModel->setDonorId($pledgeData['DonorID']);
            $pledgeModel->setCampaignId($pledgeData['CampaignID']);
            $pledgeModel->setPledgeAmount($pledgeData['PledgeAmount']);
            $pledgeModel->setInstallmentCount($pledgeData['InstallmentCount']);
            $pledgeModel->setStartDate($pledgeData['StartDate']);
            $pledgeModel->setFrequency($pledgeData['Frequency']);
            $pledgeModel->setIsActive(0); // Mark as inactive since it's paid
            $pledgeModel->setCreatedAt($pledgeData['CreatedAt']);
            $pledgeModel->setIsDeleted($pledgeData['isDeleted']);
            $pledgeModel->setStatus('Paid');

            if (!$pledgeModel->update($id)) {
                throw new Exception('Failed to update pledge status.');
            }

            // Create donation record
            $donationsController = new DonationsController();
            $donationsController->createDonationFromSource([
                'donorId' => $pledgeData['DonorID'],
                'campaignId' => $pledgeData['CampaignID'],
                'amount' => $pledgeData['PledgeAmount'],
                'paymentMethod' => 'Manual/Pledge Payment',
                'installmentId' => null,
                'frequency' => $pledgeData['Frequency']
            ]);

        } elseif ($type === 'installment') {
            $installmentModel = new PledgeInstallments();
            $instData = $installmentModel->findOne($id);
            if (!$instData || $instData['Status'] !== 'Pending') {
                throw new Exception('Installment not found or is not pending.');
            }

            // Fetch parent pledge for donor/campaign info
            $pledgeModel = new Pledges();
            $pledgeData = $pledgeModel->findOne($instData['PledgeID']);

            $installmentModel->setPledgeId($instData['PledgeID']);
            $installmentModel->setDueDate($instData['DueDate']);
            $installmentModel->setAmountDue($instData['AmountDue']);
            $installmentModel->setStatus('Paid');
            $installmentModel->setIsDeleted($instData['isDeleted']);

            if (!$installmentModel->update($id)) {
                throw new Exception('Failed to update installment status.');
            }

            // Create donation record for the installment
            $donationsController = new DonationsController();
            $donationsController->createDonationFromSource([
                'donorId' => $pledgeData['DonorID'],
                'campaignId' => $pledgeData['CampaignID'],
                'amount' => $instData['AmountDue'],
                'paymentMethod' => 'Manual/Installment Payment',
                'installmentId' => $id,
            ]);
        } else {
            throw new Exception('Invalid payment type specified.');
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Payment recorded and donation created successfully!']);

    } catch (Exception $e) {
        $conn->rollback();
        // Log the detailed error for debugging
        error_log('PayOverDueController Error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }

    $conn->close();
    exit;
}
?>