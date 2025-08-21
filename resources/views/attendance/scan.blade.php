<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            {{ $action === 'in' ? 'مسح QR لتسجيل الدخول' : 'مسح QR لتسجيل الخروج' }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        {{-- نمرّر رابط الـ POST كـ data attribute --}}
        <div
            id="reader"
            class="border rounded p-2"
            data-post-url="{{ route('attendance.set', ['action' => $action]) }}"
        ></div>

        <div id="result" class="mt-4 text-green-700 font-semibold"></div>
        <div id="error" class="mt-2 text-red-600"></div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const readerEl = document.getElementById('reader');
        const postUrl  = readerEl.dataset.postUrl;
        const resultEl = document.getElementById('result');
        const errorEl  = document.getElementById('error');

        // نجيب التوكن من الميتا الموجودة بالـ layout
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function boot() {
            if (!window.Html5QrcodeScanner) return setTimeout(boot, 150);

            const scanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: 250, rememberLastUsedCamera: true },
                false
            );

            scanner.render(onSuccess, onError);

            async function onSuccess(decodedText) {
                try {
                    await scanner.clear(); // منع تكرار الإرسال

                    const res = await fetch(postUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrf
                        },
                        body: JSON.stringify({ code: decodedText })
                    });

                    const data = await res.json();

                    if (data.ok) {
                        resultEl.textContent = data.message + " — حالتك الآن: " + String(data.presence_status || '').toUpperCase();
                        errorEl.textContent = "";
                    } else {
                        resultEl.textContent = "";
                        errorEl.textContent = data.message || "تعذر إتمام العملية.";
                    }
                } catch (e) {
                    resultEl.textContent = "";
                    errorEl.textContent = "خطأ في الاتصال بالخادم.";
                } finally {
                    setTimeout(() => window.location.href = "{{ route('dashboard') }}", 1800);
                }
            }

            function onError(_) {
                // أخطاء القراءة المتكررة طبيعية
            }
        }

        boot();
    });
    </script>
</x-app-layout>
