<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-rose-50 text-gray-800 text-sm">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-xs">EM</div>
            <div class="leading-tight">
                <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <input type="text" placeholder="search asset ID/Employee..." class="text-xs border-gray-300 rounded-full px-4 py-1.5 w-56">
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128421;</span> Assets
            </a>
            <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
                <span>&#128101;</span> Employees
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#9881;</span> Maintenance
            </a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128196;</span> Reports
            </a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">DASHBOARD</h1>
                <p class="text-xs text-gray-400">Overview of IT assets and maintenance</p>
            </div>

            {{-- KPI cards --}}
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

            {{-- Warranty row --}}
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

            {{-- Charts --}}
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

            {{-- Assets by category --}}
            <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Assets by Category</h2>
                <canvas id="categoryChart" height="180"></canvas>
            </div>
        </div>
    </div>

    <script>
        const deptLabels = @json($assetsByDepartment->pluck('name'));
        const deptData = @json($assetsByDepartment->pluck('assets_count'));
        new Chart(document.getElementById('deptChart'), {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [{ label: 'Assets', data: deptData, backgroundColor: '#ec4899' }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        const maintLabels = @json($monthlyMaintenance->pluck('month'));
        const maintData = @json($monthlyMaintenance->pluck('completed_count'));
        new Chart(document.getElementById('maintenanceChart'), {
            type: 'line',
            data: {
                labels: maintLabels,
                datasets: [{ label: 'Completed', data: maintData, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.1)', fill: true, tension: 0.3 }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        const catLabels = @json($assetsByCategory->pluck('category.name'));
        const catData = @json($assetsByCategory->pluck('total'));
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: catLabels,
                datasets: [{ label: 'Assets', data: catData, backgroundColor: '#f97316' }]
            },
            options: { indexAxis: 'y', plugins: { legend: { display: false } } }
        });
    </script>
</body>
</html>

