<?php

class Drive extends Controller {
    public function __construct() {
        parent::__construct();
        require_once APPPATH . 'middleware/AuthMiddleware.php';
        AuthMiddleware::check();
        $this->load->helper('url');
        $this->load->helper('file');
        $this->load->helper('form');
        $this->load->model('drive/MOD', 'mod');
    }

    public function index() {
        $user_id = Session::get('user_id');
        $role_id = Session::get('role_id');
        $is_admin = AuthMiddleware::isManager();

        $drives = $this->mod->get_accessible_drives($is_admin, $user_id, $role_id);

        $data = [
            'title'      => 'My Drive',
            'drives'     => $drives,
            'is_admin'   => $is_admin,
            'breadcrumb' => [['name' => 'My Drive']]
        ];

        $this->load->view('drive/v_index', $data);
    }

    // View isi Drive (dan Folder secara hirarki)
    public function view($drive_id = null) {
        if (!$drive_id) {
            redirect('drive');
        }

        if (!AuthMiddleware::canAccessDrive($drive_id)) {
            Session::set_flashdata('error', 'Anda tidak memiliki akses ke Drive ini.');
            redirect('drive');
        }

        $folder_id = isset($_GET['folder']) ? (int)$_GET['folder'] : null;

        // Normalize legacy parent_id / folder_id = 0 to NULL
        $this->db->query("UPDATE folders SET parent_id = NULL WHERE parent_id = 0");
        $this->db->query("UPDATE documents SET folder_id = NULL WHERE folder_id = 0");

        $drive = $this->mod->get_drive_by_id($drive_id);
        if (!$drive) redirect('drive');

        $folders = $this->mod->get_folders_in_drive($drive_id, $folder_id);
        $documents = $this->mod->get_documents_in_drive($drive_id, $folder_id);

        // Breadcrumb
        $breadcrumb = [];
        $breadcrumb[] = ['name' => 'My Drive', 'url' => site_url('drive')];
        
        if ($folder_id) {
            $folder_crumbs = get_breadcrumb_path($folder_id);
            $breadcrumb = array_merge($breadcrumb, $folder_crumbs);
            $current_title = end($folder_crumbs)['name'] ?? $drive->name;
        } else {
            $breadcrumb[] = ['name' => $drive->name];
            $current_title = $drive->name;
        }

        $data = [
            'title'      => $current_title,
            'drive'      => $drive,
            'current_folder_id' => $folder_id,
            'folders'    => $folders,
            'documents'  => $documents,
            'breadcrumb' => $breadcrumb,
            'is_admin'   => AuthMiddleware::isManager()
        ];

        $this->load->view('drive/v_view', $data);
    }

    // Endpoint to get all folders in a drive for Move modal
    public function get_all_folders($drive_id) {
        if (!AuthMiddleware::canAccessDrive($drive_id)) {
            echo json_encode(['status' => false, 'message' => 'Akses ditolak.']);
            return;
        }
        $folders = $this->mod->get_all_folders_by_drive($drive_id);
        
        // Build path map for better display (optional but helpful)
        // Since we already have 'path' field, we can just return it.
        $result = [];
        foreach ($folders as $f) {
            $result[] = [
                'id' => $f->id,
                'name' => $f->name,
                'path' => $f->path
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['status' => true, 'folders' => $result]);
    }
}
