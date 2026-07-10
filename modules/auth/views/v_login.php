<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — MAPUL Mie Ayam Pulean</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%231B5E20'/><text x='16' y='22' text-anchor='middle' font-size='18' font-weight='bold' fill='%23FFD600' font-family='Arial'>M</text></svg>">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <!-- Tailwind CSS -->
    <script src="<?= base_url('assets/js/tailwindcss.js') ?>"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mapul: {
                            green:      '#1B5E20',
                            'green-md': '#2E7D32',
                            'green-lt': '#388E3C',
                            yellow:     '#FFD600',
                            'yellow-lt':'#FFF9C4',
                            silver:     '#B0BEC5',
                        },
                        richblack: '#1A1A1A',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Animated rotating border — warna hijau & kuning */
        @keyframes border-spin {
            100% { transform: rotate(360deg); }
        }
        .animated-border-box {
            position: relative;
            border-radius: 1rem;
            padding: 3px;
            overflow: hidden;
        }
        .animated-border-box::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: conic-gradient(
                from 0deg,
                transparent 0%,
                transparent 50%,
                #FFD600 70%,
                #1B5E20 85%,
                transparent 100%
            );
            animation: border-spin 4s linear infinite;
            z-index: 0;
        }
        .animated-border-content {
            position: relative;
            background: white;
            border-radius: 0.85rem;
            z-index: 1;
        }

        /* Input focus */
        input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(27, 94, 32, 0.25);
            border-color: #2E7D32;
        }
    </style>
</head>
<body class="min-h-screen flex justify-center items-center font-sans antialiased p-4"
      style="background: linear-gradient(135deg, #E8F5E9 0%, #F5F5F5 50%, #E0F2F1 100%);">

    <div class="animated-border-box w-full max-w-md mx-4 shadow-2xl">
        <div class="animated-border-content p-8 w-full relative overflow-hidden">

            <!-- Logo MAPUL -->
            <div class="flex justify-center mb-5">
                <div class="bg-mapul-green p-4 rounded-2xl shadow-lg border-2 border-mapul-yellow transform hover:scale-105 transition-transform duration-300 flex flex-col items-center">
                    <!-- SVG Logo -->
                    <svg width="48" height="48" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="18" cy="22" rx="13" ry="5" fill="#145214" opacity="0.9"/>
                        <path d="M5 17 Q5 28 18 30 Q31 28 31 17 Z" fill="#388E3C"/>
                        <path d="M11 13 Q13 10 15 13 Q17 16 19 13 Q21 10 23 13 Q25 16 25 13"
                              stroke="#FFD600" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                        <path d="M13 10 Q14 7 15 10" stroke="#FFD600" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                        <path d="M20 10 Q21 7 22 10" stroke="#FFD600" stroke-width="1.8" fill="none" stroke-linecap="round"/>
                        <line x1="26" y1="6" x2="20" y2="18" stroke="#FFD600" stroke-width="1.8" stroke-linecap="round"/>
                        <line x1="29" y1="8" x2="22" y2="18" stroke="#FFD600" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <!-- Brand Name -->
            <h2 class="text-3xl font-extrabold text-center tracking-widest uppercase mb-1"
                style="color: #1B5E20;">MAPUL</h2>
            <p class="text-center text-gray-500 text-sm font-medium mb-1 tracking-wider">Mie Ayam Pulean</p>
            <p class="text-center text-gray-400 text-xs mb-6">Point of Sale System</p>

            <!-- Divider -->
            <div class="flex items-center justify-center space-x-3 mb-7 opacity-50">
                <div class="h-px w-20 bg-gradient-to-r from-transparent to-green-400"></div>
                <div class="h-2 w-2 rotate-45 bg-mapul-yellow rounded-sm"></div>
                <div class="h-px w-20 bg-gradient-to-l from-transparent to-green-400"></div>
            </div>

            <!-- Flash Error -->
            <?php if ($error = Session::flashdata('error')): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm text-center font-medium shadow-sm flex items-center justify-center gap-2">
                    <i class="fa fa-times-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form action="<?= site_url('auth/process') ?>" method="post" class="space-y-5">

                <!-- Username -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                        <i class="fa fa-user text-green-600 mr-1"></i> Username
                    </label>
                    <input type="text" name="username" required
                           value="<?= set_value('username') ?>"
                           class="w-full pl-4 pr-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-800 font-medium transition-all"
                           placeholder="Masukkan username">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                        <i class="fa fa-lock text-green-600 mr-1"></i> Password
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="passwordInput" required
                               class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-800 font-medium transition-all"
                               placeholder="Masukkan password">
                        <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-green-600 focus:outline-none transition-colors">
                            <i class="fa fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3 px-6 rounded-lg font-bold text-white shadow-lg transition-all duration-200 flex items-center justify-center gap-2 uppercase tracking-wider text-sm"
                            style="background: linear-gradient(135deg, #2E7D32, #1B5E20);"
                            onmouseover="this.style.background='linear-gradient(135deg, #1B5E20, #145214)'"
                            onmouseout="this.style.background='linear-gradient(135deg, #2E7D32, #1B5E20)'">
                        <i class="fa fa-sign-in-alt"></i> Masuk
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-7 text-center text-xs text-gray-400 font-medium border-t border-gray-100 pt-5">
                &copy; <?= date('Y') ?> <span class="font-bold text-green-700">MAPUL</span> Mie Ayam Pulean
                <span class="mx-1 text-mapul-yellow">•</span> POS System
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon  = document.getElementById('togglePasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
