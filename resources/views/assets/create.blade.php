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
            <span class="text-gray-400">&#128276;</span>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-300"></div>
                <div class="leading-tight text-xs">
                    <div class="font-semibold">{{ auth()->user()->name ?? 'Admin user' }}</div>
                    <div class="text-gray-400">{{ auth()->user()->role ?? 'administrator' }}</div>
                </div>
            </div>
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
            <a href="{{ route('employees.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-gray-400 cursor-not-allowed">
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
                                <select name="assigned_employee_id" class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">— Unassigned —</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('assigned_employee_id') == $employee->id)>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                                <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">— None —</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-[10px] font-semibold text-gray-500 mb-1">LOCATION</label>
                                <select name="location_id" class="w-full text-xs border-gray-300 rounded-md">
                                    <option value="">— None —</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                            {{ $location->building->name ?? '' }} — {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
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
        </div>
    </div>
</body>
</html>
