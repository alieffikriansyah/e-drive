<?php require_once APPPATH . 'views/layout/header_auth.php'; ?>

<div class="relative z-10 w-full max-w-md">
    <!-- Login Card (All in one) -->
    <div class="bg-white rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] p-10 relative overflow-hidden">
        
        <!-- Logo & Title -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-24 h-20 mb-6 bg-gradient-to-b from-gray-50 to-gray-200 rounded-2xl shadow-[inset_0_2px_4px_rgba(255,255,255,1),0_5px_15px_rgba(0,0,0,0.1)] border border-gray-100">
                <img src="<?= site_url('assets/iass2.png') ?>" alt="Logo" class="w-12 h-12 object-contain drop-shadow-md">
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-[0.2em] uppercase">
                E-DRIVE
            </h1>
            <p class="text-[10px] font-semibold text-slate-500 mt-2 uppercase tracking-[0.15em]">Drive untuk IASS Staf Elektrikal</p>
        </div>

        <!-- Divider -->
        <div class="flex items-center justify-center w-3/4 mx-auto mb-8 opacity-70">
            <div class="h-px bg-slate-200 flex-1"></div>
            <div class="mx-3 w-1.5 h-1.5 bg-slate-400 rotate-45"></div>
            <div class="h-px bg-slate-200 flex-1"></div>
        </div>

        <?php $error = Session::flashdata('error'); ?>
        <?php if ($error): ?>
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-5">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('auth/process') ?>" method="POST" autocomplete="off">
            <?= csrf_field() ?>
            
            <!-- Username -->
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">
                    Username
                </label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="username" 
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400
                                  focus:bg-white focus:ring-2 focus:ring-slate-800/20 focus:border-slate-800 focus:outline-none transition-all text-sm font-medium"
                           placeholder="Enter your username" 
                           value="<?= set_value('username') ?>"
                           required autofocus>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-8">
                <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-wider">
                    Password
                </label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="password" name="password" id="password-field"
                           class="w-full pl-11 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-400
                                  focus:bg-white focus:ring-2 focus:ring-slate-800/20 focus:border-slate-800 focus:outline-none transition-all text-sm font-medium"
                           placeholder="Enter your password" required>
                    <button type="button" onclick="togglePassword()" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-2">
                        <i class="fa-solid fa-eye" id="toggle-pw-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Login Button -->
            <button type="submit" 
                    class="w-full bg-[#111827] hover:bg-black text-white font-bold py-3.5 px-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2 text-sm tracking-wide">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Login</span>
            </button>
        </form>
    </div>

    <!-- Footer -->
    <p class="text-center text-xs text-edrive-muted mt-6">
        &copy; <?= date('Y') ?> E-Drive — Enterprise Document Management System
    </p>
</div>

<script>
function togglePassword() {
    const field = document.getElementById('password-field');
    const icon = document.getElementById('toggle-pw-icon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php require_once APPPATH . 'views/layout/footer_auth.php'; ?>
