{{-- resources/views/attendance/accepted.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">تم التحديث</h2>
    </x-slot>

    <div class="mx-auto max-w-xl p-6" dir="rtl">
        <div class="rounded-2xl border bg-white p-6 shadow">
            <div class="text-2xl font-bold mb-2">تم تحديث حالة {{ $user->name }}</div>
            <div class="text-gray-700">الحالة الجديدة: 
                <span class="font-semibold {{ $action === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $action === 'in' ? 'داخل' : 'خارج' }}
                </span>
            </div>
            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="text-indigo-700 hover:underline">← رجوع</a>
            </div>
        </div>
    </div>
</x-app-layout>
