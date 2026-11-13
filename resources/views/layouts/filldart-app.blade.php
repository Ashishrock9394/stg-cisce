<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="twitter:card" value="summary">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="msapplication-TileImage" content="{{ asset('resources/images/edutok-fav.ico')}}" />
        <title>@yield('title')</title>
        <link rel="icon" href="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}" sizes="192x192" />
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{ asset('resources/css/bootstrap4-compat.css') }}">
        <link rel="stylesheet" href="https://cdn.datatables.net/2.1.2/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.0/css/buttons.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.3/css/select.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

        <link rel="stylesheet" href="{{ asset('resources/css/dashboard.min.css') }}" media="screen">
        <link href="{{ asset('resources/css/style.css') }}?v={{ filemtime(public_path('resources/css/style.css')) }}" rel="stylesheet" media="all">
        <link rel="stylesheet" href="{{ asset('resources/css/custom-style.css') }}?v={{ time() }}">

        <link href="{{ asset('resources/css/print.css') }}" rel="stylesheet" media="print">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('resources/js/bootstrap5-jquery-compat.js') }}"></script>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
        
        <!-- sweet alert  -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
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
        
        @stack('custom-style')

        <style>
            #top-admin-nav {
                padding: 0px 10px;
            }

            #top-admin-nav .nav-link,
            #top-admin-nav .navbar-nav .nav-link:hover {
                text-transform: uppercase;
                font-weight: 200;
                font-size: 10px;
            }
            .avtar>a {
                display: flex;
                justify-content: center;
                align-items: center;
                color: #333 !important;
                gap: 10px;
            }

            a.dropdown-item.elements {
                padding: 0px 10px 0px 1px !important;
            }

            .dropdown-item.elements.font-bold {
                background-color: #ffffff;
                color: #000;
            }


        </style>
        <link rel="stylesheet" href="{{ asset('assets/css/take-test-root.css') }}?v={{ time() }}">
        <link rel="stylesheet" href="{{ asset('assets/css/take-test-style.css') }}?v={{ time() }}">
        <link href="{{ asset('resources/css/responsive.css') }}?v={{ filemtime(public_path('resources/css/responsive.css')) }}" rel="stylesheet" media="screen">
            @stack('sports-style')
    </head>
    @php
        $today = now()->format('m-d');

        $showIndependencePopup = in_array($today, [
            '08-14',
            '08-15',
            '08-16',
            '08-17'
        ]);
    @endphp

    @if($showIndependencePopup && !session('independence_popup_shown'))

        @include('components.celebration')

        @php
            session(['independence_popup_shown' => true]);
        @endphp

    @endif

    <body oncopy="return false" oncut="return false" class="{{ last(request()->segments()) }}  common-inner-cls" >


        <nav class="navbar navbar-light top_nav_bar px-0">
            <div class="container d-flex justify-content-between w-100">
                <!-- Left Logo -->

                <div class="ml-5 ml-md-0">
                    @php
                       $GetSchoolLogo = \App\Helpers\Helper::GetSchoolLogo();
                       $user = Auth::user();         
                    @endphp


                    @if(auth()->guard('web')->check())
                        @if($user && ($user->role_id == '9' || $user->role_id == '2'))
                            <p>{{ $GetSchoolLogo->school_name ?? '' }} </p>
                        @elseif(($user && ($user->role_id == '4' || Auth::user()->role_id == '8')) && !empty($GetSchoolLogo->logo))
                            <img src="{{ asset('assets/uploads/logos/' . $GetSchoolLogo->logo) }}" style="height:58px; width:auto; padding:0;">                                               
                        @elseif(($user && $user->role_id == '3') && !empty($GetSchoolLogo->logo))
                            @if(!Session::get('SelectSchoolId'))
                                <p>School Logo</p>
                            @else
                                <img src="{{ asset('assets/uploads/logos/' . $GetSchoolLogo->logo) }}" style="height:58px; width:auto; padding:0;">
                            @endif
                        @else
                            <p>School : {{ $GetSchoolLogo->school_name ?? '' }}  | {{ $GetSchoolLogo->school_code ?? '' }} </p>
                        @endif  
                    @elseif(auth()->guard('sstudent')->check() && !empty($GetSchoolLogo->logo))
                        <img src="{{ asset('assets/uploads/logos/' . $GetSchoolLogo->logo) }}" style="height:58px; width:auto; padding:0;">
                    @else
                        <a class="navbar-brand logo d-lg-block d-none py-0" href="{{ route('filldart.dashboard') }}">
                            <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}"  class="d-inline-block align-top" alt="">
                        </a>
                    @endif
                </div>


                <!-- Mid-Logo -->
                @if(auth()->guard('web')->check())
                
                    @if(Auth::user()->role_id == '3')
                        <a class="navbar-brand logo d-lg-block d-none py-0" href="{{ route('filldart.dashboard') }}">
                            <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}"  class="d-inline-block align-top" alt="">
                        </a>
                    @endif

                    @if(Auth::user()->role_id == '4' || Auth::user()->role_id == '8')
                        <a class="navbar-brand logo d-lg-block d-none py-0" href="{{ route('filldart.dashboard') }}">
                            <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}"  class="d-inline-block align-top" alt="">
                        </a>
                    @endif

                    @if(Auth::user()->role_id == '9' || Auth::user()->role_id == '2')
                        <a class="navbar-brand logo d-lg-block d-none py-0" href="{{ route('cicse.dashboard') }}">
                            <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}"  class="d-inline-block align-top" alt="">
                        </a>
                    @endif

                @elseif(auth()->guard('sstudent')->check())
                    <a class="navbar-brand logo d-lg-block d-none py-0" href="{{ route('student.dashboard') }}">
                        <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}"  class="d-inline-block align-top" alt="">
                    </a>
                @endif
            

                <!-- Right Side DropDown -->
                <div class="btn-group">
                    <div class="user-cred order-md-12">
                        <ul class="navbar-nav ml-auto flex-row align-items-center">

                            <!-- Notifications Dropdown Menu -->
                            @php
                                $user = auth()->user() ?? auth()->guard('sstudent')->user();
                                $unreadCount = $user?->unreadNotifications?->count() ?? 0;
                                $notifications = $user?->notifications()->latest()->take(12)->get() ?? collect();
                            @endphp

                            <li class="nav-item dropdown">
                                <a class="navbar-brand nav-link d-lg-block d-none" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <i class="far fa-bell" style="font-size:20px; color: grey;"></i>
                                    @if($unreadCount > 0)
                                        <sup><span class="badge badge-warning navbar-badge">{{ $unreadCount }}</span></sup>
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left notification" style="right: inherit; left: 0px;">
                                    <span class="dropdown-item dropdown-header">
                                        {{ $unreadCount }} Unread Notification{{ $unreadCount !== 1 ? 's' : '' }}
                                    </span>

                                   <!--  <div class="dropdown-divider"></div> -->
                                    
                                    <div style="max-height: 250px; overflow-y: auto;">
                                    @forelse($notifications as $notification)

                                        <div class="dropdown-item elements {{ $notification->read_at ? '' : 'font-bold' }}">
                                            <div class="d-flex justify-content-between align-items-center ">
                                                
                                                @if(!empty($notification->data['file_path']))
                                                <div class="d-flex" >
                                                    <span class="text-wrap " style="font-size: 13px !important;">{{ $notification->data['message'] ?? 'No message' }}
                                                    </span> 

                                                    <a href="{{ $notification->data['file_path'] }}" class="btn-link" onclick="markAsRead('{{ $notification->id }}')">Download
                                                    </a>
                                                </div>
                                                @endif

                                            </div>
                                            <small class="text-muted d-block mt-1"> {{ $notification->created_at->diffForHumans() }} </small>
                                        </div>  
                                    @empty
                                       
                                    @endforelse

                                    </div>

                               
                                    {{--
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item dropdown-footer text-center">
                                         See All Notifications
                                    </a>
                                    --}}
                                </div>

                            </li>



                            @if(auth()->guard('web')->guest() && auth()->guard('sstudent')->guest())
                                <li class="nav-item l_area mr-2">
                                    <a class="nav-link user-login" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @else
                                <li class="nav-item dropdown avtar">
                                    
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="{{-- route('filldart.dashboard') --}}" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        
                                        @if(auth()->guard('web')->check() && Auth::user()->profile_picture)
                                            <img src="{{ asset('assets/uploads/profilePictures/users/' .Auth::user()->profile_picture) }}" class="d-inline-block align-top" height="32" width="32" alt="avtar" style="border-radius: 50%;">
                                        @elseif(auth()->guard('sstudent')->check() && Auth::guard('sstudent')->user()->profile_picture)
                                            <img src="{{ asset('assets/uploads/profilePictures/student/' .Auth::guard('sstudent')->user()->profile_picture) }}" class="d-inline-block align-top" height="32" width="32" alt="avtar" style="border-radius: 50%;">
                                        @else
                                            <img src="{{ asset('resources/images/avtar.png') }}" class="d-inline-block align-top" height="32" alt="avtar">
                                        @endif  
                                        <span class="d-md-block d-none">
                                            @if(auth()->guard('web')->check())
                                                {{ Auth::user()->name }} 
                                            @elseif(auth()->guard('sstudent')->check())
                                                {{ Auth::guard('sstudent')->user()->student_name }}
                                            @endif
                                        </span>
                                    </a>
                                    
                                    <div class="dropdown-menu user-menu" aria-labelledby="navbarDropdown">                                        
                                        @if(auth()->guard('sstudent')->check()) 
                                            <a class="dropdown-item" href="{{route ('student.profile')}}"> {{ __('Edit Profile') }} </a>
                                        @elseif(auth()->guard('web')->check() && Auth::user()->role_id == '4')
                                            <a class="dropdown-item" href="{{ route('school.profile') }}"> {{ __('Edit Profile') }}</a>

                                            <a class="dropdown-item" href="{{ route('password.change') }}"> {{ __('Change Password') }}</a>

                                        @elseif(auth()->guard('web')->check() && Auth::user()->role_id == '3')
                                            <a class="dropdown-item" href="{{ route('trainer.profile') }}"> {{ __('Edit Profile') }}</a>
                                        @endif
                                                                   

                                        <a class="dropdown-item last_child" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endif


                            </ul>
                        </div>
                    </div>


            </div>
        </nav>

        @if (session('is_impersonating'))
            <style>
                .btn-outline-custom {
                    color: #ff8000 !important;
                    /* border-color: #1900ff !important; */
                }

                .btn-outline-custom:hover {
                    color: #ff8000 !important;
                    font-weight: bold;
                    border-color: #ff8000 !important;
                }
            </style>
            <div style="background-color: #292775; color: #fff; padding: 5px; text-align: center;">
                You are logged in as 
                <strong>{{ session('School_name') }}</strong>               
                <button id="leaveImpersonation" class="btn btn-sm btn-outline-custom" style="">Return to Dashboard</button>
            </div>
            <script>
                $(document).on('click', '#leaveImpersonation', function () {
                    Swal.fire({
                        icon: 'info',
                        title: 'Confirm',
                        text: 'Return to Dashboard?',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Return',
                        cancelButtonText: 'Cancel',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('leave.school') }}",
                                type: "POST",
                                dataType: "json",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                beforeSend: function () {
                                    Swal.fire({
                                        title: 'Please wait...',
                                        text: 'Returning to Dashboard',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });
                                },
                                success: function (response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: response.message || 'Redirecting...',
                                            showConfirmButton: false
                                        });
                                        window.location.replace(response.redirect_url);
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: response.message || 'Unable to return.'
                                        });
                                    }
                                },
                                error: function (xhr) {
                                    console.log('Status:', xhr.status);
                                    console.log('Response:', xhr.responseText);
                                    let message = 'Server Error. Check console.';
                                    try {
                                        let res = JSON.parse(xhr.responseText);
                                        message = res.message || message;
                                    } catch (e) {}
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: message
                                    });
                                }
                            });
                        }
                    });
                });
            </script>
        @endif

     
        @yield('content')

        @include('layouts.footer')

        @stack('scripts')



         <script>
            function disableSelect(event) {
                event.preventDefault();
            }

            function startDrag(event) {
                window.addEventListener('mouseup', onDragEnd);
                window.addEventListener('selectstart', disableSelect);
                // ... my other code
            }

            function onDragEnd() {
                window.removeEventListener('mouseup', onDragEnd);
                window.removeEventListener('selectstart', disableSelect);
                // ... my other code
            }

            window.addEventListener('selectstart', function(e) {
                e.preventDefault();
            });

            function markAsRead(id) {
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            }
            
        </script> 
       

    </body>
</html>