<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../models/Donation.php';
include_once __DIR__ . '/../models/Campaign.php';
include_once __DIR__ . '/../config/databaseconnection.php';
require_once __DIR__ . '/../core/functions.php'; // For redirectTo, isLoggedIn

class DonationsController
{
    private $donation;
    private $campaign;

    public function __construct()
    {
        $this->donation = new Donation();
        $this->campaign = new Campaign();
    }

    public function index()
    {
        $campaigns = $this->campaign->findAll();
        include __DIR__ . '/../donate.php';
    }

    public function processDonation()
    {
        global $conn;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isLoggedIn()) {
                $_SESSION['error_message'] = 'You must be logged in to make a donation.';
                redirectTo('../login.php');
            }

            $conn->begin_transaction();

            try {
                $this->donation->setId($this->donation->generateID());
                $this->donation->setDonorId($_SESSION['username']);
                $this->donation->setCampaignId(isset($_POST["CampaignID"]) && $_POST["CampaignID"] !== "" ? sanitizeInput($_POST["CampaignID"]) : null);
                $this->donation->setDonationType(sanitizeInput($_POST["DonationType"]));
                $this->donation->setDonationAmount(sanitizeInput($_POST["DonationAmount"]));
                $flag = $this->donation->getDonationAmount() > 2000 ? 1 : 0;
                $this->donation->setDonationFlag($flag);
                $this->donation->setDonationDate(date('Y-m-d H:i:s'));
                $this->donation->setPaymentMethod(isset($_POST["PaymentMethod"]) ? sanitizeInput($_POST["PaymentMethod"]) : null);
                $this->donation->setInstallmentId(isset($_POST["installmentID"]) && $_POST["installmentID"] !== "" ? sanitizeInput($_POST["installmentID"]) : null);
                $this->donation->setIsDeleted('0');
                $this->donation->setIsRecurring(isset($_POST["IsRecurring"]) ? (int)$_POST["IsRecurring"] : 0);
                $this->donation->setIsActive(1);
                $this->donation->setFrequency(
                    (isset($_POST["Frequency"]) && $_POST["Frequency"] !== "") ? sanitizeInput($_POST["Frequency"]) : null
                );

                // Compute NextBillingDate if recurring
                if ($this->donation->getIsRecurring() && $this->donation->getFrequency()) {
                    $startDate = new DateTime($this->donation->getDonationDate());
                    if ($this->donation->getFrequency() === 'Monthly') {
                        $startDate->modify('+1 month');
                    } elseif ($this->donation->getFrequency() === 'Quarterly') {
                        $startDate->modify('+3 months');
                    }
                    $this->donation->setNextBillingDate($startDate->format('Y-m-d'));
                } else {
                    $this->donation->setNextBillingDate(null);
                }
               

                // Optionally update campaign raised amount
                if ($this->donation->getCampaignId()) {
                    $campaign = $this->campaign->findOne($this->donation->getCampaignId());
                    if ($campaign) {
                        $this->campaign->setRaisedAmount((float)$campaign['amountRaised'] + (float)$this->donation->getDonationAmount());
                        if (!$this->campaign->updateRaisedAmount($campaign['CampaignID'])) {
                            throw new Exception("Cannot Update the Campaign Amount");
                        }
                    } else {
                        throw new Exception("Campaign Not Found!");
                    }
                }

                if (!$this->donation->create()) {
                    throw new Exception('Failed to create new donation');
                }

                // Generate receipt and update donation
                $receiptPath = $this->generateReceipt($this->donation->getId());
                $this->donation->setReceiptIssued($receiptPath);
                // Update the donation record with the receipt path
                $updateQuery = "UPDATE donations SET ReceiptIssued = ? WHERE DonationID = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("ss", $receiptPath, $this->donation->getId());
                $updateStmt->execute();

                $conn->commit();

                // Send confirmation email with receipt
                $userEmail = $_SESSION['email'] ?? null;
                if (!$userEmail) {
                    $userStmt = $conn->prepare("SELECT email FROM users WHERE username = ?");
                    $userStmt->bind_param("s", $_SESSION['username']);
                    $userStmt->execute();
                    $userResult = $userStmt->get_result()->fetch_assoc();
                    $userEmail = $userResult['email'] ?? null;
                }
                if ($userEmail) {
                    require_once __DIR__ . '/../vendor/autoload.php'; // for PHPMailer
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
                    $mail->Subject = "Thank you for your donation!";
                    $mail->Body = "Your donation has been received. Thank you for your support! Please find your receipt attached.";

                    // Attach the PDF using the correct filesystem path
                    $receiptFile = __DIR__ . '/../receipts/' . basename($receiptPath);
                    $mail->addAttachment($receiptFile);

                    if (!$mail->send()) {
                        error_log('Mailer Error: ' . $mail->ErrorInfo);
                        $_SESSION['notification'] = [
                            'message' => "Mailer Error: " . $mail->ErrorInfo,
                            'type' => 'error'
                        ];
                    }
                }

                // Send a subscription confirmation if this is a recurring donation (only on initial setup)
                if ($this->donation->getIsRecurring()) {
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
                    $mail->Subject = "Thank you for subscribing to recurring donations!";
                    $mail->Body = "Thank you for setting up a recurring donation with FurryHaven! Your ongoing support means so much to us and the animals we care for. You will receive a receipt each time your donation is processed.";

                    if (!$mail->send()) {
                        error_log('Mailer Error (recurring): ' . $mail->ErrorInfo);
                        echo 'Mailer Error (recurring): ' . $mail->ErrorInfo; // For debugging
                    }
                }

                $_SESSION['notification'] = [
                    'message' => "Donation has been made, Thank you for the Donation!",
                    'type' => 'success'
                ];
                redirectTo("../my_donations.php");
            } catch (Exception $e) {
                $_SESSION['notification'] = [
                    'message' => "Error: " . $e->getMessage(),
                    'type' => 'error'
                ];
                $conn->rollback();
                redirectTo("../my_donations.php");
            }
            $conn->close();
        }
    }

    // Example: Generate a PDF receipt and return the file path
    private function generateReceipt($donationId)
    {
        require_once __DIR__ . '/../fpdf.php';

        $donation = $this->donation->findOne($donationId);

        $receiptDir = __DIR__ . '/../receipts/';
        if (!is_dir($receiptDir)) {
            mkdir($receiptDir, 0777, true);
        }
        $fileName = "receipt_" . $donationId . ".pdf";
        $filePath = $receiptDir . $fileName;

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        $pdf->Cell(0, 10, 'Donation Receipt', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Donor: ' . $donation['DonorID'], 0, 1);
        $pdf->Cell(0, 10, 'Amount: R' . number_format($donation['DonationAmount'], 2), 0, 1);
        $pdf->Cell(0, 10, 'Date: ' . $donation['DonationDate'], 0, 1);
        $pdf->Cell(0, 10, 'Type: ' . $donation['DonationType'], 0, 1);
        $pdf->Cell(0, 10, 'Payment Method: ' . $donation['PaymentMethod'], 0, 1);

        $pdf->Ln(10);
        $pdf->Cell(0, 10, 'Thank you for your generosity!', 0, 1, 'C');

        $pdf->Output('F', $filePath);

        // Return the correct relative path for the web
        return '/SysDev/EzTeck/receipts/' . $fileName;
    }

    public function processRecurringDonations() {
        global $conn;
        // 1. Get all due recurring donations
        $dueDonations = $this->donation->findAllRecurringDue();

        foreach ($dueDonations as $recurring) {
            try {
                $conn->begin_transaction();

                // 2. Create a new donation record (copy most fields, new ID, new date)
                $newDonation = new Donation();
                $newDonationId = $newDonation->generateID();
                $newDonation->setId($newDonationId);
                $newDonation->setDonorId($recurring['DonorID']);
                $newDonation->setCampaignId($recurring['CampaignID']);
                $newDonation->setDonationType($recurring['DonationType']);
                $newDonation->setDonationAmount($recurring['DonationAmount']);
                $flag = $recurring['DonationAmount'] > 2000 ? 1 : 0;
                $newDonation->setDonationFlag($flag);
                $now = date('Y-m-d H:i:s');
                $newDonation->setDonationDate($now);
                $newDonation->setPaymentMethod($recurring['PaymentMethod']);
                $newDonation->setReceiptIssued(null);
                $newDonation->setInstallmentId($recurring['installmentID']);
                $newDonation->setIsDeleted('0');
                $newDonation->setIsRecurring($recurring['IsRecurring']);
                $newDonation->setIsActive(1);
                $newDonation->setFrequency($recurring['Frequency']);

                // 3. Compute and set the next billing date for the recurring record
                $nextBilling = new DateTime($recurring['NextBillingDate']);
                if ($recurring['Frequency'] === 'Monthly') {
                    $nextBilling->modify('+1 month');
                } elseif ($recurring['Frequency'] === 'Quarterly') {
                    $nextBilling->modify('+3 months');
                }
                $newDonation->setNextBillingDate($nextBilling->format('Y-m-d'));

                // 4. Create the new donation record
                if (!$newDonation->create()) {
                    throw new Exception('Failed to create recurring donation');
                }

                // 5. Update the original recurring record's NextBillingDate
                $updateRecurring = $this->donation->updateNextBillingDate($recurring['DonationID'], $nextBilling->format('Y-m-d'));
                if (!$updateRecurring) {
                    throw new Exception('Failed to update next billing date');
                }

                // 6. Generate receipt and update donation
                $receiptPath = $this->generateReceipt($newDonationId);
                $newDonation->setReceiptIssued($receiptPath);
                $updateQuery = "UPDATE donations SET ReceiptIssued = ? WHERE DonationID = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("ss", $receiptPath, $newDonationId);
                $updateStmt->execute();

                $conn->commit();

                // 7. Send confirmation email with receipt
                $userStmt = $conn->prepare("SELECT email FROM users WHERE username = ?");
                $userStmt->bind_param("s", $recurring['DonorID']);
                $userStmt->execute();
                $userResult = $userStmt->get_result()->fetch_assoc();
                $userEmail = $userResult['email'] ?? null;

                if ($userEmail) {
                    require_once __DIR__ . '/../vendor/autoload.php'; // for PHPMailer
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
                    $mail->Subject = "Thank you for your recurring donation!";
                    $mail->Body = "Your recurring donation has been processed. Thank you for your continued support! Please find your receipt attached.";

                    $mail->addAttachment(__DIR__ . '/../receipts/' . basename($receiptPath));

                    if (!$mail->send()) {
                        error_log('Mailer Error: ' . $mail->ErrorInfo);
                    }
                }

            } catch (Exception $e) {
                $conn->rollback();
                error_log("Recurring donation error: " . $e->getMessage());
                // Optionally notify admin or log error
            }
        }
    }

    /**
     * Creates a donation record from a source like a paid pledge or installment.
     * This method handles creating the donation, generating a receipt, and sending a confirmation email.
     * It's designed to be called from other controllers within a transaction.
     *
     * @param array $donationData An associative array with donation details:
     * - 'donorId' (string)
     * - 'campaignId' (string|null)
     * - 'amount' (float)
     * - 'paymentMethod' (string)
     * - 'installmentId' (string|null)
     * - 'frequency' (string|null)
     * @return bool True on success, throws Exception on failure.
     * @throws Exception
     */
    public function createDonationFromSource(array $donationData)
    {
        global $conn;

        // 1. Create a new donation record
        $newDonation = new Donation();
        $newDonationId = $newDonation->generateID();
        $newDonation->setId($newDonationId);
        $newDonation->setDonorId($donationData['donorId']);
        $newDonation->setCampaignId($donationData['campaignId'] ?? null);
        $newDonation->setDonationType('Monetary'); // Pledges are monetary
        $newDonation->setDonationAmount($donationData['amount']);
        $flag = $donationData['amount'] > 2000 ? 1 : 0;
        $newDonation->setDonationFlag($flag);
        $newDonation->setDonationDate(date('Y-m-d H:i:s'));
        $newDonation->setPaymentMethod($donationData['paymentMethod']);
        $newDonation->setReceiptIssued(null); // Will be set after generation
        $newDonation->setInstallmentId($donationData['installmentId'] ?? null);
        $newDonation->setIsDeleted(0);
        $newDonation->setIsRecurring(0); // These are one-off payments for existing pledges/installments
        $newDonation->setIsActive(1);

       
        $newDonation->setFrequency('Once');
        $newDonation->setNextBillingDate(null);

        try {
            $newDonation->create();
        } catch (Exception $e) {
            // Re-throw with more context for better debugging.
            // This will be caught by the calling controller (e.g., PayOverDueController).
            throw new Exception('Failed to create donation record from source. Database error: ' . $e->getMessage());
        }

        // 2. Update campaign amount if applicable
        if ($newDonation->getCampaignId()) {
            $campaign = $this->campaign->findOne($newDonation->getCampaignId());
            if ($campaign) {
                $this->campaign->setRaisedAmount((float)$campaign['amountRaised'] + (float)$newDonation->getDonationAmount());
                if (!$this->campaign->updateRaisedAmount($campaign['CampaignID'])) {
                    // Don't throw, but log. The donation is more important.
                    error_log("Could not update campaign amount for campaign ID: " . $newDonation->getCampaignId());
                }
            }
        }

        // 3. Generate receipt and update donation record
        $receiptPath = $this->generateReceipt($newDonationId);
        $updateQuery = "UPDATE donations SET ReceiptIssued = ? WHERE DonationID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ss", $receiptPath, $newDonationId);
        if (!$updateStmt->execute()) {
            throw new Exception('Failed to update donation with receipt path.');
        }

        // 4. Send confirmation email with receipt
        $userStmt = $conn->prepare("SELECT email, FirstName FROM users WHERE username = ?");
        $userStmt->bind_param("s", $donationData['donorId']);
        $userStmt->execute();
        $userResult = $userStmt->get_result()->fetch_assoc();
        $userEmail = $userResult['email'] ?? null;
        $userName = $userResult['FirstName'] ?? 'Supporter';

        if ($userEmail) {
            require_once __DIR__ . '/../vendor/autoload.php'; // for PHPMailer
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
            $mail->Subject = "Thank you for your donation!";
            $mail->Body = "Dear $userName,\n\nYour donation has been received. Thank you for your support! Please find your receipt attached.\n\nBest regards,\nFurryHaven Team";

            // Attach the PDF using the correct filesystem path
            $receiptFile = __DIR__ . '/../receipts/' . basename($receiptPath);
            $mail->addAttachment($receiptFile);

            if (!$mail->send()) {
                error_log('Mailer Error (pledge/inst payment): ' . $mail->ErrorInfo);
            }
        }

        return true;
    }

    public function updateRecurringDonation()
    {
        global $conn;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donationId = $_POST['DonationID'];
            $amount = floatval($_POST['DonationAmount']);
            $frequency = $_POST['Frequency'];
            $isActive = intval($_POST['isActive']);

            $donation = $this->donation->findOne($donationId);
            if (!$donation) {
                echo json_encode(['success' => false, 'message' => 'Donation not found.']);
                exit;
            }

            // Set ALL fields from the existing record, except those being updated
            $this->donation->setDonorId($donation['DonorID']);
            $this->donation->setCampaignId($donation['CampaignID']);
            $this->donation->setDonationType($donation['DonationType']);
            $this->donation->setDonationAmount($amount); // updated
            $this->donation->setDonationFlag($donation['DonationFlag']);
            $this->donation->setDonationDate($donation['DonationDate']);
            $this->donation->setPaymentMethod($donation['PaymentMethod']);
            $this->donation->setReceiptIssued($donation['ReceiptIssued']);
            $this->donation->setInstallmentId($donation['installmentID']);
            $this->donation->setIsDeleted($donation['isDeleted']);
            $this->donation->setIsRecurring($donation['IsRecurring']);
            $this->donation->setFrequency($frequency); // updated
            $this->donation->setNextBillingDate($donation['NextBillingDate']);
            $this->donation->setIsActive($isActive); // updated

            if (!$this->donation->update($donationId)) {
                echo json_encode(['success' => false, 'message' => 'Failed to update recurring donation.']);
                exit;
            }

            // Send email notification
            $userStmt = $conn->prepare("SELECT email, FirstName FROM users WHERE username = ?");
            $userStmt->bind_param("s", $donation['DonorID']);
            $userStmt->execute();
            $userResult = $userStmt->get_result()->fetch_assoc();
            $userEmail = $userResult['email'] ?? null;
            $userName = $userResult['FirstName'] ?? 'Supporter';

            if ($userEmail) {
                require_once __DIR__ . '/../vendor/autoload.php'; // for PHPMailer
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

                if ($isActive) {
                    $mail->Subject = "Your recurring donation has been updated";
                    $mail->Body = "Dear $userName,\n\nYour recurring donation has been updated to R$amount ($frequency).\n\nThank you for your continued support!\n\nBest regards,\nFurryHaven Team";
                } else {
                    $mail->Subject = "Your recurring donation has been deactivated";
                    $mail->Body = "Dear $userName,\n\nYour recurring donation has been deactivated. Thank you for your support so far!\n\nBest regards,\nFurryHaven Team";
                }

                if (!$mail->send()) {
                    error_log('Mailer Error (updateRecurring): ' . $mail->ErrorInfo);
                }
            }

            echo json_encode(['success' => true, 'message' => 'Recurring donation updated successfully.']);
            exit;
        }
    }

    public function getAllDonations() {
        return $this->donation->findAllOrderedByDate();
    }


}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $donationsController = new DonationsController();
    switch ($_POST['action']) {
        case 'donate':
            $donationsController->processDonation();
            break;
        case 'process_recurring':
            $donationsController->processRecurringDonations();
            break;
        case 'updateRecurring':
            $donationsController->updateRecurringDonation();
            break;
        default:
            // Optional: handle unknown action
            break;
    }
}