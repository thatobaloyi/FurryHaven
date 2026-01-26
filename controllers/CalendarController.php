<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../models/Events.php';
require_once __DIR__ . '/../core/functions.php';

class CalendarController
{
    private $events;

    public function __construct()
    {
        $this->events = new Events();
    }

    public function index()
    {
        include __DIR__ . '/../calender.php';
    }

    // Fetch all events for FullCalendar
    public function getAllEvents()
    {
        header('Content-Type: application/json');
        $events = [];
        $result = $this->events->getAll();
        while ($row = $result->fetch_assoc()) {
            $events[] = [
                'id' => $row['event_id'],
                'title' => $row['title'],
                'start' => $row['event_date'], // FullCalendar will parse DATETIME
                'details' => $row['details'],
                // Optionally add 'end' => $row['event_end'] if you have it
            ];
        }
        echo json_encode($events);
        exit;
    }

    // Create event (AJAX)
    public function create()
    {
        $this->events->setEventId($this->events->generateEventId());
        $this->events->setTitle(sanitizeInput($_POST['event_title']));
        $this->events->setDetails(sanitizeInput($_POST['event_details']));
        $this->events->setEventDate(sanitizeInput($_POST['event_date']));
        $this->events->setAddedBy($_SESSION['username'] ?? 'admin');
        $success = $this->events->create();
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }

    // Update event (AJAX)
    public function update()
    {
        $this->events->setEventId(sanitizeInput($_POST['event_id']));
        $this->events->setTitle(sanitizeInput($_POST['event_title']));
        $this->events->setDetails(sanitizeInput($_POST['event_details']));
        $this->events->setEventDate(sanitizeInput($_POST['event_date']));
        $success = $this->events->update($this->events->getEventId());
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }

    // Delete event (AJAX, soft delete)
    public function delete()
    {
        $eventId = sanitizeInput($_POST['event_id']);
        $success = $this->events->softdelete($eventId);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }
}

// --- ROUTER LOGIC ---
if (isset($_REQUEST['action'])) {
    $calendarController = new CalendarController();
    switch ($_REQUEST['action']) {
        case 'create':
            $calendarController->create();
            break;
        case 'update':
            $calendarController->update();
            break;
        case 'delete':
            $calendarController->delete();
            break;
        case 'fetch':
        default:
            $calendarController->getAllEvents();
            break;
    }
}
