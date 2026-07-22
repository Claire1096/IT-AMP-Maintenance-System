@extends('layouts.shell')

@section('title', 'Add Employee')

@section('content')
    <div class="flex justify-between items-start mb-6">
        <div>
            <h1 class="text-lg font-bold">ADD EMPLOYEE</h1>
            <p class="text-xs text-gray-400">Employees / Add new employee</p>
        </div>
        <a href="{{ route('employees.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-2xl">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('employees.store') }}" class="max-w-2xl">
        @csrf
        <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
            <div class="mb-3">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                <select name="department_id" id="department-select" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">Select</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">FIRST NAME *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">LAST NAME *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full text-xs border-gray-300 rounded-md">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">EMAIL</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PHONE</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. 0935-456-1236" class="w-full text-xs border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">POSITION</label>
                <select name="position" id="position-select" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">Select department first</option>
                </select>
            </div>

            <div class="text-right">
                <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                    SAVE EMPLOYEE
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const positionsByDept = @json(
        $positions->groupBy('department_id')->map(fn ($group) => $group->pluck('title', 'id'))
    );

    const deptSelect = document.getElementById('department-select');
    const posSelect = document.getElementById('position-select');
    const oldPosition = @json(old('position'));

    function populatePositions(deptId, selected = '') {
        posSelect.innerHTML = '';

        const options = positionsByDept[deptId];

        if (!options) {
            posSelect.innerHTML = '<option value="">Select department first</option>';
            return;
        }

        const blank = document.createElement('option');
        blank.value = '';
        blank.textContent = 'Select position';
        posSelect.appendChild(blank);

        Object.values(options).forEach(title => {
            const opt = document.createElement('option');
            opt.value = title;
            opt.textContent = title;
            if (title === selected) opt.selected = true;
            posSelect.appendChild(opt);
        });
    }

    deptSelect.addEventListener('change', function () {
        populatePositions(this.value);
    });

    if (deptSelect.value) {
        populatePositions(deptSelect.value, oldPosition);
    }
});
</script>
@endpush