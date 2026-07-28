@extends('layouts.shell')

@section('title', 'Damage Report ' . $report->report_number)

@section('content')
    <div class="flex justify-between items-start mb-6" id="page-controls">
        <div>
            <h1 class="text-lg font-bold">REPORTS</h1>
            <p class="text-xs text-gray-400">Reports / Asset Damage Report / {{ $report->report_number }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.damage.index') }}" class="px-4 py-1.5 border border-gray-300 text-gray-600 text-xs font-semibold rounded-full">&#8249; BACK</a>
            <button onclick="window.print()" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full">PRINT REPORT</button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md text-xs">{{ session('success') }}</div>
    @endif

    <div class="bg-white border border-rose-100 rounded-xl p-8 shadow-sm max-w-3xl mx-auto" id="printable-report">
        <div class="text-center mb-6">
            <h2 class="text-sm font-bold text-pink-600 uppercase">EM Power Beautiful Skin</h2>
            <p class="text-xs text-gray-400 uppercase">Corporation</p>
            <h3 class="text-base font-bold mt-3">ASSET DAMAGE REPORT</h3>
        </div>

        <div class="grid grid-cols-2 gap-6 text-xs mb-6">
            <div class="space-y-2">
                <div><span class="font-semibold text-gray-500">Report Number:</span> {{ $report->report_number }}</div>
                <div><span class="font-semibold text-gray-500">Date Reported:</span> {{ $report->date_reported?->format('M d, Y') }}</div>
                <div><span class="font-semibold text-gray-500">Asset Name:</span> {{ $report->asset_name ?: '—' }}</div>
                <div><span class="font-semibold text-gray-500">Asset Tag No:</span> {{ $report->asset_tag_no ?: '—' }}</div>
                <div><span class="font-semibold text-gray-500">Asset Type:</span> {{ $report->asset_type ?: '—' }}</div>
                <div><span class="font-semibold text-gray-500">Date of Incident:</span> {{ $report->date_of_incident?->format('M d, Y') ?: '—' }}</div>
                <div><span class="font-semibold text-gray-500">Time of Incident:</span> {{ $report->time_of_incident ?: '—' }}</div>
                <div><span class="font-semibold text-gray-500">Type of Incident:</span> {{ $report->type_of_incident ?: '—' }}</div>
            </div>
            <div class="space-y-2">
                <div><span class="font-semibold text-gray-500">Cause of Damage:</span> {{ $report->cause_of_damage ? ucwords(str_replace('_', ' ', $report->cause_of_damage)) : '—' }}</div>
                @if ($report->cause_of_damage === 'other' && $report->cause_other_note)
                    <div><span class="font-semibold text-gray-500">Specify:</span> {{ $report->cause_other_note }}</div>
                @endif
                <div><span class="font-semibold text-gray-500">Action Taken:</span> {{ $report->action_taken ? ucwords(str_replace('_', ' ', $report->action_taken)) : '—' }}</div>
                <div><span class="font-semibold text-gray-500">Condition:</span> {{ $report->condition ?: '—' }}</div>
                <div><span class="font-semibold text-gray-500">Inspected By:</span> {{ $report->inspected_by ?: '—' }}</div>
                <div><span class="font-semibold text-gray-500">Inspection Date:</span> {{ $report->inspection_date?->format('M d, Y') ?: '—' }}</div>
                <div><span class="font-semibold text-gray-500">Estimated Cost:</span> {{ $report->estimated_cost ? number_format($report->estimated_cost, 2) : '—' }}</div>
            </div>
        </div>

        <div class="mb-6">
            <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Description</div>
            <div class="text-xs text-gray-700 border border-gray-200 rounded-md p-3 min-h-[60px]">{{ $report->description ?: '—' }}</div>
        </div>

        <div class="mb-8">
            <div class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Remarks</div>
            <div class="text-xs text-gray-700 border border-gray-200 rounded-md p-3 min-h-[60px]">{{ $report->remarks ?: '—' }}</div>
        </div>

        <div class="flex justify-between items-end text-xs">
            <div>
                <div class="font-semibold text-gray-500 mb-1">Facilitator Name:</div>
                <div class="border-b border-gray-400 w-48 pb-1">{{ $report->facilitator_name ?: '—' }}</div>
            </div>
            <div>
                <div class="font-semibold text-gray-500 mb-1">Date:</div>
                <div class="border-b border-gray-400 w-32 pb-1">{{ $report->facilitator_date?->format('M d, Y') ?: '—' }}</div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            #printable-report, #printable-report * { visibility: visible; }
            #printable-report {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection