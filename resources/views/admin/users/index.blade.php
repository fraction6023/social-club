{{-- resources/views/admin/users/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">إدارة المشتركين</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl p-4 md:p-6 space-y-4">

        {{-- شريط البحث (واضح) --}}
        <form method="get"
              class="sticky top-0 z-10 bg-white/95 rounded-2xl border p-3 flex items-center gap-3 shadow-sm"
              dir="rtl">
            <div class="relative flex-1">
                <input
                    type="text"
                    name="q"
                    value="{{ $q ?? '' }}"
                    class="w-full rounded-xl border border-gray-300 px-12 py-3
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                           placeholder-gray-400"
                    placeholder="اكتب للبحث (اسم / جوال / بريد / هوية)">
                {{-- أيقونة العدسة (يمين لِـ RTL) --}}
                <!-- <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="M21 21l-4.3-4.3"></path>
                    </svg>
                </span> -->
            </div>

            <button type="submit"
                                  class="inline-flex items-center px-4 py-3 rounded-xl border font-medium hover:bg-gray-50 shrink-0">

                بحث
            </button>

            @if(!empty($q))
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-3 rounded-xl border font-medium hover:bg-gray-50 shrink-0">
                    إعادة تعيين
                </a>
            @endif
        </form>

        {{-- شبكة بطاقات: الاسم + رقم المشترك في نفس السطر + اللون حسب presence_status --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" dir="rtl">
            @foreach($users as $u)
                @php
                    $isIn     = ($u->presence_status === 'in');
                    $gradient = $isIn ? 'from-emerald-500 to-emerald-600' : 'from-rose-500 to-rose-600';
                @endphp

                <a href="{{ route('admin.users.edit', ['user' => $u->id]) }}"
                   class="group relative block rounded-2xl p-6 min-h-[96px] text-white shadow-lg ring-1 ring-black/10
                          bg-gradient-to-br {{ $gradient }} hover:scale-[1.01] transition
                          focus:outline-none focus:ring-2 focus:ring-white/60">
                    <div class="absolute -left-10 -top-10 w-36 h-36 rounded-full bg-white/10 blur-2xl"></div>

                    <div class="flex items-center justify-between gap-3">
                        <div class="text-lg font-extrabold truncate">{{ $u->name ?: '—' }}</div>
                        <span class="text-xs font-semibold bg-white/25 rounded-full px-2 py-0.5">#{{ $u->id }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
