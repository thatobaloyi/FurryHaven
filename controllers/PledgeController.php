<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Pledges.php';
require_once __DIR__ . '/../models/PledgeInstallments.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/PledgeInstallmentController.php';
require_once __DIR__ . '/../vendor/autoload.php'; // for PHPMailer



class PledgeController
{
    private $pledgeModel;
    private $installmentModel;
    private $userModel;

    public function __construct()
    {
        $this->pledgeModel = new Pledges();
        $this->installmentModel = new PledgeInstallments();
        $this->userModel = new User();
    }

    // Create a new pledge
    public function processPledge()
    {
        global $conn;
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['username'])) {
                echo json_encode(['success' => false, 'message' => 'Authentication required. Please log in.']);
                exit;
            }

            $conn->begin_transaction();

            try {
                // Get form data
                $pledgeId = $this->pledgeModel->generateID();
                $donorId = $_SESSION['username'];
                $campaignId = isset($_POST["CampaignID"]) && $_POST["CampaignID"] !== "" ? sanitizeInput($_POST["CampaignID"]) : null;
                $pledgeAmount = floatval($_POST["PledgeAmount"]);
                $frequency = sanitizeInput($_POST["frequency"]);
                $startDate = sanitizeInput($_POST["StartDate"]);
                $isActive = 1;
                $createdAt = date('Y-m-d H:i:s');
                $isDeleted = 0;
                $status = 'Pending'; // Default status for new pledges

                // Compute installment count
                $installmentCount = ($frequency === 'Once') ? 1 : (($frequency === 'Monthly') ? 12 : (($frequency === 'Quarterly') ? 4 : 1));

                // Set pledge fields
                $this->pledgeModel->setId($pledgeId);
                $this->pledgeModel->setDonorId($donorId);
                $this->pledgeModel->setCampaignId($campaignId);
                $this->pledgeModel->setPledgeAmount($pledgeAmount);
                $this->pledgeModel->setFrequency($frequency);
                $this->pledgeModel->setStartDate($startDate);
                $this->pledgeModel->setIsActive($isActive);
                $this->pledgeModel->setCreatedAt($createdAt);
                $this->pledgeModel->setIsDeleted($isDeleted);
                $this->pledgeModel->setInstallmentCount($installmentCount);
                $this->pledgeModel->setStatus($status);

                // Create the pledge (always)
                if (!$this->pledgeModel->create()) {
                    throw new Exception('Failed to create new pledge');
                }

                // If recurring, create installments
                if ($frequency === 'Monthly' || $frequency === 'Quarterly') {
                    $installmentAmount = $pledgeAmount / $installmentCount;
                    $installmentController = new PledgeInstallmentController();
                    $installmentController->createInstallments($pledgeId, $startDate, $frequency, $installmentCount, $installmentAmount);
                }

                $conn->commit();

                // Email logic (unchanged)
                $userStmt = $conn->prepare("SELECT email, FirstName FROM users WHERE username = ?");
                $userStmt->bind_param("s", $donorId);
                $userStmt->execute();
                $userResult = $userStmt->get_result()->fetch_assoc();
                $userEmail = $userResult['email'] ?? null;
                $userName = $userResult['FirstName'] ?? 'Supporter';

                if ($userEmail) {
                    $mail = new PHPMailer\PHPMailer\PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'furryhavendonations@gmail.com';
                    $mail->Password = 'oanj gsxj qumr voni';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('furryhavendonations@gmail.com', 'FurryHaven Donation Platform');
                    $mail->addAddress($userEmail);
                    $mail->Subject = "Thank you for your pledge!";
                    $mail->Body = "Dear $userName,\n\nThank you for making a pledge of R{$pledgeAmount} to FurryHaven. We appreciate your support!\n\nYou can log in to your account to view or fulfill your pledge.\n\nBest regards,\nFurryHaven Team";

                    if (!$mail->send()) {
                        error_log('Pledge Mailer Error: ' . $mail->ErrorInfo);
                    }
                }

                echo json_encode(['success' => true, 'message' => 'Pledge created successfully! Thank you.']);
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                error_log('PledgeController Error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                exit;
            }
            $conn->close();
        }
    }

    public function sendOverduePledgeReminders() {
        global $conn;
        require_once __DIR__ . '/../vendor/autoload.php'; // for PHPMailer

        $today = date('Y-m-d');
        $query = "SELECT p.*, u.email, u.FirstName FROM pledges p
                  JOIN users u ON p.DonorID = u.username
                  WHERE p.isDeleted = 0 AND p.IsActive = 1 AND p.Frequency = 'Once' AND p.StartDate < ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($pledge = $result->fetch_assoc()) {
            $userEmail = $pledge['email'];
            $userName = $pledge['FirstName'] ?? 'Supporter';
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'furryhavendonations@gmail.com';
            $mail->Password = 'oanj gsxj qumr voni';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('furryhavendonations@gmail.com', 'FurryHaven Donation Platform');
            $mail->addAddress($userEmail);
            $mail->Subject = "Pledge Payment Reminder";
            $mail->Body = "Dear $userName,\n\nThis is a friendly reminder that your pledge of R{$pledge['PledgeAmount']} was due on {$pledge['StartDate']}.\n\nPlease log in to your account to fulfill your pledge. Thank you for supporting FurryHaven!\n\nBest regards,\nFurryHaven Team";

            if (!$mail->send()) {
                error_log('Pledge Reminder Mailer Error: ' . $mail->ErrorInfo);
            }
        }
    }

    public function getAllPledges() {
        return $this->pledgeModel->findAllOrderedByOverdue();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST'  && isset($_POST['action'])) {
    $controller = new PledgeController();
    switch ($_POST['action']) {
        case 'create':
            $controller->processPledge();
            break;
        
        default:
            # code...
            break;
    }
}




























?>