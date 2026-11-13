@extends('layouts.filldart-app')

@section('title', 'CISCE | ' . $title)

@section('content')

<div class="container">
    <div class="t-mrg2 mb-5 pb-5">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('training.resources') }}" class="back-button">
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
                <div class="row text-center justify-content-md-center mt-2 mt-lg-4">
                   <div class="col-12 col-md-12 col-lg-12">
                        <div class="all-tests">
                            <ul class="list-group tests mt-3">
                                @forelse($TrainingKits as $training)
                                    @if($training->id != 11)
                                    <li>
                                        <div class="get_ready training-video"
                                             data-id="{{ $training->id }}"
                                             data-title="{{ $training->title }}"
                                             data-video="{{ $training->video_url }}"
                                             style="cursor: pointer;">

                                            <a href="javascript:void(0);">

                                                <span>
                                                    {{ $training->title }}
                                                </span>

                                               

                                            </a>

                                        </div>

                                        <a href="javascript:void(0);"
                                           class="play training-video"
                                           data-id="{{ $training->id }}"
                                           data-title="{{ $training->title }}"
                                           data-video="{{ $training->video_url }}">

                                            <i class="bi bi-play-circle"></i>

                                        </a>

                                    </li>
                                    @endif

                                @empty

                                    <li>
                                        <div class="get_ready">
                                            <span>
                                                No training material available.
                                           </span>
                                        </div>
                                    </li>
                                @endforelse

                                @if(Auth::user()->role_id == 9)
                                    @foreach($TrainingKits as $training)
                                        @if($training->id == 11)
                                            <li>
                                                <div class="get_ready training-video"
                                                     data-id="{{ $training->id }}"
                                                     data-title="{{ $training->title }}"
                                                     data-video="{{ $training->video_url }}"
                                                     style="cursor: pointer;">

                                                    <a href="javascript:void(0);">
                                                        <span>{{ $training->title }}</span>
                                                    </a>

                                                </div>

                                                <a href="javascript:void(0);"
                                                   class="play training-video"
                                                   data-id="{{ $training->id }}"
                                                   data-title="{{ $training->title }}"
                                                   data-video="{{ $training->video_url }}">

                                                    <i class="bi bi-play-circle"></i>
                                                </a>
                                            </li>

                                        @endif

                                    @endforeach

                                @endif

                            </ul>
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
                        class="close btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>

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