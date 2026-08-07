@extends('layouts.shell')

@section('title', 'Monthly Count')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">MONTHLY COUNT — {{ $financeCount->month->format('F Y') }}</h1>
            <p class="text-xs text-gray-400">
                Status:
                <span @class([
                    'font-semibold',
                    'text-blue-600' => $financeCount->status === 'open',
                    'text-green-600' => $financeCount->status === 'closed',
                ])>{{ strtoupper($financeCount->status) }}</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('finance-counts.index') }}" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">&#8249; BACK</a>
            @if ($financeCount->status === 'open')
                <form method="POST" action="{{ route('finance-counts.close', $financeCount) }}" onsubmit="return confirm('Close this count? Any unchecked items will be marked fully missing, and your finance items will be updated based on these results.');">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">CLOSE COUNT</button>
                </form>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-rose-100 rounded-xl p-4 shadow-sm mb-6" id="progress-banner">
        <div class="flex items-center justify-between text-xs mb-2">
            <span class="font-semibold text-gray-600">Progress</span>
            <span id="progress-count">{{ $progress['checked'] }} / {{ $progress['total'] }} checked</span>
        </div>
        <div class="w-full bg-rose-50 rounded-full h-2">
            <div id="progress-bar" class="bg-pink-600 h-2 rounded-full" style="width: {{ $progress['total'] > 0 ? round($progress['checked'] / $progress['total'] * 100) : 0 }}%"></div>
        </div>
    </div>

    <form id="count-filter-form" method="GET" class="bg-white border border-rose-100 p-5 rounded-xl shadow-sm mb-6">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SEARCH</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tag or name..." class="w-full text-xs border-gray-300 rounded-md" autocomplete="off">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">All</option>
                    <option value="unchecked" @selected(request('status') === 'unchecked')>Unchecked</option>
                    <option value="checked" @selected(request('status') === 'checked')>Checked</option>
                </select>
            </div>
        </div>
    </form>

    <div id="count-results">
        @include('finance-counts._checklist')
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('count-filter-form');
        const resultsContainer = document.getElementById('count-results');
        const isOpen = {{ $financeCount->status === 'open' ? 'true' : 'false' }};
        let debounceTimer = null;

        function loadUrl(url) {
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((res) => res.text())
                .then((html) => {
                    resultsContainer.innerHTML = html;
                    window.history.replaceState(null, '', url);
                })
                .catch((err) => console.error('Count fetch failed:', err));
        }

        function runSearch() {
            const params = new URLSearchParams(new FormData(form)).toString();
            loadUrl(`{{ url()->current() }}?${params}`);
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

        resultsContainer.addEventListener('click', (e) => {
            const link = e.target.closest('[data-page-link]');
            if (!link) return;
            e.preventDefault();
            loadUrl(link.href);
        });

        if (!isOpen) return;

        resultsContainer.addEventListener('change', async (e) => {
            const row = e.target.closest('[data-count-item-id]');
            if (!row) return;

            const itemId = row.dataset.countItemId;
            const qtyInput = row.querySelector('[data-counted-qty]');
            const deptSelect = row.querySelector('[data-dept-select]');
            const statusBadge = row.querySelector('[data-row-status]');

            const counted = qtyInput.value;
            if (counted === '') return;

            try {
                const res = await fetch(`{{ url('finance-counts/'.$financeCount->id.'/items') }}/${itemId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        counted_quantity: counted,
                        department_id: deptSelect.value || null,
                    }),
                });

                if (res.ok) {
                    statusBadge.textContent = 'CHECKED';
                    statusBadge.classList.remove('bg-gray-200', 'text-gray-500');
                    statusBadge.classList.add('bg-green-500', 'text-white');
                }
            } catch (err) {
                console.error('Save failed:', err);
            }
        });
    })();
</script>
@endpush
