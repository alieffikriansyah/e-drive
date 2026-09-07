<?php require_once APPPATH . 'views/layout/header.php'; ?>

<?php
// Smart icon mapping based on drive name keywords
if (!function_exists('get_drive_icon')) {
    function get_drive_icon($name) {
        $name_lower = strtolower($name);
        $icon_map = [
            'laporan'     => 'fa-solid fa-file-lines',
            'report'      => 'fa-solid fa-file-lines',
            'sdm'         => 'fa-solid fa-users',
            'hr'          => 'fa-solid fa-users',
            'human'       => 'fa-solid fa-users',
            'penagihan'   => 'fa-solid fa-file-invoice-dollar',
            'invoice'     => 'fa-solid fa-file-invoice-dollar',
            'billing'     => 'fa-solid fa-file-invoice-dollar',
            'visual'      => 'fa-solid fa-lightbulb',
            'runway'      => 'fa-solid fa-lightbulb',
            'admin'       => 'fa-solid fa-shield-halved',
            'project'     => 'fa-solid fa-diagram-project',
            'manager'     => 'fa-solid fa-user-tie',
            'energy'      => 'fa-solid fa-bolt',
            'power'       => 'fa-solid fa-plug-circle-bolt',
            'supply'      => 'fa-solid fa-boxes-stacked',
            'aid'         => 'fa-solid fa-hand-holding-heart',
            'terminal'    => 'fa-solid fa-building',
            'logistik'    => 'fa-solid fa-truck-fast',
            'logistics'   => 'fa-solid fa-truck-fast',
            'k3'          => 'fa-solid fa-helmet-safety',
            'safety'      => 'fa-solid fa-helmet-safety',
            'keselamatan' => 'fa-solid fa-helmet-safety',
            'kesehatan'   => 'fa-solid fa-heart-pulse',
            'finance'     => 'fa-solid fa-coins',
            'keuangan'    => 'fa-solid fa-coins',
            'legal'       => 'fa-solid fa-scale-balanced',
            'hukum'       => 'fa-solid fa-scale-balanced',
            'it'          => 'fa-solid fa-server',
            'teknik'      => 'fa-solid fa-gears',
            'engineering' => 'fa-solid fa-gears',
            'marketing'   => 'fa-solid fa-bullhorn',
            'sales'       => 'fa-solid fa-handshake',
            'operasi'     => 'fa-solid fa-cogs',
            'operation'   => 'fa-solid fa-cogs',
            'quality'     => 'fa-solid fa-clipboard-check',
            'mutu'        => 'fa-solid fa-clipboard-check',
            'arsip'       => 'fa-solid fa-box-archive',
            'archive'     => 'fa-solid fa-box-archive',
            'shared'      => 'fa-solid fa-share-nodes',
            'public'      => 'fa-solid fa-globe',
        ];
        foreach ($icon_map as $keyword => $icon) {
            if (strpos($name_lower, $keyword) !== false) {
                return $icon;
            }
        }
        return 'fa-solid fa-hard-drive';
    }
}
?>

<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <!-- Breadcrumb is handled in header layout, just show title -->
            <h1 class="section-title text-2xl flex items-center gap-2">
                <?php if ($current_folder_id): ?>
                    <i class="fa-solid fa-folder-open text-yellow-500"></i>
                <?php else: ?>
                    <i class="<?= htmlspecialchars(get_drive_icon($drive->name)) ?>"
                        style="color: <?= $drive->color ?? '#3B82F6' ?>"></i>
                <?php endif; ?>
                <?= htmlspecialchars($title) ?>
            </h1>
        </div>
        <div class="flex gap-2">
            <button class="btn-secondary" onclick="showNewFolderModal()">
                <i class="fa-solid fa-folder-plus text-edrive-muted"></i> New Folder
            </button>
            <button class="btn-primary" onclick="document.getElementById('file-upload').click()">
                <i class="fa-solid fa-cloud-arrow-up"></i> Upload File
            </button>
            <input type="file" id="file-upload" class="hidden" multiple onchange="handleFilesUpload(this.files)">
        </div>
    </div>

    <!-- Drag & Drop Zone -->
    <div id="drop-area" class="dropzone" ondragenter="handleDragEnter(event)" ondragover="handleDragOver(event)"
        ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">

        <div class="pointer-events-none">
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-edrive-text">Tarik & Letakkan File di Sini</h3>
            <p class="text-sm text-edrive-muted mt-1">atau klik tombol Upload File di atas</p>
            <p class="text-xs text-edrive-light mt-4">Maksimal ukuran file:
                <?= format_file_size($config['max_file_size'] ?? 104857600) ?></p>
        </div>
    </div>

    <!-- Folders Grid -->
    <?php if (!empty($folders)): ?>
        <div>
            <h3 class="text-sm font-semibold text-edrive-muted mb-4 uppercase tracking-wider">Folders</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
                <?php foreach ($folders as $f): ?>
                    <a href="<?= site_url('drive/view/' . $drive->id . '?folder=' . $f->id) ?>"
                        class="glass-card-hover p-4 text-center group cursor-pointer block"
                        oncontextmenu="showFolderMenu(event, <?= $f->id ?>); return false;">

                        <i class="fa-solid fa-folder text-4xl mb-3 transition-transform group-hover:scale-110"
                            style="color: <?= $f->color ?? '#F59E0B' ?>"></i>

                        <p class="text-sm font-semibold text-edrive-text line-clamp-2 leading-tight group-hover:text-edrive-accent transition-colors"
                            title="<?= htmlspecialchars($f->name) ?>">
                            <?= htmlspecialchars($f->name) ?>
                        </p>
                        <p class="text-[10px] text-edrive-muted mt-1">
                            <?= $f->dynamic_total_files ?? 0 ?> items
                        </p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Files Table -->
    <?php if (!empty($documents) || (empty($folders) && empty($documents))): ?>
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <h3 class="text-sm font-semibold text-edrive-muted uppercase tracking-wider">Files</h3>
                <div class="flex items-center gap-3">
                    <div class="relative flex-1 sm:flex-none sm:w-72">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-edrive-muted text-sm"></i>
                        <input type="text" 
                               id="file-search-input" 
                               placeholder="Cari file di folder ini..." 
                               class="w-full pl-10 pr-10 py-2 bg-white border border-edrive-border rounded-xl text-sm 
                                      focus:ring-2 focus:ring-edrive-accent/20 focus:border-edrive-accent 
                                      transition-all outline-none placeholder:text-edrive-light"
                               autocomplete="off">
                        <button id="file-search-clear" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-edrive-muted hover:text-edrive-text 
                                       transition-colors hidden"
                                onclick="clearFileSearch()" title="Hapus pencarian (Esc)">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>
                    <div id="file-search-count" class="text-xs text-edrive-muted hidden whitespace-nowrap">
                        <span id="file-match-count">0</span> file ditemukan
                    </div>
                </div>
            </div>
            <div class="glass-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-light">
                        <thead>
                            <tr>
                                <th class="w-10"></th>
                                <th>Nama File</th>
                                <th>Ukuran</th>
                                <th>Diunggah Oleh</th>
                                <th>Tanggal</th>
                                <th class="w-[300px] pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documents)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-12 text-edrive-muted">
                                        <i class="fa-regular fa-folder-open text-3xl mb-3 text-gray-300"></i>
                                        <p>Folder ini kosong</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr class="group file-row" data-doc-id="<?= $doc->id ?>" data-doc-name="<?= htmlspecialchars($doc->name) ?>" data-doc-path="<?= htmlspecialchars($doc->file_path) ?>" data-filename="<?= strtolower(htmlspecialchars($doc->name . '.' . $doc->file_type)) ?>">
                                        <td class="text-center">
                                            <button class="text-gray-300 hover:text-yellow-400 transition-colors" title="Bintangi">
                                                <i class="fa-regular fa-star"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <i class="<?= get_file_icon($doc->file_type) ?> text-2xl w-8 text-center"></i>
                                                <div class="flex-1 min-w-0 cursor-pointer" onclick="previewFile(<?= $doc->id ?>, '<?= htmlspecialchars(addslashes($doc->name)) ?>', '<?= htmlspecialchars(addslashes($doc->file_path)) ?>')">
                                                    <p class="font-medium text-edrive-text truncate hover:text-edrive-accent transition-colors"
                                                        title="<?= htmlspecialchars($doc->name) ?>">
                                                        <?= htmlspecialchars($doc->name) ?>
                                                    </p>
                                                    <p class="text-xs text-edrive-muted uppercase">
                                                        <?= htmlspecialchars($doc->file_type) ?> &bull; V<?= $doc->version ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-edrive-muted"><?= format_file_size($doc->file_size) ?></td>
                                        <td class="text-edrive-text text-sm">
                                            <?php
                                            $uploader = $this->db->table('users')->where('id', $doc->created_by)->row();
                                            echo htmlspecialchars($uploader->name ?? 'Unknown');
                                            ?>
                                        </td>
                                        <td class="text-edrive-muted">
                                            <div class="tooltip" data-tip="<?= formatDate($doc->created_at) ?>">
                                                <?= timeAgo($doc->created_at) ?>
                                            </div>
                                        </td>
                                        <td class="pr-4 text-right">
                                            <div class="flex items-center justify-end gap-1 transition-opacity">
                                                <button class="w-8 h-8 rounded hover:bg-gray-100 text-blue-600 flex items-center justify-center transition tooltip" data-tip="Preview" onclick="previewFile(<?= $doc->id ?>, '<?= htmlspecialchars(addslashes($doc->name)) ?>', '<?= htmlspecialchars(addslashes($doc->file_path)) ?>')">
                                                    <i class="fa-solid fa-eye text-[13px]"></i>
                                                </button>
                                                <?php if (!empty(Env::get('ONLYOFFICE_URL')) && in_array(strtolower($doc->file_type), ['docx', 'xlsx', 'pptx'])): ?>
                                                <a href="<?= base_url('onlyoffice/edit/' . $doc->id) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded hover:bg-orange-50 text-orange-500 flex items-center justify-center transition tooltip" data-tip="Edit di ONLYOFFICE">
                                                    <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (in_array(strtolower($doc->file_type), ['zip', 'rar'])): ?>
                                                <button class="w-8 h-8 rounded hover:bg-amber-50 text-amber-600 flex items-center justify-center transition tooltip" data-tip="Ekstrak Arsip" onclick="extractArchive(<?= $doc->id ?>, '<?= htmlspecialchars(addslashes($doc->name)) ?>')">
                                                    <i class="fa-solid fa-file-zipper text-[13px]"></i>
                                                </button>
                                                <?php endif; ?>
                                                <button class="w-8 h-8 rounded hover:bg-blue-50 text-blue-500 flex items-center justify-center transition tooltip" data-tip="Rename" onclick="renameFile(<?= $doc->id ?>, '<?= htmlspecialchars(addslashes($doc->name)) ?>')">
                                                    <i class="fa-solid fa-i-cursor text-[13px]"></i>
                                                </button>
                                                <button class="w-8 h-8 rounded hover:bg-gray-100 text-green-600 flex items-center justify-center transition tooltip" data-tip="Download" onclick="downloadFile(<?= $doc->id ?>)">
                                                    <i class="fa-solid fa-download text-[13px]"></i>
                                                </button>
                                                <button class="w-8 h-8 rounded hover:bg-gray-100 text-purple-600 flex items-center justify-center transition tooltip" data-tip="Share" onclick="shareFile(<?= $doc->id ?>)">
                                                    <i class="fa-solid fa-share-nodes text-[13px]"></i>
                                                </button>
                                                <button class="w-8 h-8 rounded hover:bg-gray-100 text-gray-600 flex items-center justify-center transition tooltip" data-tip="Versions" onclick="showVersions(<?= $doc->id ?>)">
                                                    <i class="fa-solid fa-code-commit text-[13px]"></i>
                                                </button>
                                                <button class="w-8 h-8 rounded hover:bg-teal-50 text-teal-600 flex items-center justify-center transition tooltip" data-tip="Move" onclick="moveFile(<?= $doc->id ?>, '<?= htmlspecialchars(addslashes($doc->name)) ?>')">
                                                    <i class="fa-solid fa-folder-tree text-[13px]"></i>
                                                </button>
                                                <button class="w-8 h-8 rounded hover:bg-red-50 text-red-500 flex items-center justify-center transition tooltip" data-tip="Hapus" onclick="deleteFile(<?= $doc->id ?>)">
                                                    <i class="fa-solid fa-trash text-[13px]"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Upload Progress Modal -->
<div id="upload-modal" class="hidden fixed inset-x-0 bottom-0 z-50 p-4 pointer-events-none flex justify-end">
    <div
        class="bg-white border border-edrive-border rounded-xl shadow-2xl w-full max-w-md pointer-events-auto animate-slide-in flex flex-col overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-edrive-border flex justify-between items-center">
            <h4 class="font-bold text-edrive-text text-sm">Mengunggah <span id="upload-count">0</span> file...</h4>
            <button class="text-edrive-muted hover:text-edrive-text"
                onclick="document.getElementById('upload-modal').classList.add('hidden')">
                <i class="fa-solid fa-chevron-down"></i>
            </button>
        </div>
        <div class="p-4 max-h-60 overflow-y-auto" id="upload-list">
            <!-- Upload items injected here via JS -->
        </div>
    </div>
</div>

<script>
    // --- Drag and Drop Logic ---
    const dropArea = document.getElementById('drop-area');

    function handleDragEnter(e) { e.preventDefault(); e.stopPropagation(); dropArea.classList.add('dragover'); }
    function handleDragOver(e) { e.preventDefault(); e.stopPropagation(); dropArea.classList.add('dragover'); }
    function handleDragLeave(e) { e.preventDefault(); e.stopPropagation(); dropArea.classList.remove('dragover'); }
    function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        dropArea.classList.remove('dragover');

        let dt = e.dataTransfer;
        let files = dt.files;

        if (files.length > 0) {
            handleFilesUpload(files);
        }
    }

    function handleFilesUpload(files) {
        if (files.length === 0) return;

        let uploaded = 0;
        const total = files.length;

        document.getElementById('upload-modal').classList.remove('hidden');
        document.getElementById('upload-count').innerText = total;
        const list = document.getElementById('upload-list');
        list.innerHTML = ''; // reset

        Array.from(files).forEach((file, index) => {
            const fileId = 'upload-' + index;
            list.innerHTML += `
            <div id="${fileId}" class="flex items-center gap-3 mb-3 p-3 bg-white border border-edrive-border rounded-xl">
                <i class="fa-solid fa-file text-edrive-muted text-xl"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-edrive-text truncate">${file.name}</p>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                        <div class="bg-edrive-accent h-1.5 rounded-full transition-all duration-300" style="width: 10%" id="${fileId}-progress"></div>
                    </div>
                </div>
                <div id="${fileId}-status" class="text-xs font-semibold text-edrive-muted w-16 text-right">0%</div>
            </div>
        `;

            // Execute Upload
            const formData = new FormData();
            formData.append('file', file);
            formData.append('drive_id', <?= $drive->id ?>);
            formData.append('folder_id', '<?= $current_folder_id ?>');

            // Use XMLHttpRequest for progress tracking
            const xhr = new XMLHttpRequest();
            xhr.open('POST', BASE_URL + 'document/upload', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-CSRF-Token', CSRF_TOKEN);

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    document.getElementById(`${fileId}-progress`).style.width = percent + '%';
                    document.getElementById(`${fileId}-status`).innerText = percent + '%';
                }
            };

            xhr.onload = function () {
                uploaded++;
                const res = JSON.parse(xhr.responseText);
                if (xhr.status === 200 && res.status) {
                    document.getElementById(`${fileId}-progress`).classList.replace('bg-edrive-accent', 'bg-emerald-500');
                    document.getElementById(`${fileId}-status`).innerHTML = '<i class="fa-solid fa-check text-emerald-500"></i>';
                } else {
                    document.getElementById(`${fileId}-progress`).classList.replace('bg-edrive-accent', 'bg-red-500');
                    document.getElementById(`${fileId}-status`).innerHTML = '<i class="fa-solid fa-xmark text-red-500" title="' + (res.message || 'Error') + '"></i>';
                }

                if (uploaded === total) {
                    setTimeout(() => location.reload(), 1500);
                }
            };

            xhr.onerror = function () {
                uploaded++;
                document.getElementById(`${fileId}-progress`).classList.replace('bg-edrive-accent', 'bg-red-500');
                document.getElementById(`${fileId}-status`).innerHTML = '<i class="fa-solid fa-xmark text-red-500"></i>';
            };

            xhr.send(formData);
        });
    }

    // --- Menus ---
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('.context-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });

    let currentContextFolderId = null;

    function showFolderMenu(e, id) {
        e.preventDefault();
        e.stopPropagation();
        
        currentContextFolderId = id;
        
        const menu = document.getElementById('folderContextMenu');
        menu.classList.remove('hidden');
        
        menu.style.left = e.clientX + 'px';
        menu.style.top = e.clientY + 'px';
        
        document.querySelectorAll('.context-menu').forEach(m => {
            if (m.id !== 'folderContextMenu') m.classList.add('hidden');
        });
    }

    function triggerRenameFolder() {
        document.getElementById('folderContextMenu').classList.add('hidden');
        if (currentContextFolderId) {
            const folderName = document.querySelector(`a[oncontextmenu="showFolderMenu(event, ${currentContextFolderId}); return false;"] p.font-semibold`).innerText;
            renameFolder(currentContextFolderId, folderName);
        }
    }

    function triggerDeleteFolder() {
        document.getElementById('folderContextMenu').classList.add('hidden');
        if (currentContextFolderId) deleteFolder(currentContextFolderId);
    }

    function toggleContextMenu(e, id) {
        e.stopPropagation();
        document.querySelectorAll('.context-menu').forEach(menu => {
            if (menu.id !== id) menu.classList.add('hidden');
        });
        document.getElementById(id).classList.toggle('hidden');
    }

    // --- Extract Archive ---
    function extractArchive(docId, fileName) {
        if (!confirm(`Apakah Anda yakin ingin mengekstrak arsip "${fileName}" menjadi folder?\n\nSemua file di dalam arsip akan diekstrak ke folder baru.`)) {
            return;
        }

        // Show loading overlay
        const overlay = document.createElement('div');
        overlay.id = 'extract-overlay';
        overlay.className = 'fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center';
        overlay.innerHTML = `
            <div class="bg-white rounded-2xl p-8 shadow-2xl text-center max-w-sm mx-4">
                <i class="fa-solid fa-file-zipper text-amber-500 text-5xl mb-4 animate-bounce"></i>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Mengekstrak Arsip...</h3>
                <p class="text-sm text-gray-500">Mohon tunggu, sedang mengekstrak file dari arsip <strong>${fileName}</strong></p>
                <div class="mt-4 w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-amber-500 h-2 rounded-full animate-pulse" style="width: 60%"></div>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        const formData = new FormData();
        formData.append('document_id', docId);

        fetch(BASE_URL + 'document/extract/' + docId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': CSRF_TOKEN
            },
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            document.getElementById('extract-overlay')?.remove();
            if (res.status) {
                alert('✅ ' + res.message);
                location.reload();
            } else {
                alert('❌ ' + (res.message || 'Gagal mengekstrak arsip.'));
            }
        })
        .catch(err => {
            document.getElementById('extract-overlay')?.remove();
            alert('❌ Terjadi kesalahan saat mengekstrak arsip.');
            console.error(err);
        });
    }
    
    // --- Move File ---
    function moveFile(docId, fileName) {
        document.getElementById('move-document-id').value = docId;
        document.getElementById('move-file-name').innerText = fileName;
        
        const driveId = document.getElementById('move-current-drive-id').value;
        const select = document.getElementById('move-target-folder');
        
        select.innerHTML = '<option value="">Memuat folder...</option>';
        select.disabled = true;

        fetch(BASE_URL + 'drive/get_all_folders/' + driveId)
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    select.innerHTML = '<option value="">[ Root Drive ]</option>';
                    data.folders.forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.id;
                        opt.textContent = f.path;
                        select.appendChild(opt);
                    });
                    select.disabled = false;
                } else {
                    select.innerHTML = '<option value="">Gagal memuat folder</option>';
                }
            })
            .catch(err => {
                console.error(err);
                select.innerHTML = '<option value="">Gagal memuat folder</option>';
            });

        const modal = document.getElementById('move-modal');
        const modalContent = document.getElementById('move-modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeMoveModal() {
        const modal = document.getElementById('move-modal');
        const modalContent = document.getElementById('move-modal-content');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function submitMoveFile(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btn-submit-move');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memindahkan...';
        btn.disabled = true;
        
        const formData = new FormData(document.getElementById('move-form'));
        
        fetch(BASE_URL + 'document/move', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                location.reload();
            } else {
                alert('❌ ' + data.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('❌ Terjadi kesalahan sistem.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // --- Actions ---
    function previewFile(id, name, path) {
        // Update URL address bar to include ?preview=id
        const currentUrl = new URL(window.location.href);
        if (currentUrl.searchParams.get('preview') !== String(id)) {
            currentUrl.searchParams.set('preview', id);
            history.pushState({ previewId: id }, '', currentUrl.toString());
        }

        document.getElementById('preview-modal-title').innerText = name;
        document.getElementById('preview-modal').classList.remove('hidden');
        
        const contentArea = document.getElementById('preview-content-area');
        contentArea.innerHTML = '<div class="text-center p-10"><i class="fa-solid fa-circle-notch fa-spin text-4xl text-edrive-primary"></i><p class="mt-4 text-edrive-muted">Memuat pratinjau...</p></div>';
        
        const ext = path ? path.split('.').pop().toLowerCase() : '';
        const url = BASE_URL + 'document/preview/' + id;
        
        setTimeout(() => {
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                contentArea.innerHTML = `<img src="${url}" class="max-w-full max-h-full mx-auto object-contain rounded-lg">`;
            } else if (['pdf', 'txt', 'md', 'csv'].includes(ext)) {
                contentArea.innerHTML = `<iframe src="${url}" class="w-full h-full rounded-lg border-0 bg-white"></iframe>`;
            } else if (['mp4', 'webm'].includes(ext)) {
                contentArea.innerHTML = `<video controls class="max-w-full max-h-full mx-auto rounded-lg"><source src="${url}" type="video/${ext}"></video>`;
            } else if (['mp3', 'wav', 'ogg'].includes(ext)) {
                contentArea.innerHTML = `<audio controls class="w-full mt-10"><source src="${url}" type="audio/${ext}"></audio>`;
            } else {
                contentArea.innerHTML = `
                    <div class="text-center p-10">
                        <i class="fa-solid fa-file-invoice text-6xl text-edrive-muted mb-4"></i>
                        <h3 class="text-xl font-medium text-edrive-dark">Format tidak didukung</h3>
                        <p class="text-edrive-muted mt-2">File ini tidak dapat dipratinjau langsung di browser.</p>
                        <button onclick="downloadFile(${id})" class="mt-6 px-6 py-2 bg-edrive-primary text-white rounded-xl hover:bg-blue-700 transition">
                            <i class="fa-solid fa-download mr-2"></i> Download File
                        </button>
                    </div>`;
            }
        }, 300);
    }

    function closePreview() {
        document.getElementById('preview-modal').classList.add('hidden');
        document.getElementById('preview-content-area').innerHTML = '';
        
        // Remove preview parameter from URL & pushState back to folder URL
        const currentUrl = new URL(window.location.href);
        if (currentUrl.searchParams.has('preview')) {
            currentUrl.searchParams.delete('preview');
            history.pushState({ previewId: null }, '', currentUrl.toString());
        }
    }

    function closePreviewSilently() {
        document.getElementById('preview-modal').classList.add('hidden');
        document.getElementById('preview-content-area').innerHTML = '';
    }

    function openPreviewById(id) {
        const docRow = document.querySelector(`.file-row[data-doc-id="${id}"]`);
        if (docRow) {
            const name = docRow.getAttribute('data-doc-name');
            const path = docRow.getAttribute('data-doc-path');
            previewFile(id, name, path);
            return;
        }

        fetch(BASE_URL + 'document/info/' + id)
            .then(res => res.json())
            .then(res => {
                if (res.status && res.data) {
                    previewFile(res.data.id, res.data.name, res.data.file_path);
                }
            })
            .catch(err => console.error('Gagal memuat dokumen:', err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const previewId = urlParams.get('preview');
        if (previewId) {
            openPreviewById(previewId);
        }
    });

    window.addEventListener('popstate', function(e) {
        const urlParams = new URLSearchParams(window.location.search);
        const previewId = urlParams.get('preview');
        if (previewId) {
            openPreviewById(previewId);
        } else {
            const modal = document.getElementById('preview-modal');
            if (modal && !modal.classList.contains('hidden')) {
                closePreviewSilently();
            }
        }
    });
    function downloadFile(id) { window.location.href = BASE_URL + 'document/download/' + id; }
    function shareFile(id) { showInfo('Share file ' + id + ' segera hadir (Phase 7)'); }
    function showVersions(id) { showInfo('History versi file ' + id + ' segera hadir (Phase 7)'); }

    function renameFile(id, currentName) {
        Swal.fire({
            title: 'Rename File',
            input: 'text',
            inputValue: currentName,
            inputPlaceholder: 'Nama file baru...',
            showCancelButton: true,
            confirmButtonColor: '#2563EB',
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' },
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'Nama file tidak boleh kosong!';
                }
            },
            preConfirm: async (value) => {
                try {
                    const response = await fetch(BASE_URL + 'document/rename', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF_TOKEN },
                        body: JSON.stringify({ document_id: id, new_name: value.trim() })
                    });
                    return await response.json();
                } catch (error) {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.status) {
                    showSuccess(result.value.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showError(result.value.message);
                }
            }
        });
    }

    async function deleteFile(id) {
        if (await confirmAction('Hapus File?', 'File akan dipindahkan ke Recycle Bin.', 'warning')) {
            showLoading('Menghapus...');
            const data = await fetchAPI('document/delete', { method: 'POST', body: { document_id: id } });
            hideLoading();
            if (data && data.status) {
                showSuccess(data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showError(data ? data.message : 'Gagal menghapus');
            }
        }
    }

    function showNewFolderModal() {
        Swal.fire({
            title: 'Folder Baru',
            input: 'text',
            inputPlaceholder: 'Nama folder...',
            showCancelButton: true,
            confirmButtonColor: '#2563EB',
            confirmButtonText: 'Buat Folder',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' },
            inputValidator: (value) => { if (!value) return 'Nama folder tidak boleh kosong!'; },
            preConfirm: async (value) => {
                try {
                    const response = await fetch(BASE_URL + 'folder/create', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF_TOKEN },
                        body: JSON.stringify({ name: value, drive_id: <?= $drive->id ?>, parent_id: '<?= $current_folder_id ?>' })
                    });
                    return await response.json();
                } catch (error) {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.status) {
                    showSuccess(result.value.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showError(result.value.message);
                }
            }
        });
    }

    function renameFolder(id, currentName) {
        Swal.fire({
            title: 'Rename Folder',
            input: 'text',
            inputValue: currentName,
            inputPlaceholder: 'Nama folder baru...',
            showCancelButton: true,
            confirmButtonColor: '#2563EB',
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' },
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'Nama folder tidak boleh kosong!';
                }
            },
            preConfirm: async (value) => {
                try {
                    const response = await fetch(BASE_URL + 'folder/rename', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': CSRF_TOKEN },
                        body: JSON.stringify({ folder_id: id, new_name: value.trim() })
                    });
                    return await response.json();
                } catch (error) {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.status) {
                    showSuccess(result.value.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showError(result.value.message);
                }
            }
        });
    }

    async function deleteFolder(id) {
        if (await confirmAction('Hapus Folder?', 'Folder akan dipindahkan ke Recycle Bin.', 'warning')) {
            showLoading('Menghapus...');
            const data = await fetchAPI('folder/delete', { method: 'POST', body: { folder_id: id } });
            hideLoading();
            if (data && data.status) {
                showSuccess(data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                showError(data ? data.message : 'Gagal menghapus folder');
            }
        }
    }

    // --- File Search/Filter Logic ---
    const fileSearchInput = document.getElementById('file-search-input');
    const fileSearchClear = document.getElementById('file-search-clear');
    const fileSearchCount = document.getElementById('file-search-count');
    const fileMatchCount = document.getElementById('file-match-count');

    if (fileSearchInput) {
        let debounceTimer;
        fileSearchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => filterFiles(this.value), 150);
        });

        // Ctrl+F focus ke search input
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                if (fileSearchInput) {
                    e.preventDefault();
                    fileSearchInput.focus();
                    fileSearchInput.select();
                }
            }
        });

        // Escape untuk clear search
        fileSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearFileSearch();
                this.blur();
            }
        });
    }

    function filterFiles(query) {
        const rows = document.querySelectorAll('tr.file-row');
        const keyword = query.trim().toLowerCase();

        // Toggle clear button
        if (fileSearchClear) {
            fileSearchClear.classList.toggle('hidden', keyword === '');
        }

        if (keyword === '') {
            rows.forEach(row => {
                row.style.display = '';
                unhighlightText(row);
            });
            if (fileSearchCount) fileSearchCount.classList.add('hidden');
            showNoResultMessage(false);
            return;
        }

        let matchCount = 0;
        rows.forEach(row => {
            const filename = row.getAttribute('data-filename') || '';
            if (filename.includes(keyword)) {
                row.style.display = '';
                highlightText(row, keyword);
                matchCount++;
            } else {
                row.style.display = 'none';
                unhighlightText(row);
            }
        });

        // Update counter
        if (fileSearchCount) {
            fileSearchCount.classList.remove('hidden');
            fileMatchCount.textContent = matchCount;
        }

        showNoResultMessage(matchCount === 0 && rows.length > 0);
    }

    function highlightText(row, keyword) {
        const nameEl = row.querySelector('p.font-medium');
        if (!nameEl) return;
        const original = nameEl.getAttribute('data-original') || nameEl.textContent;
        nameEl.setAttribute('data-original', original);
        const escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escaped})`, 'gi');
        nameEl.innerHTML = original.replace(regex, '<mark class="bg-yellow-200 text-yellow-900 rounded px-0.5">$1</mark>');
    }

    function unhighlightText(row) {
        const nameEl = row.querySelector('p.font-medium');
        if (!nameEl) return;
        const original = nameEl.getAttribute('data-original');
        if (original) nameEl.textContent = original;
    }

    function showNoResultMessage(show) {
        let noResult = document.getElementById('no-search-result');
        if (show) {
            if (!noResult) {
                const tbody = document.querySelector('.table-light tbody');
                if (tbody) {
                    const tr = document.createElement('tr');
                    tr.id = 'no-search-result';
                    tr.innerHTML = `
                        <td colspan="6" class="text-center py-8 text-edrive-muted">
                            <i class="fa-solid fa-magnifying-glass text-2xl mb-2 text-gray-300 block"></i>
                            <p class="text-sm">Tidak ada file yang cocok dengan pencarian</p>
                        </td>
                    `;
                    tbody.appendChild(tr);
                }
            }
        } else {
            if (noResult) noResult.remove();
        }
    }

    function clearFileSearch() {
        if (fileSearchInput) {
            fileSearchInput.value = '';
            filterFiles('');
            fileSearchInput.focus();
        }
    }
</script>

<!-- Folder Context Menu -->
<div id="folderContextMenu" class="context-menu hidden fixed bg-white shadow-lg rounded-xl border border-gray-200 z-50 overflow-hidden w-48 text-sm">
    <button class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 text-gray-700" onclick="triggerRenameFolder()">
        <i class="fa-solid fa-i-cursor text-blue-500 w-4"></i> Rename Folder
    </button>
    <div class="border-t border-gray-100 my-1"></div>
    <button class="w-full text-left px-4 py-2 hover:bg-red-50 flex items-center gap-2 text-red-600" onclick="triggerDeleteFolder()">
        <i class="fa-solid fa-trash w-4"></i> Hapus
    </button>
</div>

<!-- Move File Modal -->
<div id="move-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="move-modal-content">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-folder-tree text-blue-500"></i> Pindahkan File
            </h3>
            <button onclick="closeMoveModal()" class="text-gray-400 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="mb-5 p-3 bg-blue-50 text-blue-800 rounded-lg border border-blue-100 flex gap-3 text-sm">
                <i class="fa-solid fa-circle-info mt-0.5"></i>
                <p>Pilih folder tujuan untuk memindahkan <strong id="move-file-name" class="break-all font-medium"></strong></p>
            </div>
            
            <form id="move-form" onsubmit="submitMoveFile(event)">
                <input type="hidden" id="move-document-id" name="document_id">
                <input type="hidden" id="move-current-drive-id" value="<?= $drive->id ?>">
                
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Folder Tujuan</label>
                    <div class="relative">
                        <select id="move-target-folder" name="target_folder_id" class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-gray-50 text-gray-700 font-medium cursor-pointer" required>
                            <option value="">Memuat folder...</option>
                        </select>
                        <i class="fa-solid fa-folder-open absolute left-3.5 top-3.5 text-gray-400"></i>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-3.5 text-gray-400 text-sm pointer-events-none"></i>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeMoveModal()" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-move" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm shadow-blue-200 flex items-center gap-2 transition-all">
                        <i class="fa-solid fa-angles-right"></i> Pindahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-gray-900/90 z-[100] hidden flex flex-col backdrop-blur-sm transition-all duration-300" onclick="if(event.target === this || event.target.id === 'preview-content-area') closePreview()">
    <div class="flex items-center justify-between p-4 bg-gray-900 border-b border-gray-700 text-white">
        <h3 id="preview-modal-title" class="font-medium text-lg truncate pr-4">Preview Document</h3>
        <button onclick="closePreview()" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-800 transition">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>
    <div class="flex-1 p-6 overflow-hidden flex items-center justify-center relative" id="preview-content-area"></div>
</div>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>