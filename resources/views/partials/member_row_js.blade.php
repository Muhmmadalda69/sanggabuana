{{--
    Shared member row JS partial.
    Requires: memberIndex variable, membersContainer, btnAddMember, calculatePOS(), lucide
    Usage: @include('partials.member_row_js', ['withAddress' => true/false])
--}}
<script>
(function() {
    const membersContainer = document.getElementById('members-container');
    const btnAddMember = document.getElementById('btn-add-member');
    if (!membersContainer || !btnAddMember) return;

    // Member TomSelect registry
    const memberTomSelects = {};
    let memberIndex = 0;

    function createMemberRow(index) {
        const div = document.createElement('div');
        div.className = 'member-row bg-[#f0fdf4] p-4 rounded-xl border border-green-100 shadow-sm relative';
        div.dataset.index = index;
        div.innerHTML = `
            <div class="absolute -top-2 -left-2 w-6 h-6 rounded-full bg-forest-600 text-white flex items-center justify-center text-[10px] font-bold shadow member-index">${index + 1}</div>
            <button type="button" class="btn-remove-member absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center shadow transition-colors" title="Hapus Anggota">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
            <div class="grid grid-cols-2 sm:grid-cols-12 gap-3 items-start">
                <div class="col-span-2 sm:col-span-4">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <input type="text" name="members[${index}][name]" required placeholder="Nama Lengkap" class="w-full h-[34px] px-3 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-forest-500">
                </div>
                <div class="col-span-2 sm:col-span-4">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email</label>
                    <input type="email" name="members[${index}][email]" placeholder="Email (Opsional)" class="w-full h-[34px] px-3 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-forest-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Usia</label>
                    <input type="number" name="members[${index}][age]" required min="1" max="120" placeholder="Usia (Tahun)" class="w-full h-[34px] px-2 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-forest-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                    <select name="members[${index}][gender]" required class="w-full h-[34px] px-2 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-forest-500">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kategori</label>
                    <select name="members[${index}][is_child]" id="member_is_child_${index}" class="w-full h-[34px] px-2 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-forest-500">
                        <option value="0">Dewasa</option>
                        <option value="1">Anak-anak</option>
                    </select>
                </div>
                <div class="col-span-2 sm:col-span-3">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap</label>
                    <input type="text" name="members[${index}][address]" required placeholder="Alamat" class="w-full h-[34px] px-3 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-forest-500">
                </div>
                <div class="col-span-2 sm:col-span-3">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kategori Wilayah</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-gray-400 pointer-events-none">
                            <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                        </span>
                        <select name="members[${index}][address_type]" id="member_address_type_${index}" class="w-full h-[34px] pl-7 pr-6 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-forest-500 appearance-none cursor-pointer">
                            <option value="lokal">Lokal</option>
                            <option value="indonesia" selected>Indonesia</option>
                            <option value="mancanegara">Mancanegara</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 pointer-events-none">
                            <i data-lucide="chevron-down" class="w-3 h-3"></i>
                        </span>
                    </div>
                </div>
                <div class="col-span-2 sm:col-span-3">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Provinsi / Negara Asal</label>
                    <div class="relative">
                        <div class="ts-icon-container"><i data-lucide="map" class="w-3.5 h-3.5"></i></div>
                        <input type="hidden" name="members[${index}][province]" id="member_province_hidden_${index}">
                        <select id="member_province_${index}" placeholder="Pilih Provinsi..."></select>
                    </div>
                </div>
                <div class="col-span-2 sm:col-span-3">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kota / Kabupaten Asal</label>
                    <div class="relative">
                        <div class="ts-icon-container"><i data-lucide="building" class="w-3.5 h-3.5"></i></div>
                        <input type="hidden" name="members[${index}][city]" id="member_city_hidden_${index}">
                        <select id="member_city_${index}" placeholder="Pilih Kota..."></select>
                    </div>
                </div>
            </div>
        `;
        return div;
    }

    // ── TomSelect helpers for member rows ──
    async function loadMemberProvinces(tsP) {
        tsP.disable();
        try {
            const res = await fetch('/data/wilayah/indonesia/provinces.json');
            const data = await res.json();
            tsP.clearOptions();
            data.forEach(p => tsP.addOption({ value: p.name, text: p.name, id: p.id }));
            tsP.enable();
        } catch(e) { tsP.enable(); }
    }

    async function loadMemberCities(tsC, provinceId) {
        tsC.disable();
        try {
            const res = await fetch(`/data/wilayah/indonesia/regencies/${provinceId}.json`);
            const data = await res.json();
            tsC.clearOptions();
            data.forEach(c => tsC.addOption({ value: c.name, text: c.name }));
            tsC.enable();
        } catch(e) { tsC.enable(); }
    }

    async function loadMemberCountries(tsP) {
        tsP.disable();
        try {
            const res = await fetch('/data/wilayah/countries.json');
            const json = await res.json();
            tsP.clearOptions();
            json.forEach(c => tsP.addOption({ value: c.name, text: c.name }));
            tsP.enable();
        } catch(e) { tsP.enable(); }
    }

    async function loadMemberWorldCities(tsC, countryName) {
        tsC.disable();
        try {
            const res = await fetch('https://countriesnow.space/api/v0.1/countries/cities', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ country: countryName })
            });
            const json = await res.json();
            tsC.clearOptions();
            if (!json.error && json.data) json.data.forEach(city => tsC.addOption({ value: city, text: city }));
            tsC.enable();
        } catch(e) { tsC.enable(); }
    }

    function populateMemberProvince(tsP, tsC, type, index) {
        tsP.clear(); tsP.clearOptions();
        tsC.clear(); tsC.clearOptions();

        const hiddenP = document.getElementById(`member_province_hidden_${index}`);
        const hiddenC = document.getElementById(`member_city_hidden_${index}`);
        if (hiddenP) hiddenP.value = '';
        if (hiddenC) hiddenC.value = '';

        if (type === 'lokal') {
            tsP.addOption({ value: 'Jawa Barat', text: 'Jawa Barat' });
            tsP.setValue('Jawa Barat');
            tsP.enable();
            tsP.wrapper.style.pointerEvents = 'none';
            tsP.wrapper.style.opacity = '0.7';
            tsC.addOption({ value: 'Pangkalan', text: 'Kecamatan Pangkalan' });
            tsC.addOption({ value: 'Tegalwaru', text: 'Kecamatan Tegalwaru' });
            tsC.refreshOptions(false);
            tsC.enable();
            tsC.wrapper.style.pointerEvents = '';
            tsC.wrapper.style.opacity = '';
        } else if (type === 'indonesia') {
            tsP.enable(); tsP.wrapper.style.pointerEvents = ''; tsP.wrapper.style.opacity = '';
            tsC.enable(); tsC.wrapper.style.pointerEvents = ''; tsC.wrapper.style.opacity = '';
            loadMemberProvinces(tsP);
        } else if (type === 'mancanegara') {
            tsP.enable(); tsP.wrapper.style.pointerEvents = ''; tsP.wrapper.style.opacity = '';
            tsC.enable(); tsC.wrapper.style.pointerEvents = ''; tsC.wrapper.style.opacity = '';
            loadMemberCountries(tsP);
        }
    }

    function initMemberTomSelects(div, index) {
        const provinceEl = div.querySelector(`#member_province_${index}`);
        const cityEl = div.querySelector(`#member_city_${index}`);
        const addressTypeEl = div.querySelector(`#member_address_type_${index}`);
        if (!provinceEl || !cityEl) return;

        const tsC_ref = { instance: null };

        const tsP = new TomSelect(provinceEl, {
            create: true,
            sortField: { field: 'text', direction: 'asc' },
            placeholder: 'Pilih Provinsi...',
            onChange: function(value) {
                const hiddenP = document.getElementById(`member_province_hidden_${index}`);
                if (hiddenP) hiddenP.value = value || '';
                const type = addressTypeEl ? addressTypeEl.value : 'indonesia';
                if (type === 'indonesia') {
                    const opt = this.options[value];
                    if (opt && opt.id) loadMemberCities(tsC_ref.instance, opt.id);
                } else if (type === 'mancanegara' && value) {
                    loadMemberWorldCities(tsC_ref.instance, value);
                }
            }
        });

        const tsC = new TomSelect(cityEl, {
            create: true,
            sortField: { field: 'text', direction: 'asc' },
            placeholder: 'Pilih Kota...',
            onChange: function(value) {
                const hiddenC = document.getElementById(`member_city_hidden_${index}`);
                if (hiddenC) hiddenC.value = value || '';
            }
        });
        tsC_ref.instance = tsC;

        memberTomSelects[index] = { tsProvince: tsP, tsCity: tsC };

        const initialType = addressTypeEl ? addressTypeEl.value : 'indonesia';
        populateMemberProvince(tsP, tsC, initialType, index);

        if (addressTypeEl) {
            addressTypeEl.addEventListener('change', function() {
                populateMemberProvince(tsP, tsC, this.value, index);
            });
        }
    }

    function updateMemberIndices() {
        const rows = membersContainer.querySelectorAll('.member-row');
        rows.forEach((row, i) => {
            row.querySelector('.member-index').textContent = i + 1;
            const removeBtn = row.querySelector('.btn-remove-member');
            // Always show remove button — user can remove all rows (solo visitor)
            removeBtn.classList.remove('hidden');
        });
        if (typeof calculatePOS === 'function') calculatePOS();
    }

    // Show empty state hint when no members
    function updateEmptyState() {
        const rows = membersContainer.querySelectorAll('.member-row');
        let hint = membersContainer.querySelector('.member-empty-hint');
        if (rows.length === 0) {
            if (!hint) {
                hint = document.createElement('div');
                hint.className = 'member-empty-hint text-center py-6 text-xs text-gray-400 border-2 border-dashed border-gray-200 rounded-xl';
                hint.innerHTML = '<i data-lucide="user" class="w-6 h-6 mx-auto mb-2 text-gray-300"></i><p>Hanya penanggung jawab (tidak ada anggota tambahan)</p>';
                membersContainer.appendChild(hint);
                if (window.lucide) window.lucide.createIcons();
            }
        } else {
            if (hint) hint.remove();
        }
        if (typeof calculatePOS === 'function') calculatePOS();
    }

    // Do NOT add first row automatically — user starts solo
    updateEmptyState();

    btnAddMember.addEventListener('click', function() {
        const row = createMemberRow(memberIndex);
        membersContainer.appendChild(row);
        updateMemberIndices();
        updateEmptyState();
        if (window.lucide) window.lucide.createIcons();
        initMemberTomSelects(row, memberIndex);
        memberIndex++;
    });

    membersContainer.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.btn-remove-member');
        if (removeBtn) {
            const row = removeBtn.closest('.member-row');
            const idx = parseInt(row.dataset.index);
            if (memberTomSelects[idx]) {
                memberTomSelects[idx].tsProvince.destroy();
                memberTomSelects[idx].tsCity.destroy();
                delete memberTomSelects[idx];
            }
            row.remove();
            updateMemberIndices();
            updateEmptyState();
        }
    });
})();
</script>
