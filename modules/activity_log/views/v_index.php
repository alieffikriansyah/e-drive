<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="section-title text-2xl">Activity Log</h1>
        <p class="section-subtitle mt-1">
            <?= $is_admin ? 'Riwayat aktivitas seluruh pengguna sistem' : 'Riwayat aktivitas Anda di dalam sistem' ?>
        </p>
    </div>

    <!-- Results -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-light">
                <thead>
                    <tr>
                        <th class="w-16">Waktu</th>
                        <th>Pengguna</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-16 text-edrive-muted">
                            <i class="fa-solid fa-clock-rotate-left text-3xl mb-3 text-gray-300"></i>
                            <p>Belum ada aktivitas tercatat.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <?php 
                            $badge_color = 'bg-gray-100 text-gray-600 border-gray-200';
                            if($log->action == 'UPLOAD' || $log->action == 'CREATE') { $badge_color = 'bg-emerald-50 text-emerald-600 border-emerald-200'; }
                            elseif($log->action == 'DOWNLOAD') { $badge_color = 'bg-blue-50 text-blue-600 border-blue-200'; }
                            elseif($log->action == 'DELETE' || $log->action == 'DELETE_PERMANENT') { $badge_color = 'bg-red-50 text-red-600 border-red-200'; }
                            elseif($log->action == 'LOGIN') { $badge_color = 'bg-purple-50 text-purple-600 border-purple-200'; }
                        ?>
                        <tr class="hover:bg-gray-50/50">
                            <td class="text-xs text-edrive-muted whitespace-nowrap">
                                <div class="font-medium text-edrive-text"><?= date('d M Y', strtotime($log->created_at)) ?></div>
                                <div><?= date('H:i', strtotime($log->created_at)) ?></div>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-edrive-accent to-indigo-600 flex items-center justify-center text-white text-xs font-bold overflow-hidden shadow-sm">
                                        <?php if (!empty($log->avatar) && file_exists(STORAGEPATH . 'avatars/' . $log->avatar)): ?>
                                            <img src="<?= base_url('storage/avatars/' . $log->avatar) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?= strtoupper(substr($log->user_name, 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <span class="font-medium text-edrive-text text-sm"><?= htmlspecialchars($log->user_name) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider border <?= $badge_color ?>">
                                    <?= htmlspecialchars($log->action) ?>
                                </span>
                            </td>
                            <td class="text-sm text-edrive-text"><?= htmlspecialchars($log->description) ?></td>
                            <td class="text-xs text-edrive-muted font-mono"><?= htmlspecialchars($log->ip_address) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="px-5 py-4 border-t border-edrive-border bg-gray-50 flex items-center justify-between">
            <span class="text-sm text-edrive-muted">Halaman <?= $current_page ?> dari <?= $total_pages ?></span>
            <div class="flex gap-1">
                <?php if ($current_page > 1): ?>
                <a href="?page=<?= $current_page - 1 ?>" class="px-3 py-1.5 text-sm bg-white border border-edrive-border rounded-lg text-edrive-text hover:bg-gray-50 transition-colors">Prev</a>
                <?php endif; ?>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?= $current_page + 1 ?>" class="px-3 py-1.5 text-sm bg-white border border-edrive-border rounded-lg text-edrive-text hover:bg-gray-50 transition-colors">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
