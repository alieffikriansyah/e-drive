<?php $this->load->view('layout/header', ['title' => $title ?? 'Dashboard']); ?>

<?php
$is_admin_dash = in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin']);
?>
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-mapul-green-md drop-shadow-sm uppercase tracking-wider flex items-center gap-2">
            <i class="fa fa-chart-line text-mapul-yellow"></i> Dashboard
        </h1>
        <p class="text-gray-500 font-medium mt-1">Ringkasan aktivitas dan performa warung hari ini.</p>
    </div>
    <div class="flex flex-col md:flex-row items-end gap-3">
        <?php if ($is_admin_dash): ?>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Filter Cabang</label>
            <select id="dash_filter_cabang" onchange="loadDashboardData()" class="mapul-input py-1.5">
                <option value="0">Semua Cabang</option>
                <?php foreach ($cabang as $cb): ?>
                    <option value="<?= $cb->id ?>"><?= htmlspecialchars($cb->nama_cabang) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="mt-4 md:mt-0 text-right bg-white px-4 py-2 rounded-lg border border-mapul-silver shadow-sm">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">Tanggal Hari Ini</div>
            <div class="text-mapul-green font-bold text-lg"><?= date('d F Y') ?></div>
        </div>
    </div>
</div>

<!-- Loading Overlay for Initial Fetch -->
<div id="dashboard-loading" class="flex flex-col items-center justify-center py-20">
    <i class="fa fa-circle-notch fa-spin text-4xl text-mapul-green mb-4"></i>
    <p class="text-gray-500 font-bold">Memuat Data Dashboard...</p>
</div>

<!-- ================= SUMMARY CARDS ================= -->
<div id="dashboard-content" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- Omzet Hari Ini -->
        <div class="bg-gradient-to-br from-mapul-green via-mapul-green-md to-mapul-green rounded-xl shadow-lg border border-green-700 p-6 relative overflow-hidden transform hover:-translate-y-1 transition-all duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-10">
                <i class="fa fa-wallet text-9xl text-white"></i>
            </div>
            <div class="relative z-10">
                <h3 class="text-green-100 font-semibold text-sm uppercase tracking-wider mb-2">Omzet Hari Ini</h3>
                <div class="text-3xl font-bold text-white mb-1" id="dash_omzet_hari">Rp 0</div>
                <div class="text-green-200 text-xs font-medium" id="dash_omzet_bulan">
                    <i class="fa fa-calendar-alt mr-1"></i> Bulan ini: Rp 0
                </div>
            </div>
        </div>

        <!-- Laba Bersih Hari Ini -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border-l-4 border-l-mapul-yellow border border-gray-200 p-6 relative overflow-hidden transform hover:-translate-y-1 transition-all duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-5">
                <i class="fa fa-coins text-9xl text-black"></i>
            </div>
            <div class="relative z-10">
                <h3 class="text-gray-500 font-semibold text-sm uppercase tracking-wider mb-2">Laba Bersih Hari Ini</h3>
                <div class="text-3xl font-bold mb-1 text-gray-800" id="dash_laba_hari">Rp 0</div>
                <div class="text-gray-400 text-xs font-medium">
                    <i class="fa fa-info-circle mr-1"></i> (Omzet - Operasional)
                </div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border-l-4 border-l-blue-500 border border-gray-200 p-6 relative overflow-hidden transform hover:-translate-y-1 transition-all duration-300">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 opacity-5">
                <i class="fa fa-shopping-cart text-9xl text-black"></i>
            </div>
            <div class="relative z-10">
                <h3 class="text-gray-500 font-semibold text-sm uppercase tracking-wider mb-2">Total Transaksi (Hari Ini)</h3>
                <div class="text-3xl font-bold text-richblack mb-1" id="dash_total_transaksi">0</div>
                <div class="text-gray-400 text-xs font-medium">
                    <i class="fa fa-receipt mr-1"></i> Nota Berhasil
                </div>
            </div>
        </div>

    </div>

    <!-- ================= 1 COLUMN LAYOUT ================= -->
    <div class="grid grid-cols-1 gap-8 mb-6">
        
        <!-- TRANSAKSI TERAKHIR -->
        <div class="mapul-card">
            <div class="mapul-modal-header bg-white border-b-2 border-gray-100">
                <h3 class="font-bold text-mapul-green uppercase tracking-wider flex items-center gap-2">
                    <i class="fa fa-history text-mapul-yellow"></i> Transaksi Terakhir
                </h3>
                <a href="<?= site_url('list_penjualan') ?>" class="text-xs font-bold text-gray-500 hover:text-mapul-green transition-colors"><?php if ($is_admin_dash): ?>Lihat Semua <i class="fa fa-arrow-right"></i><?php else: ?><span class="text-transparent">-</span><?php endif; ?></a>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider border-b border-gray-200">
                            <th class="px-4 py-3">Nota</th>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Cabang</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="dash_table_transaksi">
                        <!-- Injected by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
});

function formatNumber(num) {
    return parseFloat(num).toLocaleString('id-ID');
}

function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe.toString()
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

function loadDashboardData() {
    const cabangEl  = document.getElementById('dash_filter_cabang');
    const id_cabang = cabangEl ? cabangEl.value : 0;
    fetch('<?= base_url('dashboard/get_data') ?>?id_cabang=' + id_cabang)
        .then(response => response.json())
        .then(res => {
            if (res.status) {
                const data = res.data;
                
                // Hide loading, show content
                document.getElementById('dashboard-loading').classList.add('hidden');
                document.getElementById('dashboard-content').classList.remove('hidden');

                // Update Cards
                document.getElementById('dash_omzet_hari').innerText = `Rp ${formatNumber(data.omzet_hari)}`;
                document.getElementById('dash_omzet_bulan').innerHTML = `<i class="fa fa-calendar-alt mr-1"></i> Bulan ini: Rp ${formatNumber(data.omzet_bulan)}`;
                
                const labaEl = document.getElementById('dash_laba_hari');
                labaEl.innerText = `Rp ${formatNumber(data.laba_bersih_hari)}`;
                if (data.laba_bersih_hari >= 0) {
                    labaEl.classList.add('text-mapul-green-md');
                    labaEl.classList.remove('text-red-600', 'text-gray-800');
                } else {
                    labaEl.classList.add('text-red-600');
                    labaEl.classList.remove('text-mapul-green-md', 'text-gray-800');
                }

                document.getElementById('dash_total_transaksi').innerText = formatNumber(data.total_transaksi);

                // Update Transaksi Terakhir Table
                const tTx = document.getElementById('dash_table_transaksi');
                if (!data.transaksi_terakhir || data.transaksi_terakhir.length === 0) {
                    tTx.innerHTML = `<tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 font-medium">Belum ada transaksi hari ini</td></tr>`;
                } else {
                    let htmlTx = '';
                    data.transaksi_terakhir.forEach(t => {
                        const date = new Date(t.tanggal_transaksi);
                        const timeStr = String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
                        htmlTx += `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-semibold text-mapul-green-md">${escapeHtml(t.no_nota)}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">${timeStr}</td>
                                <td class="px-4 py-3 text-gray-600">${escapeHtml(t.nama_cabang)}</td>
                                <td class="px-4 py-3 text-right font-bold">Rp ${formatNumber(t.total_bayar)}</td>
                            </tr>
                        `;
                    });
                    tTx.innerHTML = htmlTx;
                }

                // Stok menipis dinonaktifkan
            } else {
                console.error("Dashboard error:", res.message);
                document.getElementById('dashboard-loading').innerHTML = `<i class="fa fa-times text-red-500 text-4xl mb-4"></i><p class="text-red-500">Gagal memuat data.</p>`;
            }
        })
        .catch(err => {
            console.error("Fetch error:", err);
            document.getElementById('dashboard-loading').innerHTML = `<i class="fa fa-times text-red-500 text-4xl mb-4"></i><p class="text-red-500">Gagal terhubung ke server.</p>`;
        });
}
</script>

<?php $this->load->view('layout/footer'); ?>
