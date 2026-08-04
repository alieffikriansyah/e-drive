<?php

class MOD extends Model {

    public function get_all($status = 1) {
        $db = Database::get_instance();
        return $db->query("
            SELECT d.*, u.name as uploader_name
            FROM ai_documents d
            LEFT JOIN users u ON d.uploaded_by = u.id
            WHERE d.status = ?
            ORDER BY d.created_at DESC
        ", [$status])->fetchAll();
    }

    public function get_by_id($id) {
        $db = Database::get_instance();
        return $db->query("SELECT * FROM ai_documents WHERE id = ? AND status = 1", [$id])->fetch();
    }

    public function insert_document($data) {
        $db = Database::get_instance();
        $db->query("
            INSERT INTO ai_documents (title, file_name, file_path, file_type, file_size, collection_name, category, uploaded_by, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ", [
            $data['title'],
            $data['file_name'],
            $data['file_path'],
            $data['file_type'],
            $data['file_size'],
            $data['collection_name'] ?? 'edrive_knowledge',
            $data['category'] ?? 'General',
            $data['uploaded_by']
        ]);
        return $db->query("SELECT LAST_INSERT_ID() as id")->fetch()->id;
    }

    public function update_status($id, $embedding_status, $chunk_count = 0) {
        $db = Database::get_instance();
        $db->query("
            UPDATE ai_documents 
            SET embedding_status = ?, chunk_count = ?, updated_at = NOW() 
            WHERE id = ?
        ", [$embedding_status, $chunk_count, $id]);
    }

    public function delete_document($id) {
        $db = Database::get_instance();
        $db->query("UPDATE ai_documents SET status = 8, updated_at = NOW() WHERE id = ?", [$id]);
    }
}
