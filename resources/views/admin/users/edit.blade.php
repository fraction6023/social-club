{{-- resources/views/admin/users/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">تعديل مشترك: {{ $user->name }}</h2>
    </x-slot>

    {{-- مساحة سفلية لعدم تغطية المحتوى بالبار الثابت --}}
    <div class="mx-auto max-w-3xl p-4 md:p-6 space-y-6 pb-28 md:pb-10" dir="rtl">

        {{-- تنبيه نجاح --}}
        @if(session('ok'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 p-3">
                {{ session('ok') }}
            </div>
        @endif

        {{-- أخطاء التحقق --}}
        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 p-3">
                <div class="font-semibold mb-1">تحقّق من المدخلات:</div>
                <ul class="list-disc pr-6 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow border p-4 md:p-6 space-y-5">
            {{-- معلومات ثابتة --}}
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

            {{-- نموذج التعديل --}}
            <form id="user-edit-form" action="{{ route('admin.users.update', ['user' => $user->id]) }}" method="post" class="space-y-4">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="text-sm">
                        حالة الحساب
                        <select name="account_status"
                                class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="pending"   @selected(old('account_status', $user->account_status)==='pending')>pending</option>
                            <option value="active"    @selected(old('account_status', $user->account_status)==='active')>active</option>
                            <option value="suspended" @selected(old('account_status', $user->account_status)==='suspended')>suspended</option>
                        </select>
                    </label>

                    <label class="text-sm">
                        التواجد (IN/OUT)
                        <select name="presence_status"
                                class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="in"  @selected(old('presence_status', $user->presence_status)==='in')>in</option>
                            <option value="out" @selected(old('presence_status', $user->presence_status)==='out')>out</option>
                        </select>
                    </label>
                </div>
            </form>
        </div>

        <div>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-700 hover:underline">← رجوع إلى قائمة المشتركين</a>
        </div>
    </div>

    {{-- شريط حفظ ثابت أسفل الصفحة --}}
    <div class="fixed inset-x-0 bottom-0 z-[999] bg-white/95 backdrop-blur border-t">
        <div class="mx-auto max-w-3xl px-4 py-3">
            <div class="flex justify-end">
                <button type="submit" form="user-edit-form"
                    class="rounded-xl px-6 py-3 font-extrabold
                           text-white bg-indigo-600 hover:bg-indigo-700
                           shadow-lg ring-2 ring-indigo-300 active:translate-y-[1px]">
                    حفظ
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
