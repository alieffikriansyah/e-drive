<?php $this->load->view('layout/header', ['title' => 'Master Kategori']); ?>

<!-- Load SweetAlert2 -->
<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<div class="flex flex-col md:flex-row justify-between items-center mb-6 border-l-4 border-mapul-yellow pl-4">
    <h1 class="text-3xl font-extrabold tracking-wide text-mapul-green uppercase">Manajemen Kategori</h1>
    <button onclick="openModal()" class="btn-mapul mt-4 md:mt-0">
        <i class="fa fa-plus-circle text-mapul-yellow"></i> Tambah Kategori
    </button>
</div>

<!-- ================= TABEL DATA ================= -->
<div class="mapul-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="mapul-thead">
                <tr>
                    <th class="rounded-tl-lg w-16">ID</th>
                    <th>Nama Kategori</th>
                    <th class="text-center w-32">Status</th>
                    <th class="text-center w-48 rounded-tr-lg">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm" id="tableBody">
                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL FORM ================= -->
<div id="formModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-gray-300 transform transition-all scale-95 opacity-0 duration-300" id="formModalDialog">
        <div class="mapul-modal-header">
            <h3 class="text-xl font-bold text-mapul-green-md flex items-center gap-2" id="modalTitle">
                <i class="fa fa-tags text-mapul-yellow"></i> Tambah Kategori
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="dataForm" onsubmit="submitForm(event)" class="p-6 space-y-4">
            <input type="hidden" id="id" name="id">
            
            <div class="space-y-1">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kategori</label>
                <input type="text" id="nama_kategori" name="nama_kategori" required class="mapul-input" placeholder="Misal: Makanan, Minuman">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis</label>
                <select id="jenis" name="jenis" required class="mapul-input w-full p-2 border rounded">
                    <option value="bahan_baku">Bahan Baku</option>
                    <option value="setengah_jadi">Setengah Jadi</option>
                    <option value="produk_jadi">Produk Jadi</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="mapul-input w-full p-2 border rounded" placeholder="Keterangan tambahan (opsional)"></textarea>
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

    function loadData() {
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>`;
        
        fetch(baseUrl + 'kategori/load_data')
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    let html = '';
                    if (res.data.length === 0) {
                        html = `<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data kategori</td></tr>`;
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

                            html += `
                                <tr class="${trClass} transition-colors duration-150">
                                    <td class="px-6 py-4 text-gray-500 font-semibold">${d.id}</td>
                                    <td class="px-6 py-4 font-bold text-gray-800">${escapeHtml(d.nama_kategori)}</td>
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
        modalTitle.innerHTML = '<i class="fa fa-tags text-mapul-yellow"></i> Tambah Kategori';
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
        fetch(baseUrl + 'kategori/get_kategori/' + id)
            .then(res => res.json())
            .then(res => {
                if(res.status) {
                    const data = res.data;
                    openModal();
                    modalTitle.innerHTML = '<i class="fa fa-edit text-mapul-yellow"></i> Edit Kategori';
                    btnSubmitText.textContent = 'Simpan Perubahan';
                    
                    document.getElementById('id').value = data.id;
                    document.getElementById('nama_kategori').value = data.nama_kategori;
                    document.getElementById('jenis').value = data.jenis;
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
        const url = id ? baseUrl + 'kategori/edit/' + id : baseUrl + 'kategori/create';
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
            title: 'Hapus Kategori?', text: "Data akan di-soft delete", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'kategori/delete/' + id, { method: 'POST' })
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
        fetch(baseUrl + 'kategori/restore/' + id, { method: 'POST' })
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
