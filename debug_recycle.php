<?php
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);
define('MODULESPATH', ROOTPATH . 'modules' . DIRECTORY_SEPARATOR);

require_once APPPATH . 'core/Env.php';
require_once APPPATH . 'core/Session.php';
require_once APPPATH . 'core/Database.php';
require_once APPPATH . 'core/Controller.php';
require_once APPPATH . 'core/Model.php';
require_once APPPATH . 'middleware/AuthMiddleware.php';
require_once MODULESPATH . 'recycle_bin/models/MOD.php';

Env::load(ROOTPATH . '.env');
require_once APPPATH . 'config/config.php';
Session::init();
// Mock session data as seen in the user screenshot
Session::set('role_name', 'Administrator');
Session::set('user_id', 1);

$db = Database::get_instance();
$mod = new MOD();

$is_admin = AuthMiddleware::isManager();
$drives_sql = "SELECT id FROM drives WHERE status = 1";
$allowed_drives = $db->query($drives_sql)->fetchAll();
$drive_ids = array_column($allowed_drives, 'id');

echo "Is Admin: " . var_export($is_admin, true) . "\n";
echo "Drive IDs: "; print_r($drive_ids);

$deleted_docs = $mod->get_deleted_documents($drive_ids, $is_admin);
echo "Deleted Docs: "; print_r($deleted_docs);
