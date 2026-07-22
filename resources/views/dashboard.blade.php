@extends('layouts.shell')

@section('title', 'Dashboard')

@push('head-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="mb-6">
        <h1 class="text-lg font-bold">DASHBOARD</h1>
        <p class="text-xs text-gray-400">Overview of IT assets and maintenance</p>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-pink-100 flex items-center justify-center text-lg">&#128187;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">Total Assets</div>
                <div class="text-xl font-bold">{{ $totalAssets }}</div>
            </div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-lg">&#9989;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">Active Assets</div>
                <div class="text-xl font-bold">{{ $activeAssets }}</div>
            </div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center text-lg">&#128295;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">Under Repair</div>
                <div class="text-xl font-bold">{{ $underRepair }}</div>
            </div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-lg">&#128197;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">Maintenance Due (Month)</div>
                <div class="text-xl font-bold">{{ $maintenanceDueThisMonth }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-lg">&#9203;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">Warranty Expiring Soon (30 days)</div>
                <div class="text-xl font-bold">{{ $warrantyExpiringSoon }}</div>
            </div>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-lg">&#10060;</div>
            <div>
                <div class="text-[10px] font-semibold text-gray-500 uppercase">Warranty Expired</div>
                <div class="text-xl font-bold">{{ $warrantyExpired }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-6">
        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Assets by Department</h2>
            <canvas id="deptChart" height="220"></canvas>
        </div>
        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Monthly Maintenance Completed</h2>
            <canvas id="maintenanceChart" height="220"></canvas>
        </div>
    </div>

    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Assets by Category</h2>
        <canvas id="categoryChart" height="180"></canvas>
    </div>
@endsection

@push('scripts')
<script>
    const deptLabels = @json($assetsByDepartment->pluck('name'));
    const deptData = @json($assetsByDepartment->pluck('assets_count'));
    new Chart(document.getElementById('deptChart'), {
        type: 'bar',
        data: { labels: deptLabels, datasets: [{ label: 'Assets', data: deptData, backgroundColor: '#ec4899' }] },
        options: { plugins: { legend: { display: false } } }
    });

    const maintLabels = @json($monthlyMaintenance->pluck('month'));
    const maintData = @json($monthlyMaintenance->pluck('completed_count'));
    new Chart(document.getElementById('maintenanceChart'), {
        type: 'line',
        data: { labels: maintLabels, datasets: [{ label: 'Completed', data: maintData, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.1)', fill: true, tension: 0.3 }] },
        options: { plugins: { legend: { display: false } } }
    });

    const catLabels = @json($assetsByCategory->pluck('category.name'));
    const catData = @json($assetsByCategory->pluck('total'));
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: { labels: catLabels, datasets: [{ label: 'Assets', data: catData, backgroundColor: '#f97316' }] },
        options: { indexAxis: 'y', plugins: { legend: { display: false } } }
    });
</script>
@endpush