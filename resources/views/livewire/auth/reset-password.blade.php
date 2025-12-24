<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight text-black">
                {{ __('Reset password') }}
            </h1>
            <p class="text-sm text-zinc-500">
                {{ __('Please enter your new password below') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6"
            x-data="{ password: '' }">
            @csrf
            <!-- Token -->
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <!-- Email Address -->
            <flux:input name="email" value="{{ request('email') }}" :label="__('Email')" type="email" required
                autocomplete="email" />

            <!-- Password -->
            <div class="flex flex-col gap-2">
                <flux:input name="password" :label="__('Password')" type="password" required autocomplete="new-password"
                    :placeholder="__('Password')" viewable x-model="password" />
                <!-- Password Strength -->
                <div class="h-1 w-full bg-zinc-100 rounded-full overflow-hidden" x-show="password.length > 0"
                    x-transition>
                    <div class="h-full bg-[var(--color-brand-purple)] transition-all duration-500"
                        :style="'width: ' + Math.min(password.length * 12, 100) + '%'"></div>
                </div>
                <p class="text-xs text-zinc-500" x-show="password.length > 0 && password.length < 8">
                    {{ __('Password should be at least 8 characters') }}
                </p>
            </div>

            <!-- Confirm Password -->
            <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required
                autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary"
                    class="w-full bg-[var(--color-brand-purple)] hover:bg-[var(--color-brand-purple)]/90"
                    data-test="reset-password-button">
                    {{ __('Reset password') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts.auth>