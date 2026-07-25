<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Asset — EM Power Beautiful Skin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-rose-50 text-gray-800 text-sm">

    <div class="bg-rose-100 border-b border-rose-200 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-xs">EM</div>
            <div class="leading-tight">
                <div class="font-semibold text-pink-700 text-sm">EM Power Beautiful Skin</div>
                <div class="text-[10px] tracking-widest text-gray-400">CORPORATION</div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <input type="text" placeholder="search asset ID/Employee..." class="text-xs border-gray-300 rounded-full px-4 py-1.5 w-56">
            <x-notification-bell />
            <x-account-menu />
        </div>
    </div>

    <div class="flex">
        <div class="w-48 bg-rose-50 border-r border-rose-200 min-h-screen py-4 px-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#8962;</span> Dashboard
            </a>
            <a href="{{ route('assets.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-200 text-gray-800">
                <span>&#128421;</span> Assets
            </a>
            <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
                <span>&#128101;</span> Employees
            </a>
            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#9881;</span> Maintenance
            </a>
            <a href="{{ route('reports.inventory') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-600 hover:bg-rose-100">
                <span>&#128196;</span> Reports
            </a>
        </div>

        <div class="flex-1 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-lg font-bold">ADD ASSET</h1>
                    <p class="text-xs text-gray-400">Asset Management / Add new asset</p>
                </div>
                <a href="{{ route('assets.index') }}" class="px-4 py-1.5 bg-pink-500 text-white text-xs font-semibold rounded-full">&#8249; BACK</a>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-md text-xs max-w-3xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('assets.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-6 max-w-3xl">

                    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm">
                        <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128221; Asset Information</h2>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET ID</label>
                                <input type="text" disabled placeholder="auto-generated after saving"
                                       class="w-full text-xs border-gray-200 rounded-md bg-gray-50 text-gray-400">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">CATEGORY *</label>
                                <select name="category_id" required class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">Select</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSET NAME *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="eg. Dell Latitude 5420.."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">BRAND</label>
                                <input type="text" name="brand" value="{{ old('brand') }}" placeholder="eg. Dell..."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">MODEL</label>
                                <input type="text" name="model" value="{{ old('model') }}" placeholder="eg. Latitude 5420.."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">SERIAL NUMBER</label>
                                <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="eg. 42H5-Y642-W524785.."
                                       class="w-full text-xs border-gray-300 rounded-md">
                            </div>
                        </div>
<div class="mb-3">
    <label class="block text-[10px] font-semibold text-gray-500 mb-1">STATUS *</label>
    <select name="status" required class="w-full text-xs border-gray-300 rounded-md">
        <option value="active" @selected(old('status', 'active') == 'active')>Active</option>
        <option value="under_repair" @selected(old('status') == 'under_repair')>Under Repair</option>
        <option value="for_disposal" @selected(old('status') == 'for_disposal')>For Disposal</option>
        <option value="lost" @selected(old('status') == 'lost')>Lost</option>
    </select>
</div>
                        <div class="mb-3">
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">QR CODE</label>
                            <div class="text-xs text-gray-400 border border-dashed border-gray-300 rounded-md px-3 py-2">
                                Generated automatically after saving
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-gray-500 mb-1">NOTES</label>
                            <textarea name="notes" rows="2" class="w-full text-xs border-gray-300 rounded-md">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="bg-white border border-rose-100 rounded-xl p-5 shadow-sm flex flex-col justify-between">
                        <div>
                            <h2 class="text-xs font-bold text-pink-600 uppercase mb-4">&#128100; Asset Assignment</h2>

                          <div class="mb-3">
    <label class="block text-[10px] font-semibold text-gray-500 mb-1">ASSIGNED TO</label>
    <select name="assigned_employee_id" id="assigned-employee-select" class="w-full text-xs border-gray-300 rounded-md">
        <option value="">— Unassigned —</option>
        @foreach ($employees as $employee)
            <option value="{{ $employee->id }}" @selected(old('assigned_employee_id') == $employee->id)>
                {{ $employee->full_name }}
            </option>
        @endforeach
        <option value="__new__">+ Add New Employee...</option>
    </select>
</div>
<div class="grid grid-cols-2 gap-3 mb-3">
    <div>
        <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
        <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
            <option value="">Select</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
        <select name="location_id" class="w-full text-xs border-gray-300 rounded-md">
            <option value="">Select</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                    {{ $location->name }}{{ $location->building ? ' — ' . $location->building->name : '' }}
                </option>
            @endforeach
        </select>
    </div>
</div>

          

                            <h3 class="text-xs font-bold text-pink-600 uppercase mb-3">&#128722; Purchase Details</h3>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE DATE</label>
                                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                                           class="w-full text-xs border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">SUPPLIER</label>
                                    <select name="supplier_id" class="w-full text-xs border-gray-300 rounded-md">
                                        <option value="">Select</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">WARRANTY EXPIRATION</label>
                                    <input type="date" name="warranty_expiration" value="{{ old('warranty_expiration') }}"
                                           class="w-full text-xs border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PURCHASE COST</label>
                                    <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost') }}"
                                           class="w-full text-xs border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="px-6 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                                SAVE CHANGES
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div id="quick-add-employee-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
        <h3 class="text-sm font-bold text-pink-600 uppercase mb-4">Add New Employee</h3>

        <div id="quick-add-error" class="hidden mb-3 p-2 bg-red-100 text-red-700 text-xs rounded-md"></div>

        <div class="mb-3">
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">FIRST NAME *</label>
            <input type="text" id="quick-first-name" class="w-full text-xs border-gray-300 rounded-md">
        </div>
        <div class="mb-3">
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">LAST NAME *</label>
            <input type="text" id="quick-last-name" class="w-full text-xs border-gray-300 rounded-md">
        </div>
        <div class="mb-3">
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
            <select id="quick-department" class="w-full text-xs border-gray-300 rounded-md">
                <option value="">— None —</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-[10px] font-semibold text-gray-500 mb-1">POSITION</label>
            <select id="quick-position" class="w-full text-xs border-gray-300 rounded-md">
                <option value="">Select department first</option>
            </select>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" id="quick-add-cancel" class="px-4 py-1.5 text-xs font-semibold rounded-full border border-gray-300 text-gray-600">
                Cancel
            </button>
            <button type="button" id="quick-add-save" class="px-4 py-1.5 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                Save Employee
            </button>
        </div>
    </div>
</div>
        </div>
    </div>

   <script>
document.addEventListener('DOMContentLoaded', function () {
    const positionsByDept = @json(
        $positions->groupBy('department_id')->map(fn ($group) => $group->pluck('title', 'id'))
    );

    const deptSelect = document.getElementById('quick-department');
    const posSelect = document.getElementById('quick-position');

    function populatePositions(deptId) {
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
            posSelect.appendChild(opt);
        });
    }

    deptSelect.addEventListener('change', function () {
        populatePositions(this.value);
    });

    const assignedSelect = document.getElementById('assigned-employee-select');
    const modal = document.getElementById('quick-add-employee-modal');
    const errorBox = document.getElementById('quick-add-error');
    const firstNameInput = document.getElementById('quick-first-name');
    const lastNameInput = document.getElementById('quick-last-name');
    const saveBtn = document.getElementById('quick-add-save');
    const cancelBtn = document.getElementById('quick-add-cancel');

    function openModal() {
        errorBox.classList.add('hidden');
        firstNameInput.value = '';
        lastNameInput.value = '';
        deptSelect.value = '';
        posSelect.innerHTML = '<option value="">Select department first</option>';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    assignedSelect.addEventListener('change', function () {
        if (this.value === '__new__') {
            openModal();
        }
    });

    cancelBtn.addEventListener('click', function () {
        assignedSelect.value = '';
        closeModal();
    });

    saveBtn.addEventListener('click', async function () {
        const firstName = firstNameInput.value.trim();
        const lastName = lastNameInput.value.trim();

        if (!firstName || !lastName) {
            errorBox.textContent = 'First name and last name are required.';
            errorBox.classList.remove('hidden');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        try {
            const res = await fetch('{{ route("employees.quick-store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    first_name: firstName,
                    last_name: lastName,
                    department_id: deptSelect.value || null,
                    position: posSelect.value || null,
                }),
            });

            if (!res.ok) {
                const data = await res.json();
                errorBox.textContent = data.message || 'Something went wrong.';
                errorBox.classList.remove('hidden');
                return;
            }

            const employee = await res.json();

            const opt = document.createElement('option');
            opt.value = employee.id;
            opt.textContent = employee.name;
            opt.selected = true;

            const newOption = assignedSelect.querySelector('option[value="__new__"]');
            assignedSelect.insertBefore(opt, newOption);

            closeModal();
        } catch (err) {
            errorBox.textContent = 'Network error — please try again.';
            errorBox.classList.remove('hidden');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Employee';
        }
    });
});
</script>
</body>
</html>

