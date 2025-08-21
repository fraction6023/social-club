<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium">الجنسية</label>
            <input name="nationality" class="border rounded w-full" value="{{ old('nationality') }}">
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium">رقم الهوية</label>
            <input name="national_id" class="border rounded w-full" value="{{ old('national_id') }}">
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium">تاريخ الميلاد</label>
            <input type="date" name="birth_date" class="border rounded w-full" value="{{ old('birth_date') }}">
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium">رقم الجوال</label>
            <input name="phone" class="border rounded w-full" value="{{ old('phone') }}">
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium">العنوان</label>
            <input name="address" class="border rounded w-full" value="{{ old('address') }}">
        </div>
        <div class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">الوزن (كجم)</label>
                <input type="number" step="0.01" name="weight" class="border rounded w-full" value="{{ old('weight') }}">
            </div>
            <div>
                <label class="block text-sm font-medium">الطول (سم)</label>
                <input type="number" step="0.01" name="height" class="border rounded w-full" value="{{ old('height') }}">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium">الحالة الصحية</label>
            <input name="health_status" class="border rounded w-full" value="{{ old('health_status') }}">
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium">مستوى السباحة</label>
            <select name="swimming_level" class="border rounded w-full">
                <option value="none">لا يجيد</option>
                <option value="basic">أساسي</option>
                <option value="intermediate">متوسط</option>
                <option value="advanced">متقدم</option>
            </select>
        </div>


        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
