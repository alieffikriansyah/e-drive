<?php $this->load->view('layout/header', ['title' => 'Log Aktivitas Users']); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-mapul-green-md drop-shadow-sm uppercase tracking-wider flex items-center gap-2">
            <i class="fa fa-history text-mapul-yellow"></i> Log Aktivitas User
        </h1>
        <p class="text-gray-500 text-sm mt-1 font-medium">Rekaman semua aksi pengguna di dalam sistem.</p>
    </div>
</div>

<div id="authSection" class="max-w-md mx-auto mt-10">
    <div class="mapul-card p-6">
        <h4 class="text-xl font-bold text-center text-mapul-green-md mb-2">
            <i class="fa fa-lock text-mapul-yellow"></i> Keamanan Ekstra
        </h4>
        <p class="text-center text-gray-500 text-sm mb-6">Silakan masukkan password khusus untuk melihat data log aktivitas.</p>
        <form id="formAuth" autocomplete="off">
            <div class="mb-4">
                <input type="password" class="mapul-input text-center text-xl tracking-[0.3em]" name="password" id="authPassword" required placeholder="••••••••">
            </div>
            <div class="text-center">
                <button type="submit" class="btn-mapul w-full justify-center text-lg py-3">
                    <i class="fa fa-unlock-alt"></i> Buka Log
                </button>
            </div>
        </form>
    </div>
</div>

<div id="dataSection" class="hidden">
    <div class="mapul-card mb-6">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mulai Tanggal</label>
                    <input type="date" id="tgl_awal" class="mapul-input" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" id="tgl_akhir" class="mapul-input" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="w-full md:w-auto">
                    <button type="button" class="btn-mapul w-full md:w-auto justify-center" onclick="loadData()">
                        <i class="fa fa-filter"></i> Filter Data
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tableLog">
                <thead class="mapul-thead">
                    <tr>
                        <th class="py-3 px-4 border-b border-green-700 w-16 text-center">No</th>
                        <th class="py-3 px-4 border-b border-green-700">Waktu</th>
                        <th class="py-3 px-4 border-b border-green-700">User</th>
                        <th class="py-3 px-4 border-b border-green-700">Aksi</th>
                        <th class="py-3 px-4 border-b border-green-700">Keterangan</th>
                        <th class="py-3 px-4 border-b border-green-700">IP Address</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700 divide-y divide-gray-200">
                    <!-- Data dimuat via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>
<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(!empty($_SESSION['log_record_authenticated'])): ?>
        document.getElementById('authSection').classList.add('hidden');
        document.getElementById('dataSection').classList.remove('hidden');
        loadData();
    <?php endif; ?>

    var formAuth = document.getElementById('formAuth');
    if(formAuth) {
        formAuth.addEventListener('submit', function(e) {
            e.preventDefault();
            var password = document.getElementById('authPassword').value;
            
            var formData = new FormData();
            formData.append('password', password);

            fetch('<?= base_url("log_record_users/auth") ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(response => {
                if(response.status) {
                    document.getElementById('authSection').classList.add('hidden');
                    document.getElementById('dataSection').classList.remove('hidden');
                    loadData();
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            });
        });
    }
});

function loadData() {
    var tgl_awal = document.getElementById('tgl_awal').value;
    var tgl_akhir = document.getElementById('tgl_akhir').value;

    Swal.fire({
        title: 'Memuat Data...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading() }
    });

    fetch('<?= base_url("log_record_users/load_data") ?>?tgl_awal=' + tgl_awal + '&tgl_akhir=' + tgl_akhir)
        .then(res => res.json())
        .then(response => {
            Swal.close();
            var html = '';
            if(response.status && response.data.length > 0) {
                response.data.forEach(function(v, i) {
                    html += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${v.created_at}</td>
                            <td>${v.nama_user || '-'}</td>
                            <td><span class="badge-aman">${v.action}</span></td>
                            <td>${v.keterangan}</td>
                            <td>${v.ip_address || '-'}</td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="6" class="text-center">Tidak ada data ditemukan</td></tr>';
            }
            document.querySelector('#tableLog tbody').innerHTML = html;
        });
}
</script>