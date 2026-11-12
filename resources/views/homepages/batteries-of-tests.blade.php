@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .play-card {
        background-color: #fff;
        color: #000;
        text-decoration: none;
        box-shadow: 0 2px 5px rgb(202 210 227 / 55%);
        border-radius: 10px;
        transition: 0.5s;
    }
    .play-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .banner-bg-col {
        display: flex;
        justify-content: space-between;
    }
    .banner-text-col {
        margin-top: 15px;
    }
    .test-name h4 {
        font-size: 18px;
        color: #292775;
    }
    .test-name p {
        color: #000;
        margin: 0;
    }
    .tabs {
        gap: 15px;
    }
    .tabs a[aria-expanded="true"] {
        color: #211275;
        background: transparent;
    }
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        background-color: #ff8000;
    }
    .demo-video {
        width: 100%;
        height: 400px;
    }
    .open-modal-btn{
        cursor: pointer !important;
    }
   

</style>

<div class="all-chaptr-cards">
    <div class="container">
        <div class="t-mrg2 mb-5 pb-5">

            <div class="row">
                <div class="col-12 col-md">
                    <div class="heading-rw mt-0 mt-md-1 mb-0 p-0">
                        @if(auth()->guard('web')->check() || auth()->guard('sstudent')->check())

                            <x:back-button title="Battery of Tests Video and Demo" />

                            {{-- <a href="javascript:history.back()" class="back-button">
                                <span class="arrow">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                         fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                              d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 
                                              1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5"/>
                                    </svg>
                                </span>
                            </a> --}}
                        @endif
                        
                    </div>
                </div>
                <div class="col-12 col-md-auto mt-4 mt-md-0">
                    <ul class="tabs nav nav-pills mb-0 justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a href="#junior" data-toggle="tab" aria-expanded="false"
                               class="nav-link active">5-8 Years</a>
                        </li>
                        <li class="nav-item">
                            <a href="#senior" data-toggle="tab" aria-expanded="true"
                               class="nav-link">9-18+ Years</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="tab-content mt-4 mb-5">
                        <!-- Junior Tab -->
                        <div id="junior" class="tab-pane active show">
                            <div class="row">
                                @foreach ($juniorVideos as $videoGroup)
                                    <div class="col-12 col-md-6">
                                        <div class="know-more-banner p-3 play-card mb-3">
                                            <div class="test-all-banner-col">
                                                <div class="banner-bg-col d-flex justify-content-between align-items-start">
                                                    
                                                    {{-- Left Side: Skill Name and Language Links --}}
                                                    <div class="banner-text-col d-flex flex-column">
                                                        <div class="test-name mb-2">
                                                            <h4 class="mb-2">{{ $videoGroup['videos']->first()->skill_name }}</h4>

                                                            {{-- Language Links with YouTube icon --}}
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($videoGroup['videos'] as $video)
                                                                    <div class="language mr-3 mb-2 d-flex align-items-center">
                                                                        <i class="fa fa-youtube-play mr-2" style="color:red; font-size: 20px;"></i>
                                                                        <a class="text-primary open-modal-btn"
                                                                        data-toggle="modal"
                                                                        data-target="#playModal2"
                                                                        data-skill-id="{{ $video->skill_id }}"
                                                                        data-skill-name="{{ $video->skill_name }}"
                                                                        data-lang="{{ strtolower($video->type_video) }}"
                                                                        data-video-url="{{ $video->video_url }}"
                                                                        data-video-type="{{ $video->type_video }}">
                                                                        {{ ucfirst(strtolower($video->type_video)) }}
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Right Side: Icon --}}
                                                    <div class="test-icon">
                                                        <img src="{{ asset('assets/icons/BatteryOfTests/' . $videoGroup['videos']->first()->icons) }}" 
                                                            alt="icon"
                                                            style="height: 70px;">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                        <!-- Senior Tab -->
                        <div id="senior" class="tab-pane fade">
                            <div class="row">
                                @foreach ($seniorVideos as $videoGroup)
                                    <div class="col-12 col-md-6">
                                        <div class="know-more-banner p-3 play-card mb-3">
                                            <div class="test-all-banner-col">
                                                <div class="banner-bg-col d-flex justify-content-between align-items-start">
                                                    
                                                    {{-- Left Side: Skill Name and Language Links --}}
                                                    <div class="banner-text-col d-flex flex-column">
                                                        <div class="test-name mb-2">
                                                            <h4 class="mb-2">{{ $videoGroup['videos']->first()->skill_name }}</h4>

                                                            {{-- Language Links with YouTube icon --}}
                                                            <div class="d-flex flex-wrap">
                                                                @foreach ($videoGroup['videos'] as $video)
                                                                    <div class="language mr-3 mb-2 d-flex align-items-center">
                                                                        <i class="fa fa-youtube-play mr-2" style="color:red; font-size: 20px;"></i>
                                                                        <a class="text-primary open-modal-btn"
                                                                        data-toggle="modal"
                                                                        data-target="#playModal2"
                                                                        data-skill-id="{{ $video->skill_id }}"
                                                                        data-skill-name="{{ $video->skill_name }}"
                                                                        data-lang="{{ strtolower($video->type_video) }}"
                                                                        data-video-url="{{ $video->video_url }}"
                                                                        data-video-type="{{ $video->type_video }}">
                                                                        {{ ucfirst(strtolower($video->type_video)) }}
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Right Side: Icon --}}
                                                    <div class="test-icon">
                                                        <img src="{{ asset('assets/icons/BatteryOfTests/' . $videoGroup['videos']->first()->icons) }}" 
                                                            alt="icon"
                                                            style="height: 70px;">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Video Modal --}}
<div class="modal fade modal-video" id="playModal2" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Loading video...</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const allVideos = @json($videoData);

    $('#playModal2').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const skillId = button.data('skill-id');
        const skillName = button.data('skill-name');
        const selectedLang = button.data('lang')?.toLowerCase();
        const modal = $(this);

        modal.find('.modal-title').text(skillName);

        const filtered = allVideos.filter(v => v.skill_id == skillId && v.type_video.toLowerCase() === selectedLang);

        if (filtered.length === 0) {
            modal.find('.modal-body').html(`<p>No ${selectedLang} video available for this test.</p>`);
            return;
        }

        const video = filtered[0];
        let url = video.video_url;
        if (url.startsWith('_1')) {
            url = url.slice(2);  //in senior bmi
        }

        if (url.includes('youtu.be/')) {
            url = url.replace('youtu.be/', 'www.youtube.com/embed/').split('?')[0];
        } else if (url.includes('watch?v=')) {
            url = url.replace('watch?v=', 'embed/').split('&')[0];
        }

        const videoHtml = `
            <div class="video-block">
                <h4 class="test-cat" style="font-size:14px; background-color:#2a2876;color:white;padding:4px;">${video.type_video.charAt(0).toUpperCase() + video.type_video.slice(1)} Video</h4>
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item demo-video"
                            src="${url}"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                    </iframe>
                </div>
            </div>
        `;
        modal.find('.modal-body').html(videoHtml);
    });

    $('#playModal2').on('hidden.bs.modal', function () {
        $(this).find('.modal-body').html('<p class="text-muted">Loading video...</p>');
    });
});
</script>

@endsection
