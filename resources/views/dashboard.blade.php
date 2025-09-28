{{-- resources/views/dashboard.blade.php --}}
@php
    use Illuminate\Support\Facades\URL;

    $user        = auth()->user();
    $isIn        = $user?->presence_status === 'in';
    $statusText  = $isIn ? 'داخل' : 'خارج';
    $statusColor = $isIn ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

    // روابط موقعة مؤقتاً (5 دقائق) ليقرأها جهاز آخر ويحدث حالة هذا المستخدم
    $qrInUrl  = URL::temporarySignedRoute('attendance.accept', now()->addMinutes(5), ['uid' => $user->id, 'action' => 'in']);
    $qrOutUrl = URL::temporarySignedRoute('attendance.accept', now()->addMinutes(5), ['uid' => $user->id, 'action' => 'out']);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">لوحة التحكم</h2>
    </x-slot>

    <div class="mx-auto max-w-5xl p-6" dir="rtl">
        {{-- شارة الحالة الحالية --}}
        <div class="mb-6">
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $statusColor }}">
                حالتك الآن: {{ $statusText }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @if ($isIn)
                {{-- بطاقة خروج: تفتح مودال QR للخروج --}}
                <button type="button" data-kind="out"
                        class="group relative overflow-hidden rounded-2xl p-8 min-h-[160px] text-right
                               bg-gradient-to-br from-rose-500 to-rose-600 text-white
                               shadow-lg ring-1 ring-black/10 transition
                               hover:scale-[1.02] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-rose-300">
                    <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-3xl font-extrabold">خروج</div>
                            <div class="mt-2 text-white/90">اضغط لعرض QR ومسحه من جهاز آخر لتسجيل الخروج</div>
                            <div class="mt-1 text-xs text-white/80">صالح لمدة 5 دقائق</div>
                        </div>
                        <div class="shrink-0 opacity-90 group-hover:-translate-x-1 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7H8m8 10H8M21 12H10m0 0 3-3m-3 3 3 3"/>
                            </svg>
                        </div>
                    </div>
                </button>
            @else
                {{-- بطاقة دخول: تفتح مودال QR للدخول --}}
                <button type="button" data-kind="in"
                        class="group relative overflow-hidden rounded-2xl p-8 min-h-[160px] text-right
                               bg-gradient-to-br from-emerald-500 to-emerald-600 text-white
                               shadow-lg ring-1 ring-black/10 transition
                               hover:scale-[1.02] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-3xl font-extrabold">دخول</div>
                            <div class="mt-2 text-white/90">اضغط لعرض QR ومسحه من جهاز آخر لتسجيل الدخول</div>
                            <div class="mt-1 text-xs text-white/80">صالح لمدة 5 دقائق</div>
                        </div>
                        <div class="shrink-0 opacity-90 group-hover:translate-x-1 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 10h8M3 12h11m0 0-3-3m3 3-3 3"/>
                            </svg>
                        </div>
                    </div>
                </button>
            @endif
        </div>
    </div>

    {{-- مودال عرض QR (بدون JS خارجي) --}}
    <div id="qrModal" class="fixed inset-0 z-[9999] hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative mx-auto mt-20 w-[92%] max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 id="qrTitle" class="text-lg font-bold">QR</h3>
                <button type="button" id="qrClose" class="rounded-lg px-3 py-1 bg-gray-100 hover:bg-gray-200">إغلاق</button>
            </div>

            {{-- نولّد الـQR في الخادم (SVG) ونبدّل بينهما --}}
            <div id="qrInSection" class="hidden flex justify-center">
                {!! QrCode::size(260)->margin(1)->generate($qrInUrl) !!}
            </div>
            <div id="qrOutSection" class="hidden flex justify-center">
                {!! QrCode::size(260)->margin(1)->generate($qrOutUrl) !!}
            </div>

            <p class="mt-3 text-xs text-gray-500 text-center">امسح الكود من جهاز آخر لتعديل الحالة. صالح 5 دقائق.</p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal   = document.getElementById('qrModal');
        const titleEl = document.getElementById('qrTitle');
        const close   = document.getElementById('qrClose');
        const inSec   = document.getElementById('qrInSection');
        const outSec  = document.getElementById('qrOutSection');

        const show = el => el.classList.remove('hidden');
        const hide = el => el.classList.add('hidden');

        function openQR(kind) {
            // أخفِ الاثنين ثم أظهر المطلوب
            hide(inSec); hide(outSec);
            if (kind === 'in') {
                titleEl.textContent = 'رمز الدخول';
                show(inSec);
            } else {
                titleEl.textContent = 'رمز الخروج';
                show(outSec);
            }
            show(modal);
        }

        document.querySelectorAll('button[data-kind]').forEach(btn => {
            btn.addEventListener('click', () => openQR(btn.getAttribute('data-kind')));
        });

        close.addEventListener('click', () => hide(modal));
        modal.addEventListener('click', (e) => { if (e.target === modal) hide(modal); });
    });
    </script>
</x-app-layout>
