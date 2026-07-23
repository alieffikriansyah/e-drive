<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="section-title text-2xl">My Drive</h1>
            <p class="section-subtitle mt-1">Kelola dan akses dokumen berdasarkan departemen atau project</p>
        </div>
        <?php if ($is_admin): ?>
        <button onclick="showAddDriveModal()" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Drive
        </button>
        <?php endif; ?>
    </div>

    <!-- Error/Success Messages -->
    <?php if (Session::flashdata('error')): ?>
        <div class="bg-red-50 text-red-600 border border-red-200 p-4 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars(Session::flashdata('error')) ?>
        </div>
    <?php endif; ?>
    <?php if (Session::flashdata('success')): ?>
        <div class="bg-emerald-50 text-emerald-600 border border-emerald-200 p-4 rounded-xl text-sm flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars(Session::flashdata('success')) ?>
        </div>
    <?php endif; ?>

    <!-- Drives Grid -->
    <?php if (empty($drives)): ?>
        <div class="glass-card p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-hard-drive text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-bold text-edrive-text">Tidak ada Drive tersedia</h3>
            <p class="text-edrive-muted mt-1 text-sm">Anda belum memiliki akses ke drive manapun.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($drives as $d): ?>
            <a href="<?= site_url('drive/view/' . $d->id) ?>" class="glass-card-hover p-5 block group relative overflow-hidden">
                <!-- Color Bar Top -->
                <div class="absolute top-0 left-0 right-0 h-1" style="background-color: <?= $d->color ?? '#3B82F6' ?>"></div>
                
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-sm" style="background-color: <?= ($d->color ?? '#3B82F6') . '15' ?>; color: <?= $d->color ?? '#3B82F6' ?>">
                        <i class="<?= htmlspecialchars($d->icon ?? 'fa-solid fa-folder') ?> text-xl"></i>
                    </div>
                    <?php if ($is_admin): ?>
                    <div class="relative" onclick="event.preventDefault();">
                        <button class="btn-icon" onclick="toggleDriveMenu(<?= $d->id ?>)">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                        <div id="drive-menu-<?= $d->id ?>" class="hidden context-menu right-0 mt-1">
                            <div class="context-menu-item" onclick="editDrive(<?= htmlspecialchars(json_encode($d)) ?>)">
                                <i class="fa-solid fa-pen text-edrive-muted w-4"></i> Edit Drive
                            </div>
                            <div class="context-menu-divider"></div>
                            <div class="context-menu-item text-red-500 hover:text-red-600 hover:bg-red-50" onclick="deleteDrive(<?= $d->id ?>)">
                                <i class="fa-solid fa-trash w-4"></i> Hapus
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <h3 class="font-bold text-edrive-text text-lg mb-1 group-hover:text-edrive-accent transition-colors">
                    <?= htmlspecialchars($d->name) ?>
                </h3>
                <p class="text-xs text-edrive-muted line-clamp-2 h-8 mb-4">
                    <?= htmlspecialchars($d->description ?? 'Tidak ada deskripsi') ?>
                </p>

                <!-- Footer Stats -->
                <div class="flex items-center justify-between text-xs text-edrive-muted pt-4 border-t border-edrive-border">
                    <span class="flex items-center gap-1" title="Owner/Akses">
                        <i class="fa-solid fa-shield-halved"></i> 
                        <?= $d->is_shared ? 'Shared' : htmlspecialchars($d->owner_role ?? 'Semua') ?>
                    </span>
                    <span class="flex items-center gap-1 font-medium bg-gray-50 px-2 py-1 rounded">
                        <i class="fa-solid fa-database text-[10px]"></i> <?= format_file_size($d->used_size ?? 0) ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Click outside handler for context menus
document.addEventListener('click', function(e) {
    if (!e.target.closest('.relative')) {
        document.querySelectorAll('.context-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

function toggleDriveMenu(id) {
    // Hide others
    document.querySelectorAll('.context-menu').forEach(menu => {
        if (menu.id !== 'drive-menu-' + id) menu.classList.add('hidden');
    });
    // Toggle current
    document.getElementById('drive-menu-' + id).classList.toggle('hidden');
}

async function deleteDrive(id) {
    if (await confirmAction('Hapus Drive?', 'Drive yang dihapus akan dipindahkan ke Recycle Bin beserta isinya.', 'warning')) {
        showLoading('Menghapus...');
        // Implement delete via fetch...
        hideLoading();
        showInfo('Fitur hapus drive segera tersedia');
    }
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
