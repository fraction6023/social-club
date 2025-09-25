{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">لوحة التحكم</h2>
    </x-slot>

    @php
        $isIn       = auth()->user()?->presence_status === 'in';
        $status     = $isIn ? 'داخل' : 'خارج';
        $statusColor= $isIn ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
    @endphp

    <div class="mx-auto max-w-5xl p-6">
        {{-- شارة الحالة الحالية --}}
        <div class="mb-6">
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $statusColor }}">
                حالتك الآن: {{ $status }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @if ($isIn)
                {{-- بطاقة خروج فقط إذا كان داخل --}}
                <a href="{{ route('attendance.scan', ['action' => 'out']) }}"
                   class="group relative overflow-hidden rounded-2xl p-8 min-h-[160px]
                          bg-gradient-to-br from-rose-500 to-rose-600 text-white
                          shadow-lg ring-1 ring-black/10 transition
                          hover:scale-[1.02] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-rose-300">
                    <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-3xl font-extrabold">خروج</div>
                            <div class="mt-2 text-white/90">لتسجيل الخروج اضغط لفتح الكاميرا ومسح QR</div>
                        </div>
                        <div class="shrink-0 opacity-90 group-hover:-translate-x-1 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7H8m8 10H8M21 12H10m0 0 3-3m-3 3 3 3"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @else
                {{-- بطاقة دخول فقط إذا كان خارج/غير معروف --}}
                <a href="{{ route('attendance.scan', ['action' => 'in']) }}"
                   class="group relative overflow-hidden rounded-2xl p-8 min-h-[160px]
                          bg-gradient-to-br from-emerald-500 to-emerald-600 text-white
                          shadow-lg ring-1 ring-black/10 transition
                          hover:scale-[1.02] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-3xl font-extrabold">دخول</div>
                            <div class="mt-2 text-white/90">لتسجيل الدخول اضغط لفتح الكاميرا ومسح QR</div>
                        </div>
                        <div class="shrink-0 opacity-90 group-hover:translate-x-1 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 10h8M3 12h11m0 0-3-3m3 3-3 3"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
