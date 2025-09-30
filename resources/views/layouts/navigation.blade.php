@php use Illuminate\Support\Facades\Auth; @endphp

<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-6">
                {{-- حذف الشعار نهائيًا --}}
                {{-- <x-application-logo .../> --}}

                {{-- روابط علوية --}}
                <div class="hidden sm:flex items-center gap-6">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        الرئيسية
                    </x-nav-link>
                </div>
            </div>

            {{-- قائمة المستخدم (دروب داون) --}}
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 text-sm rounded-md text-gray-600 hover:text-gray-800 transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill="currentColor" fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @can('manage-users')
                            <div class="border-t my-1"></div>

                            {{-- المشتركين داخل القائمة --}}
                            <x-dropdown-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                المشتركين
                            </x-dropdown-link>

                            {{-- ماسح QR يفتح المودال في الداشبورد --}}
                            <x-dropdown-link as="button" id="menu-open-qr">
                                ماسح QR
                            </x-dropdown-link>
                        @endcan

                        <div class="border-t my-1"></div>

                        {{-- تسجيل الخروج --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- زر الموبايل --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- قائمة الموبايل --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                الرئيسية
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @can('manage-users')
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        المشتركين
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#" id="menu-open-qr-mobile">
                        ماسح QR
                    </x-responsive-nav-link>
                @endcan

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- يفتح مودال الماسح في الداشبورد --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const openDesktop = document.getElementById('menu-open-qr');
  const openMobile  = document.getElementById('menu-open-qr-mobile');
  const fire = (e) => { e?.preventDefault?.(); window.dispatchEvent(new CustomEvent('open-qr-scanner')); };
  if (openDesktop) openDesktop.addEventListener('click', fire);
  if (openMobile)  openMobile.addEventListener('click', fire);
});
</script>
