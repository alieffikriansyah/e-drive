<?php $this->load->view('layout/header', ['title' => 'Pengeluaran Operasional']); ?>

<!-- Load SweetAlert2 -->
<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<div class="flex flex-col md:flex-row justify-between items-center mb-6 border-l-4 border-mapul-yellow pl-4">
    <div>
        <h1 class="text-3xl font-extrabold tracking-wide text-mapul-green uppercase">Biaya Operasional</h1>
        <p class="text-gray-500 font-medium mt-1">Pencatatan pengeluaran harian, bulanan (Listrik, Gaji, dll)</p>
    </div>
    <button onclick="openModal()" class="btn-mapul mt-4 md:mt-0">
        <i class="fa fa-wallet text-mapul-yellow"></i> Catat Pengeluaran
    </button>
</div>

<!-- ================= FILTER & TABEL ================= -->
<div class="mapul-card overflow-hidden">
    <!-- Filter Tanggal -->
    <div class="bg-gray-50 border-b border-gray-200 p-4 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Mulai Tanggal</label>
            <input type="date" id="tgl_awal" class="mapul-input py-1.5" value="<?= date('Y-m-01') ?>">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
            <input type="date" id="tgl_akhir" class="mapul-input py-1.5" value="<?= date('Y-m-d') ?>">
        </div>
        <button onclick="loadData()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1.5 px-4 rounded border border-gray-300 transition-colors">
            <i class="fa fa-filter mr-1"></i> Filter
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="mapul-thead">
                <tr>
                    <th class="rounded-tl-lg">Tanggal</th>
                    <th>Cabang</th>
                    <th>Kategori</th>
                    <th>Judul / Keterangan</th>
                    <th class="text-right">Total Biaya</th>
                    <th class="text-center rounded-tr-lg">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm" id="tableBody">
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL FORM ================= -->
<div id="formModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-gray-300 transform transition-all scale-95 opacity-0 duration-300" id="formModalDialog">
        <div class="mapul-modal-header">
            <h3 class="text-xl font-bold text-mapul-green-md flex items-center gap-2" id="modalTitle">
                <i class="fa fa-wallet text-mapul-yellow"></i> Catat Pengeluaran
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="dataForm" onsubmit="submitForm(event)" class="p-6">
            <input type="hidden" id="id" name="id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <?php if (in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin'])): ?>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Cabang <span class="text-red-500">*</span></label>
                    <select name="id_cabang" id="id_cabang" required class="mapul-input appearance-none border-yellow-300">
                        <option value="">Pilih Cabang</option>
                        <?php foreach ($cabang as $c): ?>
                            <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nama_cabang) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="space-y-1 <?= (strtolower($_SESSION['role_name'] ?? '')) === 'owner' ? '' : 'md:col-span-2' ?>">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" id="tanggal" required class="mapul-input" value="<?= date('Y-m-d') ?>">
                </div>

                <div class="space-y-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori Pengeluaran <span class="text-red-500">*</span></label>
                    <select name="id_kat_operasional" id="id_kat_operasional" required class="mapul-input appearance-none">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kat_ops as $k): ?>
                            <option value="<?= $k->id ?>"><?= htmlspecialchars($k->nama_kategori) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Judul / Deskripsi Singkat <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_pengeluaran" id="nama_pengeluaran" required class="mapul-input" placeholder="Misal: Beli Gas LPG 3Kg, Bayar Listrik">
                </div>

                <div class="space-y-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Total Biaya (Nominal) <span class="text-red-500">*</span></label>
                    <div class="flex items-center">
                        <span class="bg-gray-100 px-3 py-2 border border-r-0 border-gray-300 rounded-l-md text-gray-500 font-bold">Rp</span>
                        <input type="number" name="total_biaya" id="total_biaya" required class="mapul-input rounded-l-none" placeholder="0">
                    </div>
                </div>

                <div class="space-y-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Keterangan Tambahan</label>
                    <textarea name="keterangan" id="keterangan" rows="2" class="mapul-input" placeholder="Catatan opsional"></textarea>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-6 py-2 bg-white text-gray-700 font-medium rounded border border-gray-300 hover:bg-gray-50 transition-colors shadow-sm">Batal</button>
                <button type="submit" id="btnSubmit" class="btn-mapul px-8 py-2">
                    <i class="fa fa-save text-mapul-yellow"></i> <span id="btnSubmitText">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const baseUrl = '<?= base_url() ?>';
    const form = document.getElementById('dataForm');
    const modal = document.getElementById('formModal');
    const modalDialog = document.getElementById('formModalDialog');
    const modalTitle = document.getElementById('modalTitle');
    const btnSubmitText = document.getElementById('btnSubmitText');

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function formatNumber(num) {
        return parseFloat(num).toLocaleString('id-ID');
    }

    function loadData() {
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>`;
        
        const tglAwal = document.getElementById('tgl_awal').value;
        const tglAkhir = document.getElementById('tgl_akhir').value;

        fetch(`${baseUrl}pengeluaran_operasional/load_data?tgl_awal=${tglAwal}&tgl_akhir=${tglAkhir}`)
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    let html = '';
                    if (res.data.length === 0) {
                        html = `<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada pengeluaran di rentang tanggal tersebut</td></tr>`;
                    } else {
                        res.data.forEach(d => {
                            const isDeleted = parseInt(d.status) === 8;
                            const trClass = isDeleted ? 'bg-red-50 hover:bg-red-100 text-gray-400 line-through' : 'hover:bg-gray-50';
                            
                            const btnAct = isDeleted 
                                ? `<button onclick="restoreData(${d.id})" class="btn-restore"><i class="fa fa-undo"></i> Restore</button>`
                                : `<button onclick="editData(${d.id})" class="btn-edit mr-1"><i class="fa fa-edit"></i> Edit</button>
                                   <button onclick="deleteData(${d.id})" class="btn-danger"><i class="fa fa-trash"></i> Hapus</button>`;

                            html += `
                                <tr class="${trClass} transition-colors duration-150">
                                    <td class="px-6 py-4 font-semibold whitespace-nowrap text-gray-600">${d.tanggal}</td>
                                    <td class="px-6 py-4 text-mapul-green font-medium">${escapeHtml(d.nama_cabang)}</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs font-bold">${escapeHtml(d.nama_kategori)}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold ${isDeleted ? '' : 'text-gray-800'}">${escapeHtml(d.nama_pengeluaran)}</div>
                                        <div class="text-xs ${isDeleted ? 'text-gray-400' : 'text-gray-500'} italic mt-0.5">${escapeHtml(d.keterangan || '-')}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-extrabold ${isDeleted ? 'text-gray-400' : 'text-red-500'}">Rp ${formatNumber(d.total_biaya)}</td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">${btnAct}</td>
                                </tr>
                            `;
                        });
                    }
                    document.getElementById('tableBody').innerHTML = html;
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Kesalahan koneksi', 'error');
            });
    }

    document.addEventListener('DOMContentLoaded', loadData);

    function openModal() {
        form.reset();
        document.getElementById('id').value = '';
        document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];
        
        modalTitle.innerHTML = '<i class="fa fa-wallet text-mapul-yellow"></i> Catat Pengeluaran';
        btnSubmitText.textContent = 'Simpan Data';
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalDialog.classList.remove('scale-95', 'opacity-0');
            modalDialog.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalDialog.classList.remove('scale-100', 'opacity-100');
        modalDialog.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function editData(id) {
        fetch(baseUrl + 'pengeluaran_operasional/get_pengeluaran/' + id)
            .then(res => res.json())
            .then(res => {
                if(res.status) {
                    const data = res.data;
                    openModal();
                    
                    modalTitle.innerHTML = '<i class="fa fa-edit text-mapul-yellow"></i> Edit Pengeluaran';
                    btnSubmitText.textContent = 'Simpan Perubahan';
                    
                    document.getElementById('id').value = data.id;
                    if(document.getElementById('id_cabang')) document.getElementById('id_cabang').value = data.id_cabang;
                    document.getElementById('tanggal').value = data.tanggal;
                    document.getElementById('id_kat_operasional').value = data.id_kat_operasional;
                    document.getElementById('nama_pengeluaran').value = data.nama_pengeluaran;
                    document.getElementById('total_biaya').value = data.total_biaya;
                    document.getElementById('keterangan').value = data.keterangan;
                } else {
                    Swal.fire('Error', 'Gagal mengambil data', 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Kesalahan jaringan', 'error'));
    }

    function submitForm(e) {
        e.preventDefault();
        const id = document.getElementById('id').value;
        const url = id ? baseUrl + 'pengeluaran_operasional/edit/' + id : baseUrl + 'pengeluaran_operasional/create';
        const formData = new FormData(form);
        const btn = document.getElementById('btnSubmit');
        const originalHtml = btn.innerHTML;
        
        btn.innerHTML = '<i class="fa fa-spinner fa-spin text-mapul-yellow"></i> Menyimpan...';
        btn.disabled = true;

        fetch(url, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    closeModal();
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                    loadData();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'))
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
    }

    function deleteData(id) {
        Swal.fire({
            title: 'Hapus Pengeluaran?', text: "Data akan dibatalkan/dihapus secara soft-delete", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'pengeluaran_operasional/delete/' + id, { method: 'POST' })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status) {
                            Swal.fire({ icon: 'success', title: 'Terhapus', timer: 1500, showConfirmButton: false });
                            loadData();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    });
            }
        });
    }

    function restoreData(id) {
        fetch(baseUrl + 'pengeluaran_operasional/restore/' + id, { method: 'POST' })
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    Swal.fire({ icon: 'success', title: 'Dipulihkan', timer: 1500, showConfirmButton: false });
                    loadData();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            });
    }
</script>

<?php $this->load->view('layout/footer'); ?>
