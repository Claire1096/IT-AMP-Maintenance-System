@extends('layouts.shell')
@section('title', 'Asset Inventory Report')
@section('content')

            <div class="mb-6">
                <h1 class="text-lg font-bold">REPORTS</h1>
                <p class="text-xs text-gray-400">Asset Inventory Report</p>
            </div>

            <div class="grid grid-cols-3 gap-6">
                {{-- Report preview (printed document look) --}}
                <div class="col-span-2 bg-white border border-pink-200 rounded-xl p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-sm mb-2">EM</div>
                        <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                        <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                        <h2 class="font-bold text-base uppercase">Asset Inventory Report</h2>
                    </div>

                    <div class="text-xs mb-6 space-y-1">
                        <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                        <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                        <div><span class="font-semibold">Category</span> : {{ optional(\App\Models\AssetCategory::find(request('category_id')))->name ?? 'All Categories' }}</div>
                        <div><span class="font-semibold">Department</span> : {{ optional(\App\Models\Department::find(request('department_id')))->name ?? 'All Departments' }}</div>
                        <div><span class="font-semibold">Status</span> : {{ request('status') ? ucwords(str_replace('_', ' ', request('status'))) : 'All Statuses' }}</div>
                        <div><span class="font-semibold">Total Assets</span> : {{ $assets->count() }}</div>
                    </div>

                    <table class="min-w-full text-xs border-t border-gray-200">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2">Asset Tag</th>
                                <th class="py-2">Name</th>
                                <th class="py-2">Category</th>
                                <th class="py-2">Assigned To</th>
                                <th class="py-2">Department</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assets as $asset)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 font-mono">{{ $asset->asset_tag }}</td>
                                    <td class="py-2">{{ $asset->name }}</td>
                                    <td class="py-2">{{ $asset->category->name ?? '—' }}</td>
                                    <td class="py-2">{{ $asset->assignedEmployee->full_name ?? '—' }}</td>
                                    <td class="py-2">{{ $asset->department->name ?? '—' }}</td>
                                    <td class="py-2">{{ ucwords(str_replace('_', ' ', $asset->status)) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-400">No assets match this filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Filter / confirmation panel --}}
                <div class="bg-white border border-pink-200 rounded-xl p-5 h-fit">
                    <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">Confirmation Details</h2>

                    <form method="GET">
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">REPORT TYPE</label>
                            <select onchange="window.location.href=this.value" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="{{ route('reports.inventory') }}" selected>Asset Inventory Report</option>
                                <option value="{{ route('reports.preventive-maintenance') }}">Preventive Maintenance Report</option>
                                <option value="{{ route('reports.warranty-expiration') }}">Warranty Expiration Report</option>
                                <option value="{{ route('reports.repair-history') }}">Repair History Report</option>
                                <option value="{{ route('reports.asset-assignment') }}">Asset Assignment Report</option>
                                <option value="{{ route('reports.annual-summary') }}">Annual Asset Summary</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                            <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">Select Department</option>
                                @foreach (\App\Models\Department::all() as $department)
                                    <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY</label>
                            <select name="category_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">Select Category</option>
                                @foreach (\App\Models\AssetCategory::all() as $category)
                                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS</label>
                            <select name="status" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">Select Status</option>
                                @foreach (['active', 'under_repair', 'for_disposal', 'lost'] as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                                SAVE
                            </button>
                        </div>
                    </form>
                </div>
            </div>

@endsection
