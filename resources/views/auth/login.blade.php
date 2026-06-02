<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>

  @vite('resources/css/app.css')
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center px-6">

  <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border p-8">

    {{-- Header --}}
    <div class="text-center mb-6">
      <div class="text-2xl font-bold text-gray-800">
        Login
      </div>

      <div class="text-sm text-gray-500">
        Masuk ke sistem rumah makan
      </div>
    </div>

    {{-- Alert Error --}}
    @if(session('error'))
      <div class="mb-4 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
        {{ session('error') }}
      </div>
    @endif

    {{-- Alert Success --}}
    @if(session('success'))
      <div class="mb-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
        {{ session('success') }}
      </div>
    @endif

    {{-- Form Login --}}
    <form method="POST"
          action="{{ route('login.process') }}"
          class="space-y-4">

      @csrf

      {{-- Username --}}
      <div>
        <label class="text-sm font-semibold">
          Username
        </label>

        <input
          type="text"
          name="username"
          class="mt-1 w-full border rounded-xl px-3 py-2"
          placeholder="username"
          required
        >
      </div>

      {{-- Password --}}
      <div>
        <label class="text-sm font-semibold">
          Password
        </label>

        <div class="relative mt-1">

          <input
            type="password"
            name="password"
            id="password"
            class="w-full border rounded-xl px-3 py-2 pr-12"
            placeholder="********"
            required
          >

          {{-- Button Mata --}}
          <button
            type="button"
            onclick="togglePassword()"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
          >

            {{-- Icon Mata Buka --}}
            <svg
              id="eye-open"
              xmlns="http://www.w3.org/2000/svg"
              class="w-5 h-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />

              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5
                   c4.478 0 8.268 2.943 9.542 7
                   -1.274 4.057-5.064 7-9.542 7
                   -4.477 0-8.268-2.943-9.542-7z"
              />
            </svg>

            {{-- Icon Mata Tutup --}}
            <svg
              id="eye-close"
              xmlns="http://www.w3.org/2000/svg"
              class="w-5 h-5 hidden"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19
                   c-4.478 0-8.268-2.943-9.542-7
                   a9.956 9.956 0 012.223-3.592M6.228 6.228
                   A9.953 9.953 0 0112 5c4.478 0
                   8.268 2.943 9.542 7a9.97 9.97 0
                   01-4.132 5.411M15 12a3 3 0
                   11-6 0 3 3 0 016 0zm6 6L3 3"
              />
            </svg>

          </button>

        </div>
      </div>

      {{-- Button Login --}}
      <button
        type="submit"
        class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700 transition"
      >
        Login
      </button>

    </form>

  </div>

  {{-- Script Toggle Password --}}
  <script>
    function togglePassword() {

      const password = document.getElementById('password');
      const eyeOpen = document.getElementById('eye-open');
      const eyeClose = document.getElementById('eye-close');

      if (password.type === 'password') {

        password.type = 'text';

        eyeOpen.classList.add('hidden');
        eyeClose.classList.remove('hidden');

      } else {

        password.type = 'password';

        eyeOpen.classList.remove('hidden');
        eyeClose.classList.add('hidden');
      }
    }
  </script>

</body>
</html>