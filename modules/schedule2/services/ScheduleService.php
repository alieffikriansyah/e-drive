<?php

require_once MODULESPATH . 'schedule/repositories/ScheduleRepository.php';
require_once MODULESPATH . 'schedule/repositories/GambarScheduleRepository.php';

class ScheduleService {
    private $repo;
    private $gambarRepo;
    private $user_id;
    private $role_id;
    private $is_admin;

    // Image upload constraints
    const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB
    const MAX_IMAGES_PER_SCHEDULE = 10;
    const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function __construct() {
        $this->repo = new ScheduleRepository();
        $this->gambarRepo = new GambarScheduleRepository();
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

        // Soft-delete semua gambar terkait
        $this->gambarRepo->softDeleteByScheduleId($id, $this->user_id);

        return $this->repo->softDelete($id, $this->user_id);
    }

    // ─── Image Management Methods ─────────────────────────────

    /**
     * Ambil semua gambar untuk sebuah schedule
     */
    public function getImages($schedule_id) {
        return $this->gambarRepo->findByScheduleId($schedule_id);
    }

    /**
     * Upload satu gambar untuk sebuah schedule
     * 
     * @param int $schedule_id ID schedule calendar
     * @param array $file $_FILES['file'] array
     * @return array ['id' => int, 'nama_file' => string, 'url' => string]
     */
    public function uploadImage($schedule_id, $file) {
        // 1. Validate schedule exists and user has access
        $event = $this->repo->findById($schedule_id);
        if (!$event) {
            throw new Exception("Agenda tidak ditemukan.");
        }
        if (!$this->is_admin && $event->user_id != $this->user_id) {
            throw new Exception("Anda tidak memiliki akses untuk menambah gambar pada agenda ini.");
        }

        // 2. Check image count limit
        $currentCount = $this->gambarRepo->countByScheduleId($schedule_id);
        if ($currentCount >= self::MAX_IMAGES_PER_SCHEDULE) {
            throw new Exception("Maksimal " . self::MAX_IMAGES_PER_SCHEDULE . " gambar per agenda.");
        }

        // 3. Validate file upload
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Gagal mengunggah gambar. Kode error: " . ($file['error'] ?? 'Unknown'));
        }

        // 4. Validate file extension
        $original_name = $file['name'];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_IMAGE_TYPES)) {
            throw new Exception("Tipe file tidak diizinkan. Hanya: " . implode(', ', self::ALLOWED_IMAGE_TYPES));
        }

        // 5. Validate file size
        if ($file['size'] > self::MAX_IMAGE_SIZE) {
            throw new Exception("Ukuran file melebihi batas maksimal (5MB).");
        }

        // 6. Prepare target directory: upload/gambar_schdule/[user_id]/
        $target_dir = UPLOADPATH . 'gambar_schdule' . DIRECTORY_SEPARATOR . $this->user_id . DIRECTORY_SEPARATOR;
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // 7. Generate unique filename
        $unique_name = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $target_file = $target_dir . $unique_name;

        // 8. Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $target_file)) {
            throw new Exception("Gagal menyimpan gambar secara fisik.");
        }

        // 9. Insert to database
        $data = [
            'schedule_calendars_id' => $schedule_id,
            'nama_file' => $unique_name,
            'created_by' => $this->user_id
        ];

        $id = $this->gambarRepo->insert($data);

        return [
            'id' => $id,
            'nama_file' => $unique_name,
            'url' => base_url('upload/gambar_schdule/' . $this->user_id . '/' . $unique_name)
        ];
    }

    /**
     * Hapus satu gambar
     */
    public function deleteImage($image_id) {
        $image = $this->gambarRepo->findById($image_id);
        if (!$image) {
            throw new Exception("Gambar tidak ditemukan.");
        }

        // Check permission: get the parent schedule
        $event = $this->repo->findById($image->schedule_calendars_id);
        if (!$event) {
            throw new Exception("Agenda terkait tidak ditemukan.");
        }
        if (!$this->is_admin && $event->user_id != $this->user_id) {
            throw new Exception("Anda tidak memiliki akses untuk menghapus gambar ini.");
        }

        // Soft-delete from database
        $this->gambarRepo->softDelete($image_id, $this->user_id);

        // Delete physical file
        $file_path = UPLOADPATH . 'gambar_schdule' . DIRECTORY_SEPARATOR . $event->user_id . DIRECTORY_SEPARATOR . $image->nama_file;
        if (file_exists($file_path)) {
            @unlink($file_path);
        }

        return true;
    }

    /**
     * Get user_id from schedule event (for building image URLs)
     */
    public function getEventOwnerId($schedule_id) {
        $event = $this->repo->findById($schedule_id);
        return $event ? $event->user_id : null;
    }
}
