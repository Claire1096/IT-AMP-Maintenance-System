<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Register New Asset</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-md text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('assets.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Asset Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Dell Latitude 5420"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category *</label>
                            <select name="category_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                            <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Model</label>
                            <input type="text" name="model" value="{{ old('model') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <hr>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Assignment</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assign to Employee</label>
                            <select name="assigned_employee_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">— Unassigned —</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('assigned_employee_id') == $employee->id)>
                                        {{ $employee->full_name }} @if($employee->department) ({{ $employee->department->name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <select name="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">— None —</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Location</label>
                            <select name="location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">— None —</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                        {{ $location->building->name ?? '' }} — {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>
                    <h3 class="text-sm font-semibold text-gray-600 uppercase">Procurement</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Purchase Cost</label>
                            <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Warranty Expiration</label>
                            <input type="date" name="warranty_expiration" value="{{ old('warranty_expiration') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supplier</label>
                            <select name="supplier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">— None —</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('assets.index') }}" class="px-4 py-2 text-sm text-gray-600">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700">
                            Register Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>