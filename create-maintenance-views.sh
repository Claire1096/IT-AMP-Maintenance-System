#!/bin/bash
set -e
mkdir -p resources/views/maintenance
echo 'Writing maintenance/create.blade.php'
cat > resources/views/maintenance/create.blade.php << 'CRT_EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Schedule Maintenance — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128421;</span> Assets
            </a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128101;</span> Employees
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#9881;</span> Maintenance
            </a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128196;</span> Reports
            </a>
        </div>

        <div class="flex-1 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-lg font-bold">SCHEDULE MAINTENANCE</h1>
                    <p class="text-xs text-gray-400">{{ $asset->asset_tag }} — {{ $asset->name }}</p>
                </div>
                <a href="{{ route('assets.show', $asset) }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-2xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('maintenance.store', $asset) }}" class="max-w-2xl">
                @csrf
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">MAINTENANCE TYPE *</label>
                            <input type="text" name="maintenance_type" value="{{ old('maintenance_type') }}" required placeholder="e.g. Cleaning, OS Update, Hardware Check"
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">FREQUENCY *</label>
                            <select name="frequency" required class="w-full text-xs border-gray-300 rounded-md">
                                <option value="one_time" @selected(old('frequency') === 'one_time')>One-time</option>
                                <option value="monthly" @selected(old('frequency') === 'monthly')>Monthly</option>
                                <option value="quarterly" @selected(old('frequency') === 'quarterly')>Quarterly</option>
                                <option value="semi_annual" @selected(old('frequency') === 'semi_annual')>Semi-Annual</option>
                                <option value="annual" @selected(old('frequency') === 'annual')>Annual</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">SCHEDULED DATE *</label>
                            <input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}" required
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSIGNED TECHNICIAN</label>
                            <select name="assigned_technician_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">— Unassigned —</option>
                                @foreach ($technicians as $tech)
                                    <option value="{{ $tech->id }}" @selected(old('assigned_technician_id') == $tech->id)>{{ $tech->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">CHECKLIST ITEMS</label>
                        <div id="checklist-items" class="space-y-2 mb-2"></div>
                        <button type="button" onclick="addChecklistItem()" class="text-xs text-pink-600 hover:underline">+ Add checklist item</button>
                    </div>

                    <div class="text-right pt-4">
                        <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                            SAVE SCHEDULE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addChecklistItem() {
            const container = document.getElementById('checklist-items');
            const row = document.createElement('div');
            row.className = 'flex gap-2';
            row.innerHTML = `
                <input type="text" name="checklist[]" placeholder="e.g. Check fan/thermal paste" class="flex-1 text-xs border-gray-300 rounded-md">
                <button type="button" onclick="this.parentElement.remove()" class="text-xs text-red-500">&#10005;</button>
            `;
            container.appendChild(row);
        }
        // Start with one empty row
        addChecklistItem();
    </script>
</body>
</html>
CRT_EOF
echo 'Writing maintenance/index.blade.php'
cat > resources/views/maintenance/index.blade.php << 'IDX_EOF'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128421;</span> Assets
            </a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128101;</span> Employees
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#9881;</span> Maintenance
            </a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128196;</span> Reports
            </a>
        </div>

        <div class="flex-1 p-6">
            <div class="mb-6">
                <h1 class="text-lg font-bold">PREVENTIVE MAINTENANCE</h1>
                <p class="text-xs text-gray-400">All scheduled maintenance</p>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                        <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All Status</option>
                            @foreach (['scheduled', 'in_progress', 'completed', 'overdue', 'skipped'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-xs">
                            <input type="checkbox" name="due_this_month" value="1" @checked(request('due_this_month'))>
                            Due this month only
                        </label>
                    </div>
                    <div class="flex items-end justify-end gap-2">
                        <a href="{{ route('maintenance.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">CLEAR</a>
                        <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full">FILTER</button>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            <div class="bg-white border border-rose-100 overflow-hidden shadow-sm rounded-xl">
                <table class="min-w-full divide-y divide-rose-100 text-xs">
                    <thead class="bg-pink-100">
                        <tr class="text-left font-semibold text-gray-700 uppercase tracking-wider">
                            <th class="px-4 py-3">Asset</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Frequency</th>
                            <th class="px-4 py-3">Next Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Technician</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-rose-50">
                        @forelse ($schedules as $schedule)
                            <tr class="hover:bg-rose-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('assets.show', $schedule->asset) }}" class="text-pink-600 hover:underline font-mono">
                                        {{ $schedule->asset->asset_tag }}
                                    </a>
                                    <div class="text-gray-400">{{ $schedule->asset->name }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $schedule->maintenance_type }}</td>
                                <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $schedule->frequency)) }}</td>
                                <td class="px-4 py-3">{{ optional($schedule->next_maintenance_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'px-2 py-1 rounded-full font-semibold text-[10px]',
                                        'bg-blue-500 text-white' => $schedule->status === 'scheduled',
                                        'bg-indigo-500 text-white' => $schedule->status === 'in_progress',
                                        'bg-green-500 text-white' => $schedule->status === 'completed',
                                        'bg-red-500 text-white' => $schedule->status === 'overdue',
                                        'bg-gray-400 text-white' => $schedule->status === 'skipped',
                                    ])>
                                        {{ strtoupper(str_replace('_', ' ', $schedule->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $schedule->technician->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if (in_array($schedule->status, ['scheduled', 'overdue', 'in_progress']))
                                        <form method="POST" action="{{ route('maintenance.complete', $schedule) }}">
                                            @csrf
                                            <button class="text-pink-600 hover:underline">Mark Complete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                    No maintenance scheduled yet. Go to an asset's page and click "Schedule Maintenance".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $schedules->withQueryString()->links() }}
            </div>
        </div>
    </div>
</body>
</html>
IDX_EOF
echo 'Both maintenance views created!'
