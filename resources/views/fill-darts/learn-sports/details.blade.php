@extends('layouts.filldart-app')
@section('title', 'Goforfit | ' . $title)


@push('sports-style')
<style>
    .video_player { height: 400px; display: block; aspect-ratio: 16 / 9; }
</style>
@endpush

@section('content')
<body class="inr-pg">

   <div class="">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('learn.sports') }}" > Sports</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sports.videos', ['sport_id' => $topic_details->sport_id]) }}">{{ $topic_details->sport_name}}</a></li> 
            <li class="breadcrumb-item active" aria-current="page">{{ $topic_details->topic}}</li>
         </ol>
      </nav>
      <div class="container-fluid">
         <div class="concepts-area">
            <div class="row">
               <div class="col-lg-4 col-xl-3 d-none d-sm-none d-md-none d-lg-block">
                  <div class="chapter-menu ml-0 ml-lg-2">
                    <nav id="sidebar">

                        <div class="sidebar-header"> <h1>{{ $topic_details->sport_name }}</h1> </div>

                        <ul class="list-unstyled components">
                            @foreach($chapters_details as $key => $details)
                                @if( $key === $topic_details->chapter)                                    
                                    <li class="active">
                                        <a href="#{{ $key }}" data-bs-toggle="collapse" aria-expanded="true" class="dropdown-toggle">
                                            <span class="chptr-nm">{{ $key }}</span> <p>{{ $details['chapter_name'] }}</p>
                                        </a>

                                        <ul class="collapse show list-unstyled" id="{{ $key }}">
                                            @foreach($details['chapterVideos'] as $video)
                                                @if($video->topic == $topic_details->topic)
                                                    <li class="active"><a href="#">{{ $video->topic }}</a></li>
                                                @else
                                                    <li><a href="{{ route('topics.videos', ['sport_id' =>$video->sport_id ,'topic_id' => $video->id]) }}" >{{ $video->topic }}</a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                    <li>
                                        <a href="#{{ $key }}" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                                            <span class="chptr-nm">{{ $key }}</span>
                                            <p>{{ $details['chapter_name'] }}</p>
                                        </a>

                                        <ul class="collapse list-unstyled" id="{{ $key }}">
                                            @foreach($details['chapterVideos'] as $video)
                                                <li><a href="{{ route('topics.videos', ['sport_id' =>$video->sport_id ,'topic_id' => $video->id]) }}" >{{ $video->topic }}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @endif
                            @endforeach
                        </ul>                       
                    </nav>
                  </div>
               </div>




               <a href="concepts.html" class="back-btn">
                  <img src="images/back-arrow.png" alt="">
                  <span class="back-to-chptr">
                     <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd">
                        <path d="M2.117 12l7.527 6.235-.644.765-9-7.521 9-7.479.645.764-7.529 6.236h21.884v1h-21.883z"></path>
                     </svg>
                     Back to Concepts
                  </span>
               </a>


               <div class="col-xs-12 col-lg-8 col-xl-9">
                  <div class="row">
                     <div class="col">
                        <div class="mt-3 activity-rw heading-rw">
                           <h2>{{ $topic_details->topic }}</h2>
                        </div>
                     </div>
                  </div>

                  <div class="activity-dv">
                    <div class="col-12 col-md-4 col-lg-3 getactive">
                        <div class="mb-4">             
                            @php
                                $videoUrl = $topic_details->video_url;
                                if (str_contains($videoUrl, 'youtube.com/watch?v=')) {
                                    $videoId = substr($videoUrl, strpos($videoUrl, 'v=') + 2);
                                    $videoUrl = "https://www.youtube.com/embed/" . $videoId;
                                }
                            @endphp
                            <iframe class="video_player" src="{{ $videoUrl }}" frameborder="0" allowfullscreen></iframe>
                            <div class="card-body">{{ $topic_details->topic }}</div>
                        </div>   
                    </div>

                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint occaecat cupidatat proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Duis
                        aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                    </p>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</body>
</html>
@endsection
