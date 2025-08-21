<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            معلومات الحساب
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            حدّث بياناتك الشخصية ومعلومات الاتصال.
        </p>
    </header>

    @php
        $user = auth()->user();
    @endphp

    {{-- شارات الحالة --}}
    <div class="mt-4 flex flex-wrap gap-2">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
            {{ $user->account_status === 'active' ? 'bg-green-100 text-green-700' : ($user->account_status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
            حالة الحساب: {{ $user->account_status }}
        </span>

        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
            {{ $user->presence_status === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
            الحالة الحالية: {{ $user->presence_status }}
        </span>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- الاسم --}}
        <div>
            <x-input-label for="name" value="الاسم" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- البريد --}}
        <div>
            <x-input-label for="email" value="البريد الإلكتروني" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        {{-- صفان --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="nationality" value="الجنسية" />
                <x-text-input id="nationality" name="nationality" type="text" class="mt-1 block w-full"
                    :value="old('nationality', $user->nationality)" />
                <x-input-error class="mt-2" :messages="$errors->get('nationality')" />
            </div>

            <div>
                <x-input-label for="national_id" value="رقم الهوية" />
                <x-text-input id="national_id" name="national_id" type="text" class="mt-1 block w-full"
                    :value="old('national_id', $user->national_id)" />
                <x-input-error class="mt-2" :messages="$errors->get('national_id')" />
            </div>

            <div>
                <x-input-label for="birth_date" value="تاريخ الميلاد" />
                <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full"
                    :value="old('birth_date', optional($user->birth_date)->format('Y-m-d'))" />
                <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
            </div>

            <div>
                <x-input-label for="phone" value="رقم الجوال" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                    :value="old('phone', $user->phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="address" value="العنوان" />
                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                    :value="old('address', $user->address)" />
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>

            <div>
                <x-input-label for="weight" value="الوزن (كجم)" />
                <x-text-input id="weight" name="weight" type="number" step="0.01" class="mt-1 block w-full"
                    :value="old('weight', $user->weight)" />
                <x-input-error class="mt-2" :messages="$errors->get('weight')" />
            </div>

            <div>
                <x-input-label for="height" value="الطول (سم)" />
                <x-text-input id="height" name="height" type="number" step="0.01" class="mt-1 block w-full"
                    :value="old('height', $user->height)" />
                <x-input-error class="mt-2" :messages="$errors->get('height')" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="health_status" value="الحالة الصحية" />
                <x-text-input id="health_status" name="health_status" type="text" class="mt-1 block w-full"
                    :value="old('health_status', $user->health_status)" />
                <x-input-error class="mt-2" :messages="$errors->get('health_status')" />
            </div>

            <div>
                <x-input-label for="swimming_level" value="مستوى السباحة" />
                <select id="swimming_level" name="swimming_level"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @php $lvl = old('swimming_level', $user->swimming_level); @endphp
                    <option value="none"         {{ $lvl==='none' ? 'selected' : '' }}>لا يجيد</option>
                    <option value="basic"        {{ $lvl==='basic' ? 'selected' : '' }}>أساسي</option>
                    <option value="intermediate" {{ $lvl==='intermediate' ? 'selected' : '' }}>متوسط</option>
                    <option value="advanced"     {{ $lvl==='advanced' ? 'selected' : '' }}>متقدم</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('swimming_level')" />
            </div>

            {{-- إن أردت تمكين التعديل للمستخدم: احذف disabled وألغِ التعليق عن القواعد في الـ FormRequest --}}
            <div>
                <x-input-label for="account_status" value="حالة الحساب" />
                <select id="account_status" name="account_status" disabled
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700">
                    @php $as = $user->account_status; @endphp
                    <option value="pending"   {{ $as==='pending' ? 'selected' : '' }}>pending</option>
                    <option value="active"    {{ $as==='active' ? 'selected' : '' }}>active</option>
                    <option value="suspended" {{ $as==='suspended' ? 'selected' : '' }}>suspended</option>
                </select>
            </div>

            <div>
                <x-input-label for="presence_status" value="الحالة الحالية (IN/OUT)" />
                <select id="presence_status" name="presence_status" disabled
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-700">
                    @php $ps = $user->presence_status; @endphp
                    <option value="in"  {{ $ps==='in'  ? 'selected' : '' }}>in</option>
                    <option value="out" {{ $ps==='out' ? 'selected' : '' }}>out</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>حفظ</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600">تم الحفظ.</p>
            @endif
        </div>
    </form>
</section>
