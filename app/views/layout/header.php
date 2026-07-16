<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — MAPUL Mie Ayam Pulean' : 'MAPUL Mie Ayam Pulean' ?></title>
    <!-- Favicon SVG inline -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%231B5E20'/><text x='16' y='22' text-anchor='middle' font-size='18' font-weight='bold' fill='%23FFD600' font-family='Arial'>M</text></svg>">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <!-- Tailwind CSS -->
    <script src="<?= base_url('assets/js/tailwindcss.js') ?>"></script>
    <!-- Tailwind Config — Tema MAPUL Hijau-Kuning-Silver -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mapul: {
                            green:      '#1B5E20',
                            'green-md': '#2E7D32',
                            'green-lt': '#388E3C',
                            'green-bg': '#E8F5E9',
                            yellow:     '#FFD600',
                            'yellow-lt':'#FFF9C4',
                            silver:     '#B0BEC5',
                            'silver-lt':'#ECEFF1',
                        },
                        richblack: '#1A1A1A',
                    },
                    fontFamily: {
                        sans: ['Inter', 'Segoe UI', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        /* Navbar utama */
        ul.navbar {
            @apply flex flex-wrap justify-center list-none m-0 p-0;
        }
        ul.navbar > li {
            @apply relative;
        }
        ul.navbar > li > a {
            @apply block px-5 py-4 text-green-100 no-underline font-medium uppercase tracking-wider text-sm transition-all duration-200;
        }
        ul.navbar > li > a:hover {
            @apply bg-mapul-green-md text-mapul-yellow border-b-2 border-mapul-yellow;
        }

        /* Dropdown */
        ul.dropdown {
            @apply hidden absolute bg-mapul-green shadow-2xl min-w-[220px] list-none p-0 m-0 z-50 border border-green-700 rounded-b-md;
        }
        ul.navbar li:hover > ul.dropdown {
            @apply block;
            animation: fadeDown 0.2s ease-out;
        }
        ul.dropdown li a {
            @apply block px-5 py-3 text-green-100 no-underline border-b border-green-800 text-sm transition-all duration-150;
        }
        ul.dropdown li a:hover {
            @apply bg-mapul-green-md text-mapul-yellow pl-7 border-l-4 border-mapul-yellow;
        }

        /* Nested dropdown */
        ul.dropdown ul.dropdown {
            @apply left-full top-0 rounded-md;
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Badge status stok */
        .badge-aman    { @apply bg-green-100 text-green-800 text-xs font-bold px-2 py-0.5 rounded-full; }
        .badge-menipis { @apply bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded-full; }
        .badge-habis   { @apply bg-red-100 text-red-800 text-xs font-bold px-2 py-0.5 rounded-full; }

        /* Tombol utama hijau */
        .btn-mapul {
            @apply bg-mapul-green-md hover:bg-mapul-green text-white font-semibold py-2 px-5 rounded-lg shadow border border-green-700 transition-all duration-200 flex items-center gap-2;
        }
        /* Tombol danger */
        .btn-danger {
            @apply bg-white hover:bg-red-50 text-red-600 border border-red-300 font-medium py-1.5 px-3 rounded text-xs transition-colors shadow-sm;
        }
        /* Tombol edit */
        .btn-edit {
            @apply bg-yellow-50 hover:bg-yellow-100 text-yellow-800 border border-yellow-300 font-medium py-1.5 px-3 rounded text-xs transition-colors shadow-sm;
        }
        /* Tombol restore */
        .btn-restore {
            @apply bg-green-50 hover:bg-green-100 text-green-800 border border-green-300 font-medium py-1.5 px-3 rounded text-xs transition-colors shadow-sm;
        }

        /* Card */
        .mapul-card {
            @apply bg-white rounded-xl shadow-md overflow-hidden border border-mapul-silver;
        }

        /* Header tabel */
        .mapul-thead {
            @apply bg-mapul-green text-white uppercase text-xs tracking-wider;
        }
        .mapul-thead th {
            @apply px-6 py-4 font-semibold;
        }

        /* Modal header */
        .mapul-modal-header {
            @apply px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-mapul-green-bg rounded-t-xl;
        }

        /* Input focus */
        .mapul-input {
            @apply w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md
                   focus:ring-2 focus:ring-green-400 focus:border-transparent
                   transition-all shadow-inner text-richblack font-medium;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-mapul-silver-lt via-gray-100 to-mapul-green-bg min-h-screen text-richblack font-sans antialiased">

    <!-- ===== TOP HEADER BAR ===== -->
    <div class="bg-gradient-to-r from-mapul-green via-mapul-green-md to-mapul-green text-white px-4 md:px-8 py-3 flex flex-col md:flex-row justify-between items-center shadow-xl border-b-2 border-mapul-yellow relative overflow-hidden">
        <!-- Glow accent kuning -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-yellow-400 to-transparent opacity-5 pointer-events-none"></div>

        <!-- Logo + Brand -->
        <div class="flex items-center space-x-3 mb-3 md:mb-0 relative z-10">
            <!-- SVG Logo MAPUL -->
            <div class="bg-mapul-yellow p-2 rounded-xl shadow-md border-2 border-yellow-300 transform hover:scale-105 transition-transform duration-300">
                <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Mangkuk -->
                    <ellipse cx="18" cy="22" rx="13" ry="5" fill="#1B5E20" opacity="0.9"/>
                    <path d="M5 17 Q5 28 18 30 Q31 28 31 17 Z" fill="#2E7D32"/>
                    <!-- Mie / uap -->
                    <path d="M11 13 Q13 10 15 13 Q17 16 19 13 Q21 10 23 13 Q25 16 25 13" stroke="#FFD600" stroke-width="2" fill="none" stroke-linecap="round"/>
                    <path d="M13 10 Q14 7 15 10" stroke="#FFD600" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    <path d="M20 10 Q21 7 22 10" stroke="#FFD600" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    <!-- Sumpit -->
                    <line x1="26" y1="6" x2="20" y2="18" stroke="#FFD600" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="29" y1="8" x2="22" y2="18" stroke="#FFD600" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-extrabold tracking-widest uppercase text-mapul-yellow leading-tight drop-shadow">
                    MAPUL
                </h2>
                <p class="text-green-200 text-xs font-light tracking-widest -mt-0.5">Mie Ayam Pulean</p>
            </div>
        </div>

        <!-- User Info + Logout -->
        <div class="flex items-center space-x-3 relative z-10">
            <div class="flex items-center gap-1.5 md:gap-2 px-3 md:px-4 py-1.5 md:py-2 rounded-full bg-green-900 border border-green-700 shadow-inner">
                <i class="fa fa-user-circle text-mapul-yellow text-sm md:text-base"></i>
                <span class="text-green-300 text-xs md:text-sm">Selamat datang,</span>
                <span class="text-mapul-yellow font-semibold text-xs md:text-sm"><?= htmlspecialchars(Session::get('name') ?? '') ?></span>
            </div>
            <a href="<?= site_url('auth/logout') ?>"
               class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg shadow border border-red-600 hover:border-red-500 transition-all duration-200 flex items-center gap-2 font-semibold text-sm">
                <i class="fa fa-power-off"></i> <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- ===== NAVBAR ===== -->
    <div class="bg-mapul-green border-b border-green-900 shadow-md">
        <?php
            $CI =& Controller::get_instance();
            $CI->load->helper('menu');
            echo render_dynamic_menu(Session::get('role_id'));
        ?>
    </div>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="container mx-auto px-4 py-6">
