<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Laravel') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">
  <div class="mx-auto flex min-h-screen w-full max-w-7xl items-center justify-center px-4">
    <div class="w-full max-w-md">
      <div class="rounded-2xl bg-white p-8 shadow-lg ring-1 ring-black/5 dark:bg-gray-800 dark:ring-white/10">
        {{ $slot }}
      </div>
    </div>
  </div>
</body>
</html>
