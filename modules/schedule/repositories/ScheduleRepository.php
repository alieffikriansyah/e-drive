<?php

require_once MODULESPATH . 'schedule/models/ScheduleModel.php';

class ScheduleRepository {
    private $db;

    public function __construct() {
        $this->db = Database::get_instance();
    }

    public function findAll($filters = []) {
        $sql = "SELECT s.*, u.name as owner_name, d.name as drive_name, doc.name as attachment_name 
                FROM schedule_calendars s
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN drives d ON s.drive_id = d.id
                LEFT JOIN documents doc ON s.attachment_document_id = doc.id
                WHERE s.status != 8"; // 8 is Soft Delete

        $params = [];

        // Apply filters
        if (!empty($filters['start']) && !empty($filters['end'])) {
            $sql .= " AND (s.start_date <= ? AND s.end_date >= ?)";
            $params[] = $filters['end'];
            $params[] = $filters['start'];
        }

        if (!empty($filters['visibility'])) {
            // Visibility logic handled in Service mostly, but we can filter here
            if (is_array($filters['visibility'])) {
                $placeholders = implode(',', array_fill(0, count($filters['visibility']), '?'));
                $sql .= " AND s.visibility IN ($placeholders)";
                $params = array_merge($params, $filters['visibility']);
            } else {
                $sql .= " AND s.visibility = ?";
                $params[] = $filters['visibility'];
            }
        }

        if (!empty($filters['user_id'])) {
             // Will be complex if combined with visibility, better handle auth/visibility in service by fetching all accessible
             // But if we want specifically created by or owned by user:
             $sql .= " AND s.user_id = ?";
             $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['q'])) {
            $sql .= " AND (s.title LIKE ? OR s.description LIKE ? OR s.location LIKE ?)";
            $search = '%' . $filters['q'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql .= " ORDER BY s.start_date ASC, s.start_time ASC";

        $results = $this->db->query($sql, $params)->result();
        
        $models = [];
        foreach ($results as $row) {
            $models[] = new ScheduleModel($row);
        }
        
        return $models;
    }

    public function findById($id) {
        $sql = "SELECT s.*, u.name as owner_name, d.name as drive_name, doc.name as attachment_name 
                FROM schedule_calendars s
                LEFT JOIN users u ON s.user_id = u.id
                LEFT JOIN drives d ON s.drive_id = d.id
                LEFT JOIN documents doc ON s.attachment_document_id = doc.id
                WHERE s.id = ? AND s.status != 8";
                
        $row = $this->db->query($sql, [$id])->row();
        
        if ($row) {
            return new ScheduleModel($row);
        }
        return null;
    }

    public function insert(ScheduleModel $model) {
        $data = $model->toArray();
        unset($data['id']); // Remove PK
        unset($data['owner_name']); // Remove relations
        unset($data['drive_name']);
        unset($data['attachment_name']);
        
        // Remove nulls so DB defaults can trigger if needed, or explicitly insert them
        $data = array_filter($data, function($val) {
            return $val !== null;
        });

        return $this->db->table('schedule_calendars')->insert($data);
    }

    public function update($id, ScheduleModel $model) {
        $data = $model->toArray();
        unset($data['id']);
        unset($data['owner_name']);
        unset($data['drive_name']);
        unset($data['attachment_name']);
        unset($data['created_at']);
        unset($data['created_by']);
        
        $this->db->table('schedule_calendars')->where('id', $id)->update($data);
        return true;
    }

    public function softDelete($id, $user_id) {
        $data = [
            'status' => 8,
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_by' => $user_id
        ];
        $this->db->table('schedule_calendars')->where('id', $id)->update($data);
        return true;
    }
}
