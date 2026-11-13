@extends('layouts.filldart-app')
@section('title', 'Goforfit | ' . $title)


@push('sports-style')
<style>
   

   .logo {
      left: 0px;
   }
    .btn { 
      min-width: auto; 
      width: auto;
   }
   .expand-dv {
      margin-top: 15px;
   }
   .expand-dv .card {
      margin-bottom: 15px;
      box-shadow: 0 1px 6px rgb(0 0 0 /20%);
      border-top: 0px solid #ccc;
      border-bottom: 0px solid #ccc;
      border-radius: 0.5rem !important;
      padding: 5px 15px;
   }
   .expand-dv h3 {
      font-size: 18px;
      margin: 5px 0;
      color: #000;
   }
   .activities-dv .activity-list li a {
      border-radius: .5rem;
      border: 1px solid #e5e5e5;
   }
   .activities-dv .activity-list span.card {
      padding: 0;
   }
   .no-of-videos {
      border: none;
      background-color: #ff8810;
      border-radius: 50px;
      padding: 2px 8px;
      margin-left: 5px;
      color: #fff;
   }
   .sport-summery {
      position: relative; 
      top: 0px;
      margin-bottom: 30px;
   }
   .card-details {
      overflow: hidden;
   }
   .back-btn {
      display: none;
   }
   .back-btn img {
      display: none;
   }
   .card-details.card  {
      margin: 0;
   }
   .card-details.card .card-body {
      margin: 0;
      padding: 0;
   }
   .card-details h1 {
      font-size: clamp(1.65rem, 0.8333rem + 1.5556vw, 2rem);
   }
   .h2, h2 {
      font-size: clamp(1.25rem, 0.8333rem + 1.5556vw, 1.75rem);
   }
   .concepts-area h2 {
      margin-bottom: 0;
   }
   .chapter-dtls .card-text {
      display: block;
   }
   .card-details {
      overflow: visible;
   }
   .card-details img {
      display: none;
   }
   .activities-dv .activity-list {
      margin: 0;
   }
   .activities-dv .activity-list li a {
      display: block;
      border-color: #ddd;
      overflow: hidden;
      border-radius: 8px;
   }
   .activities-dv .activity-list .card {
      border: 0;
      height: auto;
   }
   .activities-dv .activity-list span.card {
      background-color: #fff;
      border: none;
      border-radius: 0 !important;
   }
   .activities-dv .activity-list .card-img-left {
      width: auto;
      aspect-ratio: 4 / 3;
   }
   .activities-dv .activity-list .card h6 {
      color: #333;
   }
   .expand_btn{
      border: none;
   }


   @media only screen and (max-width: 767px) {
      

   }

   @media (min-width: 768px) {
      .card-details {
         overflow: hidden;
      }
      .card-details h1 {
        margin-bottom: 10px;
        margin-top: 0px;
      }
   }
      
   @media (min-width: 992px) {

      .sport-summery {
         position: sticky; 
         top: 105px;
      }
      .back-btn {
         display: block;
      }
      .card-details img {
        display: block;
      }
      .back-btn img {
         display: block;
      }
      .card-details.card .card-body {
        padding: 0.8rem 1.15rem 1rem 1.15rem;
      }
   }



</style>
@endpush

@section('content')

      <div class="container">
         <div class="concepts-area t-mrg2">
            <div class="row">
               <div class="col-12 col-lg-4 col-xl-3">
                  <div class="sport-summery">
                     <a href="{{ url('/learn-sports') }}"class="back-btn">
                        <img src="{{ asset('public/assets/imgs/back-arrow.png') }}" alt="">
                        <span class="back-to-chptr">
                           <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd">
                              <path d="M2.117 12l7.527 6.235-.644.765-9-7.521 9-7.479.645.764-7.529 6.236h21.884v1h-21.883z"/>
                           </svg>Back to Chapters</span>
                     </a>

                     <div class="card card-details ml-0">

                     <img src="{{ asset('public/change-sports/' . $sport_details->img) }}" class="img-fluid rounded-0" alt="Sports Img">

                     <div class="card-body chapter-dtls">
                           <h2 class="chapter-heading">{{ $sport_details->name ?? '' }}</h2>
                           <p class="card-text">{{ $sport_details->desc ?? '' }}   </p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-12 col-lg-8 col-xl-9">
                  <div class="row">
                     <div class="col-10 col-sm-10 col-md d-flex align-items-center">
                        <h2>Learn {{ $sport_details->name ?? '' }}</h2>
                     </div>
                     <div class="col-2 col-sm-auto col-md-auto d-none d-sm-none d-md-none d-lg-block">
                        <button type="button" class="btn btn-primary expand_btn">
                           <span>
                              <svg width="24"
                                 height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd"
                                 clip-rule="evenodd">
                                 <path
                                    d="M14 19h-14v-1h14v1zm9.247-8.609l-3.247 4.049-3.263-4.062-.737.622 4 5 4-5-.753-.609zm-9.247 2.609h-14v-1h14v1zm0-6h-14v-1h14v1z" />
                              </svg>
                           </span>
                           Expand All
                        </button>                   
                     </div>
                  </div>

                  <div class="expand-dv">

                     @foreach($chaptersDetails as $key => $details)

                     <div class="card">
                        <div class="card-header">
                           <div class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#{{$details['chapter_name']}}" aria-expanded="false" aria-controls="collapse">
                              <div class="row">
                                 <div class="col">
                                    {{ $key }}
                                    <label for="No of videos" class="no-of-videos mb-0">
                                       @if($details['chapterVideos'] && count($details['chapterVideos']) === 1)
                                       1 Video
                                    @elseif($details['chapterVideos'] && count($details['chapterVideos']) > 1)
                                       {{ count($details['chapterVideos']) }} Videos

                                    @else
                                       N.A.
                                    @endif
                                    </label>
                                    <h3 class="mt-2 mb-0 pt-1">{{ $details['chapter_name'] ?? 'N.A.' }}</h3>

                                 </div>
                                 <div class="col-auto">
                                    <span class="open-i">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                          height="24" viewBox="0 0 24 24">
                                          <path
                                             d="M0 16.67l2.829 2.83 9.175-9.339 9.167 9.339 2.829-2.83-11.996-12.17z" />
                                       </svg>
                                    </span>
                                    <span class="close-i">
                                       <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                          height="24" viewBox="0 0 24 24">
                                          <path
                                             d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z" />
                                       </svg>
                                    </span>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="collapse" id="{{$details['chapter_name']}}">

                           <div class="card-body p-0">
                              <div class="activities-dv pr-0 pr-md-3 mt-2 pt-2 mb-0">                                
                                 <ul class="activity-list form-row">
                                    @foreach($details['chapterVideos'] as $video)
                                       <li class="col-12 col-md-6 col-lg-6 col-xl-4">
                                          <a href="javascript:void(0);"
                                             data-bs-toggle="modal"
                                             data-bs-target="#getTutorialVideos"
                                             onclick="getTutorialVideos({{ json_encode($video->topic) }}, {{ json_encode($video->video_url) }})">
                                             <span class="card">
                                                <img class="card-img-left" src="{{ $video->thumbnail_url }}" alt="Card image cap">
                                                <span class="card-body">
                                                   <h6 class="card-title">{{ $video->topic }}</h6>
                                                </span>
                                             </span>
                                          </a>
                                       </li>
                                    @endforeach
                                 </ul>
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


<!-- The Modal -->
<div class="modal" id="getTutorialVideos" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="tutorial">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="modal-title" id="model-title-id">Dynamic Heading</h3>
                <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeModal();"></button>
            </div>

            <div class="modal-body pt-0 pb-4 px-4 mt-3">
                <iframe id="youtubeurl_id"
                        src=""
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        style="aspect-ratio: 16/9; width:100%; height:56.25%;"
                        allowfullscreen>
                </iframe>
            </div>

        </div>
    </div>
</div>

<!-- End The Model -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css">
<script src="https://cdn.jsdelivr.net/npm/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>

<script>

  $(".btn-primary").click(function() {

      if ($(this).data("closedAll")) {
          $(".collapse").collapse("show");
      } else {
          $(".collapse").collapse("hide");
      }

      // save last state
      $(this).data("closedAll", !$(this).data("closedAll"));
  });

  $(".btn-primary").data("closedAll", true);

   $(document).ready(function() {
      $("#sidebar").mCustomScrollbar({
          theme: "minimal"
      });

      $('#sidebarCollapse').on('click', function() {
          $('#sidebar, #content').toggleClass('active');
          $('.collapse.in').toggleClass('in');
          $('a[aria-expanded=true]').attr('aria-expanded', 'false');
      });
   });


  function getTutorialVideos(title, videoUrl) {
      document.getElementById('model-title-id').innerText = title;
      const iframe = document.getElementById('youtubeurl_id');
      if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
         videoUrl = videoUrl.replace("watch?v=", "embed/");
      }
      iframe.src = videoUrl + "?autoplay=1";
   }

   function closeModal() {
      $('#getTutorialVideos').hide();
      const iframe = document.getElementById('youtubeurl_id');
      iframe.src = ''; 
   }
</script>






@endsection