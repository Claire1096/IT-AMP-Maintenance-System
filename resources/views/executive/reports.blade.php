@extends('layouts.shell')
@section('title', 'Reports')
@section('content')

    <div class="mb-6">
        <h1 class="text-lg font-bold">REPORTS</h1>
        <p class="text-xs text-gray-400">Select a report to view</p>
    </div>

    <div class="bg-white border border-pink-200 rounded-xl p-5 mb-6">
        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Report Details</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT CATEGORY</label>
                <select id="report-category" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">Select Category</option>
                    <option value="it">IT Reports</option>
                    <option value="facility">Facility Maintenance Reports</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT TYPE</label>
                <select id="report-type" class="w-full text-xs border-gray-300 rounded-md" disabled>
                    <option value="">Select category first</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white border border-pink-200 rounded-xl p-10 text-center text-gray-400 text-xs">
        Choose a report category and type above to view it.
    </div>

@endsection

@push('scripts')
<script>
    const itReports = [
        { value: @json(route('reports.inventory')), label: 'Asset Inventory Report' },
        { value: @json(route('reports.preventive-maintenance')), label: 'Preventive Maintenance Report' },
        { value: @json(route('reports.warranty-expiration')), label: 'Warranty Expiration Report' },
        { value: @json(route('reports.repair-history')), label: 'Repair History Report' },
        { value: @json(route('reports.asset-assignment')), label: 'Asset Assignment Report' },
        { value: @json(route('reports.annual-summary')), label: 'Annual Asset Summary' },
    ];

    const facilityReports = [
        { value: @json(route('facility-reports.inventory')), label: 'Facility Inventory Report' },
        { value: @json(route('facility-reports.condition')), label: 'Condition Report' },
        { value: @json(route('facility-reports.department-distribution')), label: 'Department Distribution Report' },
        { value: @json(route('facility-reports.maintenance-due')), label: 'Maintenance Due Report' },
    ];

    const categorySelect = document.getElementById('report-category');
    const typeSelect = document.getElementById('report-type');

    categorySelect.addEventListener('change', function () {
        const list = this.value === 'it' ? itReports : (this.value === 'facility' ? facilityReports : []);

        typeSelect.innerHTML = '';

        if (list.length === 0) {
            typeSelect.disabled = true;
            typeSelect.innerHTML = '<option value="">Select category first</option>';
            return;
        }

        typeSelect.disabled = false;
        typeSelect.innerHTML = '<option value="">Select Report Type</option>' +
            list.map(r => `<option value="${r.value}">${r.label}</option>`).join('');
    });

    typeSelect.addEventListener('change', function () {
        if (this.value) {
            window.location.href = this.value;
        }
    });
</script>
@endpush
