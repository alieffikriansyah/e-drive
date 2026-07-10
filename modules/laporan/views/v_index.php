<?php $this->load->view('layout/header', ['title' => 'Laporan Laba Rugi']); ?>

<!-- Load SweetAlert2 -->
<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<div class="flex flex-col md:flex-row justify-between items-center mb-6 border-l-4 border-mapul-yellow pl-4">
    <div>
        <h1 class="text-3xl font-extrabold tracking-wide text-mapul-green uppercase">Laporan & Analitik</h1>
        <p class="text-gray-500 font-medium mt-1">Ringkasan Laba Rugi dan Kinerja Cabang</p>
    </div>
</div>

<!-- ================= TABS ================= -->
<div class="mb-4 border-b border-gray-200">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="laporanTabs">
        <li class="mr-2">
            <button class="inline-block p-4 border-b-2 rounded-t-lg transition-colors border-mapul-green text-mapul-green-md font-bold" 
                    id="tab-bulanan" type="button" onclick="switchTab('bulanan')">
                <i class="fa fa-chart-line mr-1"></i> Laba-Rugi Bulanan
            </button>
        </li>
        <li class="mr-2">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg transition-colors hover:text-gray-600 hover:border-gray-300 text-gray-500 font-bold" 
                    id="tab-harian" type="button" onclick="switchTab('harian')">
                <i class="fa fa-calendar-alt mr-1"></i> Detail Harian
            </button>
        </li>
        <li class="mr-2">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg transition-colors hover:text-gray-600 hover:border-gray-300 text-gray-500 font-bold" 
                    id="tab-produk" type="button" onclick="switchTab('produk')">
                <i class="fa fa-fire mr-1"></i> Produk Terlaris
            </button>
        </li>
    </ul>
</div>

<!-- ================= TAB: LABA RUGI BULANAN ================= -->
<div id="content-bulanan" class="transition-opacity duration-300">
    <div class="mapul-card overflow-hidden mb-6">
        <div class="bg-gray-50 border-b border-gray-200 p-4 flex flex-wrap gap-4 items-end">
            <?php if (in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin'])): ?>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Filter Cabang</label>
                <select id="bln_id_cabang" class="mapul-input py-1.5" onchange="loadBulanan()">
                    <option value="0">Semua Cabang (Global)</option>
                    <?php foreach($cabang as $c): ?>
                        <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nama_cabang) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Bulan</label>
                <select id="bln_bulan" class="mapul-input py-1.5 w-32" onchange="loadBulanan()">
                    <?php 
                    $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    foreach($months as $m => $name): 
                    ?>
                        <option value="<?= $m ?>" <?= date('n') == $m ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tahun</label>
                <input type="number" id="bln_tahun" class="mapul-input py-1.5 w-24" value="<?= date('Y') ?>" onchange="loadBulanan()">
            </div>
            
            <div class="ml-auto flex gap-2">
                <button onclick="loadBulanan()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1.5 px-4 rounded border border-gray-300 transition-colors">
                    <i class="fa fa-sync mr-1"></i> Load
                </button>
                <button onclick="exportBulanan()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1.5 px-4 rounded border border-green-800 transition-colors shadow-sm">
                    <i class="fa fa-file-excel mr-1"></i> Export Excel
                </button>
            </div>
        </div>
        
        <div class="p-4 text-center border-b border-gray-100 bg-white">
            <h2 class="text-xl font-bold text-mapul-green" id="bln_title_periode">Periode</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="mapul-thead">
                    <tr>
                        <th class="rounded-tl-lg">Cabang</th>
                        <th class="text-center">Jml Transaksi</th>
                        <th class="text-right text-blue-100">Total Pendapatan</th>
                        <th class="text-right text-red-200">Biaya Operasional</th>
                        <th class="text-right rounded-tr-lg font-bold text-yellow-300 text-lg">Laba Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm" id="tableBodyBulanan">
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= TAB: LABA RUGI HARIAN ================= -->
<div id="content-harian" class="hidden transition-opacity duration-300">
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4" id="harianRingkasanCards">
        <!-- Injected via JS -->
    </div>

    <div class="mapul-card overflow-hidden mb-6">
        <div class="bg-gray-50 border-b border-gray-200 p-4 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Pilih Cabang</label>
                <select id="hr_id_cabang" class="mapul-input py-1.5 border-yellow-300" onchange="loadHarian()">
                    <?php if (in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin'])): ?>
                        <option value="0">-- Pilih Cabang --</option>
                    <?php endif; ?>
                    <?php foreach($cabang as $c): ?>
                        <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nama_cabang) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Bulan</label>
                <select id="hr_bulan" class="mapul-input py-1.5 w-32" onchange="loadHarian()">
                    <?php foreach($months as $m => $name): ?>
                        <option value="<?= $m ?>" <?= date('n') == $m ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tahun</label>
                <input type="number" id="hr_tahun" class="mapul-input py-1.5 w-24" value="<?= date('Y') ?>" onchange="loadHarian()">
            </div>
            
            <div class="ml-auto flex gap-2">
                <button onclick="loadHarian()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1.5 px-4 rounded border border-gray-300 transition-colors">
                    <i class="fa fa-sync mr-1"></i> Load
                </button>
                <button onclick="exportHarian()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1.5 px-4 rounded border border-green-800 transition-colors shadow-sm">
                    <i class="fa fa-file-excel mr-1"></i> Export Excel
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="mapul-thead">
                    <tr>
                        <th class="rounded-tl-lg">Tanggal</th>
                        <th class="text-center">Transaksi</th>
                        <th class="text-right">Total Pendapatan</th>
                        <th class="text-right">Operasional</th>
                        <th class="text-right rounded-tr-lg text-yellow-300">Laba Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm" id="tableBodyHarian">
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Pilih cabang untuk melihat laporan harian</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= TAB: PRODUK TERLARIS ================= -->
<div id="content-produk" class="hidden transition-opacity duration-300">
    <div class="mapul-card overflow-hidden mb-6">
        <div class="bg-gray-50 border-b border-gray-200 p-4 flex flex-wrap gap-4 items-end">
            <?php if (in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin'])): ?>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Filter Cabang</label>
                <select id="prod_id_cabang" class="mapul-input py-1.5" onchange="loadProduk()">
                    <option value="0">Semua Cabang (Global)</option>
                    <?php foreach($cabang as $c): ?>
                        <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nama_cabang) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                <input type="date" id="prod_tgl_awal" class="mapul-input py-1.5" value="<?= date('Y-m-01') ?>" onchange="loadProduk()">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" id="prod_tgl_akhir" class="mapul-input py-1.5" value="<?= date('Y-m-d') ?>" onchange="loadProduk()">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tampilkan Top</label>
                <select id="prod_limit" class="mapul-input py-1.5 w-24" onchange="loadProduk()">
                    <option value="10">Top 10</option>
                    <option value="20">Top 20</option>
                    <option value="50">Top 50</option>
                </select>
            </div>
            
            <div class="ml-auto flex gap-2">
                <button onclick="loadProduk()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1.5 px-4 rounded border border-gray-300 transition-colors">
                    <i class="fa fa-sync mr-1"></i> Load
                </button>
                <button onclick="exportProduk()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1.5 px-4 rounded border border-green-800 transition-colors shadow-sm">
                    <i class="fa fa-file-excel mr-1"></i> Export Excel
                </button>
            </div>
        </div>

        <div class="overflow-x-auto p-4">
            <div class="grid grid-cols-1 gap-2" id="listProdukTerlaris">
                <div class="text-center py-4 text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</div>
            </div>
        </div>
    </div>
</div>

<script>
    const baseUrl = '<?= base_url() ?>';
    
    // Tab Switching Logic
    function switchTab(tab) {
        const tabs = ['bulanan', 'harian', 'produk'];
        tabs.forEach(t => {
            const btn = document.getElementById(`tab-${t}`);
            const content = document.getElementById(`content-${t}`);
            if (t === tab) {
                btn.className = 'inline-block p-4 border-b-2 rounded-t-lg transition-colors border-mapul-green text-mapul-green-md font-bold';
                content.classList.remove('hidden');
                
                // Lazy load
                if(t === 'bulanan') loadBulanan();
                if(t === 'harian') loadHarian();
                if(t === 'produk') loadProduk();
            } else {
                btn.className = 'inline-block p-4 border-b-2 border-transparent rounded-t-lg transition-colors hover:text-gray-600 hover:border-gray-300 text-gray-500 font-bold';
                content.classList.add('hidden');
            }
        });
    }

    // Formatters
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
    
    function formatNumber(num) {
        return parseFloat(num).toLocaleString('id-ID');
    }
    
    function getTextColor(num) {
        return num < 0 ? 'text-red-500' : (num > 0 ? 'text-mapul-green-md' : 'text-gray-500');
    }

    // ================= TAB 1: BULANAN =================
    function loadBulanan() {
        const tbody = document.getElementById('tableBodyBulanan');
        tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>`;
        
        let params = new URLSearchParams({
            tahun: document.getElementById('bln_tahun').value,
            bulan: document.getElementById('bln_bulan').value
        });
        
        const c = document.getElementById('bln_id_cabang');
        if(c) params.append('id_cabang', c.value);

        fetch(`${baseUrl}laporan/laba_rugi_bulanan?${params.toString()}`)
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    document.getElementById('bln_title_periode').innerHTML = `<i class="fa fa-calendar-check mr-2"></i> Laporan Laba Rugi — ${res.periode}`;
                    
                    let html = '';
                    if (res.data.length === 0) {
                        html = `<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data cabang ditemukan</td></tr>`;
                    } else {
                        // Variables for Grand Total
                        let gTrans = 0, gPend = 0, gOps = 0, gBersih = 0;
                        
                        res.data.forEach(d => {
                            gTrans += parseFloat(d.total_transaksi);
                            gPend += parseFloat(d.total_pendapatan);
                            gOps += parseFloat(d.total_biaya_operasional);
                            gBersih += parseFloat(d.laba_bersih);

                            html += `
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-800">${escapeHtml(d.nama_cabang)}</td>
                                    <td class="px-6 py-4 text-center font-semibold text-gray-600">${formatNumber(d.total_transaksi)}</td>
                                    <td class="px-6 py-4 text-right text-blue-600 font-semibold">Rp ${formatNumber(d.total_pendapatan)}</td>
                                    <td class="px-6 py-4 text-right text-gray-500">Rp ${formatNumber(d.total_biaya_operasional)}</td>
                                    <td class="px-6 py-4 text-right font-black ${getTextColor(d.laba_bersih)} text-lg">Rp ${formatNumber(d.laba_bersih)}</td>
                                </tr>
                            `;
                        });
                        
                        // Grand Total Row
                        html += `
                            <tr class="bg-gray-100 border-t-2 border-gray-300 font-bold">
                                <td class="px-6 py-4 text-gray-800 uppercase text-center" colspan="1">Total Keseluruhan</td>
                                <td class="px-6 py-4 text-center text-gray-800">${formatNumber(gTrans)}</td>
                                <td class="px-6 py-4 text-right text-blue-700">Rp ${formatNumber(gPend)}</td>
                                <td class="px-6 py-4 text-right text-gray-700">Rp ${formatNumber(gOps)}</td>
                                <td class="px-6 py-4 text-right font-black text-xl ${getTextColor(gBersih)}">Rp ${formatNumber(gBersih)}</td>
                            </tr>
                        `;
                    }
                    tbody.innerHTML = html;
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Kesalahan jaringan</td></tr>`;
            });
    }
    
    function exportBulanan() {
        let params = new URLSearchParams({
            tahun: document.getElementById('bln_tahun').value,
            bulan: document.getElementById('bln_bulan').value
        });
        const c = document.getElementById('bln_id_cabang');
        if(c) params.append('id_cabang', c.value);
        
        window.location.href = `${baseUrl}laporan/export_excel_bulanan?${params.toString()}`;
    }

    // ================= TAB 2: HARIAN =================
    function loadHarian() {
        const tbody = document.getElementById('tableBodyHarian');
        const cards = document.getElementById('harianRingkasanCards');
        const c = document.getElementById('hr_id_cabang');
        
        if(!c || c.value == '0') {
            tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Silakan pilih cabang terlebih dahulu</td></tr>`;
            cards.innerHTML = '';
            return;
        }
        
        tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>`;
        
        let params = new URLSearchParams({
            tahun: document.getElementById('hr_tahun').value,
            bulan: document.getElementById('hr_bulan').value,
            id_cabang: c.value
        });

        fetch(`${baseUrl}laporan/laba_rugi_harian?${params.toString()}`)
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    // Update Summary Cards
                    const sum = res.ringkasan;
                    cards.innerHTML = `
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 md:col-span-2">
                            <div class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Total Pendapatan</div>
                            <div class="text-2xl font-black text-blue-700">Rp ${formatNumber(sum.total_pendapatan)}</div>
                            <div class="text-xs text-blue-400 mt-1">${formatNumber(sum.total_transaksi)} Transaksi</div>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                            <div class="text-xs font-bold text-red-500 uppercase tracking-widest mb-1">Total Biaya Operasional</div>
                            <div class="text-2xl font-black text-red-700">Rp ${formatNumber(sum.total_biaya_operasional)}</div>
                            <div class="text-xs text-red-400 mt-1">Pengeluaran Lainnya</div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 shadow-sm relative overflow-hidden">
                            <div class="absolute -right-4 -bottom-4 text-green-200 opacity-50"><i class="fa fa-money-bill-wave text-7xl"></i></div>
                            <div class="relative z-10">
                                <div class="text-xs font-bold text-green-600 uppercase tracking-widest mb-1">LABA BERSIH TOTAL</div>
                                <div class="text-3xl font-black ${sum.laba_bersih >= 0 ? 'text-green-700' : 'text-red-600'}">Rp ${formatNumber(sum.laba_bersih)}</div>
                            </div>
                        </div>
                    `;

                    // Update Table
                    let html = '';
                    res.data.forEach(d => {
                        const isZero = (parseFloat(d.total_transaksi) === 0 && parseFloat(d.total_biaya_operasional) === 0);
                        const rowCls = isZero ? 'bg-gray-50/50 text-gray-400' : 'hover:bg-gray-50 text-gray-800';
                        
                        // Date Format (Y-m-d to d M)
                        const dt = new Date(d.tanggal);
                        const strDate = dt.toLocaleDateString('id-ID', {day: 'numeric', month:'short'});

                        html += `
                            <tr class="${rowCls} transition-colors">
                                <td class="px-6 py-3 font-semibold ${isZero?'text-gray-400':'text-gray-800'}">${strDate}</td>
                                <td class="px-6 py-3 text-center">${formatNumber(d.total_transaksi)}</td>
                                <td class="px-6 py-3 text-right">Rp ${formatNumber(d.total_pendapatan)}</td>
                                <td class="px-6 py-3 text-right">Rp ${formatNumber(d.total_biaya_operasional)}</td>
                                <td class="px-6 py-3 text-right font-black ${isZero ? '' : getTextColor(d.laba_bersih)} text-[15px]">Rp ${formatNumber(d.laba_bersih)}</td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Kesalahan jaringan</td></tr>`;
            });
    }

    function exportHarian() {
        const c = document.getElementById('hr_id_cabang');
        if(!c || c.value == '0') {
            Swal.fire('Info', 'Pilih cabang terlebih dahulu', 'warning');
            return;
        }
        let params = new URLSearchParams({
            tahun: document.getElementById('hr_tahun').value,
            bulan: document.getElementById('hr_bulan').value,
            id_cabang: c.value
        });
        window.location.href = `${baseUrl}laporan/export_excel_harian?${params.toString()}`;
    }

    // ================= TAB 3: PRODUK TERLARIS =================
    function loadProduk() {
        const list = document.getElementById('listProdukTerlaris');
        list.innerHTML = `<div class="text-center py-4 text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</div>`;
        
        let params = new URLSearchParams({
            tgl_awal: document.getElementById('prod_tgl_awal').value,
            tgl_akhir: document.getElementById('prod_tgl_akhir').value,
            limit: document.getElementById('prod_limit').value
        });
        
        const c = document.getElementById('prod_id_cabang');
        if(c) params.append('id_cabang', c.value);

        fetch(`${baseUrl}laporan/produk_terlaris?${params.toString()}`)
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    let html = '';
                    if (res.data.length === 0) {
                        html = `<div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">Tidak ada penjualan pada periode ini.</div>`;
                    } else {
                        res.data.forEach((d, idx) => {
                            const rank = idx + 1;
                            let rankBadge = `<div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-black text-sm">#${rank}</div>`;
                            
                            if (rank === 1) rankBadge = `<div class="w-10 h-10 rounded-full bg-yellow-400 text-yellow-900 flex items-center justify-center font-black text-lg shadow-md border-2 border-yellow-200 shadow-yellow-200/50 transform -rotate-12"><i class="fa fa-crown text-xs absolute -top-2"></i> ${rank}</div>`;
                            else if (rank === 2) rankBadge = `<div class="w-9 h-9 rounded-full bg-gray-300 text-gray-800 flex items-center justify-center font-black text-base shadow"><i class="fa fa-medal text-xs absolute -top-1"></i> ${rank}</div>`;
                            else if (rank === 3) rankBadge = `<div class="w-8 h-8 rounded-full bg-[#CD7F32] text-white flex items-center justify-center font-black text-base shadow"><i class="fa fa-medal text-xs absolute -top-1"></i> ${rank}</div>`;

                            const labaStr = d.kontribusi_laba >= 0 ? `+ Rp ${formatNumber(d.kontribusi_laba)}` : `- Rp ${formatNumber(Math.abs(d.kontribusi_laba))}`;
                            const labaCls = d.kontribusi_laba >= 0 ? 'text-green-600' : 'text-red-500';

                            html += `
                                <div class="bg-white border ${rank <= 3 ? 'border-yellow-200 bg-gradient-to-r from-yellow-50/50 to-white' : 'border-gray-200'} rounded-xl p-3 flex items-center justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
                                    ${rank === 1 ? '<div class="absolute right-0 top-0 w-24 h-24 bg-yellow-400 opacity-[0.05] rounded-bl-full group-hover:scale-150 transition-transform duration-500"></div>' : ''}
                                    <div class="flex items-center gap-4 z-10 w-1/3">
                                        <div class="w-10 flex justify-center">${rankBadge}</div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-lg leading-tight group-hover:text-mapul-green transition-colors">${escapeHtml(d.nama_produk)}</h4>
                                        </div>
                                    </div>
                                    <div class="z-10 w-1/3 text-center border-l border-r border-gray-100 px-4">
                                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Terjual</div>
                                        <div class="text-2xl font-black text-mapul-green-md">${formatNumber(d.total_terjual)} <span class="text-sm font-semibold text-gray-500">${escapeHtml(d.nama_satuan)}</span></div>
                                    </div>
                                    <div class="z-10 w-1/3 text-right pl-4">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs text-gray-500 font-bold">Total Penjualan:</span>
                                            <span class="text-sm font-bold text-gray-700">Rp ${formatNumber(d.total_pendapatan)}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    list.innerHTML = html;
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                list.innerHTML = `<div class="text-center py-4 text-red-500">Kesalahan jaringan</div>`;
            });
    }

    function exportProduk() {
        let params = new URLSearchParams({
            tgl_awal: document.getElementById('prod_tgl_awal').value,
            tgl_akhir: document.getElementById('prod_tgl_akhir').value,
            limit: document.getElementById('prod_limit').value
        });
        const c = document.getElementById('prod_id_cabang');
        if(c) params.append('id_cabang', c.value);
        
        window.location.href = `${baseUrl}laporan/export_excel_produk_terlaris?${params.toString()}`;
    }

    // Initialize first tab
    document.addEventListener('DOMContentLoaded', loadBulanan);
</script>

<?php $this->load->view('layout/footer'); ?>
