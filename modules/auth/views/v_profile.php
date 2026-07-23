<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="max-w-2xl mx-auto space-y-6">
    <!-- Page Title -->
    <div>
        <h1 class="section-title text-2xl">My Profile</h1>
        <p class="section-subtitle mt-1">Kelola informasi profil Anda</p>
    </div>

    <!-- Profile Card -->
    <div class="glass-card p-6">
        <form id="profile-form" enctype="multipart/form-data">
            <!-- Avatar -->
            <div class="flex items-center gap-5 mb-6 pb-6 border-b border-edrive-border">
                <div class="relative group">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-edrive-accent to-indigo-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg overflow-hidden"
                         id="avatar-preview">
                        <?php if (!empty($user->avatar) && file_exists(STORAGEPATH . 'avatars/' . $user->avatar)): ?>
                            <img src="<?= base_url('storage/avatars/' . $user->avatar) ?>" class="w-full h-full object-cover" alt="Avatar">
                        <?php else: ?>
                            <?= strtoupper(substr($user->name ?? 'U', 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <label class="absolute inset-0 bg-black/30 rounded-2xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                        <i class="fa-solid fa-camera text-white text-lg"></i>
                        <input type="file" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                    </label>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-edrive-text"><?= htmlspecialchars($user->name ?? '') ?></h3>
                    <span class="badge badge-primary mt-1"><?= htmlspecialchars($user->role_name ?? '') ?></span>
                </div>
            </div>

            <!-- Form Fields -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-edrive-text mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user->name ?? '') ?>" class="input-field" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-edrive-text mb-1.5">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-edrive-text mb-1.5">No. Telepon</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-edrive-text mb-1.5">Username</label>
                    <input type="text" value="<?= htmlspecialchars($user->username ?? '') ?>" class="input-field bg-gray-100" disabled>
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-6 flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-check"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover" alt="Avatar">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('profile-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        const response = await fetch(BASE_URL + 'auth/update_profile', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await response.json();
        
        if (data.status) {
            showSuccess(data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showError(data.message);
        }
    } catch (err) {
        showError('Terjadi kesalahan');
    }
});
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
