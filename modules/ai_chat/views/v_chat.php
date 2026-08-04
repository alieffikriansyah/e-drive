<?php require_once APPPATH . 'views/layout/header.php'; ?>

<!-- Marked.js & Highlight.js for Markdown & Code Highlighting -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

<div class="flex h-[calc(100vh-8.5rem)] bg-white rounded-2xl border border-edrive-border overflow-hidden shadow-sm">

    <!-- ===== LEFT SIDEBAR: CONVERSATION HISTORY ===== -->
    <div class="w-80 border-r border-edrive-border bg-slate-50/70 flex flex-col shrink-0">
        
        <!-- Header & New Chat Button -->
        <div class="p-4 border-b border-edrive-border space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-edrive-muted">
                    <?= $assistant_type === 'general' ? '🤖 AI Assistant' : '📁 E-Drive Assistant' ?>
                </span>
                <a href="<?= site_url('ai_setting') ?>" class="text-edrive-muted hover:text-edrive-accent text-xs" title="Settings">
                    <i class="fa-solid fa-gear"></i>
                </a>
            </div>
            
            <button onclick="createNewChat()" class="btn-primary w-full justify-center !py-2.5 shadow-sm">
                <i class="fa-solid fa-plus"></i> New Chat
            </button>
        </div>

        <!-- Conversation List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1" id="conversation-list">
            <?php if (empty($conversations)): ?>
                <div class="text-center py-8 text-edrive-muted text-xs">
                    Belum ada riwayat percakapan.
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $c): ?>
                    <?php $isActive = ($c->id == $active_conv_id); ?>
                    <div class="group relative flex items-center justify-between p-2.5 rounded-xl cursor-pointer transition-all text-sm <?= $isActive ? 'bg-white text-edrive-accent font-semibold shadow-sm border border-slate-200' : 'text-edrive-text hover:bg-slate-200/50' ?>"
                         onclick="selectConversation(<?= $c->id ?>)">
                        
                        <div class="flex items-center gap-2.5 truncate flex-1 pr-6">
                            <i class="fa-regular fa-message text-xs <?= $isActive ? 'text-edrive-accent' : 'text-edrive-muted' ?>"></i>
                            <span class="truncate text-xs" id="conv-title-<?= $c->id ?>" title="<?= htmlspecialchars($c->title) ?>">
                                <?= htmlspecialchars($c->title) ?>
                            </span>
                        </div>

                        <!-- Action Buttons (Hover) -->
                        <div class="absolute right-2 hidden group-hover:flex items-center gap-1 bg-gradient-to-l from-slate-100 via-slate-100 to-transparent pl-3">
                            <button onclick="event.stopPropagation(); renameConversation(<?= $c->id ?>, '<?= htmlspecialchars(addslashes($c->title)) ?>')" 
                                    class="p-1 text-slate-400 hover:text-slate-700 rounded" title="Rename">
                                <i class="fa-solid fa-pen text-[10px]"></i>
                            </button>
                            <button onclick="event.stopPropagation(); deleteConversation(<?= $c->id ?>)" 
                                    class="p-1 text-slate-400 hover:text-red-600 rounded" title="Delete">
                                <i class="fa-solid fa-trash text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Assistant Switcher Footer -->
        <div class="p-3 border-t border-edrive-border bg-white flex gap-2">
            <a href="<?= site_url('ai_chat/general') ?>" 
               class="flex-1 py-1.5 px-2 text-center text-xs font-semibold rounded-lg border transition-all <?= $assistant_type === 'general' ? 'bg-violet-50 text-violet-700 border-violet-200' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' ?>">
                🤖 General AI
            </a>
            <a href="<?= site_url('ai_chat/edrive') ?>" 
               class="flex-1 py-1.5 px-2 text-center text-xs font-semibold rounded-lg border transition-all <?= $assistant_type === 'edrive' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' ?>">
                📁 E-Drive AI
            </a>
        </div>
    </div>

    <!-- ===== RIGHT MAIN PANEL: CHAT INTERFACE ===== -->
    <div class="flex-1 flex flex-col bg-white">

        <!-- Active Chat Header -->
        <div class="h-14 border-b border-edrive-border px-6 flex items-center justify-between shrink-0 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg <?= $assistant_type === 'general' ? 'bg-violet-100 text-violet-600' : 'bg-emerald-100 text-emerald-600' ?> flex items-center justify-center font-bold">
                    <i class="<?= $assistant_type === 'general' ? 'fa-solid fa-robot' : 'fa-solid fa-folder-open' ?>"></i>
                </div>
                <div>
                    <h3 class="font-bold text-edrive-text text-sm" id="active-chat-title">
                        <?= $active_conv ? htmlspecialchars($active_conv->title) : 'Percakapan Baru' ?>
                    </h3>
                    <p class="text-[11px] text-edrive-muted">
                        <?= $assistant_type === 'general' ? 'Ollama (Qwen3) / General Knowledge' : 'E-Drive Assistant + RAG & Business Tools' ?>
                    </p>
                </div>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center gap-2">
                <button onclick="createNewChat()" class="btn-ghost text-xs">
                    <i class="fa-solid fa-rotate"></i> Reset
                </button>
            </div>
        </div>

        <!-- Message Area -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6" id="message-container">
            <?php if (empty($messages)): ?>
                <!-- Empty State Suggestion Box -->
                <div class="h-full flex flex-col items-center justify-center text-center max-w-lg mx-auto py-12">
                    <div class="w-16 h-16 rounded-2xl <?= $assistant_type === 'general' ? 'bg-violet-50 text-violet-600' : 'bg-emerald-50 text-emerald-600' ?> flex items-center justify-center text-2xl mb-4 shadow-sm">
                        <i class="<?= $assistant_type === 'general' ? 'fa-solid fa-wand-magic-sparkles' : 'fa-solid fa-hard-drive' ?>"></i>
                    </div>
                    <h2 class="text-lg font-bold text-edrive-text mb-2">
                        Ada yang bisa saya bantu hari ini?
                    </h2>
                    <p class="text-xs text-edrive-muted mb-6 leading-relaxed">
                        <?= $assistant_type === 'general' 
                            ? 'Tanyakan apa saja seputar topik umum, pemrograman, atau pembuatan konten.' 
                            : 'Tanyakan mengenai dokumen, kuota penyimpanan, cara penggunaan E-Drive, atau agenda jadwal.' ?>
                    </p>

                    <!-- Starter Suggestions -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full text-left">
                        <?php if ($assistant_type === 'general'): ?>
                            <button onclick="sendSuggestion('Jelaskan cara kerja Machine Learning secara sederhana')" 
                                    class="p-3 bg-slate-50 hover:bg-violet-50/50 border border-slate-200 hover:border-violet-200 rounded-xl text-xs text-slate-700 transition-all">
                                💡 <strong>Penjelasan Konsep</strong>
                                <span class="block text-slate-400 mt-1">Cara kerja Machine Learning...</span>
                            </button>
                            <button onclick="sendSuggestion('Buatkan email resmi izin tidak masuk kerja')" 
                                    class="p-3 bg-slate-50 hover:bg-violet-50/50 border border-slate-200 hover:border-violet-200 rounded-xl text-xs text-slate-700 transition-all">
                                ✉️ <strong>Draft Email</strong>
                                <span class="block text-slate-400 mt-1">Permohonan izin kerja...</span>
                            </button>
                        <?php else: ?>
                            <button onclick="sendSuggestion('Berapa total penyimpanan yang telah digunakan?')" 
                                    class="p-3 bg-slate-50 hover:bg-emerald-50/50 border border-slate-200 hover:border-emerald-200 rounded-xl text-xs text-slate-700 transition-all">
                                📊 <strong>Cek Storage</strong>
                                <span class="block text-slate-400 mt-1">Status penyimpanan E-Drive...</span>
                            </button>
                            <button onclick="sendSuggestion('Bagaimana cara membagikan folder ke pengguna lain?')" 
                                    class="p-3 bg-slate-50 hover:bg-emerald-50/50 border border-slate-200 hover:border-emerald-200 rounded-xl text-xs text-slate-700 transition-all">
                                📖 <strong>Panduan Akses</strong>
                                <span class="block text-slate-400 mt-1">Cara Share Drive/Folder...</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $m): ?>
                    <div class="flex gap-4 <?= $m->role === 'user' ? 'justify-end' : 'justify-start' ?>">
                        <?php if ($m->role !== 'user'): ?>
                            <div class="w-8 h-8 rounded-xl <?= $assistant_type === 'general' ? 'bg-violet-600' : 'bg-emerald-600' ?> text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm">
                                <i class="<?= $assistant_type === 'general' ? 'fa-solid fa-robot' : 'fa-solid fa-leaf' ?>"></i>
                            </div>
                        <?php endif; ?>

                        <div class="max-w-[80%] space-y-1">
                            <div class="p-4 rounded-2xl text-sm leading-relaxed <?= $m->role === 'user' ? 'bg-edrive-accent text-white rounded-br-none shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-800 rounded-bl-none markdown-body' ?>">
                                <?php if ($m->role === 'user'): ?>
                                    <?= nl2br(htmlspecialchars($m->content)) ?>
                                <?php else: ?>
                                    <div class="parsed-content" data-raw="<?= htmlspecialchars($m->content) ?>"></div>
                                <?php endif; ?>
                            </div>

                            <!-- Footer Metadata & Feedback -->
                            <?php if ($m->role === 'assistant'): ?>
                                <div class="flex items-center justify-between px-1 text-[11px] text-slate-400">
                                    <span><?= $m->response_time_ms ? $m->response_time_ms . 'ms' : '' ?></span>
                                    
                                    <?php if ($feedback_enabled): ?>
                                        <div class="flex items-center gap-2">
                                            <button onclick="sendFeedback(<?= $m->id ?>, 2)" 
                                                    class="hover:text-emerald-600 transition-colors <?= isset($m->user_rating) && $m->user_rating == 2 ? 'text-emerald-600 font-bold' : '' ?>" title="Bagus">
                                                <i class="fa-regular fa-thumbs-up"></i>
                                            </button>
                                            <button onclick="sendFeedback(<?= $m->id ?>, 1)" 
                                                    class="hover:text-red-600 transition-colors <?= isset($m->user_rating) && $m->user_rating == 1 ? 'text-red-600 font-bold' : '' ?>" title="Kurang Baik">
                                                <i class="fa-regular fa-thumbs-down"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($m->role === 'user'): ?>
                            <div class="w-8 h-8 rounded-xl bg-slate-800 text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm">
                                <?= strtoupper(substr(Session::get('name') ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Input Area -->
        <div class="p-4 border-t border-edrive-border bg-white">
            <form id="chatForm" onsubmit="submitChat(event)" class="relative flex items-center">
                <textarea id="userInput" 
                          rows="1" 
                          placeholder="Ketik pesan Anda di sini... (Shift + Enter untuk baris baru)" 
                          class="w-full pl-4 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-edrive-accent focus:bg-white resize-none max-h-32 transition-all"
                          onkeydown="handleKeyDown(event)"></textarea>

                <button type="submit" id="sendBtn" class="absolute right-2 p-2 w-9 h-9 bg-edrive-accent text-white rounded-lg hover:bg-blue-700 transition-all flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
            <p class="text-[10px] text-center text-slate-400 mt-2">
                AI dapat membuat kesalahan. Periksa informasi penting secara mandiri.
            </p>
        </div>
    </div>
</div>

<script>
let currentConvId = <?= (int)$active_conv_id ?>;
const assistantType = '<?= $assistant_type ?>';

document.addEventListener('DOMContentLoaded', () => {
    renderMarkdown();
    scrollToBottom();
});

function renderMarkdown() {
    document.querySelectorAll('.parsed-content').forEach(el => {
        const raw = el.getAttribute('data-raw');
        if (raw) {
            el.innerHTML = marked.parse(raw);
            el.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightElement(block);
            });
        }
    });
}

function scrollToBottom() {
    const container = document.getElementById('message-container');
    container.scrollTop = container.scrollHeight;
}

function handleKeyDown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        submitChat(e);
    }
}

function sendSuggestion(text) {
    document.getElementById('userInput').value = text;
    submitChat(new Event('submit'));
}

async function submitChat(e) {
    if (e) e.preventDefault();
    
    const input = document.getElementById('userInput');
    const message = input.value.trim();
    if (!message) return;

    // Clear input & disable send button
    input.value = '';
    input.style.height = 'auto';
    const sendBtn = document.getElementById('sendBtn');
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i>';

    // Append User Message to UI
    appendUserMessage(message);
    scrollToBottom();

    // Append Typing Indicator
    const typingId = appendTypingIndicator();
    scrollToBottom();

    // Send payload
    const formData = new FormData();
    formData.append('message', message);
    formData.append('assistant_type', assistantType);
    formData.append('conversation_id', currentConvId);

    try {
        const response = await fetch('<?= site_url('ai_chat/api_send') ?>', {
            method: 'POST',
            body: formData
        });
        const res = await response.json();

        // Remove typing indicator
        document.getElementById(typingId)?.remove();

        if (res.status) {
            currentConvId = res.conversation_id;
            appendAssistantMessage(res.reply, res.response_time_ms, res.assistant_message_id);
            
            // Update URL without reload if it was a new conversation
            const currentUrl = new URL(window.location.href);
            if (!currentUrl.searchParams.has('c')) {
                currentUrl.searchParams.set('c', currentConvId);
                window.history.pushState({}, '', currentUrl);
            }
        } else {
            appendAssistantMessage('⚠️ **Terjadi Kesalahan:** ' + (res.message || 'Gagal terhubung ke AI.'));
        }
    } catch (err) {
        document.getElementById(typingId)?.remove();
        appendAssistantMessage('⚠️ **Gagal mengirim pesan.** Periksa koneksi server Anda.');
    } finally {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fa-solid fa-paper-plane text-xs"></i>';
        scrollToBottom();
    }
}

function appendUserMessage(text) {
    const container = document.getElementById('message-container');
    // If starter suggestion box exists, clear it
    if (container.querySelector('.max-w-lg')) {
        container.innerHTML = '';
    }
    const userHtml = `
        <div class="flex gap-4 justify-end">
            <div class="max-w-[80%] space-y-1">
                <div class="p-4 rounded-2xl text-sm leading-relaxed bg-edrive-accent text-white rounded-br-none shadow-sm">
                    ${escapeHtml(text).replace(/\n/g, '<br>')}
                </div>
            </div>
            <div class="w-8 h-8 rounded-xl bg-slate-800 text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm">
                ME
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', userHtml);
}

function appendAssistantMessage(rawText, responseTime = null, msgId = null) {
    const container = document.getElementById('message-container');
    const parsedText = marked.parse(rawText);
    const iconClass = assistantType === 'general' ? 'fa-robot' : 'fa-leaf';
    const bgClass = assistantType === 'general' ? 'bg-violet-600' : 'bg-emerald-600';

    const aiHtml = `
        <div class="flex gap-4 justify-start">
            <div class="w-8 h-8 rounded-xl ${bgClass} text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm">
                <i class="fa-solid ${iconClass}"></i>
            </div>
            <div class="max-w-[80%] space-y-1">
                <div class="p-4 rounded-2xl text-sm leading-relaxed bg-slate-50 border border-slate-200 text-slate-800 rounded-bl-none markdown-body">
                    ${parsedText}
                </div>
                ${responseTime ? `<div class="text-[11px] text-slate-400 px-1">${responseTime}ms</div>` : ''}
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', aiHtml);
    renderMarkdown();
}

function appendTypingIndicator() {
    const container = document.getElementById('message-container');
    const id = 'typing-' + Date.now();
    const bgClass = assistantType === 'general' ? 'bg-violet-600' : 'bg-emerald-600';

    const typingHtml = `
        <div id="${id}" class="flex gap-4 justify-start items-center">
            <div class="w-8 h-8 rounded-xl ${bgClass} text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm animate-pulse">
                <i class="fa-solid fa-spinner fa-spin"></i>
            </div>
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl rounded-bl-none text-slate-400 text-xs flex items-center gap-1">
                AI sedang berpikir<span>.</span><span>.</span><span>.</span>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', typingHtml);
    return id;
}

function selectConversation(id) {
    window.location.href = '<?= site_url("ai_chat/") ?>' + assistantType + '?c=' + id;
}

async function createNewChat() {
    window.location.href = '<?= site_url("ai_chat/") ?>' + assistantType;
}

async function renameConversation(id, oldTitle) {
    const { value: newTitle } = await Swal.fire({
        title: 'Ubah Judul Chat',
        input: 'text',
        inputValue: oldTitle,
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        background: '#1e293b',
        color: '#fff'
    });

    if (newTitle && newTitle.trim()) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('title', newTitle.trim());

        const res = await fetch('<?= site_url("ai_chat/api_rename") ?>', { method: 'POST', body: formData });
        const json = await res.json();
        if (json.status) {
            document.getElementById('conv-title-' + id).innerText = newTitle.trim();
        }
    }
}

async function deleteConversation(id) {
    const res = await Swal.fire({
        title: 'Hapus Percakapan?',
        text: 'Riwayat percakapan ini akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        background: '#1e293b',
        color: '#fff'
    });

    if (res.isConfirmed) {
        const formData = new FormData();
        formData.append('id', id);
        await fetch('<?= site_url("ai_chat/api_delete") ?>', { method: 'POST', body: formData });
        window.location.href = '<?= site_url("ai_chat/") ?>' + assistantType;
    }
}

async function sendFeedback(msgId, rating) {
    const formData = new FormData();
    formData.append('message_id', msgId);
    formData.append('rating', rating);

    await fetch('<?= site_url("ai_chat/api_feedback") ?>', { method: 'POST', body: formData });
    Swal.fire({ icon: 'success', title: 'Feedback Terkirim', text: 'Terima kasih atas masukan Anda!', timer: 1200, showConfirmButton: false, background: '#1e293b', color: '#fff' });
}

function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
</script>

<?php require_once APPPATH . 'views/layout/footer.php'; ?>
