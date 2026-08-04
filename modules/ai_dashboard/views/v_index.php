<?php require_once APPPATH . 'views/layout/header.php'; ?>

<!-- ECharts -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-edrive-text">📊 AI Analytics & Overview</h1>
            <p class="text-edrive-muted mt-1">Monitoring statistik penggunaan AI, total token, dan feedback user.</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= site_url('ai_document') ?>" class="btn-secondary">
                <i class="fa-solid fa-folder-open"></i> Knowledge Base
            </a>
            <a href="<?= site_url('ai_setting') ?>" class="btn-primary">
                <i class="fa-solid fa-gear"></i> Settings
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-edrive-muted mb-1">Total Conversations</p>
                    <h3 class="text-2xl font-bold text-edrive-text"><?= number_format($total_conversations) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-600 flex items-center justify-center border border-violet-100">
                    <i class="fa-solid fa-comments text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-edrive-muted mb-1">Total Messages</p>
                    <h3 class="text-2xl font-bold text-edrive-text"><?= number_format($total_messages) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                    <i class="fa-solid fa-paper-plane text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-edrive-muted mb-1">Est. Tokens Consumed</p>
                    <h3 class="text-2xl font-bold text-edrive-text"><?= number_format($total_tokens) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                    <i class="fa-solid fa-bolt text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-edrive-muted mb-1">RAG Knowledge Docs</p>
                    <h3 class="text-2xl font-bold text-edrive-text"><?= number_format($total_kb_docs) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                    <i class="fa-solid fa-file-shield text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Usage Chart -->
        <div class="glass-card p-6 lg:col-span-2 space-y-4">
            <h3 class="text-base font-bold text-edrive-text">Tren Penggunaan Token harian</h3>
            <div id="usageChart" class="w-full h-72"></div>
        </div>

        <!-- Feedback Summary -->
        <div class="glass-card p-6 space-y-4">
            <h3 class="text-base font-bold text-edrive-text">Kepuasan Jawaban AI</h3>
            <div id="feedbackChart" class="w-full h-72 flex items-center justify-center"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Token Usage Chart
    const usageData = <?= json_encode($chart_data) ?>;
    if (document.getElementById('usageChart') && usageData.length > 0) {
        const chart = echarts.init(document.getElementById('usageChart'));
        const dates = usageData.map(item => item.date);
        const tokens = usageData.map(item => item.tokens);

        chart.setOption({
            tooltip: { trigger: 'axis' },
            xAxis: { type: 'category', data: dates },
            yAxis: { type: 'value', name: 'Tokens' },
            series: [{
                data: tokens,
                type: 'line',
                smooth: true,
                areaStyle: { opacity: 0.1 },
                itemStyle: { color: '#8B5CF6' }
            }]
        });
    } else {
        document.getElementById('usageChart').innerHTML = '<div class="h-full flex items-center justify-center text-slate-400 text-xs">Belum ada data penggunaan harian.</div>';
    }

    // 2. Feedback Donut Chart
    const up = <?= (int)($feedback_stats->thumbs_up ?? 0) ?>;
    const down = <?= (int)($feedback_stats->thumbs_down ?? 0) ?>;
    
    if (document.getElementById('feedbackChart')) {
        if (up > 0 || down > 0) {
            const fbChart = echarts.init(document.getElementById('feedbackChart'));
            fbChart.setOption({
                tooltip: { trigger: 'item' },
                series: [{
                    type: 'pie',
                    radius: ['40%', '70%'],
                    data: [
                        { value: up, name: '👍 Thumbs Up', itemStyle: { color: '#10B981' } },
                        { value: down, name: '👎 Thumbs Down', itemStyle: { color: '#EF4444' } }
                    ]
                }]
            });
        } else {
            document.getElementById('feedbackChart').innerHTML = '<div class="h-full flex items-center justify-center text-slate-400 text-xs">Belum ada feedback jawaban tercatat.</div>';
        }
    }
});
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
