{{-- resources/views/dashboard.blade.php --}}
@php
    use Illuminate\Support\Facades\URL;

    $user        = auth()->user();
    $isIn        = $user?->presence_status === 'in';
    $statusText  = $isIn ? 'داخل' : 'خارج';
    $statusColor = $isIn ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
    $isAdmin     = (bool) ($user?->is_admin ?? false);

    // روابط موقّعة (5 دقائق) لمسحها من جهاز آخر
    $qrInUrl  = URL::temporarySignedRoute('attendance.accept', now()->addMinutes(5), ['uid' => $user->id, 'action' => 'in']);
    $qrOutUrl = URL::temporarySignedRoute('attendance.accept', now()->addMinutes(5), ['uid' => $user->id, 'action' => 'out']);

    // simple-qrcode (إن كانت مثبّتة)
    $qrAvailable = class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class);
    $qrInSvg     = $qrAvailable ? \SimpleSoftwareIO\QrCode\Facades\QrCode::size(260)->margin(1)->generate($qrInUrl)  : null;
    $qrOutSvg    = $qrAvailable ? \SimpleSoftwareIO\QrCode\Facades\QrCode::size(260)->margin(1)->generate($qrOutUrl) : null;

    // شارة حالة الحساب
    $accMap = [
        'active'    => ['نشط',    'bg-blue-100 text-blue-700'],
        'pending'   => ['معلّق',  'bg-amber-100 text-amber-800'],
        'suspended' => ['موقّف',  'bg-gray-200 text-gray-700'],
    ];
    [$accText, $accChip] = $accMap[$user->account_status ?? ''] ?? ['غير معروف','bg-gray-100 text-gray-600'];

    // QR صغير لرقم المشترك
    $idQrSvg = $qrAvailable ? \SimpleSoftwareIO\QrCode\Facades\QrCode::size(56)->margin(0)->generate((string)$user->id) : null;

    // صورة افتراضية (يمكن تبديلها لاحقًا بحقل من قاعدة البيانات)
    $avatarUrl = 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Male_Avatar.jpg/960px-Male_Avatar.jpg?20201202061211';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">لوحة التحكم</h2>
    </x-slot>

    <div class="mx-auto max-w-5xl p-6 space-y-8" dir="rtl">
        {{-- شارة الحالة --}}
        <div>
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $statusColor }}">
                حالتك الآن: {{ $statusText }}
            </span>
        </div>

        {{-- ماسح QR (للمشرف فقط) --}}
        @if($isAdmin)
            <div class="rounded-2xl border bg-white p-4 shadow">
                <div class="font-bold text-lg mb-3">ماسح QR لتعديل الحضور</div>

                <div class="grid md:grid-cols-[380px_1fr] gap-6">
                    <div>
                        <div id="qr-reader"
                             class="hidden rounded-xl overflow-hidden border aspect-square max-w-[380px] bg-black/5"></div>
                    </div>

                    <div class="text-sm leading-6">
                        <div id="qrStatus" class="rounded-xl bg-gray-50 border p-3 text-gray-700">
                            اضغط
                            <button id="qrStartInline" type="button" class="font-semibold underline text-indigo-700">
                                بدء المسح
                            </button>
                            ثم وجّه الكاميرا نحو كود QR. إذا كان الرابط صالحًا سيتم تعديل حالة المشترك فورًا.
                            <div class="mt-2 text-xs text-gray-500">
                                ملاحظة: بعض المتصفحات تشترط HTTPS أو <span class="font-mono">localhost</span> للسماح بالكاميرا.
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <select id="qrCamSelect"
                                    class="hidden rounded-lg border px-2 py-2 text-sm"></select>

                            <button id="qrToggle"
                                    class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-semibold shadow hover:bg-slate-800">
                                بدء المسح
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- بطاقة الإجراء (دخول/خروج) --}}
        <div class="grid grid-cols-1 gap-8">
            @if ($isIn)
                <button type="button" data-kind="out"
                        class="group relative overflow-hidden rounded-2xl p-10 min-h-[170px] text-right
                               bg-gradient-to-br from-rose-500 to-rose-600 text-white
                               shadow-lg ring-1 ring-black/10 transition
                               hover:scale-[1.02] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-rose-300">
                    <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="flex items-center justify-between gap-6">
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
                <button type="button" data-kind="in"
                        class="group relative overflow-hidden rounded-2xl p-10 min-h-[170px] text-right
                               bg-gradient-to-br from-emerald-500 to-emerald-600 text-white
                               shadow-lg ring-1 ring-black/10 transition
                               hover:scale-[1.02] hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="flex items-center justify-between gap-6">
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

{{-- بطاقة هوية احترافية (عمودية) --}}
@php
    // تأكيد القيم محليًا لو ما كانت معرفة
    $avatarUrl = $avatarUrl ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Male_Avatar.jpg/960px-Male_Avatar.jpg?20201202061211';

    if (!isset($accText) || !isset($accChip)) {
        $accMap = [
            'active'    => ['نشط',    'bg-blue-100 text-blue-700'],
            'pending'   => ['معلّق',  'bg-amber-100 text-amber-800'],
            'suspended' => ['موقّف',  'bg-gray-200 text-gray-700'],
        ];
        [$accText, $accChip] = $accMap[$user->account_status ?? ''] ?? ['غير معروف','bg-gray-100 text-gray-600'];
    }
@endphp

<div class="mx-auto mt-6">
  <div
    class="relative rounded-2xl overflow-hidden shadow-xl ring-1 ring-black/10 bg-gradient-to-b from-slate-50 to-white
           w-[214px] h-[340px] print:w-[54mm] print:h-[85.6mm]"
    style="width:214px;height:340px">

    {{-- طبقات ديكورية خفيفة --}}
    <div class="pointer-events-none absolute -top-10 -left-12 w-48 h-48 bg-indigo-200/40 blur-2xl rounded-full"></div>
    <div class="pointer-events-none absolute -bottom-10 -right-12 w-48 h-48 bg-sky-200/40 blur-2xl rounded-full"></div>

    {{-- محتوى البطاقة --}}
    <div class="relative h-full w-full flex flex-col items-center justify-start px-4 pt-5 text-center">

      {{-- صورة: بالمنتصف فوق (ربع البطاقة تقريباً) --}}
      <div class="rounded-full overflow-hidden ring-4 ring-white shadow-md bg-gray-100"
           style="width:120px;height:120px">
        <img src="{{ $avatarUrl }}" alt="صورة المشترك" class="w-full h-full object-cover">
      </div>

      {{-- الاسم --}}
      <div class="mt-3 text-[17px] font-extrabold tracking-wide text-slate-900 leading-tight">
        {{ $user->name ?? '—' }}
      </div>

      {{-- حالة الحساب (شارة) --}}
      <span class="mt-1 inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] rounded-full {{ $accChip }} ring-1 ring-black/5">
        {{-- أيقونة حالة بسيطة --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-[10px] h-[10px]" viewBox="0 0 24 24" fill="currentColor">
          <circle cx="12" cy="12" r="10" class="opacity-20"></circle><circle cx="12" cy="12" r="3"></circle>
        </svg>
        {{ $accText }}
      </span>

      {{-- رقم المشترك (بدون كلمة) --}}
      <div class="mt-1 font-semibold text-[12px] text-slate-800 tracking-wider">
        #{{ $user->id }}
      </div>

      {{-- فاصل رفيع احترافي --}}
      <div class="mt-2 h-px w-24 bg-gradient-to-r from-transparent via-slate-300 to-transparent"></div>

      {{-- QR صغير لرقم المشترك داخل إطار أنيق --}}
      <div class="mt-2">
        @if(isset($idQrSvg) && $idQrSvg)
          <div class="rounded-lg bg-white/80 ring-1 ring-slate-200 p-2 shadow-sm">
            <div class="w-[56px] h-[56px] mx-auto flex items-center justify-center">
              {!! $idQrSvg !!}
            </div>
          </div>
        @else
          <div class="w-[56px] h-[56px] grid place-items-center rounded-lg bg-white/80 ring-1 ring-slate-200 text-[10px] text-slate-600 shadow-sm">
            QR
          </div>
        @endif
      </div>

      {{-- هامش سفلي للتنفس البصري --}}
      <div class="mt-auto mb-3"></div>
    </div>

    {{-- إطار خارجي رفيع لإحساس الطباعة الاحترافية --}}
    <div class="pointer-events-none absolute inset-0 rounded-2xl ring-1 ring-slate-200/80"></div>
  </div>
</div>

        </div>
    </div>

    {{-- مودال عرض الـQR (روابط الحضور) --}}
    <div id="qrModal" class="fixed inset-0 z-[9999] hidden">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative mx-auto mt-20 w-[92%] max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 id="qrTitle" class="text-lg font-bold">QR</h3>
                <button type="button" id="qrClose" class="rounded-lg px-3 py-1 bg-gray-100 hover:bg-gray-200">إغلاق</button>
            </div>

            @if($qrAvailable)
                <div id="qrInSection"  class="hidden flex justify-center">{!! $qrInSvg  !!}</div>
                <div id="qrOutSection" class="hidden flex justify-center">{!! $qrOutSvg !!}</div>
                <p class="mt-3 text-xs text-gray-500 text-center">امسح الكود من جهاز آخر لتعديل الحالة. صالح 5 دقائق.</p>
            @else
                <div class="rounded-xl border bg-amber-50 text-amber-900 p-4 text-sm">
                    لتفعيل الـQR في المودال: <code>composer require simplesoftwareio/simple-qrcode</code>
                </div>
            @endif
        </div>
    </div>

    {{-- مكتبة ماسح الكاميرا للأدمن --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.9/html5-qrcode.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
      /* مودال QR */
      const modal   = document.getElementById('qrModal');
      const titleEl = document.getElementById('qrTitle');
      const close   = document.getElementById('qrClose');
      const inSec   = document.getElementById('qrInSection');
      const outSec  = document.getElementById('qrOutSection');
      const show = el => el && el.classList.remove('hidden');
      const hide = el => el && el.classList.add('hidden');

      function openQR(kind) {
        hide(inSec); hide(outSec);
        if (kind === 'in') { titleEl.textContent = 'رمز الدخول'; show(inSec); }
        else               { titleEl.textContent = 'رمز الخروج'; show(outSec); }
        show(modal);
      }
      document.querySelectorAll('button[data-kind]').forEach(b => b.addEventListener('click', () => openQR(b.dataset.kind)));
      close.addEventListener('click', () => hide(modal));
      modal.addEventListener('click', e => { if (e.target === modal) hide(modal); });

      /* ماسح المشرف */
      const toggleBtn   = document.getElementById('qrToggle');
      const startInline = document.getElementById('qrStartInline');
      if (!toggleBtn) return;

      const camSelect = document.getElementById('qrCamSelect');
      const readerEl  = document.getElementById('qr-reader');
      const statusEl  = document.getElementById('qrStatus');

      let scanner = null, running = false;

      const setStatus = (msg, state = 'info') => {
        const map = { info: 'bg-gray-50 border text-gray-700',
                      ok:   'bg-emerald-50 border border-emerald-200 text-emerald-800',
                      err:  'bg-rose-50 border border-rose-200 text-rose-800' };
        statusEl.className = 'rounded-xl p-3 ' + (map[state] || map.info);
        statusEl.textContent = msg;
      };

      function syncBtn() {
        if (running) {
          toggleBtn.textContent = 'إيقاف';
          toggleBtn.className = 'px-5 py-2.5 rounded-xl bg-rose-600 text-white font-semibold shadow hover:bg-rose-700';
        } else {
          toggleBtn.textContent = 'بدء المسح';
          toggleBtn.className = 'px-5 py-2.5 rounded-xl bg-slate-900 text-white font-semibold shadow hover:bg-slate-800';
        }
      }

      async function loadCameras() {
        try {
          const devices = await Html5Qrcode.getCameras();
          if (devices.length > 0) {
            camSelect.innerHTML = devices.map(d => `<option value="${d.id}">${d.label || ('كاميرا ' + d.id)}</option>`).join('');
            if (devices.length > 1) camSelect.classList.remove('hidden');
          }
        } catch (_) {}
      }

      async function startScan() {
        if (running) return;
        try {
          if (!scanner) scanner = new Html5Qrcode('qr-reader');
          document.getElementById('qr-reader').classList.remove('hidden');
          setStatus('جاري فتح الكاميرا...');

          const cfg = { fps: 10, qrbox: { width: 300, height: 300 } };
          const camId = camSelect.value || null;

          if (camId) {
            await scanner.start({ deviceId: { exact: camId } }, cfg, onScanSuccess, onScanError);
          } else {
            try {
              await scanner.start({ facingMode: { exact: 'environment' } }, cfg, onScanSuccess, onScanError);
            } catch {
              await scanner.start({ facingMode: 'environment' }, cfg, onScanSuccess, onScanError);
            }
          }

          running = true; syncBtn();
          setStatus('وجّه الكاميرا نحو كود QR...');
        } catch (e) {
          document.getElementById('qr-reader').classList.add('hidden');
          const isSecure = location.protocol === 'https:' || location.hostname === 'localhost';
          const hint = isSecure ? '' : ' (الصفحة ليست HTTPS/localhost)';
          setStatus('تعذّر فتح الكاميرا' + hint + '.', 'err');
        }
      }

      async function stopScan() {
        if (!scanner || !running) return;
        await scanner.stop();
        await scanner.clear();
        running = false; syncBtn();
        document.getElementById('qr-reader').classList.add('hidden');
        setStatus('تم إيقاف المسح.');
      }

      async function onScanSuccess(text) {
        await stopScan();
        let url;
        try { url = new URL(text); }
        catch { try { url = new URL(text, location.origin); } catch { setStatus('الكود غير معروف.', 'err'); return; } }

        if (!/\/attendance\/accept$/.test(url.pathname)) { setStatus('هذا ليس رابط حضور صالح.', 'err'); return; }

        const action = url.searchParams.get('action') || '';
        setStatus('جاري تحديث الحالة...');
        try {
          const res = await fetch(url.toString(), { credentials: 'same-origin' });
          if (!res.ok) throw new Error();
          setStatus('تم التحديث: ' + (action === 'in' ? 'داخل' : 'خارج'), 'ok');
          if (navigator.vibrate) navigator.vibrate(120);
        } catch { setStatus('تعذّر التحديث (قد تكون صلاحية الرابط انتهت).', 'err'); }
      }
      function onScanError(_) {}

      toggleBtn.addEventListener('click', () => running ? stopScan() : startScan());
      startInline.addEventListener('click', () => running ? stopScan() : startScan());
      camSelect.addEventListener('change', () => { if (running) { stopScan().then(startScan); } });

      syncBtn(); loadCameras();
    });
    </script>
</x-app-layout>
