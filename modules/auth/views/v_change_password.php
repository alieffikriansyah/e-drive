<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="max-w-xl mx-auto space-y-6">
    <!-- Page Title -->
    <div>
        <h1 class="section-title text-2xl">Ubah Password</h1>
        <p class="section-subtitle mt-1">Perbarui password akun Anda secara berkala demi keamanan</p>
    </div>

    <!-- Form Card -->
    <div class="glass-card p-6">
        <form id="password-form">
            <div class="space-y-5">
                <!-- Current Password -->
                <div>
                    <label class="block text-sm font-semibold text-edrive-text mb-1.5">Password Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" class="input-field pr-10" required>
                        <button type="button" onclick="togglePassword('current_password', 'icon-current')" class="absolute right-3 top-1/2 -translate-y-1/2 text-edrive-muted hover:text-edrive-accent">
                            <i class="fa-solid fa-eye" id="icon-current"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-sm font-semibold text-edrive-text mb-1.5">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="new_password" class="input-field pr-10" required minlength="6">
                        <button type="button" onclick="togglePassword('new_password', 'icon-new')" class="absolute right-3 top-1/2 -translate-y-1/2 text-edrive-muted hover:text-edrive-accent">
                            <i class="fa-solid fa-eye" id="icon-new"></i>
                        </button>
                    </div>
                    <p class="text-xs text-edrive-muted mt-1.5">Minimal 6 karakter.</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-semibold text-edrive-text mb-1.5">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" name="confirm_password" id="confirm_password" class="input-field pr-10" required minlength="6">
                        <button type="button" onclick="togglePassword('confirm_password', 'icon-confirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-edrive-muted hover:text-edrive-accent">
                            <i class="fa-solid fa-eye" id="icon-confirm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="<?= site_url('auth/profile') ?>" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-key"></i> Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.getElementById('password-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Client side validation
    if (formData.get('new_password') !== formData.get('confirm_password')) {
        showError('Password baru dan konfirmasi tidak cocok!');
        return;
    }
    
    try {
        const response = await fetch(BASE_URL + 'auth/change_password', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.status) {
            showSuccess(data.message);
            this.reset();
        } else {
            showError(data.message);
        }
    } catch (err) {
        showError('Terjadi kesalahan pada server');
    }
});
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
