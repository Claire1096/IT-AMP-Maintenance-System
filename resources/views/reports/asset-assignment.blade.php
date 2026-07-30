@extends('layouts.shell')
@section('title', 'Asset Assignment Report')
@section('content')

            <div class="mb-6">
                <h1 class="text-lg font-bold">REPORTS</h1>
                <p class="text-xs text-gray-400">Asset Assignment Report</p>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-pink-200 rounded-xl p-8">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-12 h-12 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-sm mb-2">EM</div>
                        <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                        <div class="text-[10px] tracking-widest text-gray-400 mb-4">CORPORATION</div>
                        <h2 class="font-bold text-base uppercase">Asset Assignment Report</h2>
                    </div>

                    <div class="text-xs mb-6 space-y-1">
                        <div><span class="font-semibold">Report Created By</span> : {{ auth()->user()->name }}</div>
                        <div><span class="font-semibold">Date Created</span> : {{ now()->format('F j, Y') }}</div>
                        <div><span class="font-semibold">Department</span> : {{ optional(\App\Models\Department::find(request('department_id')))->name ?? 'All Departments' }}</div>
                        <div><span class="font-semibold">Filter</span> : {{ request('active_only') ? 'Active assignments only' : 'All assignments' }}</div>
                        <div><span class="font-semibold">Total Records</span> : {{ $assignments->count() }}</div>
                    </div>

                    <table class="min-w-full text-xs border-t border-gray-200">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="py-2">Asset</th>
                                <th class="py-2">Employee</th>
                                <th class="py-2">Department</th>
                                <th class="py-2">Assigned Date</th>
                                <th class="py-2">Returned Date</th>
                                <th class="py-2">Assigned By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assignments as $assignment)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 font-mono">{{ $assignment->asset->asset_tag ?? '—' }}</td>
                                    <td class="py-2">{{ $assignment->employee->full_name ?? '—' }}</td>
                                    <td class="py-2">{{ $assignment->department->name ?? '—' }}</td>
                                    <td class="py-2">{{ $assignment->assigned_date->format('M d, Y') }}</td>
                                    <td class="py-2">{{ optional($assignment->returned_date)->format('M d, Y') ?? '—' }}</td>
                                    <td class="py-2">{{ $assignment->assignedBy->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-400">No assignment records match this filter.</td></tr>
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
                                <option value="{{ route('reports.asset-assignment') }}" selected>Asset Assignment Report</option>
                                <option value="{{ route('reports.annual-summary') }}">Annual Asset Summary</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                            <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                                <option value="">All Departments</option>
                                @foreach (\App\Models\Department::all() as $department)
                                    <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center gap-2 text-xs">
                                <input type="checkbox" name="active_only" value="1" @checked(request('active_only'))>
                                Active assignments only
                            </label>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>

@endsection
