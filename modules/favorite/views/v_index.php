<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="section-title text-2xl">Favorites</h1>
        <p class="section-subtitle mt-1">Akses cepat ke file dan dokumen penting Anda</p>
    </div>

    <!-- Results -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-light">
                <thead>
                    <tr>
                        <th class="w-10"></th>
                        <th>Nama File</th>
                        <th>Lokasi Drive</th>
                        <th>Ukuran</th>
                        <th>Ditambahkan</th>
                        <th class="w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-16 text-edrive-muted">
                            <div class="w-20 h-20 bg-yellow-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-regular fa-star text-3xl text-yellow-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-edrive-text">Belum ada Favorit</h3>
                            <p class="mt-1 text-sm">Tandai file dengan bintang untuk menemukannya dengan cepat di sini.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                        <tr class="group" id="fav-row-<?= $doc->id ?>">
                            <td class="text-center">
                                <button class="text-yellow-400 hover:text-gray-300 transition-colors" title="Hapus dari Favorit" onclick="toggleFavorite(<?= $doc->id ?>, this)">
                                    <i class="fa-solid fa-star"></i>
                                </button>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <i class="<?= get_file_icon($doc->file_type) ?> text-2xl w-8 text-center"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-edrive-text truncate hover:text-edrive-accent transition-colors cursor-pointer" onclick="previewFile(<?= $doc->id ?>)">
                                            <?= htmlspecialchars($doc->name) ?>
                                        </p>
                                        <p class="text-[10px] text-edrive-muted uppercase"><?= htmlspecialchars($doc->file_type) ?> &bull; V<?= $doc->version ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info bg-blue-50 text-blue-600 border border-blue-100">
                                    <i class="fa-solid fa-hard-drive mr-1"></i> <?= htmlspecialchars($doc->drive_name) ?>
                                </span>
                            </td>
                            <td class="text-edrive-muted"><?= format_file_size($doc->file_size) ?></td>
                            <td class="text-edrive-muted">
                                <div class="tooltip" data-tip="<?= formatDate($doc->created_at) ?>">
                                    <?= timeAgo($doc->created_at) ?>
                                </div>
                            </td>
                            <td>
                                <a href="<?= site_url('document/download/' . $doc->id) ?>" class="btn-icon inline-block" title="Download">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function previewFile(id) { showInfo('Preview file ' + id + ' segera hadir (Phase 7)'); }

async function toggleFavorite(id, btnElement) {
    try {
        const formData = new FormData();
        formData.append('entity_type', 'document');
        formData.append('entity_id', id);
        
        const response = await fetch(BASE_URL + 'favorite/toggle', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        const data = await response.json();
        if (data.status) {
            if (!data.is_favorite) {
                // Remove row from table
                const row = document.getElementById('fav-row-' + id);
                if (row) {
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 300);
                }
            }
            showSuccess(data.message);
        } else {
            showError(data.message);
        }
    } catch (e) {
        showError('Gagal mengubah status favorit');
    }
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
