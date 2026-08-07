@extends(request()->boolean('embed') ? 'layouts.embed' : 'layouts.shell')
@section('title', 'Repair History Report')
@section('content')

            <div class="no-print flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-lg font-bold">REPORTS</h1>
                    <p class="text-xs text-gray-400">Repair History Report</p>
                </div>
                <div class="flex gap-2">
                    @if (auth()->user()->role === 'executive')
                        <a href="{{ route('executive.reports') }}" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">&#8249; BACK</a>
                    @endif
                    <button onclick="window.print()" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">PRINT REPORT</button>
                </div>
            </div>

            @if (auth()->user()->role === 'executive')
                <div class="print-full bg-white border border-pink-200 rounded-xl p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <img src="{{ asset('logo.svg') }}" alt="EM Power Beautiful Skin" class="w-12 h-12 rounded-full object-cover mb-2">
                        <div class="font-semibold text-sm">E<span class="text-pink-600">M</span> Power Beautiful Skin</div>
                        <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                        <h2 class="font-bold text-base uppercase">Repair History Report</h2>
                    </div>

                    <div class="text-xs mb-6 space-y-1">
                        <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                        <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                        <div><span class="font-semibold">Total Repairs</span> : {{ $repairs->count() }}</div>
                        <div><span class="font-semibold">Total Repair Cost</span> : {{ number_format($totalCost, 2) }}</div>
                    </div>

                    <table class="min-w-full text-xs border-t border-gray-200">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2">Asset</th>
                                <th class="py-2">Reported</th>
                                <th class="py-2">Issue</th>
                                <th class="py-2">Technician</th>
                                <th class="py-2">Cost</th>
                                <th class="py-2">Downtime (hrs)</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($repairs as $repair)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 font-mono">{{ $repair->asset->asset_tag }}</td>
                                    <td class="py-2">{{ $repair->reported_date->format('M d, Y') }}</td>
                                    <td class="py-2">{{ \Illuminate\Support\Str::limit($repair->issue_description, 40) }}</td>
                                    <td class="py-2">{{ $repair->technician->name ?? '—' }}</td>
                                    <td class="py-2">{{ number_format($repair->cost, 2) }}</td>
                                    <td class="py-2">{{ $repair->downtime_hours ?? '—' }}</td>
                                    <td class="py-2">{{ ucwords($repair->status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-6 text-center text-gray-400">No repairs match this filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="grid grid-cols-3 gap-6">
                    <div class="col-span-2 print-full bg-white border border-pink-200 rounded-xl p-8">
                        <div class="flex flex-col items-center text-center mb-6">
                            <img src="{{ asset('logo.svg') }}" alt="EM Power Beautiful Skin" class="w-12 h-12 rounded-full object-cover mb-2">
                            <div class="font-semibold text-sm">E<span class="text-pink-600">M</span> Power Beautiful Skin</div>
                            <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                            <h2 class="font-bold text-base uppercase">Repair History Report</h2>
                        </div>

                        <div class="text-xs mb-6 space-y-1">
                            <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                            <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                            <div><span class="font-semibold">Date Range</span> : {{ request('from') ?? 'All time' }} — {{ request('to') ?? 'Present' }}</div>
                            <div><span class="font-semibold">Total Repairs</span> : {{ $repairs->count() }}</div>
                            <div><span class="font-semibold">Total Repair Cost</span> : {{ number_format($totalCost, 2) }}</div>
                        </div>

                        <table class="min-w-full text-xs border-t border-gray-200">
                            <thead>
                                <tr class="text-left border-b border-gray-200">
                                    <th class="py-2">Asset</th>
                                    <th class="py-2">Reported</th>
                                    <th class="py-2">Issue</th>
                                    <th class="py-2">Technician</th>
                                    <th class="py-2">Cost</th>
                                    <th class="py-2">Downtime (hrs)</th>
                                    <th class="py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($repairs as $repair)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 font-mono">{{ $repair->asset->asset_tag }}</td>
                                        <td class="py-2">{{ $repair->reported_date->format('M d, Y') }}</td>
                                        <td class="py-2">{{ \Illuminate\Support\Str::limit($repair->issue_description, 40) }}</td>
                                        <td class="py-2">{{ $repair->technician->name ?? '—' }}</td>
                                        <td class="py-2">{{ number_format($repair->cost, 2) }}</td>
                                        <td class="py-2">{{ $repair->downtime_hours ?? '—' }}</td>
                                        <td class="py-2">{{ ucwords($repair->status) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-6 text-center text-gray-400">No repairs match this filter.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="no-print bg-white border border-pink-200 rounded-xl p-5 h-fit">
                        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Confirmation Details</h2>
                        <form method="GET">
                            <div class="mb-3">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT TYPE</label>
                                <select onchange="window.location.href=this.value" class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="{{ route('reports.inventory') }}">Asset Inventory Report</option>
                                    <option value="{{ route('reports.preventive-maintenance') }}">Preventive Maintenance Report</option>
                                    <option value="{{ route('reports.warranty-expiration') }}">Warranty Expiration Report</option>
                                    <option value="{{ route('reports.repair-history') }}" selected>Repair History Report</option>
                                    <option value="{{ route('reports.asset-assignment') }}">Asset Assignment Report</option>
                                    <option value="{{ route('reports.annual-summary') }}">Annual Asset Summary</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">FROM DATE</label>
                                <input type="date" name="from" value="{{ request('from') }}" class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                            <div class="mb-4">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">TO DATE</label>
                                <input type="date" name="to" value="{{ request('to') }}" class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                            <div class="text-right">
                                <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">SAVE</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

@endsection
