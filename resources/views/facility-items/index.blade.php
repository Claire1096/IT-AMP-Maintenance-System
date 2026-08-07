@extends('layouts.shell')

@section('title', 'Facility Inventory')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">FACILITY INVENTORY</h1>
            <p class="text-xs text-gray-400">General facility physical inventory tracking</p>
        </div>
            <a href="{{ route('facility-items.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ ADD ITEM</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
    @endif

    <form id="facility-filter-form" method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
        <h2 class="text-xs font-bold text-gray-500 uppercase mb-3">&#128269; Filter Items</h2>
        <div class="grid grid-cols-5 gap-4 mb-4">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                <select name="category" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ ucwords(str_replace('_', ' ', $category)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET TYPE</label>
                <select name="asset_type" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">All Asset Types</option>
                @foreach ($assetTypes as $type)
                    <option value="{{ $type }}" @selected(request('asset_type') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">All Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                <select name="location_id" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">All Locations</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>
                            {{ $location->building->name ?? '' }} — {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SEARCH</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tag or name..." class="w-full text-xs border-gray-300 rounded-md" autocomplete="off">
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('facility-items.index') }}" id="facility-clear-filters" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">CLEAR FILTERS</a>
            <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full">+ APPLY FILTERS</button>
        </div>
    </form>

    <div id="facility-results">
        @include('facility-items._results')
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('facility-filter-form');
        const resultsContainer = document.getElementById('facility-results');
        const clearBtn = document.getElementById('facility-clear-filters');
        let debounceTimer = null;

        function loadUrl(url) {
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then((res) => res.text())
                .then((html) => {
                    resultsContainer.innerHTML = html;
                    window.history.replaceState(null, '', url);
                })
                .catch((err) => console.error('Facility items fetch failed:', err));
        }

        function runSearch() {
            const params = new URLSearchParams(new FormData(form)).toString();
            // Always use the clean path, never form.action or window.location.href —
            // both default to the full current URL (query string included), which
            // caused filters to pile up on top of each other on every keystroke.
            loadUrl(`{{ route('facility-items.index') }}?${params}`);
        }

        // Text input: debounce as the user types.
        form.querySelectorAll('input[type="text"]').forEach((input) => {
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(runSearch, 350);
            });
        });

        // Selects: filter immediately on change.
        form.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', runSearch);
        });

        // "Apply Filters" via AJAX instead of a full reload.
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            runSearch();
        });

        // "Clear filters" resets fields then re-runs the search.
        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            form.reset();
            runSearch();
        });

        // Pagination links live inside the swapped-in partial, so listen on the
        // container itself (delegation) rather than on the links directly.
        resultsContainer.addEventListener('click', (e) => {
            const link = e.target.closest('[data-page-link]');
            if (!link) return;

            e.preventDefault();
            loadUrl(link.href);
        });
    })();
</script>
@endpush