{{-- resources/views/admin/users/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">تعديل مشترك: {{ $user->name }}</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl p-4 md:p-6 space-y-6">
        <div class="bg-white rounded-2xl shadow border p-4 md:p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-600">الاسم</label>
                    <input type="text" value="{{ $user->name }}" class="mt-1 w-full rounded-md border-gray-300 bg-gray-100" disabled>
                </div>
                <div>
                    <label class="text-sm text-gray-600">البريد</label>
                    <input type="text" value="{{ $user->email }}" class="mt-1 w-full rounded-md border-gray-300 bg-gray-100" disabled>
                </div>
                <div>
                    <label class="text-sm text-gray-600">الجوال</label>
                    <input type="text" value="{{ $user->phone ?? '—' }}" class="mt-1 w-full rounded-md border-gray-300 bg-gray-100" disabled>
                </div>
                <div>
                    <label class="text-sm text-gray-600">الهوية</label>
                    <input type="text" value="{{ $user->national_id ?? '—' }}" class="mt-1 w-full rounded-md border-gray-300 bg-gray-100" disabled>
                </div>
            </div>

            <form action="{{ route('admin.users.update', ['user' => $user->id]) }}" method="post" class="space-y-4">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="text-sm">
                        حالة الحساب
                        <select name="account_status"
                                class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pending"   @selected($user->account_status==='pending')>pending</option>
                            <option value="active"    @selected($user->account_status==='active')>active</option>
                            <option value="suspended" @selected($user->account_status==='suspended')>suspended</option>
                        </select>
                    </label>

                    <label class="text-sm">
                        التواجد (IN/OUT)
                        <select name="presence_status"
                                class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="in"  @selected($user->presence_status==='in')>in</option>
                            <option value="out" @selected($user->presence_status==='out')>out</option>
                        </select>
                    </label>
                </div>

                <div class="h-16"></div> {{-- مساحة للزر الثابت على الجوال --}}

                <div class="fixed md:static bottom-4 inset-x-4 md:inset-auto z-20
                            w-[calc(100%-2rem)] md:w-auto mx-auto">
                    <button type="submit"
                        class="w-full md:w-auto rounded-xl px-6 py-3 font-extrabold
                               text-white bg-indigo-600 hover:bg-indigo-700
                               shadow-lg ring-2 ring-indigo-300 active:translate-y-[1px]">
                        حفظ
                    </button>
                </div>
            </form>
        </div>

        <div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-700 hover:underline">← رجوع إلى قائمة المشتركين</a>
        </div>
    </div>
</x-app-layout>
