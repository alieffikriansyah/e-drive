<?php require_once APPPATH . 'views/layout/header.php'; ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-edrive-text">📁 AI Knowledge Base</h1>
            <p class="text-edrive-muted mt-1">Upload dan kelola dokumen referensi untuk E-Drive Assistant (RAG).</p>
        </div>
        <button onclick="openUploadModal()" class="btn-primary">
            <i class="fa-solid fa-cloud-arrow-up"></i> Upload Dokumen
        </button>
    </div>

    <!-- Document List Table -->
    <div class="glass-card overflow-hidden">
        <div class="p-5 border-b border-edrive-border flex items-center justify-between">
            <h3 class="font-bold text-edrive-text text-sm">Daftar Dokumen RAG</h3>
            <span class="text-xs text-edrive-muted">Total: <?= count($documents) ?> Dokumen</span>
        </div>

        <table class="table-light">
            <thead>
                <tr>
                    <th>Judul Dokumen</th>
                    <th>Kategori</th>
                    <th>Tipe</th>
                    <th>Ukuran</th>
                    <th>Chunks</th>
                    <th>Status Vector</th>
                    <th>Diunggah</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documents)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-8 text-edrive-muted">
                            Belum ada dokumen Knowledge Base yang diunggah.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td>
                                <p class="font-medium text-edrive-text"><?= htmlspecialchars($doc->title) ?></p>
                                <p class="text-xs text-edrive-muted"><?= htmlspecialchars($doc->file_name) ?></p>
                            </td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($doc->category) ?></span></td>
                            <td><span class="uppercase text-xs font-bold text-slate-500"><?= $doc->file_type ?></span></td>
                            <td class="text-xs text-slate-500"><?= format_file_size($doc->file_size) ?></td>
                            <td class="text-xs font-semibold text-slate-700"><?= $doc->chunk_count ?></td>
                            <td>
                                <?php
                                $badgeMap = [
                                    'completed' => 'badge-success',
                                    'pending' => 'badge-warning',
                                    'processing' => 'badge-info',
                                    'failed' => 'badge-danger'
                                ];
                                ?>
                                <span class="badge <?= $badgeMap[$doc->embedding_status] ?? 'badge-secondary' ?>">
                                    <?= ucfirst($doc->embedding_status) ?>
                                </span>
                            </td>
                            <td class="text-xs text-slate-400"><?= timeAgo($doc->created_at) ?></td>
                            <td class="text-center">
                                <button onclick="deleteDocument(<?= $doc->id ?>)" class="btn-icon text-red-500 hover:bg-red-50" title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="font-bold text-edrive-text text-sm">Upload Dokumen Knowledge Base</h3>
            <button onclick="closeUploadModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="uploadForm" onsubmit="submitUpload(event)">
            <div class="modal-body space-y-4">
                <div>
                    <label class="text-xs font-semibold text-edrive-muted block mb-1">Judul Dokumen</label>
                    <input type="text" name="title" class="input-field" placeholder="Contoh: SOP Penggunaan E-Drive 2026" required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-edrive-muted block mb-1">Kategori</label>
                    <select name="category" class="input-field">
                        <option value="SOP">SOP & Regulasi</option>
                        <option value="Tutorial">Tutorial & Panduan</option>
                        <option value="FAQ">FAQ & Problem Solving</option>
                        <option value="General" selected>Umum</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-edrive-muted block mb-1">Pilih File (PDF, DOCX, MD, HTML, TXT)</label>
                    <input type="file" name="file" accept=".pdf,.docx,.md,.html,.txt" class="input-field" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeUploadModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-upload"></i> Upload & Embed</button>
            </div>
        </form>
    </div>
</div>

<script>
function openUploadModal() { document.getElementById('uploadModal').style.display = 'flex'; }
function closeUploadModal() { document.getElementById('uploadModal').style.display = 'none'; }

async function submitUpload(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('uploadForm'));

    Swal.fire({
        title: 'Mengunggah Dokumen...',
        text: 'Mohon tunggu, proses ekstraksi & embedding sedang berjalan.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        const res = await fetch('<?= site_url("ai_document/api_upload") ?>', { method: 'POST', body: formData });
        const json = await res.json();

        if (json.status) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: json.message, timer: 1500, showConfirmButton: false });
            location.reload();
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: json.message });
        }
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan pada jaringan.' });
    }
}

async function deleteDocument(id) {
    const res = await Swal.fire({
        title: 'Hapus Dokumen?',
        text: 'Dokumen akan dihapus dari Knowledge Base RAG.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444'
    });

    if (res.isConfirmed) {
        const formData = new FormData();
        formData.append('id', id);
        const response = await fetch('<?= site_url("ai_document/api_delete") ?>', { method: 'POST', body: formData });
        const json = await response.json();
        if (json.status) location.reload();
    }
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
