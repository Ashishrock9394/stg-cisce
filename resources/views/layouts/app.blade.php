<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="twitter:card" value="summary">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <meta name="msapplication-TileImage" content="{{ asset('resources/images/edutok-fav.ico')}}"/>
      <link rel="apple-touch-icon-precomposed" href="{{ asset('resources/images/edutok-fav.ico') }}"/>
      <title>@yield('title')</title>
      <link rel="icon" href="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}" sizes="192x192" />
      <link href="{{ asset('resources/css/style.css') }}" rel="stylesheet" media="all">
      <link href="{{ asset('resources/css/responsive.css') }}" rel="stylesheet" media="screen">
      <link rel="preload" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" as="style" onload="this.rel='stylesheet'">
      <link rel="preconnect" href="https://fonts.gstatic.com">
      <link rel="preload" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" as="style" onload="this.rel='stylesheet'">
      <script src="{{ asset('resources/js/jquery.min.js') }}"></script>      
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

      @stack('icse-style')
   </head>

   <body oncopy="return false" oncut="return false" class="{{ last(request()->segments()) }}">      
      @if(last(request()->segments()) != 'login' && last(request()->segments()) != 'otp-verify') 
      <header>
         @include('layouts.header')
      </header>
      @endif

   @yield('content')

   @if(last(request()->segments()) != 'login' && last(request()->segments()) != 'otp-verify')
   <footer>
         <div class="container">
            <div class="row" style="text-align: center;">
               <div class="col">
                <div class="ftr-row">
                <span>©2025 CISCE, All rights reserved. <a href="{{ route('privacy.policy') }}" style="text-decoration: underline">Privacy Policy</a></span>
                <span>powered by <img src="{{ asset('logo/fitness365-logo-web.jpg') }}" alt="Fitness 365" class="img-fluid" style="max-width: 90px; height: 28px;"></span>
                </div>
            <div>
            </div>
         </div>
      </footer>
   @endif
   @stack('icse-footer-script')
   </body>
</html>