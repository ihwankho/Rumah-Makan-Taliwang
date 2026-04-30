<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center px-6">

  <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border p-8">

    <div class="text-center mb-6">
      <div class="text-2xl font-bold text-gray-800">Login</div>
      <div class="text-sm text-gray-500">Masuk ke sistem rumah makan</div>
    </div>

    @if(session('error'))
      <div class="mb-4 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
        {{ session('error') }}
      </div>
    @endif

    @if(session('success'))
      <div class="mb-4 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-xl">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login.process') }}" class="space-y-4">
      @csrf

      <div>
        <label class="text-sm font-semibold">Username</label>
        <input type="text" name="username"
               class="mt-1 w-full border rounded-xl px-3 py-2"
               placeholder="username"
               required>
      </div>

      <div>
        <label class="text-sm font-semibold">Password</label>
        <input type="password" name="password"
               class="mt-1 w-full border rounded-xl px-3 py-2"
               placeholder="********"
               required>
      </div>

      <button type="submit"
              class="w-full bg-orange-600 text-white font-bold py-3 rounded-xl hover:bg-orange-700">
        Login
      </button>
    </form>

    

  </div>

</body>
</html>
