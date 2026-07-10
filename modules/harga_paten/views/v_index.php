<?php $this->load->view('layout/header', ['title' => 'Harga Paten Menu']); ?>

<!-- Load SweetAlert2 -->
<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<div class="flex flex-col md:flex-row justify-between items-center mb-6 border-l-4 border-mapul-yellow pl-4">
    <div>
        <h1 class="text-3xl font-extrabold tracking-wide text-mapul-green uppercase">Harga Paten Menu</h1>
        <p class="text-gray-500 font-medium mt-1">Kelola Pagu Harga Jual Menu per Cabang</p>
    </div>
    <div class="mt-4 md:mt-0 flex gap-2">
        <?php if (in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin'])): ?>
        <select id="filter_cabang" onchange="loadData()" class="mapul-input py-2 text-sm max-w-[200px]">
            <option value="0">Semua Cabang</option>
            <?php foreach($cabang as $c): ?>
                <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nama_cabang) ?></option>
            <?php endforeach; ?>
        </select>
        <?php endif; ?>
        <button onclick="openModal()" class="btn-mapul">
            <i class="fa fa-plus-circle text-mapul-yellow"></i> Tambah Harga Paten
        </button>
    </div>
</div>

<!-- ================= TABEL DATA ================= -->
<div class="mapul-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="mapul-thead">
                <tr>
                    <th class="rounded-tl-lg">Nama Menu</th>
                    <th>Cabang</th>
                    <th>Produk</th>
                    <th class="text-right">Harga Paten</th>
                    <th class="text-center">Status</th>
                    <th class="text-center rounded-tr-lg">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm" id="tableBody">
                <tr><td colspan="8" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL FORM ================= -->
<div id="formModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-gray-300 transform transition-all scale-95 opacity-0 duration-300" id="formModalDialog">
        <div class="mapul-modal-header">
            <h3 class="text-xl font-bold text-mapul-green-md flex items-center gap-2" id="modalTitle">
                <i class="fa fa-tag text-mapul-yellow"></i> Tambah Harga Paten
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>

        <form id="dataForm" onsubmit="submitForm(event)" class="p-6">
            <input type="hidden" id="id" name="id">

            <div class="grid grid-cols-1 gap-4 mb-4">
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

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Produk Jadi <span class="text-red-500">*</span></label>
                    <select name="id_produk" id="id_produk" required class="mapul-input appearance-none">
                        <option value="">Pilih Produk Jadi</option>
                        <?php foreach ($produk_jadi as $p): ?>
                            <option value="<?= $p->id ?>">[<?= htmlspecialchars($p->nama_cabang) ?>] <?= htmlspecialchars($p->nama_produk) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">* Hanya produk dengan kategori Produk Jadi yang bisa diberi harga paten</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Tampilan Menu <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_menu" name="nama_menu" required class="mapul-input" placeholder="Misal: Mie Ayam Bakso Spesial">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Harga Jual Paten (Pagu) <span class="text-red-500">*</span></label>
                    <div class="flex items-center">
                        <span class="bg-gray-100 px-3 py-2 border border-r-0 border-gray-300 rounded-l-md text-gray-500 font-bold">Rp</span>
                        <input type="number" id="harga_jual_paten" name="harga_jual_paten" required min="1" class="mapul-input rounded-l-none" placeholder="0">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">* Harga ini akan otomatis tersinkron ke kolom Harga Jual di Master Produk</p>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Keterangan Tambahan</label>
                    <textarea id="keterangan" name="keterangan" rows="2" class="mapul-input" placeholder="Opsional"></textarea>
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
    const filterCabang = document.getElementById('filter_cabang');

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function formatRupiah(num) {
        return 'Rp ' + parseFloat(num || 0).toLocaleString('id-ID');
    }

    function loadData() {
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>`;

        let url = baseUrl + 'harga_paten/load_data';
        if (filterCabang && filterCabang.value !== '0') {
            url += '?id_cabang=' + filterCabang.value;
        }

        fetch(url)
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    let html = '';
                    if (res.data.length === 0) {
                        html = `<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Belum ada harga paten. Klik "Tambah Harga Paten" untuk memulai.</td></tr>`;
                    } else {
                        res.data.forEach(d => {
                            const isDeleted = parseInt(d.status) === 8;
                            const trClass   = isDeleted ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';
                            const badgeStr  = isDeleted
                                ? `<span class="badge-habis"><i class="fa fa-trash-alt mr-1"></i> Terhapus</span>`
                                : `<span class="badge-aman"><i class="fa fa-check-circle mr-1"></i> Aktif</span>`;

                            const btnAct = isDeleted
                                ? `<span class="text-xs text-gray-400">Tidak dapat dipulihkan</span>`
                                : `<button onclick="editData(${d.id})" class="btn-edit mr-1"><i class="fa fa-edit"></i> Edit</button>
                                   <button onclick="deleteData(${d.id})" class="btn-danger"><i class="fa fa-trash"></i> Hapus</button>`;

                            const laba      = parseFloat(d.laba_per_porsi || 0);
                            const labaClass = laba >= 0 ? 'text-green-600 font-bold' : 'text-red-600 font-bold';
                            const labaLabel = laba >= 0 ? formatRupiah(laba) : `<span class="text-red-600">${formatRupiah(Math.abs(laba))} (rugi)</span>`;

                            html += `
                                <tr class="${trClass} transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800 text-base">${escapeHtml(d.nama_menu)}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">${escapeHtml(d.nama_produk)}</div>
                                    </td>
                                    <td class="px-6 py-4 text-mapul-green font-semibold">${escapeHtml(d.nama_cabang || '-')}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">${escapeHtml(d.nama_kategori)}</td>
                                    <td class="px-6 py-4 text-right text-mapul-green-md font-extrabold text-lg">${formatRupiah(d.harga_jual_paten)}</td>
                                    <td class="px-6 py-4 text-center">${badgeStr}</td>
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
        modalTitle.innerHTML = '<i class="fa fa-tag text-mapul-yellow"></i> Tambah Harga Paten';
        btnSubmitText.textContent = 'Tambah Data';
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
        fetch(baseUrl + 'harga_paten/get_harga_paten/' + id)
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    const d = res.data;
                    openModal();
                    modalTitle.innerHTML = '<i class="fa fa-edit text-mapul-yellow"></i> Edit Harga Paten';
                    btnSubmitText.textContent = 'Simpan Perubahan';

                    document.getElementById('id').value             = d.id;
                    if (document.getElementById('id_cabang')) document.getElementById('id_cabang').value = d.id_cabang;
                    if (document.getElementById('id_produk')) document.getElementById('id_produk').value  = d.id_produk;
                    document.getElementById('nama_menu').value        = d.nama_menu;
                    document.getElementById('harga_jual_paten').value = d.harga_jual_paten;
                    document.getElementById('keterangan').value       = d.keterangan || '';
                } else {
                    Swal.fire('Error', 'Gagal mengambil data', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Kesalahan jaringan', 'error'));
    }

    function submitForm(e) {
        e.preventDefault();
        const id  = document.getElementById('id').value;
        const url = id ? baseUrl + 'harga_paten/edit/' + id : baseUrl + 'harga_paten/create';
        const formData = new FormData(form);
        const btn = document.getElementById('btnSubmit');
        const originalHtml = btn.innerHTML;

        btn.innerHTML = '<i class="fa fa-spinner fa-spin text-mapul-yellow"></i> Menyimpan...';
        btn.disabled  = true;

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
            .catch(() => Swal.fire('Error', 'Terjadi kesalahan sistem', 'error'))
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled  = false;
            });
    }

    function deleteData(id) {
        Swal.fire({
            title: 'Hapus Harga Paten?',
            text: 'Harga ini tidak akan muncul di kasir setelah dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'harga_paten/delete/' + id, { method: 'POST' })
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
</script>

<?php $this->load->view('layout/footer'); ?>
