@extends('layouts.shell')
@section('title', 'Asset Management')
@section('content')

            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-lg font-bold">ASSET MANAGEMENT</h1>
                    <p class="text-xs text-gray-400">All registered assets</p>
                </div>
                    <a href="{{ route('assets.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ ADD ASSET</a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
            @endif

            <form id="asset-filter-form" method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
                <h2 class="text-xs font-bold text-gray-500 uppercase mb-3">&#128269; Filter Assets</h2>
                <div class="grid grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">SEARCH</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search asset, category, name, department, location, status.."
                               class="w-full text-xs border-gray-300 rounded-md" autocomplete="off">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                        <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All Departments</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                        <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                            <option value="">All Status</option>
                            @foreach (['active', 'under_repair', 'for_disposal', 'lost'] as $status)
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
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('assets.index') }}" id="asset-clear-filters" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">CLEAR FILTERS</a>
                    <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full">+ APPLY FILTERS</button>
                </div>
            </form>

            <div id="asset-results">
                @include('assets._results')
            </div>

@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('asset-filter-form');
        const resultsContainer = document.getElementById('asset-results');
        const clearBtn = document.getElementById('asset-clear-filters');
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
                .catch((err) => console.error('Asset search failed:', err));
        }

        function runSearch() {
            const params = new URLSearchParams(new FormData(form)).toString();
            // Always build off the fixed route, never form.action or window.location —
            // both default to the full current URL (query string included), which
            // caused filters to pile up on top of each other on every keystroke.
            loadUrl(`{{ route('assets.index') }}?${params}`);
        }

        // Text inputs: debounce as the user types.
        form.querySelectorAll('input[type="text"]').forEach((input) => {
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(runSearch, 350);
            });
        });

        // Selects: filter immediately on change, no need to debounce.
        form.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', runSearch);
        });

        // Keep the "Apply Filters" button working, but via AJAX instead of a full reload.
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            runSearch();
        });

        // "Clear filters" resets fields then re-runs the search instead of a full page nav.
        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            form.reset();
            runSearch();
        });

        // Pagination links (Laravel's default paginator markup) live inside the
        // swapped-in partial, so listen on the container itself (delegation).
        // Any link whose href contains a "page" query param is treated as pagination;
        // everything else (asset row Edit/View links, etc.) navigates normally.
        resultsContainer.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (!link) return;

            let linkUrl;
            try {
                linkUrl = new URL(link.href, window.location.origin);
            } catch (err) {
                return;
            }

            if (!linkUrl.searchParams.has('page')) return;

            e.preventDefault();
            loadUrl(link.href);
        });
    })();
</script>
@endpush