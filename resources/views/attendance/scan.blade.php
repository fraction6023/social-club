<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            مسح QR لتبديل الحالة (IN/OUT)
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-6">
        <div id="reader" class="border rounded p-2"></div>

        <div id="result" class="mt-4 text-green-700 font-semibold"></div>
        <div id="error" class="mt-2 text-red-600"></div>
    </div>

    {{-- مكتبة المسح --}}
    <script src="https://unpkg.com/html5-qrcode" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function boot() {
                if (!window.Html5QrcodeScanner) return setTimeout(boot, 150);

                const scanner = new Html5QrcodeScanner("reader", {
                    fps: 10,
                    qrbox: 250,
                    rememberLastUsedCamera: true
                }, false);

                scanner.render(onSuccess, onError);

                async function onSuccess(decodedText) {
                    await scanner.clear(); // منع التكرار

                    try {
                        const res = await fetch("{{ route('attendance.toggle') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                code: decodedText
                            })
                        });
                        const data = await res.json();
                        if (data.ok) {
                            result.textContent = data.message + " — حالتك الآن: " + data.presence_status.toUpperCase();
                            error.textContent = "";
                        } else {
                            result.textContent = "";
                            error.textContent = data.message || "تعذر إتمام العملية.";
                        }
                    } catch (e) {
                        result.textContent = "";
                        error.textContent = "خطأ في الاتصال بالخادم.";
                    } finally {
                        setTimeout(() => location.reload(), 2000);
                    }
                }

                function onError(_) {
                    /* تجاهل أخطاء القراءة المتكررة */ }
            }
            boot();
        });
    </script>
</x-app-layout>
