@extends('layouts.shell')

@section('title', 'Audit')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">AUDIT</h1>
            <p class="text-xs text-gray-400">Facility asset audit and discrepancy tracking</p>
        </div>
        <a href="{{ route('finance-items.create') }}" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">+ ADD ITEM</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
    @endif

    <form id="audit-filter-form" method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
        <div class="grid grid-cols-3 gap-4 mb-4">
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
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SEARCH</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tag or name..." class="w-full text-xs border-gray-300 rounded-md" autocomplete="off">
            </div>
            <div class="flex items-end justify-end gap-2">
                <a href="{{ route('audit.index') }}" id="audit-clear-filters" class="px-4 py-2 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">CLEAR</a>
                <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full">FILTER</button>
            </div>
        </div>
    </form>

    <div id="audit-results">
        @include('audit._results')
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('audit-filter-form');
        const resultsContainer = document.getElementById('audit-results');
        const clearBtn = document.getElementById('audit-clear-filters');
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
                .catch((err) => console.error('Audit fetch failed:', err));
        }

        function runSearch() {
            const params = new URLSearchParams(new FormData(form)).toString();
            loadUrl(`{{ route('audit.index') }}?${params}`);
        }

        form.querySelectorAll('input[type="text"]').forEach((input) => {
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(runSearch, 350);
            });
        });

        form.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', runSearch);
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            runSearch();
        });

        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            form.reset();
            runSearch();
        });

        resultsContainer.addEventListener('click', (e) => {
            const link = e.target.closest('[data-page-link]');
            if (!link) return;

            e.preventDefault();
            loadUrl(link.href);
        });
    })();
</script>
@endpush
