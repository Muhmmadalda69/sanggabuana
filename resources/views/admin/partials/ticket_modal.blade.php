<style>
    @media print {
        /* Hide everything on the page */
        body * {
            visibility: hidden !important;
        }
        /* Show only the ticket modal and its children */
        #ticket-modal-overlay,
        #ticket-modal-overlay * {
            visibility: visible !important;
        }
        /* Force exact color/background printing for gradients and badges */
        #ticket-modal-overlay * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        /* Place modal overlay at the top left of printed page */
        #ticket-modal-overlay {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            height: auto !important;
            background: white !important;
            backdrop-filter: none !important;
            display: flex !important;
            align-items: flex-start !important;
            justify-content: center !important;
            padding: 0 !important;
            margin: 0 !important;
            z-index: 9999999 !important;
        }
        /* Reset modal shadow, height and transform */
        #ticket-modal {
            box-shadow: none !important;
            border: 1px solid #e2e8f0 !important;
            width: 100% !important;
            max-width: 450px !important;
            margin: 20px auto !important;
            max-height: none !important;
            transform: none !important;
            opacity: 1 !important;
            display: flex !important;
            flex-direction: column !important;
        }
        /* Force body section scroll/hidden to expand naturally */
        #ticket-modal > div {
            max-height: none !important;
            overflow: visible !important;
        }
        /* Hide interaction buttons */
        #ticket-modal-overlay button,
        #ticket-modal-overlay button * {
            display: none !important;
            visibility: hidden !important;
        }
    }
</style>

{{-- Ticket Detail Modal Component --}}
<div id="ticket-modal-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-100 hidden items-center justify-center p-4" style="display:none; align-items: center; justify-content: center;">
    <div id="ticket-modal" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all duration-300 scale-95 opacity-0" style="max-height: 85vh; display: flex; flex-direction: column; overflow: hidden;">
        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-emerald-600 to-forest-700 px-6 py-4 flex items-center justify-between" style="flex-shrink: 0;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <i data-lucide="ticket" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-white text-sm tracking-wide">Detail E-Ticket</h4>
                    <p class="text-emerald-100 text-[11px] mt-0.5">Wisata Sanggabuana</p>
                </div>
            </div>
            <button type="button" onclick="closeTicketModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        
        {{-- Modal Body --}}
        <div class="px-6 py-6 space-y-5" style="overflow-y: auto; flex: 1 1 auto; max-height: calc(85vh - 120px);">
            {{-- Status Badge --}}
            <div class="flex items-center justify-between">
                <span id="modal-status-badge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold"></span>
            </div>
            
            {{-- Ticket Number --}}
            <div class="text-center py-3 border-y border-dashed border-gray-200">
                <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-widest mb-1">Nomor Tiket</div>
                <div id="modal-ticket-no" class="font-black text-gray-900 text-3xl tracking-tight"></div>
            </div>
            
            {{-- QR Code --}}
            <div class="flex justify-center">
                <div class="w-36 h-36 p-2 bg-white border-2 border-gray-100 rounded-xl shadow-sm">
                    <img id="modal-qr-code" src="" alt="QR Code" class="w-full h-full object-contain" loading="lazy">
                </div>
            </div>
            
            {{-- Ticket Details --}}
            <div id="modal-details-grid" class="grid grid-cols-2 gap-x-6 gap-y-3 text-xs pt-3 border-t border-dashed border-gray-200">
            </div>
        </div>
        
        {{-- Modal Footer --}}
        <div class="bg-gray-50 border-t border-dashed border-gray-200 px-6 py-3 flex items-center justify-between" style="flex-shrink: 0;">
            <span class="text-[10px] text-gray-400">Digital Ticketing System</span>
            <button type="button" onclick="window.print()" class="text-[10px] text-forest-600  hover:text-forest-800 font-bold flex items-center gap-1 transition-colors">
                <i data-lucide="printer" class="w-3 h-3"></i> Cetak Tiket
            </button>
        </div>
    </div>
</div>

<script>
    // ── Ticket Detail Modal JavaScript Handler ──
    function showTicketModal(ticketNo, name, destination, qty, paymentMethod, totalPrice, checkedInAt, status, community, purpose, campingDuration, leaderName, qtyMale, qtyFemale, qtyKids) {
        const overlay = document.getElementById('ticket-modal-overlay');
        const modal = document.getElementById('ticket-modal');

        if (!overlay || !modal) return;

        // Status badge
        const badge = document.getElementById('modal-status-badge');
        if (badge) {
            if (status === 'in') {
                badge.className = 'inline-flex items-center mt-3 gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif — Di Dalam Lokasi';
            } else {
                badge.className = 'inline-flex items-center mt-3 gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Sudah Keluar';
            }
        }

        // Ticket number
        const ticketNoEl = document.getElementById('modal-ticket-no');
        if (ticketNoEl) ticketNoEl.innerText = ticketNo;

        // QR Code
        const qrCodeEl = document.getElementById('modal-qr-code');
        if (qrCodeEl) qrCodeEl.src = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(ticketNo) + '&margin=5';

        // Details grid
        const grid = document.getElementById('modal-details-grid');
        if (grid) {
            let html = '';

            const addDetail = (label, value, colSpan) => {
                const spanClass = colSpan ? ' col-span-2' : '';
                html += '<div class="' + spanClass + '">';
                html += '<span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">' + label + '</span>';
                html += '<div class="text-gray-800 font-bold mt-0.5 mb-3">' + value + '</div>';
                html += '</div>';
            };

            addDetail('Pengunjung', name);
            addDetail('Destinasi', destination);
            if (leaderName && leaderName !== name) {
                addDetail('Penanggung Jawab', leaderName, true);
            }
            addDetail('Jumlah Rombongan', qty + ' Orang');
            // Gender/demografi
            if (qtyMale !== undefined || qtyFemale !== undefined) {
                const isSingle = (parseInt(qty)||1) === 1;
                if (isSingle) {
                    const genderLabel = parseInt(qtyMale) > 0 ? '<span class="text-blue-600">Laki-laki</span>' : '<span class="text-pink-600">Perempuan</span>';
                    addDetail('Jenis Kelamin', genderLabel);
                } else {
                    const demoStr = '<span class="text-blue-600 font-bold">L: ' + (qtyMale||0) + '</span> &nbsp; <span class="text-pink-600 font-bold">P: ' + (qtyFemale||0) + '</span>' + (parseInt(qtyKids) > 0 ? ' &nbsp; <span class="text-amber-600 font-bold">A: ' + qtyKids + '</span>' : '');
                    addDetail('Demografi', demoStr);
                }
            }
            addDetail('Metode Bayar', paymentMethod);
            addDetail('Total Bayar', '<span class="text-forest-700 font-extrabold">' + totalPrice + '</span>');
            addDetail('Waktu Masuk', checkedInAt);

            if (community) addDetail('Komunitas', community, true);
            if (purpose) addDetail('Tujuan', purpose);
            if (campingDuration) addDetail('Lama Camping', campingDuration + ' Malam');

            grid.innerHTML = html;
        }

        // Show modal
        overlay.style.display = 'flex';
        overlay.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('scale-95', 'opacity-0');
            modal.classList.add('scale-100', 'opacity-100');
        });

        // Re-initialize lucide icons if available
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeTicketModal() {
        const overlay = document.getElementById('ticket-modal-overlay');
        const modal = document.getElementById('ticket-modal');
        if (!overlay || !modal) return;

        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.classList.add('hidden');
        }, 200);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Close modal on overlay click
        const overlay = document.getElementById('ticket-modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) closeTicketModal();
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeTicketModal();
        });
    });
</script>
