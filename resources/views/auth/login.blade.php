<x-guest-layout>
    <div style="display:flex; flex-direction:column; gap:18px;">
        <div>
            <p style="margin:0; font-size:12px; letter-spacing:.32em; text-transform:uppercase; color:#be185d;">Login</p>
            <h2 style="margin:10px 0 0; font-size:38px; line-height:1.05; color:#1f2937; font-weight:800;">Masuk ke sistem</h2>
            <p style="margin:12px 0 0; color:#64748b; line-height:1.8;">Masukkan email dan password yang sudah terdaftar.</p>
        </div>

        <x-auth-session-status class="mb-2" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" style="display:flex; flex-direction:column; gap:18px;">
            @csrf

            <div>
                <label for="email" style="display:block;color:#334155;font-weight:700;margin-bottom:8px;">Email</label>
                <input id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    style="display:block;width:100%;border-radius:16px;border:1px solid #f0b6c9;padding:14px 16px;color:#1f2937;background:#fff;outline:none;box-sizing:border-box;">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <label for="password" style="display:block;color:#334155;font-weight:700;margin-bottom:8px;">Password</label>
                <div style="position:relative;">
                    <input id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        style="display:block;width:100%;border-radius:16px;border:1px solid #f0b6c9;padding:14px 76px 14px 16px;color:#1f2937;background:#fff;outline:none;box-sizing:border-box;">
                    <button type="button" id="togglePassword"
                        style="position:absolute; top:50%; right:14px; transform:translateY(-50%); border:0; background:transparent; color:#9f1d4f; font-size:13px; font-weight:700; cursor:pointer;">
                        Lihat
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label for="remember_me" style="display:inline-flex; align-items:center; gap:10px; color:#64748b; font-size:14px;">
                <input id="remember_me" type="checkbox" name="remember" style="width:18px;height:18px;border-radius:6px;border:1px solid #d8b4c7;">
                Ingat saya
            </label>

            <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; padding-top:6px;">
                <a href="{{ route('register') }}" style="font-size:14px; font-weight:600; text-decoration:none;">
                    Buat akun
                </a>

                <button type="submit"
                    style="display:inline-flex;align-items:center;justify-content:center;border-radius:16px;padding:14px 22px;background:linear-gradient(90deg,#c0265f,#ec4899);border:0;box-shadow:0 16px 30px rgba(190,24,93,.22);color:#111827;font-weight:700;cursor:pointer;">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword?.addEventListener('click', function() {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            togglePassword.textContent = isHidden ? 'Sembunyi' : 'Lihat';
        });
    </script>
</x-guest-layout>
