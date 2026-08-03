<?php

require_once MODULESPATH . 'schedule/repositories/ScheduleRepository.php';

class ScheduleService {
    private $repo;
    private $user_id;
    private $role_id;
    private $is_admin;

    public function __construct() {
        $this->repo = new ScheduleRepository();
        $this->user_id = Session::get('user_id');
        $this->role_id = Session::get('role_id');
        
        // E-Drive role: 1 = Admin, 2 = PM, 3 = Staff, etc. (Check AuthMiddleware)
        $this->is_admin = in_array($this->role_id, [1, 2]); 
    }

    public function getAccessibleEvents($start, $end) {
        $all_events = $this->repo->findAll(['start' => $start, 'end' => $end]);
        
        if ($this->is_admin) {
            return $all_events;
        }

        $filtered = [];
        foreach ($all_events as $event) {
            if ($event->user_id == $this->user_id) {
                $filtered[] = $event;
            } elseif ($event->visibility === 'public') {
                $filtered[] = $event;
            }
        }

        return $filtered;
    }
    
    public function getEventById($id) {
        $event = $this->repo->findById($id);
        if (!$event) return null;
        
        if ($this->is_admin || $event->user_id == $this->user_id) {
            return $event;
        }
        
        if ($event->visibility === 'public') return $event;
        
        return null;
    }

    public function createEvent($data) {
        $model = new ScheduleModel($data);
        $model->user_id = $this->user_id;
        $model->created_by = $this->user_id;
        $model->created_at = date('Y-m-d H:i:s');
        $model->status = 1; // Active
        
        // Validate dates
        if ($model->start_date > $model->end_date) {
            throw new Exception("Tanggal mulai tidak boleh lebih dari tanggal selesai.");
        }

        return $this->repo->insert($model);
    }

    public function updateEvent($id, $data) {
        $event = $this->repo->findById($id);
        if (!$event) throw new Exception("Agenda tidak ditemukan.");
        
        // Check permissions
        if (!$this->is_admin && $event->user_id != $this->user_id) {
            throw new Exception("Anda tidak memiliki akses untuk mengubah agenda ini.");
        }

        // Protect fields yang tidak boleh di-overwrite saat update
        unset($data['user_id']);
        unset($data['status']);

        $model = new ScheduleModel($data);
        // Pertahankan nilai asli dari database
        $model->user_id = $event->user_id;
        $model->status = $event->status;
        $model->updated_by = $this->user_id;
        $model->updated_at = date('Y-m-d H:i:s');
        
        return $this->repo->update($id, $model);
    }

    public function deleteEvent($id) {
        $event = $this->repo->findById($id);
        if (!$event) throw new Exception("Agenda tidak ditemukan.");
        
        // Check permissions
        if (!$this->is_admin && $event->user_id != $this->user_id) {
            throw new Exception("Anda tidak memiliki akses untuk menghapus agenda ini.");
        }

        return $this->repo->softDelete($id, $this->user_id);
    }
}
