<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="section-title text-2xl">Global Search</h1>
        <p class="section-subtitle mt-1">Cari dokumen di seluruh drive yang Anda miliki aksesnya</p>
    </div>

    <!-- Search Box -->
    <div class="glass-card p-6">
        <form action="<?= site_url('search') ?>" method="GET" class="flex gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-edrive-muted"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($q ?? '') ?>" 
                       class="w-full pl-11 pr-4 py-3 bg-edrive-bg border border-edrive-border rounded-xl text-edrive-text placeholder-gray-400 focus:ring-2 focus:ring-edrive-accent/20 focus:border-edrive-accent focus:outline-none transition-all"
                       placeholder="Ketik nama file, ekstensi, atau tag..." autofocus>
            </div>
            <button type="submit" class="btn-primary">Cari</button>
        </form>
    </div>

    <!-- Results -->
    <?php if (!empty($q)): ?>
    <div class="glass-card overflow-hidden">
        <div class="p-5 border-b border-edrive-border bg-white">
            <h3 class="text-sm font-semibold text-edrive-text">
                Menemukan <span class="text-edrive-accent"><?= count($results) ?></span> hasil untuk "<?= htmlspecialchars($q) ?>"
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table-light">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Lokasi Drive / Folder</th>
                        <th>Ukuran</th>
                        <th>Tanggal</th>
                        <th class="w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-12 text-edrive-muted">
                            <i class="fa-solid fa-magnifying-glass-minus text-4xl mb-3 text-gray-300"></i>
                            <p>Tidak ada dokumen yang cocok dengan pencarian Anda.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($results as $doc): ?>
                        <tr class="group">
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
                                <div class="text-sm text-edrive-text flex items-center gap-2">
                                    <span class="badge badge-info bg-blue-50 text-blue-600 border border-blue-100">
                                        <i class="fa-solid fa-hard-drive mr-1"></i> <?= htmlspecialchars($doc->drive_name) ?>
                                    </span>
                                    <?php if ($doc->folder_name): ?>
                                    <i class="fa-solid fa-chevron-right text-[10px] text-edrive-muted"></i>
                                    <span class="text-edrive-muted"><?= htmlspecialchars($doc->folder_name) ?></span>
                                    <?php endif; ?>
                                </div>
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
    <?php endif; ?>
</div>

<script>
function previewFile(id) { showInfo('Preview file ' + id + ' segera hadir (Phase 7)'); }
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
