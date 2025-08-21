<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">إدارة المشتركين</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4 md:p-6 space-y-4">

        {{-- شريط البحث (ثابت أعلى الصفحة على الجوال) --}}
        <form method="get"
              class="sticky top-0 z-10 bg-white/90 backdrop-blur rounded-xl border p-3 flex items-center gap-2">
            <input type="text" name="q" value="{{ $q }}"
                   class="flex-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="بحث بالاسم/الإيميل/الجوال/الهوية">
            <button class="hidden md:inline-flex items-center px-4 py-2 rounded-md bg-slate-900 text-white">بحث</button>
            <button class="md:hidden inline-flex items-center px-3 py-2 rounded-md bg-slate-900 text-white">بحث</button>
            @if($q)
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-3 py-2 rounded-md border">إعادة تعيين</a>
            @endif
        </form>

        {{-- عرض بطاقات على الجوال --}}
        <div class="md:hidden space-y-4">
            @foreach($users as $u)
                <form class="user-row rounded-2xl border shadow-sm p-4 space-y-3"
                      action="{{ route('admin.users.update', ['user'=>$u->id], false) }}"
                      method="post">
                    @csrf
                    @method('patch')

                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-bold">{{ $u->name }}</div>
                            <div class="text-xs text-gray-500">{{ $u->email }}</div>
                            <div class="text-xs text-gray-400">الجوال: {{ $u->phone ?? '—' }}</div>
                        </div>
                        <div class="text-xs text-gray-500">#{{ $u->id }}</div>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        <label class="text-sm">
                            حالة الحساب
                            @php $as = $u->account_status; @endphp
                            <select name="account_status"
                                    class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="pending"   @selected($as==='pending')>pending</option>
                                <option value="active"    @selected($as==='active')>active</option>
                                <option value="suspended" @selected($as==='suspended')>suspended</option>
                            </select>
                        </label>

                        <label class="text-sm">
                            التواجد (IN/OUT)
                            @php $ps = $u->presence_status; @endphp
                            <select name="presence_status"
                                    class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="in"  @selected($ps==='in')>in</option>
                                <option value="out" @selected($ps==='out')>out</option>
                            </select>
                        </label>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="save-row flex-1 inline-flex justify-center items-center px-4 py-2 rounded-xl
                                       bg-emerald-600 text-white font-semibold shadow hover:bg-emerald-700
                                       active:translate-y-[1px]">
                            حفظ
                        </button>
                        <span class="ok-badge hidden text-emerald-700 text-xs">تم</span>
                        <span class="err-badge hidden text-red-600 text-xs">خطأ</span>
                    </div>
                </form>
            @endforeach

            <div>{{ $users->links() }}</div>
        </div>

        {{-- جدول على الشاشات المتوسطة فما فوق --}}
        <div class="hidden md:block overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-right">#</th>
                        <th class="px-4 py-3 text-right">الاسم</th>
                        <th class="px-4 py-3 text-right">البريد</th>
                        <th class="px-4 py-3 text-right">الجوال</th>
                        <th class="px-4 py-3 text-right">رقم الهوية</th>
                        <th class="px-4 py-3 text-right">حالة الحساب</th>
                        <th class="px-4 py-3 text-right">التواجد</th>
                        <th class="px-4 py-3 text-right">حفظ</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                @foreach($users as $u)
                    <tr>
                        <td class="px-4 py-2">{{ $u->id }}</td>
                        <td class="px-4 py-2">
                            <div class="font-semibold">{{ $u->name }}</div>
                            <div class="text-xs text-gray-500">الجنسية: {{ $u->nationality ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $u->email }}</td>
                        <td class="px-4 py-2">{{ $u->phone ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $u->national_id ?? '—' }}</td>

                        <td class="px-4 py-2">
                            <form class="user-row flex items-center gap-2"
                                  action="{{ route('admin.users.update', ['user'=>$u->id], false) }}"
                                  method="post">
                                @csrf
                                @method('patch')

                                @php $as = $u->account_status; @endphp
                                <select name="account_status" class="rounded-md border-gray-300">
                                    <option value="pending"   @selected($as==='pending')>pending</option>
                                    <option value="active"    @selected($as==='active')>active</option>
                                    <option value="suspended" @selected($as==='suspended')>suspended</option>
                                </select>

                                @php $ps = $u->presence_status; @endphp
                                <select name="presence_status" class="rounded-md border-gray-300">
                                    <option value="in"  @selected($ps==='in')>in</option>
                                    <option value="out" @selected($ps==='out')>out</option>
                                </select>

                                <button type="submit"
                                        class="save-row inline-flex items-center px-3 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
                                    حفظ
                                </button>
                                <span class="ok-badge ml-2 text-xs text-emerald-700 hidden">تم</span>
                                <span class="err-badge ml-2 text-xs text-red-700 hidden">خطأ</span>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="hidden md:block">{{ $users->links() }}</div>
    </div>

    <script>
    // نفس التعزيز: AJAX + يعمل بدون JS
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form.user-row').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const ok  = form.querySelector('.ok-badge');
                const err = form.querySelector('.err-badge');
                ok?.classList.add('hidden'); err?.classList.add('hidden');

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',                      // نرسل _method=PATCH
                        headers: { 'Accept': 'application/json' },
                        body: new FormData(form),
                        credentials: 'same-origin',
                    });

                    if (res.redirected || [401,403,419].includes(res.status)) {
                        err?.classList.remove('hidden');
                        if (err) err.textContent = 'تأكد من التحقق بالبريد وصلاحية المشرف.';
                        setTimeout(() => err?.classList.add('hidden'), 2200);
                        return;
                    }

                    const data = await res.json().catch(() => ({}));
                    if (data.ok || res.ok) {
                        ok?.classList.remove('hidden');
                        setTimeout(() => ok?.classList.add('hidden'), 1500);
                    } else {
                        err?.classList.remove('hidden');
                        setTimeout(() => err?.classList.add('hidden'), 2200);
                    }
                } catch {
                    err?.classList.remove('hidden');
                    setTimeout(() => err?.classList.add('hidden'), 2200);
                }
            });
        });
    });
    </script>
</x-app-layout>
