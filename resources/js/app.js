import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('global-search-input');
    const resultsBox = document.getElementById('global-search-results');
    if (!input) return;

    let debounceTimer = null;
    let currentController = null;

    function hideResults() {
        resultsBox.classList.add('hidden');
        resultsBox.innerHTML = '';
    }

    function renderResults(data) {
    const hasAssets = data.assets && data.assets.length > 0;
    const hasEmployees = data.employees && data.employees.length > 0;
    const hasFacilityItems = data.facilityItems && data.facilityItems.length > 0;
    const hasDamageReports = data.damageReports && data.damageReports.length > 0;

    if (!hasAssets && !hasEmployees && !hasFacilityItems && !hasDamageReports) {
        resultsBox.innerHTML = `<div class="p-3 text-sm text-gray-500">No results for "${data.query}"</div>`;
        resultsBox.classList.remove('hidden');
        return;
    }

    if (hasDamageReports) {
    html += `<div class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase">Damage Reports</div>`;
    data.damageReports.forEach(r => {
        html += `
            <a href="${r.url ?? '#'}" class="block px-3 py-2 hover:bg-pink-50 border-b border-gray-100">
                <div class="text-sm font-medium text-gray-800">${r.report_number ?? ''}</div>
                <div class="text-xs text-gray-500">${r.asset_name ?? ''} ${r.cause ? '· ' + r.cause : ''}</div>
            </a>`;
    });
}

    let html = '';

    if (hasAssets) {
        html += `<div class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase">Assets</div>`;
        data.assets.forEach(a => {
            html += `
                <a href="${a.url ?? '#'}" class="block px-3 py-2 hover:bg-pink-50 border-b border-gray-100">
                    <div class="text-sm font-medium text-gray-800">${a.name ?? ''} <span class="text-xs text-gray-400">#${a.asset_tag ?? ''}</span></div>
                    <div class="text-xs text-gray-500">${a.category ?? ''} ${a.assigned_to ? '· ' + a.assigned_to : ''}</div>
                </a>`;
        });
    }

    if (hasFacilityItems) {
        html += `<div class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase">Facility Items</div>`;
        data.facilityItems.forEach(f => {
            html += `
                <a href="${f.url ?? '#'}" class="block px-3 py-2 hover:bg-pink-50 border-b border-gray-100">
                    <div class="text-sm font-medium text-gray-800">${f.name ?? ''} <span class="text-xs text-gray-400">#${f.item_tag ?? ''}</span></div>
                    <div class="text-xs text-gray-500">${f.category ?? ''} ${f.department ? '· ' + f.department : ''}</div>
                </a>`;
        });
    }

    if (hasEmployees) {
        html += `<div class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase">Employees</div>`;
        data.employees.forEach(e => {
            html += `
                <a href="${e.url ?? '#'}" class="block px-3 py-2 hover:bg-pink-50 border-b border-gray-100">
                    <div class="text-sm font-medium text-gray-800">${e.name}</div>
                    <div class="text-xs text-gray-500">${e.department ? e.department : ''}</div>
                </a>`;
        });
    }

    resultsBox.innerHTML = html;
    resultsBox.classList.remove('hidden');
}

    input.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(debounceTimer);

        if (q.length === 0) {
            hideResults();
            return;
        }

        debounceTimer = setTimeout(async () => {
            if (currentController) currentController.abort();
            currentController = new AbortController();

            try {
                const res = await fetch(`/search?q=${encodeURIComponent(q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    signal: currentController.signal,
                });
                const data = await res.json();
                renderResults(data);
            } catch (err) {
                if (err.name !== 'AbortError') console.error(err);
            }
        }, 250);
    });

    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('global-search-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            hideResults();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const bell = document.getElementById('notification-bell');
    const badge = document.getElementById('notification-badge');
    const dropdown = document.getElementById('notification-dropdown');
    if (!bell) return;

    function renderNotifications(data) {
        let html = '';

        if (data.warranty.length === 0 && data.maintenance.length === 0) {
            dropdown.innerHTML = '<div class="p-3 text-gray-500">No notifications right now.</div>';
            return;
        }

        if (data.warranty.length > 0) {
            html += '<div class="px-3 pt-2 pb-1 font-semibold text-gray-400 uppercase text-[10px]">Warranty Expiring</div>';
            data.warranty.forEach(w => {
                html += `<a href="${w.url}" class="block px-3 py-2 hover:bg-pink-50 border-b border-gray-100">
                    <div class="font-medium text-gray-800">${w.label}</div>
                    <div class="text-gray-500">${w.detail}</div>
                </a>`;
            });
        }

        if (data.maintenance.length > 0) {
            html += '<div class="px-3 pt-2 pb-1 font-semibold text-gray-400 uppercase text-[10px]">Maintenance Due</div>';
            data.maintenance.forEach(m => {
                html += `<a href="${m.url}" class="block px-3 py-2 hover:bg-pink-50 border-b border-gray-100">
                    <div class="font-medium text-gray-800">${m.label}</div>
                    <div class="text-gray-500">${m.detail}</div>
                </a>`;
            });
        }

        dropdown.innerHTML = html;
    }

    async function loadNotifications() {
        try {
            const res = await fetch('/notifications', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (data.count > 0) {
                badge.textContent = data.count > 9 ? '9+' : data.count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }

            renderNotifications(data);
        } catch (err) {
            console.error('Failed to load notifications', err);
        }
    }

    bell.addEventListener('click', function () {
        dropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', function (e) {
        const wrapper = document.getElementById('notification-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    loadNotifications();
});