<?php $this->load->view('layout/header', ['title' => 'List Penjualan']); ?>

<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<!-- ================= PAGE HEADER ================= -->
<div class="flex flex-col md:flex-row justify-between items-center mb-6 border-l-4 border-mapul-yellow pl-4">
    <div>
        <h1 class="text-3xl font-extrabold tracking-wide text-mapul-green uppercase">List Penjualan</h1>
        <p class="text-gray-500 font-medium mt-1">Daftar Transaksi Penjualan per Cabang</p>
    </div>
</div>

<!-- ================= FILTER BAR ================= -->
<div class="mapul-card overflow-hidden mb-6">
    <div class="bg-gray-50 border-b border-gray-200 p-4 flex flex-wrap gap-4 items-end">

        <?php if (in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin'])): ?>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Filter Cabang</label>
                <select id="filter_cabang" class="mapul-input py-1.5">
                    <option value="0">Semua Cabang</option>
                    <?php foreach ($cabang as $c): ?>
                        <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nama_cabang) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Awal</label>
            <input type="date" id="filter_tgl_awal" class="mapul-input py-1.5" value="<?= date('Y-m-01') ?>">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Akhir</label>
            <input type="date" id="filter_tgl_akhir" class="mapul-input py-1.5" value="<?= date('Y-m-d') ?>">
        </div>

        <div class="ml-auto flex gap-2">
            <button onclick="loadData()"
                class="bg-mapul-green hover:bg-mapul-green-md text-white font-bold py-1.5 px-4 rounded transition-colors shadow-sm">
                <i class="fa fa-search mr-1"></i> Tampilkan
            </button>
        </div>
    </div>

    <!-- Summary Row -->
    <div class="px-4 py-3 bg-white border-b border-gray-100 flex flex-wrap gap-6 text-sm">
        <div>
            <span class="text-gray-500">Total Transaksi:</span>
            <span class="font-bold text-mapul-green ml-1" id="summary_total_transaksi">—</span>
        </div>
        <div>
            <span class="text-gray-500">Total Pendapatan:</span>
            <span class="font-bold text-mapul-green ml-1" id="summary_total_pendapatan">—</span>
        </div>
        <div>
            <span class="text-gray-500">Total Diskon:</span>
            <span class="font-bold text-red-500 ml-1" id="summary_total_diskon">—</span>
        </div>
    </div>

    <!-- ================= TABLE ================= -->
    <div class="p-4 overflow-x-auto">
        <table class="mapul-table w-full">
            <thead>
                <tr>
                    <th class="text-center w-8">No</th>
                    <th>No. Nota</th>
                    <th>Tanggal &amp; Waktu</th>
                    <th>Cabang</th>
                    <th>Kasir</th>
                    <th class="text-center">Item</th>
                    <th class="text-right">Total Bayar</th>
                    <th class="text-right">Diskon</th>
                    <th class="text-center">Metode Bayar</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody_penjualan">
                <tr>
                    <td colspan="10" class="text-center py-10 text-gray-400">
                        <i class="fa fa-filter mr-2"></i> Pilih filter lalu klik Tampilkan
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL NOTA ================= -->
<div id="modal_nota" class="fixed inset-0 z-50 flex items-center justify-center hidden"
    style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-mapul-green px-6 py-4 flex justify-between items-center">
            <div>
                <h2 class="text-white font-extrabold text-lg uppercase tracking-wide">
                    <i class="fa fa-receipt mr-2"></i> Nota Transaksi
                </h2>
                <p class="text-green-200 text-sm" id="nota_nomor">—</p>
            </div>
            <button onclick="closeNota()" class="text-white hover:text-green-200 text-2xl leading-none">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <!-- Kop Usaha -->
            <div class="text-center mb-4">
                <div class="text-2xl font-extrabold text-mapul-green tracking-wide">Mie Ayam Pulean</div>
                <div class="text-xs text-gray-400 tracking-widest uppercase">Nota Transaksi</div>
            </div>
            <hr class="mb-4 border-dashed border-gray-300">
            <!-- Info Transaksi -->
            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm mb-4">
                <div class="text-gray-500">Cabang</div>
                <div class="font-semibold text-right" id="nota_cabang">—</div>
                <div class="text-gray-500">Kasir</div>
                <div class="font-semibold text-right" id="nota_kasir">—</div>
                <div class="text-gray-500">Tanggal</div>
                <div class="font-semibold text-right" id="nota_tanggal">—</div>
                <div class="text-gray-500">Metode Bayar</div>
                <div class="font-semibold text-right" id="nota_metode">—</div>
            </div>

            <hr class="my-3 border-dashed border-gray-300">

            <!-- Tabel Item Produk -->
            <table class="w-full text-sm mb-4">
                <thead>
                    <tr class="bg-gray-100 text-gray-600">
                        <th class="text-left py-2 px-2 rounded-l">Produk</th>
                        <th class="text-center py-2 px-2">Qty</th>
                        <th class="text-right py-2 px-2">Harga</th>
                        <th class="text-right py-2 px-2 rounded-r">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="nota_items">
                    <!-- filled by JS -->
                </tbody>
            </table>

            <hr class="my-3 border-dashed border-gray-300">

            <!-- Ringkasan Pembayaran -->
            <div class="text-sm space-y-1">
                <div class="flex justify-between">
                    <span class="text-gray-500">Subtotal</span>
                    <span id="nota_subtotal" class="font-semibold">—</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Diskon</span>
                    <span id="nota_diskon" class="font-semibold text-red-500">—</span>
                </div>
                <div
                    class="flex justify-between text-base font-bold text-mapul-green border-t border-gray-200 pt-2 mt-2">
                    <span>Total Bayar</span>
                    <span id="nota_total_bayar">—</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Bayar</span>
                    <span id="nota_bayar" class="font-semibold">—</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Kembalian</span>
                    <span id="nota_kembalian" class="font-semibold text-blue-600">—</span>
                </div>
            </div>

            <!-- Keterangan -->
            <div id="nota_ket_wrap" class="mt-3 hidden">
                <p class="text-xs text-gray-400 italic" id="nota_keterangan"></p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center gap-2">
            <div class="flex gap-2">
                <button onclick="printNota()"
                    class="inline-flex items-center gap-1 bg-mapul-green hover:bg-mapul-green-md text-white font-bold py-2 px-4 rounded transition-colors shadow-sm">
                    <i class="fa fa-print"></i> Print Thermal
                </button>
                <button onclick="downloadPdf()"
                    class="inline-flex items-center gap-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors shadow-sm">
                    <i class="fa fa-file-pdf"></i> PDF
                </button>
            </div>
            <button onclick="closeNota()"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-5 rounded transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    const BASE = '<?= base_url() ?>';
    const IS_ADMIN = <?= in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin']) ? 'true' : 'false' ?>;

    function rp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    function formatDatetime(str) {
        if (!str) return '—';
        const d = new Date(str);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
            + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    function metodeBadge(m) {
        const map = {
            tunai: '<span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">Tunai</span>',
            qris: '<span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">QRIS</span>',
            transfer: '<span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 text-xs font-bold">Transfer</span>',
            lainnya: '<span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">Lainnya</span>',
        };
        return map[m] || m;
    }

    async function loadData() {
        const tbody = document.getElementById('tbody_penjualan');
        tbody.innerHTML = '<tr><td colspan="10" class="text-center py-10 text-gray-400"><i class="fa fa-spinner fa-spin mr-2"></i>Memuat data...</td></tr>';

        const tgl_awal = document.getElementById('filter_tgl_awal').value;
        const tgl_akhir = document.getElementById('filter_tgl_akhir').value;
        const cabangEl = document.getElementById('filter_cabang');
        const id_cabang = (IS_ADMIN && cabangEl) ? cabangEl.value : 0;

        try {
            const res = await fetch(BASE + 'list_penjualan/load_data?tgl_awal=' + tgl_awal + '&tgl_akhir=' + tgl_akhir + '&id_cabang=' + id_cabang);
            const json = await res.json();

            if (!json.status || !json.data || json.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" class="text-center py-10 text-gray-400"><i class="fa fa-inbox mr-2"></i>Tidak ada data transaksi pada periode ini</td></tr>';
                document.getElementById('summary_total_transaksi').textContent = '0';
                document.getElementById('summary_total_pendapatan').textContent = rp(0);
                document.getElementById('summary_total_diskon').textContent = rp(0);
                return;
            }

            let totalPendapatan = 0, totalDiskon = 0;
            let rows = '';

            json.data.forEach(function (d, i) {
                totalPendapatan += parseFloat(d.total_bayar || 0);
                totalDiskon += parseFloat(d.total_diskon || 0);

                const diskonHtml = parseFloat(d.total_diskon) > 0
                    ? rp(d.total_diskon)
                    : '<span class="text-gray-300">—</span>';

                rows += '<tr>' +
                    '<td class="text-center text-gray-400 text-sm">' + (i + 1) + '</td>' +
                    '<td class="font-mono text-sm font-bold text-mapul-green">' + d.no_nota + '</td>' +
                    '<td class="text-sm">' + formatDatetime(d.tanggal_transaksi) + '</td>' +
                    '<td class="text-sm">' + (d.nama_cabang || '<span class="text-gray-400">—</span>') + '</td>' +
                    '<td class="text-sm">' + (d.nama_kasir || '<span class="text-gray-400">—</span>') + '</td>' +
                    '<td class="text-center text-sm"><span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-bold">' + d.jumlah_item + ' item</span></td>' +
                    '<td class="text-right font-bold text-sm">' + rp(d.total_bayar) + '</td>' +
                    '<td class="text-right text-sm text-red-500">' + diskonHtml + '</td>' +
                    '<td class="text-center">' + metodeBadge(d.metode_bayar) + '</td>' +
                    '<td class="text-center"><button onclick="openNota(' + d.id + ')" class="inline-flex items-center gap-1 bg-mapul-green hover:bg-mapul-green-md text-white text-xs font-bold px-3 py-1.5 rounded transition-colors shadow-sm"><i class="fa fa-receipt"></i> Nota</button></td>' +
                    '</tr>';
            });

            tbody.innerHTML = rows;
            document.getElementById('summary_total_transaksi').textContent = json.data.length;
            document.getElementById('summary_total_pendapatan').textContent = rp(totalPendapatan);
            document.getElementById('summary_total_diskon').textContent = rp(totalDiskon);

        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-10 text-red-400"><i class="fa fa-exclamation-triangle mr-2"></i>Gagal memuat data</td></tr>';
        }
    }

    async function openNota(id) {
        document.getElementById('modal_nota').classList.remove('hidden');
        document.getElementById('nota_items').innerHTML = '<tr><td colspan="4" class="text-center py-6 text-gray-400"><i class="fa fa-spinner fa-spin"></i></td></tr>';

        try {
            const res = await fetch(BASE + 'list_penjualan/get_nota/' + id);
            const json = await res.json();

            if (!json.status) {
                Swal.fire('Error', json.message || 'Terjadi kesalahan', 'error');
                closeNota();
                return;
            }

            var pj = json.penjualan;
            document.getElementById('nota_nomor').textContent = pj.no_nota;
            document.getElementById('nota_cabang').textContent = pj.nama_cabang || '—';
            document.getElementById('nota_kasir').textContent = pj.nama_kasir || '—';
            document.getElementById('nota_tanggal').textContent = formatDatetime(pj.tanggal_transaksi);
            document.getElementById('nota_metode').innerHTML = metodeBadge(pj.metode_bayar);
            document.getElementById('nota_total_bayar').textContent = rp(pj.total_bayar);
            document.getElementById('nota_diskon').textContent = rp(pj.total_diskon);
            document.getElementById('nota_bayar').textContent = rp(pj.bayar);
            document.getElementById('nota_kembalian').textContent = rp(pj.kembalian);

            var subtotalRaw = (json.items || []).reduce(function (s, it) { return s + parseFloat(it.subtotal || 0); }, 0);
            document.getElementById('nota_subtotal').textContent = rp(subtotalRaw);

            if (pj.keterangan) {
                document.getElementById('nota_keterangan').textContent = 'Ket: ' + pj.keterangan;
                document.getElementById('nota_ket_wrap').classList.remove('hidden');
            } else {
                document.getElementById('nota_ket_wrap').classList.add('hidden');
            }

            var itemRows = '';
            if (!json.items || json.items.length === 0) {
                itemRows = '<tr><td colspan="4" class="text-center py-4 text-gray-400">Tidak ada item</td></tr>';
            } else {
                json.items.forEach(function (it) {
                    var qty = parseFloat(it.jumlah_beli);
                    var diskon = parseFloat(it.diskon || 0);
                    var qtyStr = (qty % 1 === 0) ? parseInt(qty) : qty;
                    var diskonHtml = diskon > 0 ? '<div class="text-xs text-red-400">Diskon: ' + rp(diskon) + '</div>' : '';
                    var ketHtml = it.ket_item ? '<div class="text-xs text-gray-400">' + it.ket_item + '</div>' : '';

                    itemRows += '<tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">' +
                        '<td class="py-2 px-2"><div class="font-semibold text-sm">' + it.nama_produk + '</div>' + diskonHtml + ketHtml + '</td>' +
                        '<td class="text-center text-sm py-2 px-2">' + qtyStr + ' ' + it.nama_satuan + '</td>' +
                        '<td class="text-right text-sm py-2 px-2">' + rp(it.harga_satuan) + '</td>' +
                        '<td class="text-right font-bold text-sm py-2 px-2 text-mapul-green">' + rp(it.subtotal) + '</td>' +
                        '</tr>';
                });
            }
            document.getElementById('nota_items').innerHTML = itemRows;

        } catch (e) {
            Swal.fire('Error', 'Gagal memuat nota transaksi', 'error');
            closeNota();
        }
    }

    function closeNota() {
        document.getElementById('modal_nota').classList.add('hidden');
    }

    document.getElementById('modal_nota').addEventListener('click', function (e) {
        if (e.target === this) closeNota();
    });

    function buildNotaHtml(forPdf) {
        var nomor = document.getElementById('nota_nomor').textContent;
        var cabang = document.getElementById('nota_cabang').textContent;
        var kasir = document.getElementById('nota_kasir').textContent;
        var tanggal = document.getElementById('nota_tanggal').textContent;
        var metode = document.getElementById('nota_metode').textContent;
        var subtotal = document.getElementById('nota_subtotal').textContent;
        var diskon = document.getElementById('nota_diskon').textContent;
        var total = document.getElementById('nota_total_bayar').textContent;
        var bayar = document.getElementById('nota_bayar').textContent;
        var kemb = document.getElementById('nota_kembalian').textContent;
        var ket = document.getElementById('nota_keterangan').textContent;
        var ketWrap = document.getElementById('nota_ket_wrap');

        // Build items rows
        var itemsHTML = '';
        var rows = document.querySelectorAll('#nota_items tr');
        rows.forEach(function (tr) {
            var cells = tr.querySelectorAll('td');
            if (cells.length >= 4) {
                var produk = cells[0].querySelector('div.font-semibold') ? cells[0].querySelector('div.font-semibold').textContent : cells[0].textContent;
                var qty = cells[1].textContent.trim();
                var harga = cells[2].textContent.trim();
                var sub = cells[3].textContent.trim();
                itemsHTML += '<tr><td class="prod">' + produk + '</td><td class="qty">' + qty + '</td><td class="harga">' + harga + '</td><td class="sub">' + sub + '</td></tr>';
            }
        });

        var ketHtml = (!ketWrap.classList.contains('hidden') && ket) ? '<p class="ket">Ket: ' + ket + '</p>' : '';
        var width = forPdf ? '210mm' : '80mm';
        var padding = forPdf ? '20mm' : '4mm';

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Nota - ' + nomor + '</title>' +
            '<style>' +
            'body{font-family:"Courier New",Courier,monospace;font-size:' + (forPdf ? '12pt' : '9pt') + ';margin:0;padding:' + padding + ';width:' + width + ';color:#000;}' +
            '.kop{text-align:center;margin-bottom:6px;}' +
            '.kop-nama{font-size:' + (forPdf ? '18pt' : '13pt') + ';font-weight:bold;}' +
            '.kop-sub{font-size:' + (forPdf ? '10pt' : '8pt') + ';color:#555;}' +
            'hr{border:none;border-top:1px dashed #000;margin:5px 0;}' +
            'table{width:100%;border-collapse:collapse;}' +
            'th{text-align:left;font-size:' + (forPdf ? '10pt' : '8pt') + ';border-bottom:1px dashed #000;padding:2px 1px;}' +
            'td{vertical-align:top;padding:2px 1px;}' +
            'td.qty,td.harga,td.sub{text-align:right;}' +
            'td.qty{text-align:center;}' +
            '.info{font-size:' + (forPdf ? '10pt' : '8pt') + ';margin-bottom:4px;}' +
            '.info tr td:first-child{color:#555;width:40%;}' +
            '.info tr td:last-child{text-align:right;font-weight:bold;}' +
            '.ring{margin-top:4px;}' +
            '.ring tr td:first-child{color:#555;}' +
            '.ring tr td:last-child{text-align:right;}' +
            '.total-row td{font-weight:bold;font-size:' + (forPdf ? '13pt' : '10pt') + ';border-top:1px dashed #000;border-bottom:1px dashed #000;padding-top:3px;padding-bottom:3px;}' +
            '.total-row td:last-child{text-align:right;}' +
            '.ket{font-size:' + (forPdf ? '9pt' : '7pt') + ';color:#777;font-style:italic;margin-top:4px;}' +
            '.footer{text-align:center;font-size:' + (forPdf ? '9pt' : '7pt') + ';color:#777;margin-top:10px;}' +
            '</style></head><body>' +
            '<div class="kop"><div class="kop-nama">Mie Ayam Pulean</div><div class="kop-sub">Nota Transaksi</div></div>' +
            '<hr>' +
            '<table class="info"><tr><td>No. Nota</td><td>' + nomor + '</td></tr>' +
            '<tr><td>Cabang</td><td>' + cabang + '</td></tr>' +
            '<tr><td>Kasir</td><td>' + kasir + '</td></tr>' +
            '<tr><td>Tanggal</td><td>' + tanggal + '</td></tr>' +
            '<tr><td>Metode</td><td>' + metode + '</td></tr></table>' +
            '<hr>' +
            '<table><thead><tr><th>Produk</th><th style="text-align:center">Qty</th><th style="text-align:right">Harga</th><th style="text-align:right">Sub</th></tr></thead>' +
            '<tbody>' + itemsHTML + '</tbody></table>' +
            '<hr>' +
            '<table class="ring">' +
            '<tr><td>Subtotal</td><td style="text-align:right">' + subtotal + '</td></tr>' +
            '<tr><td>Diskon</td><td style="text-align:right">' + diskon + '</td></tr>' +
            '</table>' +
            '<table><tr class="total-row"><td>TOTAL</td><td>' + total + '</td></tr></table>' +
            '<table class="ring">' +
            '<tr><td>Bayar</td><td style="text-align:right">' + bayar + '</td></tr>' +
            '<tr><td>Kembalian</td><td style="text-align:right">' + kemb + '</td></tr>' +
            '</table>' +
            ketHtml +
            '<div class="footer">Terima kasih sudah berkunjung!<br>Mie Ayam Pulean</div>' +
            '</body></html>';
    }

    function printNota() {
        var w = window.open('', '_blank', 'width=400,height=700');
        w.document.write(buildNotaHtml(false));
        w.document.close();
        w.focus();
        setTimeout(function () { w.print(); }, 400);
    }

    function downloadPdf() {
        var w = window.open('', '_blank');
        w.document.write(buildNotaHtml(true));
        w.document.close();
        w.focus();
        setTimeout(function () { w.print(); }, 400);
    }

    document.addEventListener('DOMContentLoaded', loadData);
</script>

<?php $this->load->view('layout/footer'); ?>