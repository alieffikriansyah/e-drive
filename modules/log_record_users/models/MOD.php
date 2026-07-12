<?php

class MOD extends Model {

    public function get_logs($tgl_awal = null, $tgl_akhir = null) {
        $sql = "
            SELECT l.*, u.name as nama_user 
            FROM log_record_users l
            LEFT JOIN users u ON l.id_user = u.id
            WHERE 1=1
        ";
        
        $params = [];
        if ($tgl_awal && $tgl_akhir) {
            $sql .= " AND DATE(l.created_at) BETWEEN ? AND ?";
            $params[] = $tgl_awal;
            $params[] = $tgl_akhir;
        }
        
        $sql .= " ORDER BY l.id DESC";
        
        return $this->db->query($sql, $params)->result();
    }
}
