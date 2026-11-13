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
      <link href="{{ asset('resources/css/style.css') }}?v={{ filemtime(public_path('resources/css/style.css')) }}" rel="stylesheet" media="all">
      <link href="{{ asset('resources/css/responsive.css') }}?v={{ filemtime(public_path('resources/css/responsive.css')) }}" rel="stylesheet" media="screen">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="{{ asset('resources/css/bootstrap4-compat.css') }}" rel="stylesheet">
      <link href="https://cdn.datatables.net/2.1.2/css/dataTables.bootstrap5.min.css" rel="stylesheet">
      <link href="https://cdn.datatables.net/buttons/3.1.0/css/buttons.bootstrap5.min.css" rel="stylesheet">
      <link href="https://cdn.datatables.net/select/2.0.3/css/select.bootstrap5.min.css" rel="stylesheet">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet">
      <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
      <link rel="preconnect" href="https://fonts.gstatic.com">
      <link rel="preload" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" as="style" onload="this.rel='stylesheet'">
      <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
      <script src="{{ asset('resources/js/bootstrap5-jquery-compat.js') }}"></script>
      <script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
      <script src="https://cdn.datatables.net/2.1.2/js/dataTables.bootstrap5.min.js"></script>
      <script src="https://cdn.datatables.net/select/2.0.3/js/dataTables.select.js"></script>
      <script src="https://cdn.datatables.net/select/2.0.3/js/select.bootstrap5.js"></script>
      <script src="https://cdn.datatables.net/datetime/1.5.3/js/dataTables.dateTime.min.js"></script>
      <script src="https://cdn.datatables.net/colreorder/2.0.3/js/dataTables.colReorder.js"></script>
      <script src="https://cdn.datatables.net/colreorder/2.0.3/js/colReorder.bootstrap5.js"></script>
      <script src="https://cdn.datatables.net/buttons/3.1.0/js/dataTables.buttons.js"></script>
      <script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.bootstrap5.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
      <script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.html5.min.js"></script>
      <script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.print.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>

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