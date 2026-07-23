<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — E-Drive' : 'E-Drive — Enterprise Document Management' ?></title>
    <meta name="description" content="E-Drive Enterprise Document Management System">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%232563EB'/><path d='M8 10h16v3H8zm0 5h12v3H8zm0 5h14v3H8z' fill='%23fff' opacity='.9'/></svg>">
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <!-- Tailwind CSS -->
    <script src="<?= base_url('assets/js/tailwindcss.js') ?>"></script>
    <!-- SweetAlert2 -->
    <script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>
    
    <!-- Tailwind Config — E-Drive Light Enterprise Theme -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        edrive: {
                            primary:   '#1E3A5F',
                            accent:    '#2563EB',
                            'accent-light': '#DBEAFE',
                            bg:        '#F1F5F9',
                            card:      '#FFFFFF',
                            sidebar:   '#FFFFFF',
                            'sidebar-active': '#EFF6FF',
                            text:      '#1E293B',
                            muted:     '#64748B',
                            light:     '#94A3B8',
                            border:    '#E2E8F0',
                            'border-light': '#F1F5F9',
                            highlight: '#FFF8E7',
                            success:   '#10B981',
                            warning:   '#F59E0B',
                            danger:    '#EF4444',
                            info:      '#3B82F6',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'Segoe UI', 'sans-serif'],
                    },
                    borderRadius: {
                        'xl':  '0.75rem',
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                    },
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        /* ─── Global ─── */
        * { scrollbar-width: thin; scrollbar-color: #CBD5E1 #F1F5F9; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #F8FAFC; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        body {
            @apply bg-edrive-bg text-edrive-text font-sans antialiased min-h-screen;
        }

        /* ─── Glass Card ─── */
        .glass-card {
            @apply bg-edrive-card border border-edrive-border rounded-2xl shadow-sm;
        }
        .glass-card-hover {
            @apply glass-card hover:border-edrive-accent/30 hover:shadow-lg hover:shadow-edrive-accent/5 transition-all duration-300;
        }

        /* ─── Buttons ─── */
        .btn-primary {
            @apply bg-edrive-accent hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-xl shadow-md shadow-edrive-accent/20 hover:shadow-lg hover:shadow-edrive-accent/30 transition-all duration-200 flex items-center gap-2 text-sm;
        }
        .btn-secondary {
            @apply bg-white hover:bg-gray-50 text-edrive-text border border-edrive-border font-medium py-2 px-4 rounded-xl transition-all duration-200 flex items-center gap-2 text-sm shadow-sm;
        }
        .btn-danger {
            @apply bg-white hover:bg-red-50 text-red-600 border border-red-200 font-medium py-2 px-4 rounded-xl transition-all duration-200 flex items-center gap-2 text-sm shadow-sm;
        }
        .btn-success {
            @apply bg-white hover:bg-emerald-50 text-emerald-600 border border-emerald-200 font-medium py-2 px-4 rounded-xl transition-all duration-200 flex items-center gap-2 text-sm shadow-sm;
        }
        .btn-icon {
            @apply p-2 rounded-xl text-edrive-muted hover:text-edrive-accent hover:bg-edrive-accent/5 transition-all duration-200;
        }
        .btn-ghost {
            @apply text-edrive-muted hover:text-edrive-text hover:bg-gray-100 px-3 py-2 rounded-xl transition-all duration-200 text-sm;
        }

        /* ─── Inputs ─── */
        .input-field {
            @apply w-full px-4 py-2.5 bg-white border border-edrive-border rounded-xl text-edrive-text placeholder-gray-400
                   focus:ring-2 focus:ring-edrive-accent/20 focus:border-edrive-accent focus:outline-none
                   transition-all duration-200 text-sm;
        }

        /* ─── Sidebar ─── */
        .sidebar-link {
            @apply flex items-center gap-3 px-3 py-2.5 mx-3 rounded-xl text-edrive-muted hover:text-edrive-accent hover:bg-edrive-accent/5 transition-all duration-200 text-sm font-medium;
        }
        .sidebar-link.active {
            @apply text-edrive-accent bg-edrive-sidebar-active font-semibold;
        }
        .sidebar-link.active i {
            @apply text-edrive-accent;
        }
        .sidebar-link i {
            @apply w-5 text-center text-base;
        }

        /* ─── Badge ─── */
        .badge {
            @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold;
        }
        .badge-primary { @apply bg-blue-100 text-blue-700; }
        .badge-success { @apply bg-emerald-100 text-emerald-700; }
        .badge-warning { @apply bg-amber-100 text-amber-700; }
        .badge-danger  { @apply bg-red-100 text-red-700; }
        .badge-info    { @apply bg-cyan-100 text-cyan-700; }

        /* ─── Table ─── */
        .table-light {
            @apply w-full text-sm text-left;
        }
        .table-light thead {
            @apply text-xs text-edrive-muted uppercase tracking-wider bg-gray-50/80 border-b border-edrive-border;
        }
        .table-light thead th {
            @apply px-6 py-4 font-semibold;
        }
        .table-light tbody tr {
            @apply border-b border-edrive-border/60 hover:bg-edrive-accent/[0.02] transition-colors;
        }
        .table-light tbody td {
            @apply px-6 py-4;
        }

        /* ─── Animations ─── */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-16px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.15); }
        }
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        .animate-slide-in { animation: slideInLeft 0.3s ease-out; }

        /* ─── Stat Card ─── */
        .stat-card {
            @apply glass-card p-6 relative overflow-hidden;
        }

        /* ─── Dropzone ─── */
        .dropzone {
            @apply border-2 border-dashed border-edrive-border rounded-2xl p-12 text-center 
                   hover:border-edrive-accent/50 hover:bg-edrive-accent/5 transition-all duration-300 cursor-pointer;
        }
        .dropzone.dragover {
            @apply border-edrive-accent bg-edrive-accent/10 scale-[1.01];
        }

        /* ─── Context Menu ─── */
        .context-menu {
            @apply absolute z-50 bg-white border border-edrive-border rounded-xl shadow-xl py-1.5 min-w-[200px] animate-fade-in;
        }
        .context-menu-item {
            @apply flex items-center gap-3 px-4 py-2.5 text-sm text-edrive-muted hover:text-edrive-accent hover:bg-edrive-accent/5 transition-all duration-150 cursor-pointer;
        }
        .context-menu-divider {
            @apply border-t border-edrive-border my-1;
        }

        /* ─── Tooltip ─── */
        .tooltip { @apply relative; }
        .tooltip::after {
            content: attr(data-tip);
            @apply absolute z-50 bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1.5 text-xs font-medium text-white 
                   bg-gray-800 rounded-lg opacity-0 invisible transition-all duration-200 whitespace-nowrap pointer-events-none;
        }
        .tooltip:hover::after { @apply opacity-100 visible; }

        /* ─── Modal ─── */
        .modal-overlay {
            @apply fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4;
        }
        .modal-content {
            @apply bg-white border border-edrive-border rounded-2xl shadow-2xl w-full max-w-lg animate-fade-in;
        }
        .modal-header {
            @apply flex items-center justify-between px-6 py-4 border-b border-edrive-border;
        }
        .modal-body {
            @apply px-6 py-5;
        }
        .modal-footer {
            @apply flex items-center justify-end gap-3 px-6 py-4 border-t border-edrive-border;
        }

        /* ─── Section Title ─── */
        .section-title {
            @apply text-lg font-bold text-edrive-text;
        }
        .section-subtitle {
            @apply text-sm text-edrive-muted;
        }
    </style>
</head>
<body>
<?php
    $CI =& Controller::get_instance();
    $CI->load->helper('url');
    $CI->load->helper('menu');
    
    // Get current URL segment
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_path = str_replace('index.php', '', $script_name);
    $uri = str_replace($base_path, '', $uri);
    if (($pos = strpos($uri, '?')) !== false) $uri = substr($uri, 0, $pos);
    $current_segment = trim($uri, '/');
    $segments = explode('/', $current_segment);
    $active_module = !empty($segments[0]) ? $segments[0] : 'dashboard';
    
    // Get sidebar menus
    $role_id = Session::get('role_id');
    $user_name = Session::get('name') ?? 'User';
    $user_email = Session::get('email') ?? '';
    $user_avatar = Session::get('avatar') ?? '';
    $role_name = Session::get('role_name') ?? '';
    
    $sidebar_menus = [];
    if ($role_id) {
        $sidebar_menus = $CI->db->query("
            SELECT m.* 
            FROM system_menus m 
            JOIN role_menu_access rma ON m.id = rma.menu_id 
            WHERE rma.role_id = ? AND m.is_active = 1 AND m.status != 8 AND m.parent_id IS NULL
            ORDER BY m.order_num ASC
        ", [$role_id])->fetchAll();
    }

    // Unread notifications count
    $unread_count = 0;
    if (Session::get('user_id')) {
        $notif_result = $CI->db->query("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0 AND status = 1", [Session::get('user_id')])->fetch();
        $unread_count = $notif_result ? $notif_result->cnt : 0;
    }
?>

<div class="flex flex-col min-h-screen bg-slate-50" id="app-layout">
    
    <!-- ===== TOP NAVBAR ===== -->
    <header class="bg-white border-b border-edrive-border sticky top-0 z-50 shadow-sm">
        <div class="flex items-center justify-between px-6 h-16">
            <!-- Left: Logo & Menus -->
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <a href="<?= site_url('dashboard') ?>" class="flex items-center gap-3 group">
                    <div class="w-8 h-8 bg-gradient-to-br from-edrive-accent to-blue-700 rounded-lg flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-hard-drive text-white text-sm"></i>
                    </div>
                    <div id="navbar-brand">
                        <h1 class="text-lg font-bold text-edrive-text tracking-tight">E-Drive</h1>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center gap-1">
                    <?php foreach ($sidebar_menus as $menu): ?>
                        <?php 
                            $is_active = ($active_module === $menu->url);
                            $menu_url = ($menu->url === '#') ? '#' : site_url($menu->url);
                        ?>
                        <a href="<?= $menu_url ?>" 
                           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors <?= $is_active ? 'bg-blue-50 text-edrive-accent' : 'text-edrive-muted hover:bg-slate-50 hover:text-edrive-text' ?>"
                           title="<?= htmlspecialchars($menu->name) ?>">
                            <i class="<?= htmlspecialchars($menu->icon) ?>"></i>
                            <span><?= htmlspecialchars($menu->name) ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <!-- Right: Actions & User -->
            <div class="flex items-center gap-4">
                <!-- Global Search Removed -->

                <!-- Notification Bell -->
                <a href="<?= site_url('notification') ?>" class="btn-icon relative" title="Notifications">
                    <i class="fa-solid fa-bell text-lg"></i>
                    <?php if ($unread_count > 0): ?>
                        <span class="absolute -top-0.5 -right-0.5 bg-edrive-danger text-white text-[9px] font-bold w-[18px] h-[18px] rounded-full flex items-center justify-center shadow-sm"><?= $unread_count > 9 ? '9+' : $unread_count ?></span>
                    <?php endif; ?>
                </a>

                <!-- User Dropdown Trigger -->
                <div class="relative">
                    <div class="flex items-center gap-3 px-2 py-1.5 rounded-xl hover:bg-slate-50 transition-all cursor-pointer group" onclick="toggleUserMenu()">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-edrive-accent to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                            <?= strtoupper(substr($user_name, 0, 1)) ?>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold text-edrive-text leading-tight"><?= htmlspecialchars($user_name) ?></p>
                            <p class="text-[11px] text-edrive-muted"><?= htmlspecialchars($role_name) ?></p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-hover:text-slate-600 ml-1"></i>
                    </div>
                    
                    <!-- User Dropdown Menu -->
                    <div id="user-menu" class="hidden absolute right-0 top-full mt-2 w-48 bg-white border border-edrive-border rounded-xl shadow-xl py-1.5 animate-fade-in z-50">
                        <a href="<?= site_url('auth/profile') ?>" class="context-menu-item">
                            <i class="fa-solid fa-user-pen w-4 text-edrive-light"></i> My Profile
                        </a>
                        <a href="<?= site_url('auth/change_password') ?>" class="context-menu-item">
                            <i class="fa-solid fa-key w-4 text-edrive-light"></i> Change Password
                        </a>
                        <div class="context-menu-divider"></div>
                        <a href="<?= site_url('auth/logout') ?>" class="context-menu-item !text-red-500 hover:!text-red-600 hover:!bg-red-50">
                            <i class="fa-solid fa-right-from-bracket w-4"></i> Logout
                        </a>
                    </div>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button onclick="toggleMobileMenu()" class="lg:hidden btn-icon text-edrive-text">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation (Hidden by default) -->
        <nav id="mobile-nav" class="hidden border-t border-edrive-border bg-white p-4 space-y-2 lg:hidden">
            <?php foreach ($sidebar_menus as $menu): ?>
                <?php 
                    $is_active = ($active_module === $menu->url);
                    $menu_url = ($menu->url === '#') ? '#' : site_url($menu->url);
                ?>
                <a href="<?= $menu_url ?>" 
                   class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors <?= $is_active ? 'bg-blue-50 text-edrive-accent' : 'text-edrive-muted hover:bg-slate-50 hover:text-edrive-text' ?>">
                    <i class="<?= htmlspecialchars($menu->icon) ?>"></i>
                    <span><?= htmlspecialchars($menu->name) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </header>

    <!-- ===== MAIN CONTENT AREA ===== -->
    <main class="flex-1 w-full max-w-7xl mx-auto p-6 animate-fade-in flex flex-col">
        
        <?php if (isset($breadcrumb) || isset($title)): ?>
        <div class="mb-6 flex items-center gap-2 text-sm shrink-0">
            <a href="<?= site_url('dashboard') ?>" class="text-edrive-muted hover:text-edrive-accent transition-colors">
                <i class="fa-solid fa-house text-xs"></i>
            </a>
            <?php if (isset($breadcrumb) && is_array($breadcrumb)): ?>
                <?php foreach ($breadcrumb as $crumb): ?>
                    <i class="fa-solid fa-chevron-right text-[10px] text-edrive-border"></i>
                    <?php if (isset($crumb['url'])): ?>
                        <a href="<?= $crumb['url'] ?>" class="text-edrive-muted hover:text-edrive-accent transition-colors"><?= htmlspecialchars($crumb['name']) ?></a>
                    <?php else: ?>
                        <span class="text-edrive-text font-medium"><?= htmlspecialchars($crumb['name']) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php elseif (isset($title)): ?>
                <i class="fa-solid fa-chevron-right text-[10px] text-edrive-border"></i>
                <span class="text-edrive-text font-medium"><?= htmlspecialchars($title) ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
