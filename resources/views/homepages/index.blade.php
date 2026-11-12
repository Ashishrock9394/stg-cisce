@extends('layouts.filldart-app')

@section('title', 'CISCE | ' . $title)

@section('content')

<div class="container">
    <div class="t-mrg2 mb-5 pb-5">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('filldart.dashboard') }}" class="back-button">
                            <span class="arrow">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     width="16"
                                     height="16"
                                     fill="currentColor"
                                     class="bi bi-arrow-left-short"
                                     viewBox="0 0 16 16">

                                    <path fill-rule="evenodd"
                                          d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147
                                          2.146a.5.5 0 0 1-.708.708l-3-3a.5.5
                                          0 0 1 0-.708l3-3a.5.5
                                          0 1 1 .708.708L5.707 7.5H11.5a.5.5
                                          0 0 1 .5.5" />

                                </svg>
                            </span>
                        </a>
                        <div class="heading-rw mt-0 mt-md-2 mb-0 pt-0">
                            <h1 class="px-0 px-lg-0 mr-0">
                                {{ $title }}
                           </h1>
                        </div>
                    </div>
                </div>

                <div class="t-mrg">

			        <div class="row text-center justify-content-md-center">
			            <div class="col-12 col-md-12 col-lg-12 col-xl-10">
			                <div class="form-row" style="justify-content: center;">

			                	<!-- Trainers Dashboard  -->			                    					
								
								
			                    <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
									<a href="{{ route('training_manual') }}" class="box"><div>
									<img class="img-fluid" alt="" src="{{asset('assets/icons/trainer-manual2.svg') }}" target="_blank"></div><span>
										Training Manual</span>
									</a>
								</div>

				                <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
									<a href="{{ route('training.videos') }}" class="box"><div>

									<img class="img-fluid" alt="" src="{{asset('assets/icons/test-demo.svg') }}" target="_blank"></div><span>
										Training videos</span>
									</a>
								</div>

                                <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
                                    <a href="{{ route('test_videos') }}" class="box"><div>
                                    <img class="img-fluid" alt="" src="{{asset('assets/icons/batteryOfTest.png') }}"></div><span>
                                    Battery of Tests</span></a>
                                </div>


                               
                                <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
                                    <a href="{{ route('faq') }}" class="box"><div>
                                    <img class="img-fluid" alt="" src="{{asset('assets/icons/faq.svg') }}" target="_blank"></div><span>
                                        Faq</span>
                                    </a>
                                </div>
                                
                                @if(Auth::user()->role_id != 9)
                               
                                <div class="col-4 col-md-3 col-lg-2 col-xl-2 mb-4">
                                    <a href="{{ route('contact_me') }}" class="box"><div>
                                    <img class="img-fluid" alt="contact-us"  src="{{asset('assets/icons/contact-us.png') }}" target="_blank"></div><span>
                                        Contact</span>
                                    </a>
                                </div>
                                @endif
			                </div>
			            </div>
			        </div>
			    </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-video"
     id="playModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="modalVideoTitle"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg"
         role="document">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="modalVideoTitle">
                    Training Video
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>

            </div>

            <div class="modal-body text-center"
                 id="modalVideoBody">

                <p class="text-muted mb-0">
                    Loading video...
                </p>

            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>

$(document).ready(function () {

    $(document).on('click', '.training-video', function (e) {

        e.preventDefault();

        let videoTitle = $(this).data('title');
        let videoUrl = $(this).data('video');

        $('#modalVideoTitle').text(videoTitle);

        if (!videoUrl || videoUrl === '#') {

            $('#modalVideoBody').html(`
                <div class="py-5">

                    <i class="bi bi-exclamation-circle"
                       style="font-size: 40px;">
                    </i>

                    <p class="mt-3 mb-0">
                        Video is not available.
                    </p>

                </div>
            `);

            $('#playModal').modal('show');

            return;
        }

        let videoId = '';

        try {

            let url = new URL(videoUrl);

            if (url.hostname === 'www.youtube.com' ||
                url.hostname === 'youtube.com') {

                videoId = url.searchParams.get('v');

            } else if (url.hostname === 'youtu.be') {

                videoId = url.pathname.substring(1);

            }

        } catch (error) {

            console.log('Invalid YouTube URL');

        }

        if (!videoId) {

            $('#modalVideoBody').html(`
                <div class="py-5">

                    <i class="bi bi-exclamation-circle"
                       style="font-size: 40px;">
                    </i>

                    <p class="mt-3 mb-0">
                        Invalid YouTube video URL.
                    </p>

                </div>
            `);

            $('#playModal').modal('show');

            return;
        }

        let videoHtml = `
            <div class="embed-responsive embed-responsive-16by9">

                <iframe
                    class="embed-responsive-item"
                    src="https://www.youtube.com/embed/${videoId}"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>

            </div>
        `;

        $('#modalVideoBody').html(videoHtml);

        $('#playModal').modal('show');

    });

    $('#playModal').on('hidden.bs.modal', function () {

        $('#modalVideoBody').html(`
            <p class="text-muted mb-0">
                Loading video...
            </p>
        `);

        $('#modalVideoTitle').text('Training Video');

    });

});

</script>

@endpush