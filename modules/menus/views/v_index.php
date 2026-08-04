<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-edrive-text">⚙️ Menu Management</h1>
            <p class="text-edrive-muted mt-1">Kelola daftar menu navigasi dan hak akses role pengguna.</p>
        </div>
    </div>

    <!-- Menus Table -->
    <div class="glass-card overflow-hidden">
        <div class="p-5 border-b border-edrive-border flex items-center justify-between">
            <h3 class="font-bold text-edrive-text text-sm">Daftar System Menus</h3>
            <span class="text-xs text-edrive-muted">Total: <?= count($menus) ?> Menus</span>
        </div>

        <table class="table-light">
            <thead>
                <tr>
                    <th class="w-12 text-center">Urutan</th>
                    <th>Icon</th>
                    <th>Nama Menu</th>
                    <th>URL Module</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menus as $m): ?>
                    <tr>
                        <td class="text-center font-bold text-slate-500"><?= $m->order_num ?></td>
                        <td class="text-center">
                            <i class="<?= htmlspecialchars($m->icon) ?> text-lg text-edrive-accent"></i>
                        </td>
                        <td>
                            <p class="font-semibold text-edrive-text"><?= htmlspecialchars($m->name) ?></p>
                        </td>
                        <td>
                            <code class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-700"><?= htmlspecialchars($m->url) ?></code>
                        </td>
                        <td>
                            <span class="badge <?= $m->is_active ? 'badge-success' : 'badge-danger' ?>">
                                <?= $m->is_active ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="editMenu(<?= htmlspecialchars(json_encode($m)) ?>)" class="btn-icon text-slate-600 hover:text-edrive-accent" title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <button onclick="toggleMenu(<?= $m->id ?>)" class="btn-icon text-slate-400 hover:text-amber-600" title="Toggle Status">
                                    <i class="fa-solid fa-power-off text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Role Access Matrix -->
    <div class="glass-card p-6 space-y-4">
        <h3 class="font-bold text-edrive-text text-base flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-edrive-accent"></i> Matriks Hak Akses Role & Menu
        </h3>
        <p class="text-xs text-edrive-muted">Centang menu yang boleh diakses oleh masing-masing role pengguna.</p>

        <?php
        // Build access map [role_id][menu_id] => true
        $access_map = [];
        foreach ($access as $a) {
            $access_map[$a->role_id][$a->menu_id] = true;
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($roles as $r): ?>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <h4 class="font-bold text-edrive-text text-sm"><?= htmlspecialchars($r->role_name ?? $r->name ?? '') ?></h4>
                        <button type="button" onclick="saveRoleAccess(<?= $r->id ?>)" class="btn-primary !py-1 !px-3 !text-xs">
                            <i class="fa-solid fa-save"></i> Simpan
                        </button>
                    </div>

                    <form id="role-form-<?= $r->id ?>" class="space-y-2">
                        <input type="hidden" name="role_id" value="<?= $r->id ?>">
                        <?php foreach ($menus as $m): ?>
                            <?php $isChecked = isset($access_map[$r->id][$m->id]); ?>
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700 hover:text-edrive-accent">
                                <input type="checkbox" name="menu_ids[]" value="<?= $m->id ?>" <?= $isChecked ? 'checked' : '' ?>
                                       class="w-4 h-4 rounded border-slate-300 text-edrive-accent focus:ring-edrive-accent">
                                <i class="<?= htmlspecialchars($m->icon) ?> w-4 text-center text-slate-400"></i>
                                <span><?= htmlspecialchars($m->name) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Edit Menu Modal -->
<div id="editModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="font-bold text-edrive-text text-sm">Edit System Menu</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="editForm" onsubmit="submitEdit(event)">
            <input type="hidden" id="edit-id" name="id">
            <div class="modal-body space-y-4">
                <div>
                    <label class="text-xs font-semibold text-edrive-muted block mb-1">Nama Menu</label>
                    <input type="text" id="edit-name" name="name" class="input-field" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-edrive-muted block mb-1">Class FontAwesome Icon</label>
                    <input type="text" id="edit-icon" name="icon" class="input-field" placeholder="fa-solid fa-folder" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-edrive-muted block mb-1">Nomor Urutan</label>
                    <input type="number" id="edit-order" name="order_num" class="input-field" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editMenu(menu) {
    document.getElementById('edit-id').value = menu.id;
    document.getElementById('edit-name').value = menu.name;
    document.getElementById('edit-icon').value = menu.icon;
    document.getElementById('edit-order').value = menu.order_num;
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

async function submitEdit(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('editForm'));
    const res = await fetch('<?= site_url("menus/api_save") ?>', { method: 'POST', body: formData });
    const json = await res.json();
    if (json.status) location.reload();
}

async function toggleMenu(id) {
    const formData = new FormData();
    formData.append('id', id);
    const res = await fetch('<?= site_url("menus/api_toggle") ?>', { method: 'POST', body: formData });
    const json = await res.json();
    if (json.status) location.reload();
}

async function saveRoleAccess(roleId) {
    const formData = new FormData(document.getElementById('role-form-' + roleId));
    const res = await fetch('<?= site_url("menus/api_save_access") ?>', { method: 'POST', body: formData });
    const json = await res.json();
    Swal.fire({ icon: json.status ? 'success' : 'error', title: json.status ? 'Berhasil' : 'Gagal', text: json.message, timer: 1200, showConfirmButton: false });
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
