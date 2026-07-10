<?php $this->load->view('layout/header', ['title' => 'Master Produk & Bahan Baku']); ?>

<!-- Load SweetAlert2 -->
<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<div class="flex flex-col md:flex-row justify-between items-center mb-6 border-l-4 border-mapul-yellow pl-4">
    <div>
        <h1 class="text-3xl font-extrabold tracking-wide text-mapul-green uppercase">Master Produk</h1>
        <p class="text-gray-500 font-medium mt-1">Data Produk Global &mdash; Bahan Baku, Setengah Jadi, Produk Jadi</p>
    </div>
    <div class="mt-4 md:mt-0">
        <button onclick="openModal()" class="btn-mapul">
            <i class="fa fa-plus-circle text-mapul-yellow"></i> Tambah Produk
        </button>
    </div>
</div>

<!-- ================= TABEL DATA ================= -->
<div class="mapul-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="mapul-thead">
                <tr>
                    <th class="rounded-tl-lg">Kode / Nama</th>
                    <th>Kategori</th>
                    <th class="text-right">Stok Min.</th>
                    <th class="text-center">Status</th>
                    <th class="text-center rounded-tr-lg">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm" id="tableBody">
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500"><i
                            class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL FORM ================= -->
<div id="formModal"
    class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-300 transform transition-all scale-95 opacity-0 duration-300"
        id="formModalDialog">
        <div class="mapul-modal-header">
            <h3 class="text-xl font-bold text-mapul-green-md flex items-center gap-2" id="modalTitle">
                <i class="fa fa-box-open text-mapul-yellow"></i> Tambah Produk
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>

        <form id="dataForm" onsubmit="submitForm(event)" class="p-6">
            <input type="hidden" id="id" name="id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="space-y-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori <span
                            class="text-red-500">*</span></label>
                    <select name="id_kategori" id="id_kategori" required class="mapul-input appearance-none">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k->id ?>">[<?= htmlspecialchars($k->jenis) ?>]
                                <?= htmlspecialchars($k->nama_kategori) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Kode Produk
                        (Opsional)</label>
                    <input type="text" id="kode_produk" name="kode_produk" class="mapul-input"
                        placeholder="Misal: BRG-001">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Produk <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="nama_produk" name="nama_produk" required class="mapul-input"
                        placeholder="Misal: Tepung Terigu">
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Satuan <span
                            class="text-red-500">*</span></label>
                    <select name="id_satuan" id="id_satuan" required class="mapul-input appearance-none">
                        <option value="">Pilih Satuan</option>
                        <?php foreach ($satuan as $s): ?>
                            <option value="<?= $s->id ?>"><?= htmlspecialchars($s->nama_satuan) ?>
                                (<?= htmlspecialchars($s->singkatan) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Stok Minimum
                        (Alert)</label>
                    <input type="number" step="0.01" id="stok_minimum" name="stok_minimum" class="mapul-input"
                        value="0">
                    <p class="text-[10px] text-gray-400 mt-1">* Untuk peringatan stok menipis di semua cabang</p>
                </div>
            </div>

            <!-- Harga Jual — hanya muncul jika kategori produk_jadi -->
            <div id="harga_jual_group" class="hidden mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fa fa-tag text-mapul-yellow"></i>
                    <span class="text-xs font-bold text-yellow-800 uppercase tracking-wider">Harga Jual (Produk Jadi)</span>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Harga Jual <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500 font-bold text-sm">Rp</span>
                        <input type="number" step="1" id="harga_jual" name="harga_jual" class="mapul-input pl-10" placeholder="15000" value="0">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">* Akan otomatis tersimpan sebagai harga paten untuk kasir</p>
                </div>
            </div>

            <div class="space-y-1 mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Keterangan
                    Tambahan</label>
                <textarea id="keterangan" name="keterangan" rows="2" class="mapul-input"
                    placeholder="Opsional"></textarea>
            </div>

            <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeModal()"
                    class="px-6 py-2 bg-white text-gray-700 font-medium rounded border border-gray-300 hover:bg-gray-50 transition-colors shadow-sm">Batal</button>
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
    const stokAwalGroup = document.getElementById('stok_awal_group');
    const filterCabang = document.getElementById('filter_cabang');

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function formatNumber(num) {
        return parseFloat(num).toLocaleString('id-ID');
    }

    function loadData() {
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>`;

        fetch(baseUrl + 'produk/load_data')
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    let html = '';
                    if (res.data.length === 0) {
                        html = `<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada produk. Klik "Tambah Produk" untuk mulai.</td></tr>`;
                    } else {
                        res.data.forEach(d => {
                            const isDeleted = parseInt(d.status) === 8;
                            const trClass = isDeleted ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';

                            const badgeStr = isDeleted ? `<span class="badge-habis"><i class="fa fa-trash-alt mr-1"></i> Terhapus</span>`
                                : `<span class="badge-aman"><i class="fa fa-check-circle mr-1"></i> Aktif</span>`;

                            const btnAct = isDeleted
                                ? `<button onclick="restoreData(${d.id})" class="btn-restore"><i class="fa fa-undo"></i> Restore</button>`
                                : `<button onclick="editData(${d.id})" class="btn-edit mr-1"><i class="fa fa-edit"></i> Edit</button>
                                   <button onclick="deleteData(${d.id})" class="btn-danger"><i class="fa fa-trash"></i> Hapus</button>`;

                            let stokClass = 'text-green-600 font-bold';
                            let stokWarning = '';
                            if (!isDeleted && d.status_stok === 'MENIPIS') {
                                stokClass = 'text-red-600 font-bold';
                                stokWarning = ' <i class="fa fa-exclamation-triangle text-red-500" title="Stok Menipis"></i>';
                            }

                            html += `
                                <tr class="${trClass} transition-colors duration-150">
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-400 font-bold">${escapeHtml(d.kode_produk || '-')}</div>
                                        <div class="font-bold text-gray-800 text-base">${escapeHtml(d.nama_produk)}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs font-bold">${escapeHtml(d.jenis_kategori)}</span>
                                        <div class="text-xs text-gray-500 mt-1">${escapeHtml(d.nama_kategori)}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-500">
                                        ${formatNumber(d.stok_minimum)} <span class="text-xs">${escapeHtml(d.satuan_singkatan)}</span>
                                    </td>
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
        modalTitle.innerHTML = '<i class="fa fa-box-open text-mapul-yellow"></i> Tambah Produk';
        btnSubmitText.textContent = 'Tambah Data';
        // Sembunyikan field harga_jual saat buka modal tambah
        document.getElementById('harga_jual_group').classList.add('hidden');
        document.getElementById('harga_jual').required = false;
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
        fetch(baseUrl + 'produk/get_produk/' + id)
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    const data = res.data;
                    openModal();
                    modalTitle.innerHTML = '<i class="fa fa-edit text-mapul-yellow"></i> Edit Produk';
                    btnSubmitText.textContent = 'Simpan Perubahan';

                    document.getElementById('id').value = data.id;
                    document.getElementById('id_kategori').value = data.id_kategori;
                    document.getElementById('id_satuan').value = data.id_satuan;
                    document.getElementById('kode_produk').value = data.kode_produk;
                    document.getElementById('nama_produk').value = data.nama_produk;
                    document.getElementById('stok_minimum').value = data.stok_minimum;
                    document.getElementById('keterangan').value = data.keterangan || '';
                    document.getElementById('harga_jual').value = data.harga_jual || 0;
                    toggleHargaJual(); // munculkan field harga jika produk_jadi
                } else {
                    Swal.fire('Error', 'Gagal mengambil data', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Kesalahan jaringan', 'error'));
    }


    function submitForm(e) {
        e.preventDefault();
        const id = document.getElementById('id').value;
        const url = id ? baseUrl + 'produk/edit/' + id : baseUrl + 'produk/create';
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
            title: 'Hapus Produk?', text: "Data akan di-soft delete", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'produk/delete/' + id, { method: 'POST' })
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
        fetch(baseUrl + 'produk/restore/' + id, { method: 'POST' })
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
    // Tampilkan/sembunyikan field harga_jual berdasarkan kategori yang dipilih.
    // Gunakan == (loose) bukan === agar id integer dari JSON cocok dengan string value di SELECT.
    const kategoris = <?php echo json_encode(array_map(function($k) {
        return ['id' => (int)$k->id, 'jenis' => $k->jenis];
    }, $kategori)); ?>;

    function toggleHargaJual() {
        const idKategori = parseInt(document.getElementById('id_kategori').value);
        const kat = kategoris.find(k => k.id === idKategori);
        const group = document.getElementById('harga_jual_group');
        const hargaInput = document.getElementById('harga_jual');
        if (kat && kat.jenis === 'produk_jadi') {
            group.classList.remove('hidden');
            hargaInput.required = true;
        } else {
            group.classList.add('hidden');
            hargaInput.required = false;
        }
    }

    document.getElementById('id_kategori').addEventListener('change', toggleHargaJual);
</script>

<?php $this->load->view('layout/footer'); ?>