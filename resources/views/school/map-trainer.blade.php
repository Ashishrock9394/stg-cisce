@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style type="text/css">
   .header-container {
       display: flex;
       align-content: center;
       align-items: center;
       margin-top: 22px;
   }
   #enableExportBtn:hover {
        background-color: #ff7800 !important;
        border-color: #ff7800 !important;
    }
    #trainer_search_box, #enableExportBtn, #searchBtn {
        transition: none !important;
    }
    .toggler{
        overflow: visible !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    
<div class="container">

    <div class="t-mrg2 mb-5 pb-5">

        <div class="all-chaptr-cards mb-4" style="margin: 0;">
            <div class="row">
                <x:back-button title="{{$title}}" >
                    
                </x:back-button>
            </div>
        </div>

        <div class="header-container1 row gy-3 align-items-center mt-3">
            <div class="col-12 col-md-5 d-flex" id="trainer_search_box">
                <form class="form-inline d-flex w-100 flex-wrap">
                    <div class="d-flex w-100">
                        <input class="form-control flex-grow-1"
                            type="search"
                            id="trainer_search"
                            name="query"
                            placeholder="Search by : Registration ID | Name | Email"
                            aria-label="Search" style="width:240px;">

                        <button class="btn btn-outline-success ms-0 ml-2" type="submit" id="searchBtn">
                            Search
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-start">
                <div class="btn-group btn-group-toggle toggler" data-toggle="buttons">
                    <label class="btn btn-secondary {{ $errors->any() ? '' : 'active' }}">
                    <input type="radio" name="options" id="option1" autocomplete="off"
                            {{ $errors->any() ? '' : 'checked' }}>
                    Registered With School
                    </label>

                    <label class="btn btn-secondary {{ $errors->any() ? 'active' : '' }}">
                    <input type="radio" name="options" id="option2" autocomplete="off"
                            {{ $errors->any() ? 'checked' : '' }}>
                    Add Trainers
                    </label>
                </div>
            </div>

            @php
                $schoolIds = \App\Models\ActiveSchool::where('status', 'active')->pluck('school_user_id')->toArray();
            @endphp
            <div class="col-12 col-md-3 d-flex justify-content-center justify-content-md-end">
                <button id="enableExportBtn" type="button"
                        class="btn btn-outline-primary btn-md d-flex align-items-center">
                    <i class="fa-solid fa-download"></i>
                    <span class="d-block trainer-action-btn m-2">Credentials</span>
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        @endif

        <div id="mappedTrainersContainer" class="row mt-3" style="display: none;">
        <div class="col-12">
            <div id="mapped_trainer_table_container" class="table-responsive">

                @if(count($mappedTrainers))
                <table class="table table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>Trainer ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                        @if(Auth::user()->id == '3251' || Auth::user()->id == '3139')  
                        <th width="20px">Remove</th>
                        @endif
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($mappedTrainers as $trainer)
                    <tr data-userid="{{ $trainer->id }}">
                        <td>{{ $trainer->self_registrationId ?? 'N.A.' }}</td>
                        <td>{{ $trainer->name }}</td>
                        <td>{{ $trainer->email }}</td>
                        <td>{{ $trainer->phone }}</td>
                        <td>
                            <span class="badge badge-success">{{ $trainer->status }}</span>
                        </td>
                        <td>
                            @if($trainer->status == 'Mapped')
                                <a href="#" data-trainer-id="{{ $trainer->id }}" class="unmap-trainer-btn" id="unmap-trainer">Unmap</a>
                            @else
                                <a href="#" data-trainer-id="{{ $trainer->id }}" class="map-trainer-btn" id="map-trainer">Map</a>
                            @endif
                        </td>

                        @if(Auth::user()->id == '3251' || Auth::user()->id == '3139')  
                            <td  style="text-align: center;">
                            <a href="#" data-trainer-id="{{ $trainer->id }}" class="remove-trainer-btn" id="remove-trainer">
                                <i class="fa fa-trash-o" style="font-size:24px;color:red"></i>
                            </a>
                        </td>
                        @endif

                    </tr>
                    @endforeach
                    </tbody>
                </table>

                @else
                <p>No trainers are currently mapped to your school.</p>
                @endif

            </div>
            <div id="activity_from_div2" class="sports-filtr overlay">
                <div class="row">
                    <div class="col-12">

                    <div class="w-100 studs-list">

                        <div id="trainer_table_container" class="table-responsive" style="display: none;">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Trainer ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody id="trainer_info_table"></tbody>
                            </table>
                        </div>

                    </div>

                    </div>
                </div>
            </div>

        </div>
        </div>

        <div id="addTrainerForm" class="row mt-3" style="display: none;">
        <div class="col-12">
            <div class="bg-white p-3 p-md-4 rounded" style="margin-bottom:100px;">
                @include('auth.self_registration.trainer')
            </div>
        </div>
        </div>

    </div>

</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>


$(document).ready(function () {
    function toggleSections() {

        if ($('#option2').is(':checked')) {
            console.log("trainer form");
            $('#addTrainerForm').show();

            $('#mappedTrainersContainer').hide();
            $('#trainer_search_box').attr('style', 'visibility: hidden !important;');
            $('#enableExportBtn').attr('style', 'visibility: hidden !important;');
        } else {
            // Show mapped trainers section
            $('#addTrainerForm').hide();
            $('#mappedTrainersContainer').show();
            $('#trainer_search_box').attr('style', 'visibility: visible !important;');
            $('#enableExportBtn').attr('style', 'visibility: visible !important;');
        }
    }
    $('input[name="options"]').on('change', toggleSections);
    toggleSections();

});

// export trainers credentials 

$('#enableExportBtn').on('click', function() {
        // Collect all user IDs from the table
    var schoolUserIds = [];
    $('#mappedTrainersContainer tbody tr').each(function() {
        var userid = $(this).data('userid');
        if(userid) schoolUserIds.push(userid);
    });

    console.log(schoolUserIds);
    if(schoolUserIds.length === 0) {
        alert('No users to export!');
        return;
    }

    $.ajax({
        url: "{{ route('export.school.users') }}",
        type: "POST",
        data: { school_user_ids: schoolUserIds, export_type: 'excel', role: 'trainer' },
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        xhrFields: { responseType: 'blob' }, // important for downloading file
        success: function(blob) {
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "trainers-credential.xlsx";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            Swal.fire({
                title: 'Success',
                icon: 'success',
                text: 'Trainers credentials downloaded.',
                allowOutsideClick: false
            });
        },
        error: function(err) {
            console.error(err);
            alert('Error generating Excel');
        }
    });
});

$(document).ready(function () {

    // Autocomplete functionality
    $('#trainer_search').on('keyup', function() {
        if ($(this).val().trim() === '') {
            location.reload();
        }
    });
    $("#trainer_search").autocomplete({
        source: "{{ route('autocomplete.trainer') }}",
        minLength: 2,
        select: function (event, ui) {
            $('#trainer_search').val(ui.item.label);

            // Populate the table
            $('#trainer_info_table').html(`
                <tr>
                    <td>${ui.item.id}</td>
                    <td>${ui.item.name}</td>
                    <td>${ui.item.email}</td>
                    <td>${ui.item.phone}</td>
                    ${ui.item.activated ? `<td>                       
                        <button class="btn btn-primary btn-sm unmap-trainer-btn" data-trainer-id="${ui.item.userid}">Un-Map</button>
                        </td>` : `<td>
                            <button class="btn btn-primary btn-sm map-trainer-btn" data-trainer-id="${ui.item.userid}">Map</button>
                        </td>`}
                </tr>
            `);

            // Show the table
            $('#trainer_table_container').show();
            $('#mapped_trainer_table_container').css('display','none');
            return false;
        }
    });

    // Handle manual form submission (Search button)
    $('.form-inline').submit(function (e) {
        e.preventDefault();

        let query = $('#trainer_search').val();

        if(query.length > 1){
            $.ajax({
                url: "{{ route('autocomplete.trainer') }}",
                data: { term: query },
                success: function (data) {
                    let rows = '';
                    if (data.length > 0) {
                        data.forEach(function (trainer) {
                            rows += `
                                <tr>
                                    <td>${trainer.id}</td>
                                    <td>${trainer.name}</td>
                                    <td>${trainer.email}</td>
                                    <td>${trainer.phone}</td>
                                    ${trainer.activated ? `
                                        <td><button class="btn btn-primary btn-sm unmap-trainer-btn" data-trainer-id="${trainer.userid}">Un-Map</button></td>
                                    ` : `
                                        <td><button class="btn btn-primary btn-sm map-trainer-btn" data-trainer-id="${trainer.userid}">Map</button></td>
                                    `}
                                </tr>
                            `;
                        });
                    } else {
                        rows = '<tr><td colspan="5">No trainer found</td></tr>';
                    }

                    $('#trainer_info_table').html(rows);
                    $('#trainer_table_container').show();
                    $('#mapped_trainer_table_container').css('display','none');
                }
            });
        }
    });
});



$(document).on('click', '.map-trainer-btn', function () {
    const trainerId = $(this).data('trainer-id');


    handleResponseMessages( 'warning',  'Are you sure?', 'Do you want to map this trainer with the school?', {
        showCancel: true,
        confirmText: 'Yes, Map it!',
        cancelText: 'Cancel',
        onConfirm: () => { 
            $.ajax({
                url: "{{ route('trainer.map') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    trainer_id: trainerId
                },
                success: function (response) {
                    handleResponseMessages( 'success',  '', response.message, {
                        confirmText: 'OK',
                        onConfirm: () => { location.reload(); }
                    });
                },

                error: function (xhr) {
                    handleResponseMessages( 'error',  '', 'An error occurred while mapping the trainer.');
                }
            });
        },
        onCancel: () => { console.log('Cancelled!'); }
    });
    
});


$(document).on('click', '.unmap-trainer-btn', function () {
    const trainerId = $(this).data('trainer-id');


     handleResponseMessages( 'warning',  'Are you sure?', 'Do you want to unmap this trainer from the school?', {
        showCancel: true,
        confirmText: 'Yes, Unmap it!',
        cancelText: 'Cancel',
        onConfirm: () => { 
            $.ajax({
                    url: "{{ route('trainer.unmap') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        trainer_id: trainerId
                    },
                    success: function (response) {
                            handleResponseMessages( 'success',  '', response.message, {
                            confirmText: 'OK',
                            onConfirm: function () {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        alert("An error occurred while de activating the trainer.");
                    }
                });
        },
        onCancel: () => { console.log('Cancelled!'); }
    });

});

$(document).on('click', '.remove-trainer-btn', function () {

    let action = 'Remove';
    const trainerId = $(this).data('trainer-id');
    let route = "{{ route('remove.trainer') }}";
    let message = `Do you want to remove this trainer from the school?`;
    TakeAction(action, message, trainerId, route);

});
function TakeAction(action, message, trainerId, route ){

    handleResponseMessages( 'warning',  'Are you sure?', `${message}`, {
        showCancel: true,
        confirmText: `Yes, ${action} it!`,
        cancelText: 'Cancel',
        onConfirm: () => { 
            $.ajax({
                url: route,
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    trainer_id: trainerId
                },
                success: function (response) {
                    handleResponseMessages( 'success',  '', response.message, {
                        confirmText: 'OK',
                        onConfirm: () => { location.reload(); }
                    });
                },

                error: function (xhr) {
                    handleResponseMessages( 'error',  '', 'An error occurred while mapping the trainer.');
                }
            });
        },
        onCancel: () => { console.log('Cancelled!'); }
    });
}

</script>

@endsection