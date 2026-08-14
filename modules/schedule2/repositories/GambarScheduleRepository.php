<?php

require_once MODULESPATH . 'schedule/models/GambarScheduleModel.php';

class GambarScheduleRepository {
    private $db;

    public function __construct() {
        $this->db = Database::get_instance();
    }

    /**
     * Ambil semua gambar aktif milik sebuah schedule
     */
    public function findByScheduleId($schedule_id) {
        $sql = "SELECT * FROM gambar_schdule_calendars 
                WHERE schedule_calendars_id = ? AND status != 8 
                ORDER BY created_at ASC";
        
        $results = $this->db->query($sql, [$schedule_id])->result();
        
        $models = [];
        foreach ($results as $row) {
            $models[] = new GambarScheduleModel($row);
        }
        
        return $models;
    }

    /**
     * Cari satu gambar berdasarkan ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM gambar_schdule_calendars WHERE id = ? AND status != 8";
        $row = $this->db->query($sql, [$id])->row();
        
        if ($row) {
            return new GambarScheduleModel($row);
        }
        return null;
    }

    /**
     * Insert record gambar baru
     */
    public function insert($data) {
        return $this->db->table('gambar_schdule_calendars')->insert($data);
    }

    /**
     * Soft-delete satu gambar berdasarkan ID
     */
    public function softDelete($id, $user_id) {
        $data = [
            'status' => 8,
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_by' => $user_id
        ];
        $this->db->table('gambar_schdule_calendars')->where('id', $id)->update($data);
        return true;
    }

    /**
     * Soft-delete semua gambar milik sebuah schedule
     */
    public function softDeleteByScheduleId($schedule_id, $user_id) {
        $data = [
            'status' => 8,
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_by' => $user_id
        ];
        $this->db->table('gambar_schdule_calendars')
            ->where('schedule_calendars_id', $schedule_id)
            ->update($data);
        return true;
    }

    /**
     * Hitung jumlah gambar aktif per schedule
     */
    public function countByScheduleId($schedule_id) {
        $sql = "SELECT COUNT(*) as total FROM gambar_schdule_calendars 
                WHERE schedule_calendars_id = ? AND status != 8";
        $row = $this->db->query($sql, [$schedule_id])->row();
        return $row ? (int)$row->total : 0;
    }
}
