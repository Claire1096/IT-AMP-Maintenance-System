@props(['departments'])

<div x-show="showAddEmployee" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" style="display: none;">
    <div @click.away="showAddEmployee = false" class="bg-white rounded-xl p-5 w-full max-w-sm shadow-lg">
        <h3 class="text-xs font-bold text-pink-600 uppercase mb-4">Quick Add Employee</h3>

        <form method="POST" action="{{ route('employees.store') }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

            <div class="grid grid-cols-2 gap-2 mb-2">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">FIRST NAME *</label>
                    <input type="text" name="first_name" required class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">LAST NAME *</label>
                    <input type="text" name="last_name" required class="w-full text-xs border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mb-2">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">DEPARTMENT</label>
                <select name="department_id" class="w-full text-xs border-gray-300 rounded-md">
                    <option value="">— None —</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2 mb-2">
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">EMAIL</label>
                    <input type="email" name="email" class="w-full text-xs border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-gray-500 mb-1">PHONE</label>
                    <input type="text" name="phone" placeholder="e.g. 0935-456-1236" class="w-full text-xs border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-[10px] font-semibold text-gray-500 mb-1">POSITION</label>
                <input type="text" name="position" placeholder="e.g. IT Support" class="w-full text-xs border-gray-300 rounded-md">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" @click="showAddEmployee = false" class="px-4 py-2 text-xs text-gray-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-pink-600 text-white text-xs font-semibold rounded-full hover:bg-pink-700">
                    Save Employee
                </button>
            </div>
        </form>
    </div>
</div>

