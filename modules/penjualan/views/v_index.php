<?php $this->load->view('layout/header', ['title' => 'Kasir / Point of Sale (POS)']); ?>

<!-- Load SweetAlert2 -->
<script src="<?= base_url('assets/js/sweetalert2.min.js') ?>"></script>

<style>
    /* Custom Scrollbar for Cart */
    .cart-container::-webkit-scrollbar {
        width: 6px;
    }

    .cart-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .cart-container::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    .cart-container::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Animation for adding item */
    @keyframes popIn {
        0% {
            transform: scale(0.9);
            opacity: 0;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .cart-item-anim {
        animation: popIn 0.3s ease-out forwards;
    }
</style>

<div class="flex flex-col md:flex-row h-[calc(100vh-140px)] gap-4 overflow-hidden">

    <!-- LEFT SIDE: MENU PRODUK -->
    <div
        class="w-full md:w-2/3 flex flex-col h-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Menu -->
        <div class="bg-gray-50 border-b border-gray-200 p-4 flex justify-between items-center z-10">
            <h2 class="text-xl font-extrabold text-mapul-green flex items-center gap-2">
                <i class="fa fa-utensils text-mapul-yellow"></i> Menu Resto
            </h2>
            <div class="relative w-64">
                <input type="text" id="searchMenu" onkeyup="filterMenu()" placeholder="Cari menu..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm focus:outline-none focus:border-mapul-yellow focus:ring-1 focus:ring-mapul-yellow transition-colors">
                <i class="fa fa-search absolute left-4 top-2.5 text-gray-400"></i>
            </div>
        </div>

        <?php if (in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin'])): ?>
            <div class="bg-yellow-50 text-yellow-800 p-3 text-xs border-b border-yellow-200 flex items-center gap-2">
                <i class="fa fa-info-circle"></i> Mode Owner: Pilih Cabang transaksi di panel Pembayaran. Menu di bawah
                adalah gabungan semua cabang.
            </div>
        <?php endif; ?>

        <!-- Menu Grid -->
        <div class="p-4 overflow-y-auto flex-1 bg-gray-50/50" id="menuContainer">
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="menuGrid">
                <?php if (empty($menu_kasir)): ?>
                    <div class="col-span-full text-center py-10 text-gray-500">
                        <i class="fa fa-box-open text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada menu produk jadi yang tersedia.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($menu_kasir as $menu): ?>
                        <div class="menu-card bg-white border border-gray-200 rounded-xl p-4 cursor-pointer hover:shadow-lg hover:border-mapul-yellow transition-all group flex flex-col justify-between"
                            onclick="addToCart(<?= $menu->id_harga_paten ?>, '<?= addslashes(htmlspecialchars($menu->nama_menu)) ?>', <?= (float)$menu->harga_jual_paten ?>)"
                            data-nama="<?= strtolower($menu->nama_menu) ?>"
                            data-kategori="<?= strtolower($menu->nama_kategori) ?>">

                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                    <?= htmlspecialchars($menu->nama_kategori) ?></div>
                                <h3
                                    class="font-bold text-gray-800 leading-tight mb-2 group-hover:text-mapul-green transition-colors">
                                    <?= htmlspecialchars($menu->nama_menu) ?></h3>
                            </div>

                            <div class="mt-4 flex justify-between items-end">
                                <span class="font-extrabold text-mapul-green-md">Rp
                                    <?= number_format($menu->harga_jual_paten, 0, ',', '.') ?></span>
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-medium"><i
                                        class="fa fa-plus text-mapul-yellow"></i></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: KERANJANG (CART) -->
    <div
        class="w-full md:w-1/3 flex flex-col h-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
        <div class="bg-mapul-green text-white p-4 flex justify-between items-center z-10 shadow-sm">
            <h2 class="text-lg font-bold flex items-center gap-2">
                <i class="fa fa-shopping-cart text-mapul-yellow"></i> Pesanan
            </h2>
            <button onclick="clearCart()"
                class="text-white hover:text-red-300 transition-colors text-sm font-medium bg-mapul-green-md px-3 py-1 rounded">
                <i class="fa fa-trash-alt mr-1"></i> Kosongkan
            </button>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4 cart-container space-y-3 bg-gray-50" id="cartContainer">
            <div id="emptyCartMsg" class="h-full flex flex-col items-center justify-center text-gray-400 opacity-70">
                <i class="fa fa-shopping-basket text-5xl mb-3"></i>
                <p class="font-medium">Keranjang masih kosong</p>
                <p class="text-xs mt-1">Silakan pilih menu di samping</p>
            </div>
            <!-- Cart items will be injected here by JS -->
        </div>

        <!-- Cart Summary & Action -->
        <div class="bg-white border-t border-gray-200 p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-10">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-500 font-bold">Subtotal</span>
                <span class="font-bold text-gray-700" id="cartSubtotal">Rp 0</span>
            </div>
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100 border-dashed">
                <span class="text-gray-500 font-bold">Total Diskon</span>
                <span class="font-bold text-red-500" id="cartDiskon">- Rp 0</span>
            </div>
            <div class="flex justify-between items-end mb-4">
                <span class="text-gray-800 font-extrabold text-lg">TOTAL</span>
                <span class="font-black text-3xl text-mapul-green-md" id="cartTotal">Rp 0</span>
            </div>
            <button onclick="openPaymentModal()" id="btnBayarUtama"
                class="w-full bg-mapul-green hover:bg-mapul-green-md text-white font-bold py-3 rounded-xl shadow-lg transition-all transform hover:scale-[1.02] flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                disabled>
                <i class="fa fa-wallet text-mapul-yellow text-xl"></i>
                <span class="text-lg tracking-wide uppercase">Bayar Sekarang</span>
            </button>
        </div>
    </div>
</div>

<!-- ================= MODAL PEMBAYARAN ================= -->
<div id="paymentModal"
    class="fixed inset-0 z-50 hidden bg-black bg-opacity-60 flex justify-center items-center backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-300 transform transition-all scale-95 opacity-0 duration-300"
        id="paymentModalDialog">

        <div class="bg-mapul-green text-white p-5 rounded-t-2xl flex justify-between items-center">
            <h3 class="text-xl font-bold flex items-center gap-2">
                <i class="fa fa-cash-register text-mapul-yellow"></i> Pembayaran
            </h3>
            <button onclick="closePaymentModal()"
                class="text-white hover:text-red-300 transition-colors focus:outline-none">
                <i class="fa fa-times text-2xl"></i>
            </button>
        </div>

        <div class="p-6">
            <div class="text-center mb-6 bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1">Total Tagihan</p>
                <p class="text-4xl font-black text-mapul-green-md" id="modalTotalTagihan">Rp 0</p>
            </div>

            <form id="paymentForm" onsubmit="processPayment(event)">

                <?php if (in_array(strtolower($_SESSION['role_name'] ?? ''), ['owner', 'admin'])): ?>
                    <!-- Pilihan Cabang Khusus Owner/Admin -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Pilih Cabang
                            Transaksi <span class="text-red-500">*</span></label>
                        <select id="pay_id_cabang" required
                            class="w-full border-2 border-yellow-400 rounded-lg p-2.5 focus:outline-none focus:border-mapul-green font-bold text-gray-700 bg-yellow-50">
                            <option value="">-- Pilih Cabang --</option>
                            <?php foreach ($cabang as $c): ?>
                            <option value="<?= $c->id ?>"><?= htmlspecialchars($c->nama_cabang) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Metode
                        Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_bayar" value="tunai" class="peer hidden" checked
                                onchange="toggleUangPasBtn()">
                            <div
                                class="border border-gray-200 rounded-lg p-2 text-center peer-checked:bg-mapul-green-md peer-checked:text-white peer-checked:border-mapul-green-md transition-colors font-bold text-sm">
                                <i class="fa fa-money-bill-wave block mb-1 text-lg"></i> Tunai
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_bayar" value="qris" class="peer hidden"
                                onchange="toggleUangPasBtn()">
                            <div
                                class="border border-gray-200 rounded-lg p-2 text-center peer-checked:bg-mapul-green-md peer-checked:text-white peer-checked:border-mapul-green-md transition-colors font-bold text-sm">
                                <i class="fa fa-qrcode block mb-1 text-lg"></i> QRIS
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_bayar" value="transfer" class="peer hidden"
                                onchange="toggleUangPasBtn()">
                            <div
                                class="border border-gray-200 rounded-lg p-2 text-center peer-checked:bg-mapul-green-md peer-checked:text-white peer-checked:border-mapul-green-md transition-colors font-bold text-sm">
                                <i class="fa fa-exchange-alt block mb-1 text-lg"></i> Transfer
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-5 relative">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Nominal Dibayar
                        <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500 font-bold">Rp</span>
                        <input type="text" id="pay_bayar_display"
                            onkeyup="formatCurrencyInput(this); calculateKembalian()" required
                            class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg font-bold text-xl text-gray-800 focus:outline-none focus:border-mapul-green transition-colors"
                            placeholder="0">
                        <input type="hidden" id="pay_bayar">

                        <button type="button" id="btnUangPas" onclick="setUangPas()"
                            class="absolute right-2 top-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold px-3 py-1.5 rounded transition-colors">
                            Uang Pas
                        </button>
                    </div>
                </div>

                <div class="mb-6 p-4 rounded-lg bg-gray-100 border border-gray-200 flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-500 uppercase tracking-widest">Kembalian</span>
                    <span class="text-2xl font-black text-gray-800" id="modalKembalian">Rp 0</span>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closePaymentModal()"
                        class="w-1/3 py-3 bg-white text-gray-700 font-bold rounded-xl border border-gray-300 hover:bg-gray-50 transition-colors shadow-sm">Batal</button>
                    <button type="submit" id="btnProsesBayar"
                        class="w-2/3 bg-mapul-green hover:bg-mapul-green-md text-white font-bold py-3 rounded-xl shadow-md transition-all flex justify-center items-center gap-2">
                        <i class="fa fa-check-circle text-mapul-yellow"></i> <span>Proses Transaksi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const baseUrl = '<?= base_url() ?>';
    let cart = []; // Array of objects: { id, nama, harga, qty, diskon }
    const role = '<?= strtolower($_SESSION['role_name'] ?? '') ?>';

    // Format Numbers
    function formatNumber(num) {
        return parseFloat(num).toLocaleString('id-ID');
    }

    function parseNumber(str) {
        return parseFloat(str.replace(/\./g, '').replace(/,/g, '')) || 0;
    }

    // Escape HTML to prevent XSS in JS
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Filter Menu (Search)
    function filterMenu() {
        const query = document.getElementById('searchMenu').value.toLowerCase();
        const cards = document.querySelectorAll('.menu-card');

        cards.forEach(card => {
            const nama = card.getAttribute('data-nama');
            const kat = card.getAttribute('data-kategori');
            if (nama.includes(query) || kat.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Cart Management
    function addToCart(id, nama, harga) {
        // Find if already exists
        const index = cart.findIndex(item => item.id === id);
        if (index > -1) {
            cart[index].qty += 1;
        } else {
            cart.push({
                id: id,
                nama: nama,
                harga: harga,
                qty: 1,
                diskon: 0
            });
        }
        renderCart();
    }

    function updateQty(id, change) {
        const index = cart.findIndex(item => item.id === id);
        if (index > -1) {
            cart[index].qty += change;
            if (cart[index].qty <= 0) {
                cart.splice(index, 1);
            }
            renderCart();
        }
    }

    function updateDiskon(id, inputElem) {
        const index = cart.findIndex(item => item.id === id);
        if (index > -1) {
            let val = parseNumber(inputElem.value);
            // Cap discount to max price * qty
            const maxDiskon = cart[index].harga * cart[index].qty;
            if (val > maxDiskon) val = maxDiskon;

            cart[index].diskon = val;

            // Format back to input
            if (val > 0) {
                inputElem.value = formatNumber(val);
            } else {
                inputElem.value = '';
            }
            renderCartSummary();
        }
    }

    function removeCartItem(id) {
        const index = cart.findIndex(item => item.id === id);
        if (index > -1) {
            cart.splice(index, 1);
            renderCart();
        }
    }

    function clearCart() {
        if (cart.length === 0) return;
        Swal.fire({
            title: 'Kosongkan Keranjang?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Kosongkan'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = [];
                renderCart();
            }
        });
    }

    function renderCart() {
        const container = document.getElementById('cartContainer');
        const emptyMsg = document.getElementById('emptyCartMsg');
        const btnBayar = document.getElementById('btnBayarUtama');

        // Remove existing items (except empty msg)
        const items = container.querySelectorAll('.cart-item');
        items.forEach(item => item.remove());

        if (cart.length === 0) {
            emptyMsg.style.display = 'flex';
            btnBayar.disabled = true;
            renderCartSummary();
            return;
        }

        emptyMsg.style.display = 'none';
        btnBayar.disabled = false;

        let html = '';
        cart.forEach(item => {
            const subtotal = (item.harga * item.qty);
            const isDiskonMode = item.diskon > 0;
            const finalPrice = subtotal - item.diskon;

            html += `
                <div class="cart-item cart-item-anim bg-white border border-gray-200 rounded-xl p-3 shadow-sm relative">
                    <button onclick="removeCartItem(${item.id})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 shadow transition-colors">
                        <i class="fa fa-times"></i>
                    </button>
                    
                    <div class="font-bold text-gray-800 mb-2 truncate pr-4">${escapeHtml(item.nama)}</div>
                    
                    <div class="flex justify-between items-center mb-2">
                        <div class="text-mapul-green-md font-bold text-sm">Rp ${formatNumber(item.harga)}</div>
                        
                        <div class="flex items-center bg-gray-100 rounded-lg p-0.5 border border-gray-200">
                            <button onclick="updateQty(${item.id}, -1)" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-white rounded hover:shadow-sm transition-all"><i class="fa fa-minus text-xs"></i></button>
                            <span class="w-8 text-center font-bold text-sm">${item.qty}</span>
                            <button onclick="updateQty(${item.id}, 1)" class="w-7 h-7 flex items-center justify-center text-mapul-green hover:bg-white rounded hover:shadow-sm transition-all"><i class="fa fa-plus text-xs"></i></button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between border-t border-gray-100 pt-2 mt-2">
                        <div class="flex items-center w-1/2">
                            <span class="text-[10px] text-gray-400 font-bold mr-1 uppercase">Disc</span>
                            <input type="text" placeholder="Rp" class="w-full text-xs border border-gray-200 rounded px-2 py-1 text-red-500 focus:outline-none focus:border-red-300 font-bold text-right" 
                                   value="${item.diskon > 0 ? formatNumber(item.diskon) : ''}" 
                                   onblur="updateDiskon(${item.id}, this)"
                                   onkeyup="if(event.key==='Enter') this.blur(); formatCurrencyInput(this)">
                        </div>
                        <div class="w-1/2 text-right">
                            ${isDiskonMode ? `<div class="text-[10px] text-gray-400 line-through">Rp ${formatNumber(subtotal)}</div>` : ''}
                            <div class="font-extrabold text-gray-800">Rp ${formatNumber(finalPrice)}</div>
                        </div>
                    </div>
                </div>
            `;
        });

        // Append HTML
        container.insertAdjacentHTML('beforeend', html);

        // Scroll to bottom
        container.scrollTop = container.scrollHeight;

        renderCartSummary();
    }

    function renderCartSummary() {
        let subtotal = 0;
        let diskon = 0;

        cart.forEach(item => {
            subtotal += (item.harga * item.qty);
            diskon += item.diskon;
        });

        const total = subtotal - diskon;

        document.getElementById('cartSubtotal').textContent = `Rp ${formatNumber(subtotal)}`;
        document.getElementById('cartDiskon').textContent = `- Rp ${formatNumber(diskon)}`;
        document.getElementById('cartTotal').textContent = `Rp ${formatNumber(total)}`;

        // Save to global for payment modal
        window.cartTotalFinal = total;
    }

    // Utilities for Input
    function formatCurrencyInput(input) {
        let val = input.value.replace(/\./g, '').replace(/[^0-9]/g, '');
        if (val !== '') {
            input.value = formatNumber(val);
        }
    }

    // ================= PAYMENT MODAL =================



    function openPaymentModal() {
        if (cart.length === 0) return;

        document.getElementById('modalTotalTagihan').textContent = `Rp ${formatNumber(window.cartTotalFinal)}`;

        const modal = document.getElementById('paymentModal');
        const dialog = document.getElementById('paymentModalDialog');

        // Reset form
        document.getElementById('paymentForm').reset();
        document.getElementById('pay_bayar_display').value = '';
        document.getElementById('pay_bayar').value = '';
        document.getElementById('modalKembalian').textContent = 'Rp 0';
        document.getElementById('modalKembalian').classList.remove('text-red-500');

        // Default select tunai triggers toggleUangPasBtn
        toggleUangPasBtn();

        modal.classList.remove('hidden');
        setTimeout(() => {
            dialog.classList.remove('scale-95', 'opacity-0');
            dialog.classList.add('scale-100', 'opacity-100');
            document.getElementById('pay_bayar_display').focus();
        }, 10);
    }

    function closePaymentModal() {
        const modal = document.getElementById('paymentModal');
        const dialog = document.getElementById('paymentModalDialog');

        dialog.classList.remove('scale-100', 'opacity-100');
        dialog.classList.add('scale-95', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function toggleUangPasBtn() {
        const method = document.querySelector('input[name="metode_bayar"]:checked').value;
        const btnUangPas = document.getElementById('btnUangPas');
        if (method === 'qris' || method === 'transfer') {
            setUangPas();
            btnUangPas.style.display = 'none';
        } else {
            btnUangPas.style.display = 'block';
        }
    }

    function setUangPas() {
        const inputDisp = document.getElementById('pay_bayar_display');
        inputDisp.value = formatNumber(window.cartTotalFinal);
        calculateKembalian();
    }

    function calculateKembalian() {
        const inputDisp = document.getElementById('pay_bayar_display');
        const val = parseNumber(inputDisp.value);
        document.getElementById('pay_bayar').value = val; // Set hidden real value

        const tagihan = window.cartTotalFinal;
        const kembalian = val - tagihan;

        const kembalianElem = document.getElementById('modalKembalian');

        if (kembalian < 0) {
            kembalianElem.textContent = `- Rp ${formatNumber(Math.abs(kembalian))}`;
            kembalianElem.classList.add('text-red-500');
            kembalianElem.classList.remove('text-mapul-green-md', 'text-gray-800');
        } else {
            kembalianElem.textContent = `Rp ${formatNumber(kembalian)}`;
            kembalianElem.classList.remove('text-red-500', 'text-gray-800');
            kembalianElem.classList.add('text-mapul-green-md');
        }
    }

    function processPayment(e) {
        e.preventDefault();

        const bayar = parseNumber(document.getElementById('pay_bayar_display').value);
        const tagihan = window.cartTotalFinal;

        if (bayar < tagihan) {
            Swal.fire('Uang Kurang', 'Nominal bayar tidak boleh kurang dari total tagihan!', 'error');
            return;
        }

        const method = document.querySelector('input[name="metode_bayar"]:checked').value;
        let id_cabang = 0;
        if (role === 'owner' || role === 'admin') {
            id_cabang = document.getElementById('pay_id_cabang').value;
            if (!id_cabang) {
                Swal.fire('Peringatan', 'Owner/Admin wajib memilih cabang transaksi!', 'warning');
                return;
            }
        }

        // Prepare payload
        const payload = {
            items: cart.map(item => ({
                id_harga_paten: item.id,    // id dari harga_paten (bukan id_produk)
                jumlah_beli:    item.qty,
                diskon:         item.diskon
            })),
            bayar:        bayar,
            metode_bayar: method,
            keterangan:   '',
            id_cabang:    id_cabang
        };


        const btn = document.getElementById('btnProsesBayar');
        const originalHtml = btn.innerHTML;

        btn.innerHTML = '<i class="fa fa-spinner fa-spin text-mapul-yellow"></i> <span>Memproses...</span>';
        btn.disabled = true;

        fetch(baseUrl + 'penjualan/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(res => {
                if (res.status) {
                    closePaymentModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Sukses!',
                        text: 'Kembalian: Rp ' + formatNumber(bayar - tagihan),
                        confirmButtonColor: '#10B981',
                        confirmButtonText: 'OK, Selesai'
                    }).then(() => {
                        // Reset POS
                        cart = [];
                        renderCart();
                        // Optionally, reload menu to update stock if needed
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
            })
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
    }

</script>

<?php $this->load->view('layout/footer'); ?>