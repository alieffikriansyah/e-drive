<?php
// Helper function to get setting value from settings array
function _getAiSetting($key) {
    global $_ai_settings_map;
    return $_ai_settings_map[$key] ?? '';
}
// Build lookup map
$_ai_settings_map = [];
foreach ($settings as $s) {
    $_ai_settings_map[$s->key] = $s->value;
}
require_once APPPATH . 'views/layout/header.php';
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-edrive-text"><i class="fa-solid fa-sliders text-edrive-accent mr-1"></i> AI Settings</h1>
            <p class="text-edrive-muted mt-1">Konfigurasi AI Assistant, model, dan integrasi.</p>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit" id="settings-tabs">
        <button onclick="switchTab('connection')" class="tab-btn active" data-tab="connection">
            <i class="fa-solid fa-plug"></i> Koneksi
        </button>
        <button onclick="switchTab('model')" class="tab-btn" data-tab="model">
            <i class="fa-solid fa-brain"></i> Model
        </button>
        <button onclick="switchTab('prompts')" class="tab-btn" data-tab="prompts">
            <i class="fa-solid fa-message"></i> Prompts
        </button>
        <button onclick="switchTab('tools')" class="tab-btn" data-tab="tools">
            <i class="fa-solid fa-wrench"></i> Tools
        </button>
        <button onclick="switchTab('security')" class="tab-btn" data-tab="security">
            <i class="fa-solid fa-shield-halved"></i> Security
        </button>
    </div>

    <!-- Tab Content -->
    <form id="settingsForm">

        <!-- Connection Tab -->
        <div class="tab-content active" id="tab-connection">
            <div class="glass-card p-6 space-y-6">
                <h3 class="text-lg font-bold text-edrive-text flex items-center gap-2">
                    <i class="fa-solid fa-plug text-edrive-accent"></i> Koneksi External Services
                </h3>

                <!-- Flowise -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-edrive-text"><i class="fa-solid fa-diagram-project text-blue-500 mr-1"></i> Flowise</h4>
                        <button type="button" onclick="testConnection('flowise')" class="text-xs btn-secondary !py-1 !px-3">
                            <i class="fa-solid fa-wifi"></i> Test
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-edrive-muted block mb-1">Server URL</label>
                            <input type="text" name="settings[flowise_url]" value="<?= htmlspecialchars(_getAiSetting( 'flowise_url')) ?>" class="input-field" placeholder="http://localhost:3000">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-edrive-muted block mb-1">API Key</label>
                            <input type="password" name="settings[flowise_api_key]" value="<?= htmlspecialchars(_getAiSetting( 'flowise_api_key')) ?>" class="input-field" placeholder="Optional">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-edrive-muted block mb-1">Flow A ID (General AI)</label>
                            <input type="text" name="settings[flowise_flow_a_id]" value="<?= htmlspecialchars(_getAiSetting( 'flowise_flow_a_id')) ?>" class="input-field" placeholder="Flowise chatflow ID">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-edrive-muted block mb-1">Flow B ID (E-Drive Assistant)</label>
                            <input type="text" name="settings[flowise_flow_b_id]" value="<?= htmlspecialchars(_getAiSetting( 'flowise_flow_b_id')) ?>" class="input-field" placeholder="Flowise chatflow ID">
                        </div>
                    </div>
                    <div id="flowise-status" class="text-xs text-edrive-muted hidden"></div>
                </div>

                <!-- Ollama -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-edrive-text"><i class="fa-solid fa-brain text-purple-500 mr-1"></i> Ollama</h4>
                        <button type="button" onclick="testConnection('ollama')" class="text-xs btn-secondary !py-1 !px-3">
                            <i class="fa-solid fa-wifi"></i> Test
                        </button>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-edrive-muted block mb-1">Server URL</label>
                        <input type="text" name="settings[ollama_url]" value="<?= htmlspecialchars(_getAiSetting( 'ollama_url')) ?>" class="input-field" placeholder="http://localhost:11434">
                    </div>
                    <div id="ollama-status" class="text-xs text-edrive-muted hidden"></div>
                </div>

                <!-- Cloud LLM API (Groq / OpenRouter / OpenAI) -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-edrive-text"><i class="fa-solid fa-cloud text-amber-500 mr-1"></i> Direct Cloud LLM API (Groq / OpenRouter / OpenAI)</h4>
                        <span class="badge badge-success">Dynamic LLM</span>
                    </div>
                    <p class="text-xs text-edrive-muted">Opsikan ini jika Anda ingin AI langsung menjawab secara 100% dinamis menggunakan API Key Cloud tanpa perlu menjalankan Flowise/Ollama di laptop.</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-edrive-muted block mb-1">API Endpoint URL</label>
                            <input type="text" name="settings[cloud_api_url]" value="<?= htmlspecialchars(_getAiSetting( 'cloud_api_url')) ?: 'https://api.groq.com/openai/v1' ?>" class="input-field" placeholder="https://api.groq.com/openai/v1">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-edrive-muted block mb-1">API Key</label>
                            <input type="text" name="settings[cloud_api_key]" value="<?= htmlspecialchars(_getAiSetting( 'cloud_api_key')) ?>" class="input-field" placeholder="gsk_... / sk-or-v1-...">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-edrive-muted block mb-1">Model Name</label>
                            <input type="text" name="settings[cloud_model]" value="<?= htmlspecialchars(_getAiSetting( 'cloud_model')) ?: 'llama-3.3-70b-versatile' ?>" class="input-field" placeholder="llama-3.3-70b-versatile / gpt-4o-mini">
                        </div>
                    </div>
                </div>

                <!-- Vector DB -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
                    <h4 class="font-semibold text-edrive-text"><i class="fa-solid fa-database text-emerald-500 mr-1"></i> Vector Database</h4>¸ Vector Database</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-edrive-muted block mb-1">Type</label>
                            <select name="settings[vector_db_type]" class="input-field">
                                <option value="chromadb" <?= _getAiSetting( 'vector_db_type') === 'chromadb' ? 'selected' : '' ?>>ChromaDB</option>
                                <option value="qdrant" <?= _getAiSetting( 'vector_db_type') === 'qdrant' ? 'selected' : '' ?>>Qdrant</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-edrive-muted block mb-1">URL</label>
                            <input type="text" name="settings[vector_db_url]" value="<?= htmlspecialchars(_getAiSetting( 'vector_db_url')) ?>" class="input-field" placeholder="http://localhost:8000">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Model Tab -->
        <div class="tab-content" id="tab-model" style="display:none;">
            <div class="glass-card p-6 space-y-6">
                <h3 class="text-lg font-bold text-edrive-text flex items-center gap-2">
                    <i class="fa-solid fa-brain text-violet-500"></i> Model Configuration
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs font-semibold text-edrive-muted block mb-1">Default Model (General AI)</label>
                        <input type="text" name="settings[default_model]" value="<?= htmlspecialchars(_getAiSetting( 'default_model')) ?>" class="input-field" placeholder="qwen3">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-edrive-muted block mb-1">E-Drive Model</label>
                        <input type="text" name="settings[edrive_model]" value="<?= htmlspecialchars(_getAiSetting( 'edrive_model')) ?>" class="input-field" placeholder="qwen3">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-edrive-muted block mb-1">Max Tokens</label>
                        <input type="number" name="settings[max_tokens]" value="<?= htmlspecialchars(_getAiSetting( 'max_tokens')) ?>" class="input-field" min="256" max="8192">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-edrive-muted block mb-1">Temperature (0-1)</label>
                        <input type="number" name="settings[temperature]" value="<?= htmlspecialchars(_getAiSetting( 'temperature')) ?>" class="input-field" min="0" max="1" step="0.1">
                    </div>
                </div>
            </div>
        </div>

        <!-- Prompts Tab -->
        <div class="tab-content" id="tab-prompts" style="display:none;">
            <div class="space-y-4">
                <?php foreach ($prompts as $prompt): ?>
                <div class="glass-card p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="font-semibold text-edrive-text"><?= htmlspecialchars($prompt->name) ?></h4>
                            <span class="badge <?= $prompt->assistant_type === 'general' ? 'badge-primary' : 'badge-success' ?>">
                                <?= $prompt->assistant_type === 'general' ? 'ðŸ¤– General' : 'ðŸ“ E-Drive' ?>
                            </span>
                        </div>
                        <button type="button" onclick="savePrompt(<?= $prompt->id ?>)" class="btn-primary !py-1.5 !px-4 !text-xs">
                            <i class="fa-solid fa-save"></i> Simpan
                        </button>
                    </div>
                    <textarea id="prompt-<?= $prompt->id ?>" rows="6" class="input-field font-mono text-xs"><?= htmlspecialchars($prompt->content) ?></textarea>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Tools Tab -->
        <div class="tab-content" id="tab-tools" style="display:none;">
            <div class="glass-card overflow-hidden">
                <div class="p-5 border-b border-edrive-border">
                    <h3 class="text-lg font-bold text-edrive-text flex items-center gap-2">
                        <i class="fa-solid fa-wrench text-amber-500"></i> Registered AI Tools
                    </h3>
                    <p class="text-xs text-edrive-muted mt-1">Tools yang terdaftar sebagai endpoint untuk Flowise. Enable/disable sesuai kebutuhan.</p>
                </div>
                <table class="table-light">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Endpoint</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tools as $tool): ?>
                        <tr id="tool-row-<?= $tool->id ?>">
                            <td>
                                <p class="font-medium text-edrive-text"><?= htmlspecialchars($tool->name) ?></p>
                                <p class="text-xs text-edrive-muted line-clamp-1"><?= htmlspecialchars($tool->description) ?></p>
                            </td>
                            <td><code class="text-xs bg-slate-100 px-2 py-1 rounded"><?= htmlspecialchars($tool->endpoint) ?></code></td>
                            <td><span class="badge badge-info"><?= $tool->method ?></span></td>
                            <td>
                                <span id="tool-status-<?= $tool->id ?>" class="badge <?= $tool->is_active ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $tool->is_active ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button type="button" onclick="toggleTool(<?= $tool->id ?>)" class="btn-icon text-sm" title="Toggle">
                                    <i class="fa-solid fa-power-off"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Security Tab -->
        <div class="tab-content" id="tab-security" style="display:none;">
            <div class="glass-card p-6 space-y-6">
                <h3 class="text-lg font-bold text-edrive-text flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-red-500"></i> Security & Limits
                </h3>

                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
                    <h4 class="font-semibold text-edrive-text">ðŸ”‘ Internal API Key</h4>
                    <p class="text-xs text-edrive-muted">Key ini digunakan Flowise untuk mengakses REST API internal E-Drive.</p>
                    <div class="flex gap-3 items-end">
                        <div class="flex-1">
                            <input type="text" id="api-key-display" value="<?= htmlspecialchars(_getAiSetting( 'internal_api_key')) ?>" class="input-field font-mono text-xs" readonly>
                        </div>
                        <button type="button" onclick="generateApiKey()" class="btn-primary !py-2.5">
                            <i class="fa-solid fa-rotate"></i> Generate
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs font-semibold text-edrive-muted block mb-1">Rate Limit (req/menit/user)</label>
                        <input type="number" name="settings[rate_limit_per_minute]" value="<?= htmlspecialchars(_getAiSetting( 'rate_limit_per_minute')) ?>" class="input-field" min="1" max="300">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-edrive-muted block mb-1">History Retention (hari)</label>
                        <input type="number" name="settings[history_retention_days]" value="<?= htmlspecialchars(_getAiSetting( 'history_retention_days')) ?>" class="input-field" min="7" max="365">
                    </div>
                </div>

                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="settings[streaming_enabled]" value="0">
                        <input type="checkbox" name="settings[streaming_enabled]" value="1" <?= _getAiSetting( 'streaming_enabled') == '1' ? 'checked' : '' ?>
                            class="w-4 h-4 rounded border-slate-300 text-edrive-accent focus:ring-edrive-accent">
                        <span class="text-sm text-edrive-text">Enable Streaming Response (SSE)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="settings[feedback_enabled]" value="0">
                        <input type="checkbox" name="settings[feedback_enabled]" value="1" <?= _getAiSetting( 'feedback_enabled') == '1' ? 'checked' : '' ?>
                            class="w-4 h-4 rounded border-slate-300 text-edrive-accent focus:ring-edrive-accent">
                        <span class="text-sm text-edrive-text">Enable Feedback Buttons</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Save Button (for connection, model, security tabs) -->
        <div class="flex justify-end mt-6" id="save-bar">
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Semua Settings
            </button>
        </div>
    </form>
</div>

<style>
    .tab-btn {
        @apply px-4 py-2 rounded-lg text-sm font-medium text-edrive-muted transition-all flex items-center gap-2;
    }
    .tab-btn.active {
        @apply bg-white text-edrive-accent shadow-sm;
    }
    .tab-btn:hover:not(.active) {
        @apply text-edrive-text;
    }
</style>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tab).style.display = 'block';
    document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
    // Hide save bar on prompts/tools tab (they have individual save)
    document.getElementById('save-bar').style.display = ['prompts','tools'].includes(tab) ? 'none' : 'flex';
}

document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    try {
        const res = await fetch('<?= site_url('ai_setting/api_save') ?>', { method: 'POST', body: formData });
        const json = await res.json();
        await Swal.fire({ icon: json.status ? 'success' : 'error', title: json.status ? 'Berhasil' : 'Gagal', text: json.message, timer: 1200, showConfirmButton: false, background: '#1e293b', color: '#fff' });
        if (json.status) location.reload();
    } catch(err) { console.error(err); }
});

async function testConnection(service) {
    const el = document.getElementById(service + '-status');
    el.classList.remove('hidden');
    el.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Testing...';
    try {
        const res = await fetch('<?= site_url('ai_setting/api_test_connection') ?>?service=' + service);
        const json = await res.json();
        el.innerHTML = json.status
            ? `<span class="text-emerald-600">${json.message}</span>`
            : `<span class="text-red-500">${json.message}</span>`;
    } catch(err) {
        el.innerHTML = '<span class="text-red-500">Connection test failed.</span>';
    }
}

async function toggleTool(id) {
    const formData = new FormData();
    formData.append('id', id);
    try {
        const res = await fetch('<?= site_url('ai_setting/api_toggle_tool') ?>', { method: 'POST', body: formData });
        const json = await res.json();
        if (json.status) location.reload();
    } catch(err) { console.error(err); }
}

async function savePrompt(id) {
    const content = document.getElementById('prompt-' + id).value;
    const formData = new FormData();
    formData.append('id', id);
    formData.append('content', content);
    try {
        const res = await fetch('<?= site_url('ai_setting/api_save_prompt') ?>', { method: 'POST', body: formData });
        const json = await res.json();
        Swal.fire({ icon: json.status ? 'success' : 'error', title: json.status ? 'Berhasil' : 'Gagal', text: json.message, timer: 1500, showConfirmButton: false, background: '#1e293b', color: '#fff' });
    } catch(err) { console.error(err); }
}

async function generateApiKey() {
    const result = await Swal.fire({ title: 'Generate API Key Baru?', text: 'Key lama akan diganti.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#2563EB', confirmButtonText: 'Generate', background: '#1e293b', color: '#fff' });
    if (!result.isConfirmed) return;
    try {
        const res = await fetch('<?= site_url('ai_setting/api_generate_key') ?>', { method: 'POST' });
        const json = await res.json();
        if (json.status) {
            document.getElementById('api-key-display').value = json.key;
            Swal.fire({ icon: 'success', title: 'Berhasil', text: json.message, timer: 1500, showConfirmButton: false, background: '#1e293b', color: '#fff' });
        }
    } catch(err) { console.error(err); }
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
