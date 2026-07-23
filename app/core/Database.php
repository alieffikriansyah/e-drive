<?php

class Database {
    private static $instance = null;
    private $pdo;
    
    // Query Builder Components
    private $selects = ['*'];
    private $from = '';
    private $joins = [];
    private $wheres = [];
    private $likes = [];
    private $group_by = [];
    private $havings = [];
    private $order_by = [];
    private $limit = null;
    private $offset = null;
    
    private $params = []; 
    
    private $group_started = false;
    private $skip_status_filter = false;

    // Tabel yang TIDAK pakai status
    private $tables_without_status = [
        'role_menu_access',
    ];

    // Tabel yang TIDAK pakai timestamp fields
    private $tables_without_timestamps = [
        'role_menu_access',
    ];

    const STATUS_DRAFT   = 0;
    const STATUS_ACTIVE  = 1;
    const STATUS_DELETED = 8;
    
    private $stmt = null;

    private function __construct() {
        $dbConfig = $this->_readEnvFile();

        $host    = $dbConfig['DB_HOST'] ?? 'localhost';
        $db      = $dbConfig['DB_NAME'] ?? '';
        $user    = $dbConfig['DB_USER'] ?? 'root';
        $pass    = $dbConfig['DB_PASS'] ?? '';
        $charset = 'utf8mb4';

        if (empty($db)) {
            throw new \RuntimeException('DB_NAME tidak ditemukan di file .env');
        }

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
            $this->pdo->exec("USE `$db`");
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    private function _readEnvFile(): array {
        $path = defined('ROOTPATH') ? ROOTPATH . '.env' : __DIR__ . '/../../.env';
        $config = [];

        if (!file_exists($path)) {
            return $config;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                continue;
            }
            [$key, $val] = explode('=', $line, 2);
            $config[trim($key)] = trim($val);
        }

        return $config;
    }


    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getPdo() {
        return $this->pdo;
    }

    private function _reset_builder() {
        $this->selects = ['*'];
        $this->from = '';
        $this->joins = [];
        $this->wheres = [];
        $this->likes = [];
        $this->group_by = [];
        $this->havings = [];
        $this->order_by = [];
        $this->limit = null;
        $this->offset = null;
        $this->params = [];
        $this->skip_status_filter = false;
    }

    private function has_status_column($table) {
        $parts = explode(' ', trim($table));
        $real_table = $parts[0];
        return !in_array($real_table, $this->tables_without_status);
    }

    private function has_timestamps($table) {
        $parts = explode(' ', trim($table));
        $real_table = $parts[0];
        return !in_array($real_table, $this->tables_without_timestamps);
    }

    public function query($sql, $params = []) {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($params);
        return $this; 
    }
    
    public function result_array() {
        if ($this->from !== '') $this->get();
        if ($this->stmt) return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        return [];
    }
    
    public function row_array() {
        if ($this->from !== '') $this->get('', 1);
        if ($this->stmt) {
            $res = $this->stmt->fetch(PDO::FETCH_ASSOC);
            return $res ? $res : [];
        }
        return [];
    }
    
    public function result() {
        if ($this->from !== '') $this->get();
        if ($this->stmt) return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        return [];
    }
    
    public function fetchAll($mode = PDO::FETCH_OBJ) {
        if ($this->stmt) return $this->stmt->fetchAll($mode);
        return [];
    }
    
    public function fetch($mode = PDO::FETCH_OBJ) {
        if ($this->from !== '') $this->get('', 1);
        if ($this->stmt) return $this->stmt->fetch($mode);
        return null;
    }
    
    public function row() {
        return $this->fetch();
    }
    
    public function num_rows() {
        if ($this->stmt) return $this->stmt->rowCount();
        return 0;
    }

    // --- CI-style Query Builder Methods ---
    
    public function select($select = '*') {
        if ($select !== '*') {
            if ($this->selects === ['*']) $this->selects = [];
            if (is_array($select)) {
                $this->selects = array_merge($this->selects, $select);
            } else {
                $this->selects[] = $select;
            }
        }
        return $this;
    }

    public function from($from) {
        $this->from = $from;
        return $this;
    }
    
    public function table($table) {
        $this->_reset_builder();
        $this->from = $table;
        return $this;
    }

    public function join($table, $cond, $type = 'INNER') {
        $this->joins[] = strtoupper($type) . " JOIN $table ON $cond";
        return $this;
    }

    public function where($field, $value = null, $operator = '=') {
        if (is_array($field)) {
            foreach ($field as $k => $v) {
                if (is_int($k)) {
                    $this->wheres[] = ['AND', $v];
                } else {
                    $this->where($k, $v);
                }
            }
            return $this;
        }
        
        if ($value === null && strpos($field, ' ') !== false) {
             $this->wheres[] = ['AND', $field];
             return $this;
        }
        
        $this->wheres[] = ['AND', "$field $operator ?"];
        $this->params[] = $value;
        return $this;
    }

    public function or_where($field, $value = null, $operator = '=') {
         if (is_array($field)) {
            foreach ($field as $k => $v) {
                $this->or_where($k, $v);
            }
            return $this;
        }
        
        if ($value === null && strpos($field, ' ') !== false) {
             $this->wheres[] = ['OR', $field];
             return $this;
        }
        
        $this->wheres[] = ['OR', "$field $operator ?"];
        $this->params[] = $value;
        return $this;
    }
    
    public function where_in($field, $values) {
        if (!is_array($values)) $values = [$values];
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = ['AND', "$field IN ($placeholders)"];
        foreach ($values as $v) $this->params[] = $v;
        return $this;
    }
    
    public function where_not_in($field, $values) {
        if (!is_array($values)) $values = [$values];
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = ['AND', "$field NOT IN ($placeholders)"];
        foreach ($values as $v) $this->params[] = $v;
        return $this;
    }
    
    public function like($field, $match = '', $side = 'both') {
        if (is_array($field)) {
            foreach($field as $k => $v) {
                $this->like($k, $v, $side);
            }
            return $this;
        }
        
        $v = $match;
        if ($side === 'before') $v = "%$match";
        elseif ($side === 'after') $v = "$match%";
        elseif ($side === 'both') $v = "%$match%";
        
        $this->wheres[] = ['AND', "$field LIKE ?"];
        $this->params[] = $v;
        return $this;
    }
    
    public function or_like($field, $match = '', $side = 'both') {
         if (is_array($field)) {
            foreach($field as $k => $v) {
                $this->or_like($k, $v, $side);
            }
            return $this;
        }
        
        $v = $match;
        if ($side === 'before') $v = "%$match";
        elseif ($side === 'after') $v = "$match%";
        elseif ($side === 'both') $v = "%$match%";
        
        $this->wheres[] = ['OR', "$field LIKE ?"];
        $this->params[] = $v;
        return $this;
    }
    
    public function group_start() {
        $this->wheres[] = ['AND', '('];
        $this->group_started = true;
        return $this;
    }
    
    public function or_group_start() {
        $this->wheres[] = ['OR', '('];
        $this->group_started = true;
        return $this;
    }
    
    public function group_end() {
        $this->wheres[] = ['', ')'];
        $this->group_started = false;
        return $this;
    }

    public function group_by($by) {
        if (is_array($by)) {
            foreach ($by as $b) {
                $this->group_by[] = $b;
            }
        } else {
            $this->group_by[] = $by;
        }
        return $this;
    }
    
    public function having($field, $value = null) {
        if ($value === null && strpos($field, ' ') !== false) {
             $this->havings[] = $field;
             return $this;
        }
        $this->havings[] = "$field = ?";
        $this->params[] = $value;
        return $this;
    }

    public function order_by($orderby, $direction = 'ASC') {
        $this->order_by[] = "$orderby $direction";
        return $this;
    }

    public function limit($limit, $offset = null) {
        $this->limit = $limit;
        if ($offset !== null) {
            $this->offset = $offset;
        }
        return $this;
    }
    
    public function get_where($table = '', $where = null, $limit = null, $offset = null) {
        if ($table !== '') $this->from($table);
        if ($where !== null) $this->where($where);
        if ($limit !== null) $this->limit($limit, $offset);
        return $this->get();
    }
    
    private function _compile_select() {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM " . $this->from;
        
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        
        if ($this->has_status_column($this->from) && !$this->skip_status_filter) {
            $parts = explode(' ', $this->from);
            $alias = isset($parts[1]) ? $parts[1] : $parts[0];
            $this->wheres[] = ['AND', "$alias.status != " . self::STATUS_DELETED];
        }

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $first = true;
            foreach ($this->wheres as $w) {
                $op = $w[0];
                $cond = $w[1];
                
                if ($cond === '(') {
                    if (!$first) $sql .= " $op ";
                    $sql .= "(";
                    $first = true; 
                } elseif ($cond === ')') {
                    $sql .= ")";
                    $first = false;
                } else {
                    if (!$first) $sql .= " $op ";
                    $sql .= $cond;
                    $first = false;
                }
            }
        }
        
        if (!empty($this->group_by)) {
            $sql .= " GROUP BY " . implode(', ', $this->group_by);
        }
        
        if (!empty($this->havings)) {
            $sql .= " HAVING " . implode(' AND ', $this->havings);
        }
        
        if (!empty($this->order_by)) {
            $sql .= " ORDER BY " . implode(', ', $this->order_by);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT " . (int)$this->limit;
            if ($this->offset !== null) {
                $sql .= " OFFSET " . (int)$this->offset;
            }
        }
        
        return $sql;
    }

    public function get($table = '', $limit = null, $offset = null) {
        if ($table !== '') $this->from($table);
        if ($limit !== null) $this->limit($limit, $offset);
        
        $sql = $this->_compile_select();
        $params = $this->params;
        
        $this->query($sql, $params);
        $this->_reset_builder(); 
        return $this;
    }
    
    public function insert($table = '', $data = []) {
        if (is_array($table)) {
            $data = $table;
            $table = $this->from;
        } elseif ($table !== '') {
            $this->from($table);
        }
        
        // Auto-inject status
        if ($this->has_status_column($table) && !isset($data['status'])) {
            $data['status'] = self::STATUS_ACTIVE;
        }

        // Auto-inject timestamps
        if ($this->has_timestamps($table)) {
            if (!isset($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
            if (!isset($data['created_by'])) {
                $data['created_by'] = $this->_current_user_id();
            }
        }

        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = "INSERT INTO " . $table . " (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";

        $this->query($sql, array_values($data));
        $id = $this->pdo->lastInsertId();
        
        // Auto Log
        $this->_auto_log('CREATE', $table, 'Menambah data ke tabel ' . $table . '. ID: ' . $id);
        
        $this->_reset_builder();
        return $id;
    }

    public function update($table = '', $data = null, $where = null) {
        if (is_array($table)) {
            $data = $table;
            $table = $this->from;
        } elseif ($table !== '') {
            $this->from($table);
        }
        
        if ($where !== null) $this->where($where);
        
        // Auto-inject timestamps
        if ($this->has_timestamps($table)) {
            if (!isset($data['updated_at'])) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }
            if (!isset($data['updated_by'])) {
                $data['updated_by'] = $this->_current_user_id();
            }
        }

        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $sql = "UPDATE " . $table . " SET " . implode(', ', $fields);

        if (!empty($this->wheres)) {
            $sql .= " WHERE ";
            $first = true;
            foreach ($this->wheres as $w) {
                $op = $w[0];
                $cond = $w[1];
                if (!$first) $sql .= " $op ";
                $sql .= $cond;
                $first = false;
            }
            $values = array_merge($values, $this->params);
        }

        $this->query($sql, $values);
        $rowCount = $this->stmt->rowCount();
        
        // Auto Log
        $this->_auto_log('UPDATE', $table, 'Mengubah data di tabel ' . $table);
        
        $this->_reset_builder();
        return $rowCount;
    }

    public function delete($table = '', $where = null) {
        if ($table === '') $table = $this->from;
        if ($where !== null) $this->where($where);
        
        $where_sql = "";
        if (!empty($this->wheres)) {
            $where_sql = " WHERE ";
            $first = true;
            foreach ($this->wheres as $w) {
                $op = $w[0];
                $cond = $w[1];
                if (!$first) $where_sql .= " $op ";
                $where_sql .= $cond;
                $first = false;
            }
        }

        if ($this->has_status_column($table)) {
            // Soft delete: set status=8, deleted_at, updated_by
            $set_parts = "status = " . self::STATUS_DELETED;
            if ($this->has_timestamps($table)) {
                $set_parts .= ", deleted_at = '" . date('Y-m-d H:i:s') . "'";
                $uid = $this->_current_user_id();
                if ($uid) {
                    $set_parts .= ", updated_by = " . (int)$uid;
                }
            }
            $sql = "UPDATE " . $table . " SET " . $set_parts . $where_sql;
        } else {
            $sql = "DELETE FROM " . $table . $where_sql;
        }
        
        $this->query($sql, $this->params);
        $rowCount = $this->stmt->rowCount();
        
        // Auto Log
        $this->_auto_log('DELETE', $table, 'Menghapus data di tabel ' . $table);
        
        $this->_reset_builder();
        return $rowCount;
    }
    
    public function empty_table($table = '') {
        if ($table === '') $table = $this->from;
        $sql = "TRUNCATE TABLE " . $table;
        $this->query($sql);
        $this->_reset_builder();
        return true;
    }

    public function restore() {
        $table = $this->from;
        $where_sql = "";
        if (!empty($this->wheres)) {
            $where_sql = " WHERE ";
            $first = true;
            foreach ($this->wheres as $w) {
                $op = $w[0];
                $cond = $w[1];
                if (!$first) $where_sql .= " $op ";
                $where_sql .= $cond;
                $first = false;
            }
        }
        
        $set_parts = "status = " . self::STATUS_ACTIVE . ", deleted_at = NULL";
        if ($this->has_timestamps($table)) {
            $set_parts .= ", updated_at = '" . date('Y-m-d H:i:s') . "'";
            $uid = $this->_current_user_id();
            if ($uid) {
                $set_parts .= ", updated_by = " . (int)$uid;
            }
        }
        
        $sql = "UPDATE " . $table . " SET " . $set_parts . $where_sql;
        $this->query($sql, $this->params);
        
        // Auto Log
        $this->_auto_log('RESTORE', $table, 'Memulihkan data di tabel ' . $table);
        
        $this->_reset_builder();
        return $this->stmt->rowCount();
    }

    public function with_trashed() {
        $this->skip_status_filter = true;
        return $this;
    }

    private function _current_user_id() {
        if (session_status() === PHP_SESSION_NONE) return null;
        return $_SESSION['user_id'] ?? null;
    }

    private function _auto_log($action, $table, $keterangan) {
        // Prevent infinite loop
        if (in_array($table, ['activity_logs', 'log_record_users'])) return;
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) return;

        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $sql = "INSERT INTO activity_logs (user_id, action, entity_type, description, ip_address, user_agent, created_at, status) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$user_id, $action, $table, $keterangan, $ip_address, $user_agent]);
        } catch (\Throwable $e) {
            // Abaikan error pada logging agar transaksi utama tidak gagal
        }
    }
}
