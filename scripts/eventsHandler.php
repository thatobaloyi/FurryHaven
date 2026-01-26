<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../models/Events.php';
require_once __DIR__ . '/../core/functions.php';

class EventsHandler
{
    private $events;

    public function __construct()
    {
        $this->events = new Events();
    }

    // Fetch all events for FullCalendar
    public function getAllEvents()
    {
        $result = $this->events->getAll();
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = [
                'id' => $row['event_id'],
                'title' => $row['title'],
                'start' => date('Y-m-d\TH:i', strtotime($row['event_date'])),
                'details' => $row['details']
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($events);
        exit;
    }

    // Create event (AJAX)
    public function processCreate()
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
    public function processUpdate()
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
    public function processDelete()
    {
        $eventId = sanitizeInput($_POST['event_id']);
        $success = $this->events->softdelete($eventId);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    }
}

// --- ROUTER LOGIC ---
$handler = new EventsHandler();
$action = $_REQUEST['action'] ?? 'fetch';

switch ($action) {
    case 'create':
        $handler->processCreate();
        break;
    case 'update':
        $handler->processUpdate();
        break;
    case 'delete':
        $handler->processDelete();
        break;
    case 'fetch':
    default:
        $handler->getAllEvents();
        break;
}