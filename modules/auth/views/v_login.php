<?php require_once APPPATH . 'views/layout/header_auth.php'; ?>

<div class="relative z-10 w-full max-w-md">
    <!-- Logo -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-edrive-accent to-blue-700 rounded-2xl shadow-xl shadow-edrive-accent/20 mb-4 float-anim">
            <i class="fa-solid fa-hard-drive text-white text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-edrive-text tracking-tight">E-Drive</h1>
        <p class="text-sm text-edrive-muted mt-1">Enterprise Document Management System</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 border border-edrive-border p-8">
        <h2 class="text-xl font-bold text-edrive-text mb-1">Selamat Datang</h2>
        <p class="text-sm text-edrive-muted mb-6">Silakan masuk ke akun Anda</p>

        <?php $error = Session::flashdata('error'); ?>
        <?php if ($error): ?>
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-5">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('auth/process') ?>" method="POST" autocomplete="off">
            <?= csrf_field() ?>
            
            <!-- Username -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-edrive-text mb-2">
                    <i class="fa-solid fa-user text-edrive-muted mr-1 text-xs"></i> Username
                </label>
                <input type="text" name="username" 
                       class="w-full px-4 py-3 bg-edrive-bg border border-edrive-border rounded-xl text-edrive-text placeholder-gray-400
                              focus:ring-2 focus:ring-edrive-accent/20 focus:border-edrive-accent focus:outline-none transition-all text-sm"
                       placeholder="Masukkan username" 
                       value="<?= set_value('username') ?>"
                       required autofocus>
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-edrive-text mb-2">
                    <i class="fa-solid fa-lock text-edrive-muted mr-1 text-xs"></i> Password
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password-field"
                           class="w-full px-4 py-3 bg-edrive-bg border border-edrive-border rounded-xl text-edrive-text placeholder-gray-400
                                  focus:ring-2 focus:ring-edrive-accent/20 focus:border-edrive-accent focus:outline-none transition-all text-sm pr-12"
                           placeholder="Masukkan password" required>
                    <button type="button" onclick="togglePassword()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-edrive-muted hover:text-edrive-accent transition-colors p-1">
                        <i class="fa-solid fa-eye" id="toggle-pw-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Login Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-edrive-accent to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-3 px-6 rounded-xl shadow-lg shadow-edrive-accent/20 hover:shadow-xl hover:shadow-edrive-accent/30 transition-all duration-300 flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk</span>
            </button>
        </form>
    </div>

    <!-- Footer -->
    <p class="text-center text-xs text-edrive-muted mt-6">
        &copy; <?= date('Y') ?> E-Drive — Enterprise Document Management System
    </p>
</div>

<script>
function togglePassword() {
    const field = document.getElementById('password-field');
    const icon = document.getElementById('toggle-pw-icon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php require_once APPPATH . 'views/layout/footer_auth.php'; ?>
