{{-- resources/views/admin/users/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">إدارة المشتركين</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4 md:p-6 space-y-4">

        {{-- شريط البحث --}}
        <form method="get"
              class="sticky top-0 z-10 bg-white/90 backdrop-blur rounded-xl border p-3 flex items-center gap-2">
            <input type="text" name="q" value="{{ $q ?? '' }}"
                   class="flex-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                   placeholder="بحث بالاسم/الإيميل/الجوال/الهوية">
            <button class="inline-flex items-center px-4 py-2 rounded-md bg-slate-900 text-white">بحث</button>
            @if(!empty($q))
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-3 py-2 rounded-md border">إعادة تعيين</a>
            @endif
        </form>

        {{-- شبكة بطاقات (للجوال والتابلت) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:hidden">
            @foreach($users as $u)
                @php
                    $isActive = ($u->account_status === 'active'); // أو presence: ($u->presence_status === 'in')
                    $gradient = $isActive ? 'from-emerald-500 to-emerald-600' : 'from-rose-500 to-rose-600';
                @endphp

                <a href="{{ route('admin.users.edit', ['user' => $u->id]) }}"
                   class="group relative block rounded-2xl p-5 text-white shadow-lg ring-1 ring-black/10
                          bg-gradient-to-br {{ $gradient }} hover:scale-[1.01] transition
                          focus:outline-none focus:ring-2 focus:ring-white/60"
                   aria-label="فتح {{ $u->name }} للتعديل">
                    <div class="absolute -right-10 -top-10 w-36 h-36 rounded-full bg-white/10 blur-2xl"></div>

                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-lg font-extrabold">{{ $u->name ?: '—' }}</div>
                            <div class="text-xs text-white/90">{{ $u->email ?: '—' }}</div>
                            <div class="mt-1 text-xs text-white/80">الجوال: {{ $u->phone ?: '—' }}</div>
                        </div>
                        <span class="text-xs bg-white/20 rounded-full px-2 py-0.5">#{{ $u->id }}</span>
                    </div>

                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="px-2 py-1 rounded-full bg-white/20">الحالة: {{ $u->account_status ?? '—' }}</span>
                        <span class="px-2 py-1 rounded-full bg-white/20">التواجد: {{ $u->presence_status ?? '—' }}</span>
                    </div>

                    <div class="mt-4 inline-flex items-center gap-1 text-xs font-semibold underline decoration-white/40">
                        فتح للتعديل
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:translate-x-0.5 transition" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- جدول للديسكتوب مع حفظ سريع --}}
        <div class="hidden md:block overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-right">#</th>
                        <th class="px-4 py-3 text-right">الاسم</th>
                        <th class="px-4 py-3 text-right">البريد</th>
                        <th class="px-4 py-3 text-right">الجوال</th>
                        <th class="px-4 py-3 text-right">رقم الهوية</th>
                        <th class="px-4 py-3 text-right">حالة الحساب + التواجد + حفظ</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                @foreach($users as $u)
                    <tr>
                        <td class="px-4 py-2">{{ $u->id }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.users.edit', ['user' => $u->id]) }}" class="font-semibold text-indigo-700 hover:underline">
                                {{ $u->name }}
                            </a>
                            <div class="text-xs text-gray-500">الجنسية: {{ $u->nationality ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $u->email }}</td>
                        <td class="px-4 py-2">{{ $u->phone ?? '—' }}</td>
                        <td class="px-4 py-2">{{ $u->national_id ?? '—' }}</td>

                        <td class="px-4 py-2">
                            @php
                                $as = $u->account_status;
                                $ps = $u->presence_status;
                            @endphp

                            <form class="user-row inline-flex flex-wrap items-center gap-2"
                                  action="{{ route('admin.users.update', ['user' => $u->id]) }}"
                                  method="post">
                                @csrf
                                @method('patch')

                                <select name="account_status" class="rounded-md border-gray-300">
                                    <option value="pending"   @selected($as==='pending')>pending</option>
                                    <option value="active"    @selected($as==='active')>active</option>
                                    <option value="suspended" @selected($as==='suspended')>suspended</option>
                                </select>

                                <select name="presence_status" class="rounded-md border-gray-300">
                                    <option value="in"  @selected($ps==='in')>in</option>
                                    <option value="out" @selected($ps==='out')>out</option>
                                </select>

                                <button type="submit"
                                        class="save-row inline-flex items-center px-3 py-2 rounded-md
                                               bg-emerald-600 text-white hover:bg-emerald-700 ring-2 ring-emerald-300 font-semibold">
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

        <div class="mt-4">{{ $users->links() }}</div>
    </div>

    <script>
    // حفظ سريع AJAX (ويعمل أيضاً بدون JS)
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form.user-row').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const ok  = form.querySelector('.ok-badge');
                const err = form.querySelector('.err-badge');
                ok?.classList.add('hidden'); err?.classList.add('hidden');

                try {
                    const fd = new FormData(form);
                    fd.set('_method','PATCH');
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: fd,
                        credentials: 'same-origin',
                    });

                    if (!res.ok) throw new Error('bad');
                    const data = await res.json().catch(() => ({}));
                    if (data.ok || res.ok) {
                        ok?.classList.remove('hidden');
                        setTimeout(() => ok?.classList.add('hidden'), 1500);
                    } else {
                        throw new Error('no ok');
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
