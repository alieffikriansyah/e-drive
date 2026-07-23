<?php

require_once MODULESPATH . 'schedule/services/ScheduleService.php';

class Schedule extends Controller {
    private $service;

    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        
        $this->service = new ScheduleService();
        $this->load->helper('url');
    }

    public function index() {
        $data = [
            'title' => 'Schedule & Calendar',
            'user_id' => Session::get('user_id'),
            // Fetch accessible drives for the modal dropdown
            'drives' => $this->get_drives_dropdown()
        ];
        $this->load->view('schedule/v_index', $data);
    }

    private function get_drives_dropdown() {
        $this->load->model('drive/MOD', 'drive_mod');
        $is_admin = in_array(Session::get('role_id'), [1, 2]);
        return $this->drive_mod->get_accessible_drives($is_admin, Session::get('user_id'), Session::get('role_id'));
    }

    // --- API Endpoints for FullCalendar ---

    public function api_events() {
        header('Content-Type: application/json');
        
        $start = isset($_GET['start']) ? substr($_GET['start'], 0, 10) : date('Y-m-01');
        $end = isset($_GET['end']) ? substr($_GET['end'], 0, 10) : date('Y-m-t');

        try {
            $events = $this->service->getAccessibleEvents($start, $end);
            $formatted = [];
            
            foreach ($events as $ev) {
                $start_datetime = $ev->start_date;
                $end_datetime = $ev->end_date;
                
                if (!$ev->all_day) {
                    $start_datetime .= 'T' . ($ev->start_time ?: '00:00:00');
                    $end_datetime .= 'T' . ($ev->end_time ?: '23:59:59');
                } else {
                    // FullCalendar exclusive end date for all day events
                    $end_datetime = date('Y-m-d', strtotime($ev->end_date . ' +1 day'));
                }

                $formatted[] = [
                    'id' => $ev->id,
                    'title' => $ev->title,
                    'start' => $start_datetime,
                    'end' => $end_datetime,
                    'allDay' => (bool)$ev->all_day,
                    'backgroundColor' => $ev->color,
                    'borderColor' => $ev->color,
                    'extendedProps' => [
                        'description' => $ev->description,
                        'location' => $ev->location,
                        'category' => $ev->category,
                        'priority' => $ev->priority,
                        'visibility' => $ev->visibility,
                        'drive_id' => $ev->drive_id,
                        'user_id' => $ev->user_id,
                        'owner_name' => $ev->owner_name
                    ]
                ];
            }
            
            echo json_encode($formatted);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function api_save() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? null,
            'location' => $_POST['location'] ?? null,
            'category' => $_POST['category'] ?? null,
            'priority' => $_POST['priority'] ?? 'Medium',
            'start_date' => $_POST['start_date'] ?? date('Y-m-d'),
            'end_date' => $_POST['end_date'] ?? date('Y-m-d'),
            'start_time' => !empty($_POST['start_time']) ? $_POST['start_time'] : null,
            'end_time' => !empty($_POST['end_time']) ? $_POST['end_time'] : null,
            'all_day' => isset($_POST['all_day']) && $_POST['all_day'] == '1' ? 1 : 0,
            'color' => $_POST['color'] ?? '#2563EB',
            'visibility' => $_POST['visibility'] ?? 'private',
            'drive_id' => !empty($_POST['drive_id']) ? (int)$_POST['drive_id'] : null
        ];

        try {
            if ($id > 0) {
                $this->service->updateEvent($id, $data);
                $message = 'Agenda berhasil diperbarui.';
            } else {
                $id = $this->service->createEvent($data);
                $message = 'Agenda berhasil dibuat.';
            }
            
            echo json_encode(['status' => true, 'message' => $message, 'id' => $id]);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function api_delete() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Invalid method']);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        try {
            $this->service->deleteEvent($id);
            echo json_encode(['status' => true, 'message' => 'Agenda berhasil dihapus.']);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
