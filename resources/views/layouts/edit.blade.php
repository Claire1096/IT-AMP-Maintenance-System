<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                IT Assets
            </h2>
            <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + Register Asset
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">{{ session('success') }}</div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="mb-4 bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tag, name, serial..."
                           class="border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select name="status" class="border-gray-300 rounded-md text-sm">
                        <option value="">All</option>
                        @foreach (['active', 'under_repair', 'for_disposal', 'lost'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-md uppercase">
                    Filter
                </button>
                <a href="{{ route('assets.index') }}" class="px-4 py-2 text-xs text-gray-500 uppercase">Clear</a>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3">Asset Tag</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Assigned To</th>
                            <th class="px-4 py-3">Department</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Warranty</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($assets as $asset)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs">{{ $asset->asset_tag }}</td>
                                <td class="px-4 py-3">{{ $asset->name }}</td>
                                <td class="px-4 py-3">{{ $asset->category->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $asset->assignedEmployee->full_name ?? '— unassigned —' }}</td>
                                <td class="px-4 py-3">{{ $asset->department->name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'px-2 py-1 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-800' => $asset->status === 'active',
                                        'bg-yellow-100 text-yellow-800' => $asset->status === 'under_repair',
                                        'bg-gray-200 text-gray-700' => $asset->status === 'for_disposal',
                                        'bg-red-100 text-red-800' => $asset->status === 'lost',
                                    ])>
                                        {{ ucwords(str_replace('_', ' ', $asset->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($asset->warranty_expiration)
                                        @if ($asset->isUnderWarranty())
                                            <span class="text-green-700 text-xs">until {{ $asset->warranty_expiration->format('M d, Y') }}</span>
                                        @else
                                            <span class="text-red-600 text-xs">expired {{ $asset->warranty_expiration->format('M d, Y') }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('assets.show', $asset) }}" class="text-indigo-600 hover:underline text-xs">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                    No assets registered yet. <a href="{{ route('assets.create') }}" class="text-indigo-600 hover:underline">Register the first one.</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $assets->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>