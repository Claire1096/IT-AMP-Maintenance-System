@extends('layouts.shell')
@section('title', 'Reports')
@section('content')
    <div class="no-print flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">REPORTS</h1>
            <p class="text-xs text-gray-400">Select a report to view</p>
        </div>
        <button id="print-report-btn" disabled class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full opacity-50 cursor-not-allowed">PRINT REPORT</button>
    </div>

    <div class="no-print bg-white border border-pink-200 rounded-xl p-5 mb-6">
        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Report Details</h2>
        <div class="grid grid-cols-3 gap-4 max-w-3xl">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SELECT CATEGORY</label>
                <select id="report-category" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">Select Category</option>
                    <option value="it">IT Reports</option>
                    <option value="facility">Facility Reports</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SELECT REPORT TYPE</label>
                <select id="report-type" class="w-full text-xs border-gray-300 rounded-md" disabled>
                    <option value="">Select category first</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT PERIOD</label>
                <select id="report-period" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="all">All</option>
                    <option value="monthly">Monthly</option>
                    <option value="annual">Annual</option>
                </select>
            </div>
        </div>
    </div>

    <div id="report-preview" class="bg-white border border-pink-200 rounded-xl min-h-[300px] flex items-center justify-center text-center text-gray-400 text-xs p-10">
        Will show which category-report type-monthly/yearly depending on what the user picks.
    </div>
@endsection

@push('scripts')
<script>
    const itReports = [
        { value: 'inventory', url: @json(route('reports.inventory')), label: 'Asset Inventory Report' },
        { value: 'preventive-maintenance', url: @json(route('reports.preventive-maintenance')), label: 'Preventive Maintenance Report' },
        { value: 'warranty-expiration', url: @json(route('reports.warranty-expiration')), label: 'Warranty Expiration Report' },
        { value: 'repair-history', url: @json(route('reports.repair-history')), label: 'Repair History Report' },
        { value: 'asset-assignment', url: @json(route('reports.asset-assignment')), label: 'Asset Assignment Report' },
    ];
    const facilityReports = [
        { value: 'inventory', url: @json(route('facility-reports.inventory')), label: 'Facility Inventory Report' },
        { value: 'condition', url: @json(route('facility-reports.condition')), label: 'Condition Report' },
        { value: 'department-distribution', url: @json(route('facility-reports.department-distribution')), label: 'Department Distribution Report' },
        { value: 'maintenance-due', url: @json(route('facility-reports.maintenance-due')), label: 'Maintenance Due Report' },
    ];
    const periodReports = {
        it: {
            monthly: @json(route('reports.monthly-summary')),
            annual: @json(route('reports.annual-summary')),
        },
        facility: {
            monthly: @json(route('facility-reports.monthly-summary')),
            annual: @json(route('facility-reports.yearly-summary')),
        },
    };

    const categorySelect = document.getElementById('report-category');
    const typeSelect = document.getElementById('report-type');
    const periodSelect = document.getElementById('report-period');
    const preview = document.getElementById('report-preview');
    const printBtn = document.getElementById('print-report-btn');

    function currentCategoryList() {
        return categorySelect.value === 'it' ? itReports : (categorySelect.value === 'facility' ? facilityReports : []);
    }

    function showEmptyState(message) {
        preview.classList.add('flex', 'items-center', 'justify-center', 'text-center', 'text-gray-400', 'text-xs', 'p-10');
        preview.innerHTML = message;
        printBtn.disabled = true;
        printBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }

    let currentReportUrl = null;

    async function loadPreview(url) {
        currentReportUrl = url;
        showEmptyState('Loading report...');
        try {
            const res = await fetch(url + (url.includes('?') ? '&' : '?') + 'embed=1');
            const html = await res.text();
            preview.classList.remove('flex', 'items-center', 'justify-center', 'text-center', 'text-gray-400', 'text-xs', 'p-10');
            preview.innerHTML = html;
            preview.classList.add('p-6');
            preview.querySelectorAll('.no-print').forEach(el => el.remove());
            printBtn.disabled = false;
            printBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } catch (err) {
            showEmptyState('Could not load this report. Please try again.');
        }
    }

    printBtn.addEventListener('click', async function () {
        if (!currentReportUrl) return;

        const res = await fetch(currentReportUrl + (currentReportUrl.includes('?') ? '&' : '?') + 'embed=1&print=1');
        const html = await res.text();
        preview.innerHTML = html;
        preview.querySelectorAll('.no-print').forEach(el => el.remove());

        window.print();

        // Restore the normal (paginated) view after printing.
        loadPreview(currentReportUrl);
    });

    function buildUrl(base, extraParams) {
        const url = new URL(base, window.location.origin);
        Object.entries(extraParams).forEach(([k, v]) => url.searchParams.set(k, v));
        return url.toString();
    }

    function resolveAndLoad() {
        const category = categorySelect.value;
        const period = periodSelect.value;
        const type = typeSelect.value;

        if (!category) {
            showEmptyState('Will show which category-report type-monthly/yearly depending on what the user picks.');
            return;
        }

        // No specific report type chosen: fall back to the dedicated Monthly/Annual summary report if a period is set.
        if (!type) {
            if (period === 'monthly' || period === 'annual') {
                loadPreview(periodReports[category][period]);
            } else {
                showEmptyState('Select a report type to view.');
            }
            return;
        }

        const entry = currentCategoryList().find(r => r.value === type);
        if (!entry) return;

        if (period === 'monthly') {
            loadPreview(buildUrl(entry.url, { period: 'monthly', month: new Date().getMonth() + 1, year: new Date().getFullYear() }));
        } else if (period === 'annual') {
            loadPreview(buildUrl(entry.url, { period: 'annual', year: new Date().getFullYear() }));
        } else {
            loadPreview(entry.url);
        }
    }

    categorySelect.addEventListener('change', function () {
        const list = currentCategoryList();

        if (list.length === 0) {
            typeSelect.disabled = true;
            typeSelect.innerHTML = '<option value="">Select category first</option>';
        } else {
            typeSelect.disabled = false;
            typeSelect.innerHTML = '<option value="">Select Report Type</option>' +
                list.map(r => `<option value="${r.value}">${r.label}</option>`).join('');
        }

        resolveAndLoad();
    });

    typeSelect.addEventListener('change', resolveAndLoad);
    periodSelect.addEventListener('change', resolveAndLoad);
</script>
@endpush
