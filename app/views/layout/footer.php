        </main>
        <!-- End Page Content -->
    </div>
    <!-- End Main Content Area -->
</div>
<!-- End App Layout -->

<!-- ===== Global JavaScript ===== -->
<script>
// ─── CSRF Token ───
const CSRF_TOKEN = '<?= (new Security())->generate_csrf_token() ?>';
const BASE_URL = '<?= base_url() ?>';

// ─── Mobile Navbar Toggle ───
function toggleMobileMenu() {
    const nav = document.getElementById('mobile-nav');
    if (nav) {
        nav.classList.toggle('hidden');
    }
}

// ─── User Menu Toggle ───
function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    if (menu) menu.classList.toggle('hidden');
}

// Close user menu on click outside
document.addEventListener('click', function(e) {
    const menu = document.getElementById('user-menu');
    if (menu && !menu.parentElement.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

// ─── Fetch API Wrapper ───
async function fetchAPI(url, options = {}) {
    const defaultHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-Token': CSRF_TOKEN,
    };

    if (!(options.body instanceof FormData)) {
        defaultHeaders['Content-Type'] = 'application/json';
    }

    const mergedOptions = {
        ...options,
        headers: { ...defaultHeaders, ...(options.headers || {}) },
    };
    
    if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
        mergedOptions.body = JSON.stringify(options.body);
    }

    try {
        const response = await fetch(BASE_URL + url, mergedOptions);
        if (response.status === 401) {
            window.location.href = BASE_URL + 'auth';
            return null;
        }
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { status: false, message: 'Koneksi bermasalah. Silakan coba lagi.' };
    }
}

// ─── Toast Notifications ───
function showToast(icon, title, timer = 3000) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: title,
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        customClass: {
            popup: 'rounded-xl shadow-lg',
        }
    });
}

function showSuccess(msg) { showToast('success', msg); }
function showError(msg)   { showToast('error', msg); }
function showWarning(msg) { showToast('warning', msg); }
function showInfo(msg)    { showToast('info', msg); }

// ─── Confirm Dialog ───
async function confirmAction(title, text, icon = 'warning') {
    const result = await Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#2563EB',
        cancelButtonColor: '#94A3B8',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl',
            cancelButton: 'rounded-xl',
        }
    });
    return result.isConfirmed;
}

// ─── Format File Size ───
function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// ─── Format Date ───
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

// ─── Time Ago ───
function timeAgo(dateStr) {
    if (!dateStr) return '-';
    const now = new Date();
    const date = new Date(dateStr);
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
    if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
    if (diff < 604800) return Math.floor(diff / 86400) + ' hari lalu';
    return formatDate(dateStr);
}

// ─── Keyboard Shortcuts ───
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        window.location.href = BASE_URL + 'search';
    }
});

// ─── Loading Overlay ───
function showLoading(text = 'Memproses...') {
    Swal.fire({
        title: text,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: { popup: 'rounded-2xl' },
        didOpen: () => { Swal.showLoading(); }
    });
}
function hideLoading() { Swal.close(); }

// ─── Responsive Tables ───
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table:not(.fc-scrollgrid)').forEach(table => {
        // Cek jika tabel sudah dibungkus elemen dengan class overflow
        if (!table.parentElement.classList.contains('overflow-x-auto')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'overflow-x-auto w-full';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
});
</script>

</body>
</html>
