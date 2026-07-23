<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <!-- Breadcrumb is handled in header layout, just show title -->
            <h1 class="section-title text-2xl flex items-center gap-2">
                <?php if ($current_folder_id): ?>
                    <i class="fa-solid fa-folder-open text-yellow-500"></i>
                <?php else: ?>
                    <i class="<?= htmlspecialchars($drive->icon ?? 'fa-solid fa-hard-drive') ?>"
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
                            <?= $f->total_files ?> items
                        </p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Files Table -->
    <?php if (!empty($documents) || (empty($folders) && empty($documents))): ?>
        <div>
            <h3 class="text-sm font-semibold text-edrive-muted mb-4 uppercase tracking-wider">Files</h3>
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
                                <th class="w-48 pr-6 text-right">Aksi</th>
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
                                    <tr class="group">
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
                                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button class="w-8 h-8 rounded hover:bg-gray-100 text-blue-600 flex items-center justify-center transition tooltip" data-tip="Preview" onclick="previewFile(<?= $doc->id ?>, '<?= htmlspecialchars(addslashes($doc->name)) ?>', '<?= htmlspecialchars(addslashes($doc->file_path)) ?>')">
                                                    <i class="fa-solid fa-eye text-[13px]"></i>
                                                </button>
                                                <?php if (in_array(strtolower($doc->file_type), ['docx', 'xlsx', 'pptx'])): ?>
                                                <a href="<?= base_url('onlyoffice/edit/' . $doc->id) ?>" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded hover:bg-orange-50 text-orange-500 flex items-center justify-center transition tooltip" data-tip="Edit di ONLYOFFICE">
                                                    <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                                </a>
                                                <?php endif; ?>
                                                <button class="w-8 h-8 rounded hover:bg-gray-100 text-green-600 flex items-center justify-center transition tooltip" data-tip="Download" onclick="downloadFile(<?= $doc->id ?>)">
                                                    <i class="fa-solid fa-download text-[13px]"></i>
                                                </button>
                                                <button class="w-8 h-8 rounded hover:bg-gray-100 text-purple-600 flex items-center justify-center transition tooltip" data-tip="Share" onclick="shareFile(<?= $doc->id ?>)">
                                                    <i class="fa-solid fa-share-nodes text-[13px]"></i>
                                                </button>
                                                <button class="w-8 h-8 rounded hover:bg-gray-100 text-gray-600 flex items-center justify-center transition tooltip" data-tip="Versions" onclick="showVersions(<?= $doc->id ?>)">
                                                    <i class="fa-solid fa-code-commit text-[13px]"></i>
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

    function toggleContextMenu(e, id) {
        e.stopPropagation();
        document.querySelectorAll('.context-menu').forEach(menu => {
            if (menu.id !== id) menu.classList.add('hidden');
        });
        document.getElementById(id).classList.toggle('hidden');
    }

    // --- Actions ---
    function previewFile(id, name, path) {
        document.getElementById('preview-modal-title').innerText = name;
        document.getElementById('preview-modal').classList.remove('hidden');
        
        const contentArea = document.getElementById('preview-content-area');
        contentArea.innerHTML = '<div class="text-center p-10"><i class="fa-solid fa-circle-notch fa-spin text-4xl text-edrive-primary"></i><p class="mt-4 text-edrive-muted">Memuat pratinjau...</p></div>';
        
        const ext = path.split('.').pop().toLowerCase();
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
        }, 500);
    }

    function closePreview() {
        document.getElementById('preview-modal').classList.add('hidden');
        document.getElementById('preview-content-area').innerHTML = '';
    }
    function downloadFile(id) { window.location.href = BASE_URL + 'document/download/' + id; }
    function shareFile(id) { showInfo('Share file ' + id + ' segera hadir (Phase 7)'); }
    function showVersions(id) { showInfo('History versi file ' + id + ' segera hadir (Phase 7)'); }

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
</script>

<!-- Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-gray-900/90 z-[100] hidden flex flex-col backdrop-blur-sm transition-all duration-300">
    <div class="flex items-center justify-between p-4 bg-gray-900 border-b border-gray-700 text-white">
        <h3 id="preview-modal-title" class="font-medium text-lg truncate pr-4">Preview Document</h3>
        <button onclick="closePreview()" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-800 transition">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>
    <div class="flex-1 p-6 overflow-hidden flex items-center justify-center relative" id="preview-content-area"></div>
</div>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>