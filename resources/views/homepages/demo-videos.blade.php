@extends('layouts.app')
@section('content')


<div class="container">
    <div class="py-5">
        <div class="container-fluid p-0 pt-3">
            <div class="row mt-5 mb-4">
                <div class="col">
                    <div class="heading-rw mt-0 p-0">
                        <h1>Videos and Demo</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- <div class="col-12 col-md-6">
                    <a href="#a" class="d-flex w-100 justify-content-between align-items-center p-3 py-4 play-card mb-3">
                        <span>Register School On Webportal</span>
                        <span class="play-mg"><img src="{{ asset('assets/imgs/video_i2.png') }}" alt=""></span>
                    </a>
                </div> -->
                <div class="col-12 col-md-6">
                    <a href="#a" class="d-flex w-100 justify-content-between align-items-center p-3 py-4 play-card mb-3" data-toggle="modal" data-target=".upload-student-data">
                        <span>Upload Student Data</span>
                        <span class="play-mg"><img src="{{ asset('assets/imgs/video_i2.png') }}" alt=""></span>
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <a href="#a" class="d-flex w-100 justify-content-between align-items-center p-3 py-4 play-card mb-3" data-toggle="modal" data-target=".rectify-error-uploading-data">
                        <span>How To Rectify Error While Uploading Data</span>
                        <span class="play-mg"><img src="{{ asset('assets/imgs/video_i2.png') }}" alt=""></span>
                    </a>
                </div>

                <div class="col-12 col-md-6">
                    <a href="#a" class="d-flex w-100 justify-content-between align-items-center p-3 py-4 play-card mb-3" data-toggle="modal" data-target=".student-idcard">
                        <span>Download Student ID Cards</span>
                        <span class="play-mg"><img src="{{ asset('assets/imgs/video_i2.png') }}" alt=""></span>
                    </a>
                </div>
                <!-- <div class="col-12 col-md-6">
                    <a href="#a" class="d-flex w-100 justify-content-between align-items-center p-3 py-4 play-card mb-3">
                        <span>Promote Students</span>
                        <span class="play-mg"><img src="{{ asset('assets/imgs/video_i2.png') }}" alt=""></span>
                    </a>
                </div> -->
                <div class="col-12 col-md-6">
                    <a href="#a" class="d-flex w-100 justify-content-between align-items-center p-3 py-4 play-card mb-3" data-toggle="modal" data-target=".registration-assessor">
                        <span>Registration of Assessor</span>
                        <span class="play-mg"><img src="{{ asset('assets/imgs/video_i2.png') }}" alt=""></span>
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <a href="#a" class="d-flex w-100 justify-content-between align-items-center p-3 py-4 play-card mb-3" data-toggle="modal" data-target=".manage-assessor">
                        <span>Manage Assessor</span>
                        <span class="play-mg"><img src="{{ asset('assets/imgs/video_i2.png') }}" alt=""></span>
                    </a>
                </div>

                <!-- <div class="col-12 col-md-6">
                    <a href="#a" class="d-flex w-100 justify-content-between align-items-center p-3 py-4 play-card mb-3">
                        <span>View Report Of Assessed Students</span>
                        <span class="play-mg"><img src="{{ asset('assets/imgs/video_i2.png') }}" alt=""></span>
                    </a>
                </div> -->
            </div>

        </div>
    </div>


</div>

<!-- Upload Student Data -->
<div class="modal fade upload-student-data1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Student Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="https://www.youtube.com/embed/JyLCKxxQ680" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" style="aspect-ratio: 16/9;"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- How To Rectify Error While Uploading Data -->
<div class="modal fade rectify-error-uploading-data1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">How To Rectify Error While Uploading Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="https://www.youtube.com/embed/JyLCKxxQ680" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" style="aspect-ratio: 16/9;"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- Download Student ID Cards -->
<div class="modal fade student-idcard1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Download Student ID Cards</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="https://www.youtube.com/embed/JyLCKxxQ680" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" style="aspect-ratio: 16/9;"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- Registration of Assessor -->
<div class="modal fade registration-assessor1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registration of Assessor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="https://www.youtube.com/embed/JyLCKxxQ680" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" style="aspect-ratio: 16/9;"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- Manage Assessor -->
<div class="modal fade manage-assessor1" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Manage Assessor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe src="https://www.youtube.com/embed/JyLCKxxQ680" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" style="aspect-ratio: 16/9;"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection