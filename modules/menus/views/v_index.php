<?php $this->load->view('layout/header', ['title' => 'Sistem Menu']); ?>

<!-- Load SweetAlert2 -->
<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<div class="flex flex-col md:flex-row justify-between items-center mb-6">
    <h1 class="text-3xl font-bold tracking-wide text-richblack mb-4 md:mb-0 border-l-4 border-gray-500 pl-4">Manajemen Menu</h1>
    <button onclick="openModal()" class="bg-gradient-to-r from-gray-800 to-black hover:from-black hover:to-gray-800 text-white font-medium py-2 px-6 rounded shadow-lg border border-gray-600 transition-all duration-300 flex items-center gap-2">
        <i class="fa fa-plus-circle"></i> Tambah Menu Baru
    </button>
</div>

<div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-300">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-richblack text-gray-200 uppercase text-xs tracking-wider">
                    <th class="px-6 py-4 font-semibold">ID</th>
                    <th class="px-6 py-4 font-semibold">Nama Menu</th>
                    <th class="px-6 py-4 font-semibold">URL/Route</th>
                    <th class="px-6 py-4 font-semibold">Icon</th>
                    <th class="px-6 py-4 font-semibold">Parent</th>
                    <th class="px-6 py-4 font-semibold">Urutan</th>
                    <th class="px-6 py-4 font-semibold">is_active</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm" id="menuTableBody">
                <!-- Data akan diload via AJAX -->
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        <i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form Menu -->
<div id="menuModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-300 transform transition-all scale-95 opacity-0 duration-300" id="menuModalDialog">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h3 class="text-xl font-bold text-richblack flex items-center gap-2" id="modalTitle">
                <i class="fa fa-plus-circle text-gray-500"></i> Tambah Menu Baru
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-700 transition-colors">
                <i class="fa fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="menuForm" onsubmit="submitForm(event)" class="p-6 space-y-6">
            <input type="hidden" id="menu_id" name="menu_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Menu -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Menu</label>
                    <input type="text" id="name" name="name" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400 focus:border-transparent transition-all shadow-inner text-richblack font-medium" placeholder="Contoh: Kelola Pengguna">
                </div>
                
                <!-- URL -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">URL / Route</label>
                    <input type="text" id="url" name="url" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400 focus:border-transparent transition-all shadow-inner text-richblack font-medium" placeholder="Contoh: users">
                </div>
                
                <!-- Icon -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Icon (FontAwesome)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i id="iconPreview" class="fa fa-font"></i>
                        </span>
                        <input type="text" id="icon" name="icon" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400 focus:border-transparent transition-all shadow-inner text-richblack font-medium" placeholder="Contoh: fa-users" onkeyup="document.getElementById('iconPreview').className = 'fa ' + this.value">
                    </div>
                </div>
                
                <!-- Urutan -->
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Urutan (Order)</label>
                    <input type="number" id="order_num" name="order_num" required class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400 focus:border-transparent transition-all shadow-inner text-richblack font-medium" value="0">
                </div>
                
                <!-- Parent Menu -->
                <div class="space-y-1 md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Parent Menu</label>
                    <select id="parent_id" name="parent_id" class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:ring-2 focus:ring-gray-400 focus:border-transparent transition-all shadow-inner text-richblack font-medium appearance-none">
                        <option value="">-- Sebagai Parent Utama --</option>
                        <?php foreach ($parent_menus as $p): ?>
                            <option value="<?= $p->id ?>"><?= htmlspecialchars($p->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Hak Akses Role -->
                <div class="space-y-2 md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-inner">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Akses Role (Pilih Minimal 1)</label>
                    <div class="flex flex-wrap gap-4">
                        <?php foreach ($roles as $r): ?>
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="roles[]" value="<?= $r->id ?>" class="form-checkbox h-5 w-5 text-gray-800 border-gray-300 rounded focus:ring-gray-500 transition-colors role-checkbox">
                            <span class="ml-2 text-sm text-gray-700 font-medium group-hover:text-black transition-colors"><?= htmlspecialchars($r->role_name) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Status Aktif -->
                <div class="space-y-1 md:col-span-2 flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked class="form-checkbox h-5 w-5 text-gray-800 border-gray-300 rounded focus:ring-gray-500 transition-colors">
                    <label for="is_active" class="text-sm font-bold text-gray-700 cursor-pointer">Menu Aktif Ditampilkan?</label>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-6 py-2 bg-white text-gray-700 font-medium rounded border border-gray-300 hover:bg-gray-50 transition-colors shadow-sm">Batal</button>
                <button type="submit" id="btnSubmit" class="px-6 py-2 bg-gradient-to-r from-gray-800 to-black hover:from-black hover:to-gray-800 text-white font-medium rounded shadow-lg border border-gray-600 transition-all duration-300 flex items-center gap-2">
                    <i class="fa fa-save"></i> <span id="btnSubmitText">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Menyimpan elemen-elemen penting ke dalam variabel agar mudah dipanggil (Analogi: Mengambil dan menaruh perlengkapan kerja di atas meja).
    const modal = document.getElementById('menuModal');
    const modalDialog = document.getElementById('menuModalDialog');
    const form = document.getElementById('menuForm');
    const modalTitle = document.getElementById('modalTitle');
    const btnSubmitText = document.getElementById('btnSubmitText');
    const baseUrl = '<?= base_url() ?>';
    
    // Fungsi keamanan untuk mencegah script XSS saat menampilkan data (Analogi: Saringan anti racun untuk makanan).
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
             .toString()
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    // Fungsi utama untuk memuat data menu dari server menggunakan AJAX (Analogi: Meminta daftar menu terbaru dari dapur tanpa mematikan restoran).
    function loadData() {
        // Tampilkan indikator loading (Analogi: Menaruh tulisan "Sedang menyusun menu" di buku menu).
        document.getElementById('menuTableBody').innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                    <i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...
                </td>
            </tr>
        `;
        
        // Panggil kurir fetch ke alamat PHP
        fetch(baseUrl + 'menus/load_data')
            .then(response => response.json()) // Terjemahkan balasan menjadi JSON
            .then(res => {
                // Jika server merespon sukses
                if(res.status) {
                    let html = ''; // Siapkan keranjang HTML kosong
                    
                    if(res.data.length === 0) {
                        html = `<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td></tr>`;
                    } else {
                        // Loop setiap data menu yang diterima (Analogi: Membaca setiap resep satu per satu).
                        res.data.forEach(m => {
                            // Cek apakah punya menu induk, jika tidak beri tanda strip '-'
                            const parentName = m.parent_name ? escapeHtml(m.parent_name) : '-';
                            
                            // Badge untuk status aktif/tidak aktif
                            const isActiveBadge = parseInt(m.is_active) === 1 
                                ? `<span class="inline-flex items-center gap-1 text-green-700 bg-green-100 px-2 py-1 rounded text-xs font-bold"><i class="fa fa-check"></i> Ya</span>` 
                                : `<span class="inline-flex items-center gap-1 text-red-700 bg-red-100 px-2 py-1 rounded text-xs font-bold"><i class="fa fa-times"></i> Tidak</span>`;
                            
                            let statusBadge = '';
                            let actionButtons = '';
                            
                            // Cek apakah menu ini sedang dihapus sementara (soft delete = 8)
                            if (parseInt(m.status) === 8) {
                                statusBadge = `<span class="text-red-600 font-bold text-xs uppercase tracking-wider"><i class="fa fa-trash-alt mr-1"></i> Deleted (8)</span>`;
                                actionButtons = `<button onclick="restoreMenu(${m.id})" class="inline-block bg-green-100 hover:bg-green-200 text-green-800 border border-green-300 font-medium py-1.5 px-3 rounded text-xs transition-colors shadow-sm" title="Restore"><i class="fa fa-undo"></i> Restore</button>`;
                            } else {
                                statusBadge = `<span class="text-green-600 font-bold text-xs uppercase tracking-wider"><i class="fa fa-check-circle mr-1"></i> Aktif (0)</span>`;
                                actionButtons = `
                                    <button onclick="editMenu(${m.id})" class="inline-block bg-gray-200 hover:bg-gray-300 text-richblack font-medium py-1.5 px-3 rounded text-xs transition-colors border border-gray-400 shadow-sm" title="Edit">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button onclick="deleteMenu(${m.id})" class="inline-block bg-white hover:bg-red-50 text-red-600 border border-red-300 font-medium py-1.5 px-3 rounded text-xs transition-colors shadow-sm" title="Hapus">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                `;
                            }
                            
                            // Tentukan warna baris tabel (merah muda jika terhapus, putih abu jika tidak)
                            const trClass = parseInt(m.status) === 8 ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';
                            
                            // Rangkai menjadi baris tabel HTML
                            html += `
                                <tr class="${trClass} transition-colors duration-150">
                                    <td class="px-6 py-4 text-gray-600 font-medium">${m.id}</td>
                                    <td class="px-6 py-4 font-bold text-richblack">${escapeHtml(m.name)}</td>
                                    <td class="px-6 py-4 text-gray-600">${escapeHtml(m.url)}</td>
                                    <td class="px-6 py-4 text-gray-600"><i class="fa ${escapeHtml(m.icon)} w-5 text-center text-gray-500"></i> ${escapeHtml(m.icon)}</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs font-semibold">
                                            ${parentName}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-mono font-medium">${m.order_num}</td>
                                    <td class="px-6 py-4">${isActiveBadge}</td>
                                    <td class="px-6 py-4">${statusBadge}</td>
                                    <td class="px-6 py-4 text-center space-x-1">${actionButtons}</td>
                                </tr>
                            `;
                        });
                    }
                    // Tampilkan seluruh keranjang HTML tadi ke dalam tabel
                    document.getElementById('menuTableBody').innerHTML = html;
                } else {
                    document.getElementById('menuTableBody').innerHTML = `<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Gagal memuat data</td></tr>`;
                    Swal.fire({ icon: 'error', title: 'Oops...', text: res.message || 'Gagal mengambil data' });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('menuTableBody').innerHTML = `<tr><td colspan="9" class="px-6 py-4 text-center text-red-500">Terjadi kesalahan koneksi</td></tr>`;
                Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Tidak dapat terhubung ke server.' });
            });
    }

    // Jalankan fungsi loadData saat pertama kali halaman selesai dimuat.
    document.addEventListener('DOMContentLoaded', loadData);
    
    // Fungsi untuk membuka jendela pop-up/modal form (Analogi: Menampilkan secarik formulir pendaftaran ke pelanggan).
    function openModal() {
        form.reset(); // Bersihkan formulir dari sisa data sebelumnya
        document.getElementById('menu_id').value = '';
        document.getElementById('iconPreview').className = 'fa fa-font';
        modalTitle.innerHTML = '<i class="fa fa-plus-circle text-gray-500"></i> Tambah Menu Baru';
        btnSubmitText.textContent = 'Tambah Data';
        
        // Tampilkan modal dengan efek animasi transisi
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalDialog.classList.remove('scale-95', 'opacity-0');
            modalDialog.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    
    // Fungsi untuk menutup modal form (Analogi: Menyimpan kembali formulir ke dalam laci).
    function closeModal() {
        modalDialog.classList.remove('scale-100', 'opacity-100');
        modalDialog.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
    
    // Fungsi untuk memanggil data menu ke dalam form edit (Analogi: Mengambil data pasien lama untuk dikoreksi datanya).
    function editMenu(id) {
        // Ambil data menu tertentu via ID
        fetch(baseUrl + 'menus/get_menu/' + id)
            .then(response => response.json())
            .then(res => {
                if(res.status) {
                    const data = res.data;
                    openModal(); // Buka modal
                    // Ubah judul dan tombol menjadi mode Edit
                    modalTitle.innerHTML = '<i class="fa fa-edit text-gray-500"></i> Edit Menu';
                    btnSubmitText.textContent = 'Simpan Perubahan';
                    
                    // Suntikkan data dari server ke masing-masing input HTML
                    document.getElementById('menu_id').value = data.id;
                    document.getElementById('name').value = data.name;
                    document.getElementById('url').value = data.url;
                    document.getElementById('icon').value = data.icon;
                    document.getElementById('iconPreview').className = 'fa ' + data.icon;
                    document.getElementById('order_num').value = data.order_num;
                    document.getElementById('parent_id').value = data.parent_id || '';
                    document.getElementById('is_active').checked = parseInt(data.is_active) === 1;
                    
                    // Ceklis otomatis jabatan (roles) yang memiliki akses ke menu ini
                    const roleCheckboxes = document.querySelectorAll('.role-checkbox');
                    roleCheckboxes.forEach(cb => {
                        cb.checked = data.role_ids.includes(parseInt(cb.value));
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Gagal mengambil data menu: ' + (res.message || '') });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Terjadi kesalahan saat mengambil data.' });
            });
    }
    
    // Fungsi hapus (soft delete) menu (Sama seperti user, dipanggil lewat tombol Hapus)
    function deleteMenu(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Menu ini akan dihapus (soft delete)!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'menus/delete/' + id, { method: 'POST' })
                    .then(response => response.json())
                    .then(res => {
                        if(res.status) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
                            loadData(); // Segarkan tabel
                        } else {
                            Swal.fire('Gagal!', res.message || 'Menu gagal dihapus.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                    });
            }
        });
    }

    // Fungsi restore menu terhapus (Sama seperti user)
    function restoreMenu(id) {
        Swal.fire({
            title: 'Restore Menu?',
            text: "Menu ini akan diaktifkan kembali!",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Restore!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'menus/restore/' + id, { method: 'POST' })
                    .then(response => response.json())
                    .then(res => {
                        if(res.status) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
                            loadData();
                        } else {
                            Swal.fire('Gagal!', res.message || 'Menu gagal di-restore.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                    });
            }
        });
    }

    // Fungsi submit form untuk Edit/Tambah Data (Analogi: Menyerahkan form pendaftaran/revisi ke petugas).
    function submitForm(e) {
        e.preventDefault(); // Mencegah reload halaman
        
        const id = document.getElementById('menu_id').value;
        // Tentukan URL tujuan: jika ada ID berarti Edit (update), jika tidak ada berarti Create (tambah baru).
        const url = id ? baseUrl + 'menus/edit/' + id : baseUrl + 'menus/create';
        const formData = new FormData(form);
        
        // Ubah tampilan tombol saat memproses
        const btn = document.getElementById('btnSubmit');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;
        
        // Kirim via Fetch API
        fetch(url, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(res => {
            if(res.status) {
                closeModal(); // Tutup modal otomatis setelah berhasil
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });
                loadData(); // Segarkan tabel untuk melihat hasil yang baru disimpan
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message || 'Terjadi kesalahan' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Kesalahan Jaringan', text: 'Gagal mengirim data ke server.' });
        })
        .finally(() => {
            // Kembalikan tombol ke bentuk semula (selesai loading)
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
</script>

<?php $this->load->view('layout/footer'); ?>
