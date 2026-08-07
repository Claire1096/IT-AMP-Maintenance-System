<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log Repair — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-rose-50 text-gray-800 text-sm">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <img src="{{ asset('logo.svg') }}" alt="EM Power Beautiful Skin" class="w-8 h-8 rounded-full object-cover">
            <div class="leading-tight">
                <div class="font-semibold text-sm">E<span class="text-pink-600">M</span> Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128421;</span> Assets
            </a>
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
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
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-lg font-bold">LOG REPAIR</h1>
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

            <form method="POST" action="{{ route('repairs.store', $asset) }}" class="max-w-2xl">
                @csrf
                <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORTED DATE *</label>
                            <input type="date" name="reported_date" value="{{ old('reported_date', now()->format('Y-m-d')) }}" required
                                   class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">TECHNICIAN</label>
                            <select name="technician_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">— Unassigned —</option>
                                @foreach ($technicians as $tech)
                                    <option value="{{ $tech->id }}" @selected(old('technician_id') == $tech->id)>{{ $tech->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">ISSUE DESCRIPTION *</label>
                        <textarea name="issue_description" rows="3" required placeholder="Describe the reported issue..."
                                  class="w-full text-xs border-gray-300 rounded-md">{{ old('issue_description') }}</textarea>
                    </div>

                    <div class="mb-2">
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">PARTS (if known already)</label>
                        <div id="parts-items" class="space-y-2 mb-2"></div>
                        <button type="button" onclick="addPartRow()" class="text-xs text-pink-600 hover:underline">+ Add part</button>
                    </div>

                    <div class="text-right pt-4">
                        <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                            LOG REPAIR
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let partIndex = 0;
        function addPartRow() {
            const container = document.getElementById('parts-items');
            const row = document.createElement('div');
            row.className = 'flex gap-2 items-center';
            row.innerHTML = `
                <input type="text" name="parts[${partIndex}][part_name]" placeholder="Part name" class="flex-1 text-xs border-gray-300 rounded-md">
                <input type="number" name="parts[${partIndex}][quantity]" placeholder="Qty" value="1" min="1" class="w-16 text-xs border-gray-300 rounded-md">
                <input type="number" step="0.01" name="parts[${partIndex}][unit_cost]" placeholder="Unit cost" class="w-24 text-xs border-gray-300 rounded-md">
                <button type="button" onclick="this.parentElement.remove()" class="text-xs text-red-500">&#10005;</button>
            `;
            container.appendChild(row);
            partIndex++;
        }
    </script>
</body>
</html>
