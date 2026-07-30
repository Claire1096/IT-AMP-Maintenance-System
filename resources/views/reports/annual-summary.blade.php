@extends('layouts.shell')
@section('title', 'Annual Asset Summary')
@section('content')

            <div class="mb-6">
                <h1 class="text-lg font-bold">REPORTS</h1>
                <p class="text-xs text-gray-400">Annual Asset Summary</p>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-pink-200 rounded-xl p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-sm mb-2">EM</div>
                        <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                        <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                        <h2 class="font-bold text-base uppercase">Annual Asset Summary — {{ $year }}</h2>
                    </div>

                    <div class="text-xs mb-6 space-y-1 text-center">
                        <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                        <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">New Assets</div>
                            <div class="text-xl font-bold">{{ $newAssets }}</div>
                        </div>
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">Disposed Assets</div>
                            <div class="text-xl font-bold">{{ $disposedAssets }}</div>
                        </div>
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">Maintenance Completed</div>
                            <div class="text-xl font-bold">{{ $totalMaintenanceCompleted }}</div>
                        </div>
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">Total Purchase Spend</div>
                            <div class="text-xl font-bold">{{ number_format($totalSpendOnPurchases, 2) }}</div>
                        </div>
                        <div class="border border-rose-100 rounded-lg p-4 text-center">
                            <div class="text-[10px] font-semibold text-gray-500 uppercase">Total Repair Cost</div>
                            <div class="text-xl font-bold">{{ number_format($totalRepairCost, 2) }}</div>
                        </div>
                    </div>

                    <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">New Assets by Category</h3>
                    <table class="min-w-full text-xs border-t border-gray-200">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2">Category</th>
                                <th class="py-2">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($byCategory as $row)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2">{{ $row->category->name ?? '—' }}</td>
                                    <td class="py-2">{{ $row->total }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-6 text-center text-gray-400">No new assets registered in {{ $year }}.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white border border-pink-200 rounded-xl p-5 h-fit">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Confirmation Details</h2>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT TYPE</label>
                            <select onchange="window.location.href=this.value" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="{{ route('reports.inventory') }}">Asset Inventory Report</option>
                                <option value="{{ route('reports.preventive-maintenance') }}">Preventive Maintenance Report</option>
                                <option value="{{ route('reports.warranty-expiration') }}">Warranty Expiration Report</option>
                                <option value="{{ route('reports.repair-history') }}">Repair History Report</option>
                                <option value="{{ route('reports.asset-assignment') }}">Asset Assignment Report</option>
                                <option value="{{ route('reports.annual-summary') }}" selected>Annual Asset Summary</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">YEAR</label>
                            <input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="w-full text-xs border-gray-300 rounded-md">
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>

@endsection
