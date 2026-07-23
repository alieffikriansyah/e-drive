<?php require_once APPPATH . 'views/layout/header.php'; ?>

<!-- ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>

<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-edrive-text">Halo, <?= htmlspecialchars(Session::get('name')) ?> 👋</h1>
            <p class="text-edrive-muted mt-1">Berikut adalah ringkasan aktivitas dan penyimpanan Anda hari ini.</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= site_url('drive') ?>" class="btn-primary">
                <i class="fa-solid fa-cloud-arrow-up"></i> Upload Baru
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Storage -->
        <div class="stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-edrive-muted mb-1">Total Penyimpanan</p>
                    <h3 class="text-2xl font-bold text-edrive-text"><?= format_file_size($total_size) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                    <i class="fa-solid fa-database text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Documents -->
        <div class="stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-edrive-muted mb-1">Total Dokumen</p>
                    <h3 class="text-2xl font-bold text-edrive-text"><?= number_format($total_docs) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100">
                    <i class="fa-solid fa-file-lines text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Drives -->
        <div class="stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-edrive-muted mb-1">Drive Diakses</p>
                    <h3 class="text-2xl font-bold text-edrive-text"><?= number_format($total_drives) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600 shadow-sm border border-purple-100">
                    <i class="fa-solid fa-hard-drive text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Users (Admin only) -->
        <?php if ($is_admin): ?>
        <div class="stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-edrive-muted mb-1">Total Pengguna</p>
                    <h3 class="text-2xl font-bold text-edrive-text"><?= number_format($total_users) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600 shadow-sm border border-orange-100">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-edrive-muted mb-1">Role Anda</p>
                    <h3 class="text-lg font-bold text-edrive-text leading-tight mt-1"><?= htmlspecialchars(Session::get('role_name')) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-600 shadow-sm border border-gray-200">
                    <i class="fa-solid fa-id-badge text-xl"></i>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Col (Charts & Recent) -->
        <div class="<?= $is_admin ? 'lg:col-span-2' : 'lg:col-span-3' ?> space-y-6">
            <!-- Chart Card -->
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-edrive-text">Penyimpanan per Drive</h3>
                    <button class="btn-icon"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                </div>
                <div id="storageChart" class="w-full h-[300px]"></div>
            </div>

            <!-- Recent Documents -->
            <div class="glass-card overflow-hidden">
                <div class="p-5 border-b border-edrive-border flex items-center justify-between bg-white">
                    <h3 class="text-lg font-bold text-edrive-text">Dokumen Terbaru</h3>
                    <a href="<?= site_url('recent') ?>" class="text-sm text-edrive-accent hover:underline font-medium">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-light">
                        <thead>
                            <tr>
                                <th>Nama File</th>
                                <th>Lokasi</th>
                                <th>Ukuran</th>
                                <th>Diunggah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($recent_docs)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-8 text-edrive-muted">Belum ada dokumen yang diunggah</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach($recent_docs as $doc): ?>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <i class="<?= get_file_icon($doc->file_type) ?> text-xl w-6 text-center"></i>
                                            <div>
                                                <p class="font-medium text-edrive-text line-clamp-1" title="<?= htmlspecialchars($doc->name) ?>">
                                                    <?= htmlspecialchars($doc->name) ?>
                                                </p>
                                                <p class="text-xs text-edrive-muted"><?= htmlspecialchars($doc->uploader_name) ?></p>
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
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($is_admin): ?>
        <!-- Right Col (Activity Log) -->
        <div class="lg:col-span-1">
            <div class="glass-card p-0 h-full flex flex-col">
                <div class="p-5 border-b border-edrive-border flex items-center justify-between bg-white rounded-t-2xl">
                    <h3 class="text-lg font-bold text-edrive-text">Aktivitas Terkini</h3>
                    <a href="<?= site_url('activity_log') ?>" class="text-sm text-edrive-accent hover:underline font-medium">Log</a>
                </div>
                <div class="p-5 flex-1 bg-gray-50/30 rounded-b-2xl">
                    <?php if(empty($activities)): ?>
                        <div class="text-center py-8 text-edrive-muted">Belum ada aktivitas tercatat</div>
                    <?php else: ?>
                        <div class="relative border-l border-edrive-border ml-3 space-y-6 pb-4">
                            <?php foreach($activities as $act): ?>
                            <?php 
                                $icon = 'fa-circle text-gray-400';
                                $bg = 'bg-white';
                                $border = 'border-gray-200';
                                if($act->action == 'UPLOAD' || $act->action == 'CREATE') { $icon = 'fa-arrow-up text-emerald-500'; $bg = 'bg-emerald-50'; $border = 'border-emerald-200'; }
                                elseif($act->action == 'DOWNLOAD') { $icon = 'fa-arrow-down text-blue-500'; $bg = 'bg-blue-50'; $border = 'border-blue-200'; }
                                elseif($act->action == 'DELETE') { $icon = 'fa-trash text-red-500'; $bg = 'bg-red-50'; $border = 'border-red-200'; }
                                elseif($act->action == 'LOGIN') { $icon = 'fa-right-to-bracket text-purple-500'; $bg = 'bg-purple-50'; $border = 'border-purple-200'; }
                            ?>
                            <div class="relative pl-6">
                                <span class="absolute -left-[17px] top-1 w-8 h-8 rounded-full border shadow-sm flex items-center justify-center <?= $bg ?> <?= $border ?>">
                                    <i class="fa-solid <?= $icon ?> text-xs"></i>
                                </span>
                                <div class="bg-white border border-edrive-border p-3 rounded-xl shadow-sm">
                                    <p class="text-sm text-edrive-text">
                                        <span class="font-semibold"><?= htmlspecialchars($act->user_name) ?></span>
                                        <?= htmlspecialchars($act->description) ?>
                                    </p>
                                    <p class="text-xs text-edrive-muted mt-1.5 flex items-center gap-1">
                                        <i class="fa-regular fa-clock"></i> <?= timeAgo($act->created_at) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare Data for ECharts
    const chartDataRaw = <?= json_encode($chart_data) ?>;
    
    if (chartDataRaw.length > 0 && document.getElementById('storageChart')) {
        const chartDom = document.getElementById('storageChart');
        const myChart = echarts.init(chartDom);
        
        let categories = [];
        let dataSeries = [];
        
        chartDataRaw.forEach(item => {
            categories.push(item.name);
            // Convert to MB for chart display
            let mb = (item.total_size / (1024 * 1024)).toFixed(2);
            dataSeries.push({
                value: mb,
                name: item.name,
                itemStyle: { borderRadius: [4, 4, 0, 0] }
            });
        });

        const option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'shadow' },
                formatter: function (params) {
                    let val = params[0];
                    return `<div class="font-sans">
                                <strong>${val.name}</strong><br/>
                                <span class="text-xs text-gray-500">Penyimpanan: </span>
                                <span class="font-bold">${val.value} MB</span>
                            </div>`;
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                top: '10%',
                containLabel: true
            },
            xAxis: [
                {
                    type: 'category',
                    data: categories,
                    axisTick: { alignWithLabel: true },
                    axisLine: { lineStyle: { color: '#E2E8F0' } },
                    axisLabel: { color: '#64748B', fontFamily: 'Inter', fontSize: 11, width: 80, overflow: 'truncate' }
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    name: 'Ukuran (MB)',
                    nameTextStyle: { color: '#94A3B8', fontSize: 11, padding: [0, 0, 0, 20] },
                    splitLine: { lineStyle: { color: '#F1F5F9', type: 'dashed' } },
                    axisLabel: { color: '#64748B', fontFamily: 'Inter', fontSize: 11 }
                }
            ],
            series: [
                {
                    name: 'Storage',
                    type: 'bar',
                    barWidth: '40%',
                    data: dataSeries,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#3B82F6' },
                            { offset: 1, color: '#1D4ED8' }
                        ])
                    }
                }
            ]
        };

        myChart.setOption(option);
        
        // Responsive chart
        window.addEventListener('resize', function() {
            myChart.resize();
        });
        
        // Hook into sidebar toggle to resize chart
        const originalToggle = window.toggleSidebar;
        window.toggleSidebar = function() {
            if(originalToggle) originalToggle();
            setTimeout(() => { myChart.resize(); }, 350);
        };
    } else {
        document.getElementById('storageChart').innerHTML = '<div class="h-full flex items-center justify-center text-edrive-muted">Data penyimpanan belum tersedia</div>';
    }
});
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>