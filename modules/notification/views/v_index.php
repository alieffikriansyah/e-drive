<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="section-title text-2xl">Notifikasi</h1>
        <p class="section-subtitle mt-1">Pemberitahuan terkait dokumen, folder, dan aktivitas akun Anda</p>
    </div>

    <!-- Notifications List -->
    <div class="glass-card p-0 overflow-hidden">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-16 text-edrive-muted">
                <i class="fa-regular fa-bell text-4xl mb-4 text-gray-300"></i>
                <h3 class="text-lg font-bold text-edrive-text">Tidak ada notifikasi</h3>
                <p class="mt-1 text-sm">Anda telah membaca semua pemberitahuan terbaru.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-100">
                <?php foreach ($notifications as $notif): ?>
                <?php 
                    $bg_color = 'bg-gray-50'; $icon_color = 'text-gray-500';
                    if ($notif->type == 'success') { $bg_color = 'bg-emerald-50'; $icon_color = 'text-emerald-500'; }
                    elseif ($notif->type == 'warning') { $bg_color = 'bg-yellow-50'; $icon_color = 'text-yellow-500'; }
                    elseif ($notif->type == 'danger') { $bg_color = 'bg-red-50'; $icon_color = 'text-red-500'; }
                    elseif ($notif->type == 'info') { $bg_color = 'bg-blue-50'; $icon_color = 'text-blue-500'; }
                ?>
                <a href="<?= $notif->link ? site_url($notif->link) : '#' ?>" class="block p-5 hover:bg-gray-50/50 transition-colors <?= !$notif->is_read ? 'bg-blue-50/30' : '' ?>">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 <?= $bg_color ?> <?= $icon_color ?>">
                            <i class="<?= htmlspecialchars($notif->icon ?? 'fa-solid fa-bell') ?>"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="text-sm font-bold <?= !$notif->is_read ? 'text-blue-700' : 'text-edrive-text' ?>">
                                    <?= htmlspecialchars($notif->title) ?>
                                </h4>
                                <span class="text-xs text-edrive-muted whitespace-nowrap"><?= timeAgo($notif->created_at) ?></span>
                            </div>
                            <p class="text-sm mt-1 <?= !$notif->is_read ? 'text-gray-700 font-medium' : 'text-edrive-muted' ?>">
                                <?= htmlspecialchars($notif->message) ?>
                            </p>
                        </div>
                        <?php if (!$notif->is_read): ?>
                        <div class="w-2 h-2 bg-blue-600 rounded-full mt-1.5 flex-shrink-0"></div>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
