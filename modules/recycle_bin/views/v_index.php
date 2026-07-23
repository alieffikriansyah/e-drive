<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="section-title text-2xl text-red-600 flex items-center gap-2">
                <i class="fa-solid fa-trash-can"></i> Recycle Bin
            </h1>
            <p class="section-subtitle mt-1">File di sini dapat dipulihkan atau dihapus selamanya</p>
        </div>
        <?php if (!empty($documents)): ?>
        <button class="btn-secondary text-red-600 hover:bg-red-50 hover:border-red-200" onclick="emptyRecycleBin()">
            <i class="fa-solid fa-dumpster-fire"></i> Kosongkan
        </button>
        <?php endif; ?>
    </div>

    <!-- Results -->
    <div class="glass-card overflow-hidden border-t-4 border-t-red-500">
        <div class="overflow-x-auto">
            <table class="table-light whitespace-nowrap">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Lokasi Asal (Drive)</th>
                        <th>Ukuran</th>
                        <th>Dihapus Oleh</th>
                        <th>Waktu Dihapus</th>
                        <th class="w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-16 text-edrive-muted">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 shadow-inner">
                                <i class="fa-solid fa-wind text-3xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-edrive-text">Recycle Bin Kosong</h3>
                            <p class="mt-1 text-sm">Tidak ada file yang dihapus.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $doc): ?>
                        <tr class="group" id="trash-row-<?= $doc->id ?>">
                            <td>
                                <div class="flex items-center gap-3 opacity-60 group-hover:opacity-100 transition-opacity">
                                    <i class="<?= get_file_icon($doc->file_type) ?> text-2xl w-8 text-center grayscale group-hover:grayscale-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-edrive-text line-through group-hover:no-underline truncate" title="<?= htmlspecialchars($doc->name) ?>">
                                            <?= htmlspecialchars($doc->name) ?>
                                        </p>
                                        <p class="text-[10px] text-edrive-muted uppercase"><?= htmlspecialchars($doc->file_type) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info bg-gray-100 text-gray-600 border border-gray-200">
                                    <i class="fa-solid fa-hard-drive mr-1"></i> <?= htmlspecialchars($doc->drive_name) ?>
                                </span>
                            </td>
                            <td class="text-edrive-muted"><?= format_file_size($doc->file_size) ?></td>
                            <td class="text-edrive-text text-sm"><?= htmlspecialchars($doc->deleted_by_name ?? 'System') ?></td>
                            <td class="text-red-500 text-sm">
                                <div class="tooltip" data-tip="<?= formatDate($doc->updated_at) ?>">
                                    <?= timeAgo($doc->updated_at) ?>
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="btn-icon bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-200" title="Pulihkan (Restore)" onclick="restoreFile(<?= $doc->id ?>)">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                    <button class="btn-icon bg-red-50 text-red-600 hover:bg-red-100 border border-red-200" title="Hapus Permanen" onclick="destroyFile(<?= $doc->id ?>)">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
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
async function restoreFile(id) {
    if (await confirmAction('Pulihkan File?', 'File akan dikembalikan ke lokasi asalnya.', 'info', 'Ya, Pulihkan')) {
        showLoading('Memulihkan...');
        const data = await fetchAPI('recycle_bin/restore', { method: 'POST', body: { document_id: id } });
        hideLoading();
        if (data && data.status) {
            document.getElementById('trash-row-' + id).remove();
            showSuccess(data.message);
            // Refresh if empty
            if (document.querySelectorAll('tbody tr').length === 0) location.reload();
        } else {
            showError(data ? data.message : 'Gagal memulihkan file');
        }
    }
}

async function destroyFile(id) {
    if (await confirmAction('Hapus Permanen?', 'File fisik akan dihapus dari server dan tidak dapat dikembalikan lagi! Aksi ini bersifat PERMANEN.', 'error', 'Ya, Hapus Permanen')) {
        showLoading('Menghapus Permanen...');
        const data = await fetchAPI('recycle_bin/permanent_delete', { method: 'POST', body: { document_id: id } });
        hideLoading();
        if (data && data.status) {
            document.getElementById('trash-row-' + id).remove();
            showSuccess(data.message);
            if (document.querySelectorAll('tbody tr').length === 0) location.reload();
        } else {
            showError(data ? data.message : 'Gagal menghapus file');
        }
    }
}

function emptyRecycleBin() {
    showInfo('Fitur kosongkan semua (Batch Delete) segera hadir.');
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
