/**
 * script.js - JavaScript Global Functions
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Script loaded');
    
    // ===== SIDEBAR MOBILE =====
    const menuToggle = document.getElementById('mobileMenuToggle');
    const closeBtn = document.getElementById('closeSidebarBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    // Cek elemen
    if (!menuToggle) console.error('Menu toggle tidak ditemukan');
    if (!closeBtn) console.error('Close button tidak ditemukan');
    if (!sidebar) console.error('Sidebar tidak ditemukan');
    if (!overlay) console.error('Overlay tidak ditemukan');
    
    if (menuToggle && closeBtn && sidebar && overlay) {
        console.log('✅ Sidebar elements found');
        
        // Buka sidebar
        function openSidebar() {
            console.log('Opening sidebar');
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            menuToggle.classList.add('hidden');
        }
        
        // Tutup sidebar
        function closeSidebar() {
            console.log('Closing sidebar');
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            menuToggle.classList.remove('hidden');
        }
        
        // Event: klik tombol menu
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openSidebar();
        });
        
        // Event: klik tombol close
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeSidebar();
        });
        
        // Event: klik overlay
        overlay.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeSidebar();
        });
        
        // Event: klik menu item (untuk mobile)
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    const href = this.getAttribute('href');
                    if (href && href !== '#' && !href.startsWith('javascript')) {
                        e.preventDefault();
                        closeSidebar();
                        setTimeout(() => {
                            window.location.href = href;
                        }, 300);
                    }
                }
            });
        });
        
        // Event: resize window
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                // Desktop
                sidebar.classList.remove('-translate-x-full', 'translate-x-0');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                menuToggle.classList.add('hidden');
            } else {
                // Mobile
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                menuToggle.classList.remove('hidden');
            }
        });
        
        // Trigger resize awal
        window.dispatchEvent(new Event('resize'));
    }
    
    // ===== MODAL FUNCTIONS =====
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    };
    
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    };
    
    // ===== FORMAT RUPIAH =====
    window.formatRupiah = function(angka) {
        if (isNaN(angka) || angka === null) angka = 0;
        return 'Rp ' + parseInt(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };
    
    window.formatNumber = function(angka) {
        if (isNaN(angka) || angka === null) angka = 0;
        return parseInt(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };
    
    // Format input rupiah
    document.addEventListener('input', function(e) {
        if (e.target.matches('[onkeyup*="formatRupiah"]')) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value) {
                e.target.value = window.formatNumber(parseInt(value));
            }
        }
    });
    
    // ===== PREVENT DOUBLE SUBMIT =====
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.tagName === 'FORM') {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            }
        }
    });
    
    console.log('✅ All functions loaded');
});