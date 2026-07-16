<?php $this->load->view('layout/header', ['title' => 'Manajemen User']); ?>

<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<style>
    @media (max-width: 768px) {
        .res-table thead {
            display: none;
        }
        .res-table, .res-table tbody, .res-table tr, .res-table td {
            display: block;
            width: 100%;
        }
        .res-table tr {
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background-color: #fff;
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .res-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem !important;
            border-bottom: 1px solid #f3f4f6;
            text-align: right !important;
        }
        .res-table td:last-child {
            border-bottom: none;
        }
        .res-table td::before {
            content: attr(data-label);
            font-weight: 700;
            color: #4b5563;
            text-transform: uppercase;
            font-size: 0.75rem;
            text-align: left;
            margin-right: 1rem;
        }
        /* Memastikan tombol aksi berbaris horizontal di mobile jika ada 2 tombol */
        .res-table td[data-label="Aksi"] {
            justify-content: space-between;
        }
        .res-table td[data-label="Aksi"] > div {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
            width: 100%;
        }
    }
</style>

<div class="flex flex-col md:flex-row justify-between items-center mb-6 border-l-4 border-mapul-yellow pl-4">
    <h1 class="text-3xl font-extrabold tracking-wide text-mapul-green uppercase">Manajemen User</h1>
</div>

<!-- ================= FORM TAMBAH USER ================= -->
<div class="mapul-card p-6 mb-8">
    <h3 class="text-xl font-bold text-mapul-green-md mb-4 flex items-center gap-2 border-b border-gray-100 pb-2">
        <i class="fa fa-user-plus text-mapul-yellow" id="formIcon"></i> <span id="formTitleText">Tambah User Baru</span>
    </h3>
    <form id="userForm" onsubmit="submitForm(event)" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
        <input type="hidden" name="id" id="userId" value="">
        
        <!-- Cabang -->
        <div class="lg:col-span-1 space-y-1">
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Cabang</label>
            <select name="id_cabang" id="id_cabang" class="mapul-input appearance-none">
                <option value="">Semua Cabang (Owner)</option>
                <?php foreach ($cabang as $c): ?>
                    <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nama_cabang) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Role -->
        <div class="lg:col-span-1 space-y-1">
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Role / Hak Akses</label>
            <select name="role_id" id="role_id" required class="mapul-input appearance-none">
                <?php foreach ($roles as $r): ?>
                    <?php if (strtolower($r->role_name) === 'owner' && strtolower($_SESSION['role_name'] ?? '') !== 'owner') continue; ?>
                    <option value="<?= $r->id ?>"><?= htmlspecialchars($r->role_name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nama -->
        <div class="lg:col-span-1 space-y-1">
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Lengkap</label>
            <input type="text" name="name" id="name" placeholder="Nama" required class="mapul-input">
        </div>

        <!-- Username -->
        <div class="lg:col-span-1 space-y-1">
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Username</label>
            <input type="text" name="username" id="username" placeholder="Username" required class="mapul-input">
        </div>

        <!-- Password -->
        <div class="lg:col-span-1 space-y-1">
            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" required class="mapul-input">
        </div>

        <!-- Tombol -->
        <div class="lg:col-span-1 flex flex-col gap-2 mt-2">
            <button type="submit" id="btnSubmit" class="btn-mapul px-8 py-2.5 w-full justify-center">
                <i class="fa fa-save text-mapul-yellow"></i> <span id="btnSubmitText">Simpan</span>
            </button>
            <button type="button" id="btnCancel" onclick="resetEditForm()" class="hidden bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-5 rounded-lg shadow border border-gray-600 transition-all duration-200 w-full text-center flex items-center justify-center gap-2">
                <i class="fa fa-times"></i> Batal
            </button>
        </div>
    </form>
</div>

<!-- ================= TABEL DATA ================= -->
<div class="mapul-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="res-table w-full text-left border-collapse">
            <thead class="mapul-thead">
                <tr>
                    <th class="rounded-tl-lg">ID</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Cabang</th>
                    <th>Role</th>
                    <th class="text-center">Status</th>
                    <th class="text-center rounded-tr-lg">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm" id="tableBody">
                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    const baseUrl = '<?= base_url() ?>';
    const form = document.getElementById('userForm');

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function loadData() {
        document.getElementById('tableBody').innerHTML = `<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin mr-2"></i> Memuat data...</td></tr>`;
        
        fetch(baseUrl + 'users/load_data')
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    let html = '';
                    if (res.data.length === 0) {
                        html = `<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td></tr>`;
                    } else {
                        res.data.forEach(u => {
                            const isDeleted = parseInt(u.status) === 8;
                            const trClass = isDeleted ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50';
                            const badgeStr = isDeleted ? `<span class="badge-habis"><i class="fa fa-trash-alt mr-1"></i> Terhapus</span>` 
                                                       : `<span class="badge-aman"><i class="fa fa-check-circle mr-1"></i> Aktif</span>`;
                            const btnAct = isDeleted 
                                ? `<button onclick="restoreData(${u.id})" class="btn-restore"><i class="fa fa-undo"></i> Restore</button>`
                                : `<button onclick="editData(${u.id})" class="btn-edit mr-1"><i class="fa fa-edit"></i> Edit</button>
                                   <button onclick="deleteData(${u.id})" class="btn-danger"><i class="fa fa-trash"></i> Hapus</button>`;
                            
                            const roleName = u.role_name ? escapeHtml(u.role_name) : '-';
                            let roleBadge = '';
                            if (roleName.toLowerCase() === 'owner') roleBadge = '<span class="text-mapul-green-md font-bold uppercase text-xs">Owner</span>';
                            else if (roleName.toLowerCase() === 'admin cabang') roleBadge = '<span class="text-blue-600 font-bold uppercase text-xs">Admin Cabang</span>';
                            else roleBadge = `<span class="text-gray-600 font-bold uppercase text-xs">${roleName}</span>`;

                            html += `
                                <tr class="${trClass} transition-colors duration-150">
                                    <td data-label="ID" class="px-6 py-4 text-gray-500 font-semibold">${u.id}</td>
                                    <td data-label="Nama Lengkap" class="px-6 py-4 font-bold text-gray-800">${escapeHtml(u.name)}</td>
                                    <td data-label="Username" class="px-6 py-4 font-medium text-gray-600">${escapeHtml(u.username)}</td>
                                    <td data-label="Cabang" class="px-6 py-4 text-mapul-green font-semibold">${u.nama_cabang ? escapeHtml(u.nama_cabang) : 'Pusat (Owner)'}</td>
                                    <td data-label="Role" class="px-6 py-4">${roleBadge}</td>
                                    <td data-label="Status" class="px-6 py-4 text-center">${badgeStr}</td>
                                    <td data-label="Aksi" class="px-6 py-4 text-center whitespace-nowrap">
                                        <div>${btnAct}</div>
                                    </td>
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

    function submitForm(e) {
        e.preventDefault();
        const id = document.getElementById('userId').value;
        const url = id ? baseUrl + 'users/edit/' + id : baseUrl + 'users/create';
        const formData = new FormData(form);
        const btn = document.getElementById('btnSubmit');
        const originalHtml = btn.innerHTML;
        
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;

        fetch(url, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                if (res.status) {
                    resetEditForm();
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false });
                    loadData();
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                console.error("Submit error:", err);
                Swal.fire('Error', 'Terjadi kesalahan sistem: ' + err.message, 'error');
            });
    }

    function editData(id) {
        fetch(baseUrl + 'users/get_user/' + id)
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    const u = res.data;
                    document.getElementById('userId').value = u.id;
                    document.getElementById('id_cabang').value = u.id_cabang !== null ? u.id_cabang : '';
                    document.getElementById('role_id').value = u.role_id;
                    document.getElementById('name').value = u.name;
                    document.getElementById('username').value = u.username;
                    
                    const passwordInput = document.getElementById('password');
                    passwordInput.required = false;
                    passwordInput.placeholder = 'Kosongkan jika tidak diubah';
                    passwordInput.value = '';

                    document.getElementById('formIcon').className = 'fa fa-user-edit text-mapul-yellow';
                    document.getElementById('formTitleText').textContent = 'Edit User: ' + u.username;
                    document.getElementById('btnSubmitText').textContent = 'Perbarui';
                    document.getElementById('btnCancel').classList.remove('hidden');

                    // Scroll to form smoothly
                    document.getElementById('userForm').scrollIntoView({ behavior: 'smooth' });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Gagal mengambil data user', 'error'));
    }

    function resetEditForm() {
        form.reset();
        document.getElementById('userId').value = '';
        
        const passwordInput = document.getElementById('password');
        passwordInput.required = true;
        passwordInput.placeholder = 'Password';
        
        document.getElementById('formIcon').className = 'fa fa-user-plus text-mapul-yellow';
        document.getElementById('formTitleText').textContent = 'Tambah User Baru';
        document.getElementById('btnSubmitText').textContent = 'Simpan';
        document.getElementById('btnCancel').classList.add('hidden');
    }

    function deleteData(id) {
        Swal.fire({
            title: 'Hapus User?', text: "Data akan di-soft delete", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'users/delete/' + id, { method: 'POST' })
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
        Swal.fire({
            title: 'Pulihkan User?', text: "Akun user akan diaktifkan kembali", icon: 'question',
            showCancelButton: true, confirmButtonColor: '#2e7d32', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, pulihkan!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(baseUrl + 'users/restore/' + id, { method: 'POST' })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status) {
                            Swal.fire({ icon: 'success', title: 'Dipulihkan', text: res.message, timer: 1500, showConfirmButton: false });
                            loadData();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    })
                    .catch(err => Swal.fire('Error', 'Kesalahan jaringan', 'error'));
            }
        });
    }
</script>

<?php $this->load->view('layout/footer'); ?>
