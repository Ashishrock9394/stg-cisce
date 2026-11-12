@extends('layouts.app')
@section('title', 'CISCE | Physical Health and Fitness Assessment Programme')

@push('icse-style')
<style>
        main { height: calc(100vh);}
        .jumbotron {  background-color: rgb(0 0 0 / 40%); height: 100%; display: flex; align-items: center;justify-content: center;
        position: absolute; top: 0; left: 0; bottom: 0; right: 0; z-index: 1;}
        .jumbotron h1 { color: #fff; text-shadow: 1px 1px 6px rgb(0 0 0 / 30%); }
        .main___banner .btn { width: auto; height: auto; font-size: large; border-radius: 8px; }
        .main___banner .btn-primary { border: 2px solid #e67400; background-color: #e67400;}
        .main___banner .btn-primary:hover { border: 2px solid #e67400; }
        .main___banner .btn-outline-primary { border: 2px solid #e67400; color: #e67400; }
        .main___banner .btn-outline-primary:hover { border: 2px solid #e67400; background-color: #e67400; color: #fff;}
     </style>
@endpush

@section('content')
    <main role="__main">
        <div class="jumbotron mb-0 text-center">
            <div class="main___banner">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <h1 class="mb-4 pb-3">CISCE Physical Health and Fitness Assessment Programme</h1>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                <a class="btn btn-primary btn-lg user-login" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <img src="{{ asset('assets/imgs/cisce-bg1.webp') }}" sizes="100vw" fetchpriority="high" class="home-banner" alt="CISCE Banner">
    </main>






    {{--
    <!-- Modal (Announcement) -->
    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>The system will be unavailable today, 20 January 2026, from 4:00 PM to 6:00 PM due to scheduled maintenance.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script>
        $(window).on('load', function() {
            var delayMs = 200; // delay in milliseconds
            setTimeout(function() {
                $('#alertModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#alertModal').modal('show');
            }, delayMs);
        });


        document.addEventListener("DOMContentLoaded", function () {
            const loginBtn = document.getElementById("loginBtn");

            // Get current time
            const now = new Date();
            const hour = now.getHours();
            const minutes = now.getMinutes();
            const todayFormatted = now.toLocaleDateString('en-GB', {
                day: 'numeric', month: 'long', year: 'numeric', weekday: 'long'
            });

            // Check if in maintenance window (between 21:00 to 23:59 or 00:00)
            const inMaintenanceWindow = (hour >= 16 || hour < 18);

            if (inMaintenanceWindow) {
                // Disable button and show info
                loginBtn.disabled = true;
                loginBtn.style.cursor = "not-allowed";

                Swal.fire({
                    icon: 'info',
                    title: 'Scheduled Maintenance',
                     html: `
                        The system is currently under maintenance on <b>${todayFormatted}</b> 
                        from <b>04:00 PM to 06:00 PM</b>.<br><br>
                        <span style="color:#d33; font-weight:bold;">Login will be available after the scheduled time.</span>
                    `,
                    confirmButtonText: 'Okay'
                });
            }

            loginBtn.addEventListener("click", function () {
                if (inMaintenanceWindow) {
                    // If clicked manually (in case user bypassed the disable)
                    Swal.fire({
                        icon: 'info',
                        title: 'Scheduled Maintenance',
                        html: `
                            The system is under maintenance until <b>06:00 PM</b> today.<br><br>
                            <span style="color:#d33; font-weight:bold;">Please try to login after the scheduled time.</span>
                        `,
                        confirmButtonText: 'Got it'
                    });
                } else {
                    // Proceed with login
                    Swal.fire({
                        icon: 'success',
                        title: 'Logging in...',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Simulate form submission
                    // document.getElementById('loginForm').submit();
                }
            });
        });
    </script>
    --}}

@endsection