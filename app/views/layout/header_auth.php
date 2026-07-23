<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — E-Drive' : 'Login — E-Drive' ?></title>
    <meta name="description" content="E-Drive Enterprise Document Management System">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%232563EB'/><path d='M8 10h16v3H8zm0 5h12v3H8zm0 5h14v3H8z' fill='%23fff' opacity='.9'/></svg>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>">
    <script src="<?= base_url('assets/js/tailwindcss.js') ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        edrive: {
                            accent: '#2563EB',
                            text: '#1E293B',
                            muted: '#64748B',
                            bg: '#F1F5F9',
                            border: '#E2E8F0',
                        },
                    },
                    fontFamily: { sans: ['Inter', 'Segoe UI', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .float-anim { animation: float 6s ease-in-out infinite; }
    </style>
</head>
<body class="bg-edrive-bg font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-edrive-accent/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl"></div>
    </div>
