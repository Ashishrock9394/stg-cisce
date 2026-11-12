@extends('layouts.filldart-app')
@section('title', $title)

@section('content')

<div class="container">
    <div class="t-mrg2 mb-5 pb-5">





        {{-- Header --}}
        <div class="all-chaptr-cards mb-4" style="margin: 0;">
            <div class="row">
                <x:back-button title="{{ $title }}">
                    <x-slot name="actionsOutside">
				        <div >
				        	
				        	<button class="btn btn-sm btn-primary" onclick="promotionStatus()">Promotion History</button>
				        	@include('modals.class-promotion-status')
				        </div>
				    </x-slot>
                </x:back-button>
            </div>
        </div>



        <div class="alert alert-info d-flex align-items-start gap-2 mt-2" role="alert" style="border-left: 4px solid #0d6efd;">  
            <i class="bi bi-info-circle-fill text-primary"></i>
            <div>
            <strong>&nbsp;&nbsp;Promotion Instructions (Session 2026-2027):</strong>  
                <ul class="mb-0 mt-1">
                    <li>This module allows you to <strong>promote all students of a class to the next higher class</strong> in a single action.</li>
                    <li>Each class will be promoted to its <strong>next consecutive class</strong> (e.g., Class 11 → Class 12).</li>
                    <li>Please <strong>review the student count carefully</strong> before proceeding with the promotion.</li>
                    
                    <li>After promotion, students will be moved to the next class while <strong>retaining their current section</strong>. If required, sections can be updated manually from the <strong>Manage Students</strong> module.</li>
                    <li><strong>Class 12 Students:</strong> Upon promotion, all Class 12 students will be moved to the <strong>Transfer List</strong>.</li>
                    <li><strong>This action cannot be undone or reverted.</strong> Ensure all data is correct before continuing.</li>
                </ul>
            </div>
        </div>



        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="studentsPromotionTable" class="table table-bordered table-hover">
                        <thead class="thead-primary">
                            <tr>
                                <th>Current Class</th>
                                <th>Promotion Class</th>
                                <th>Number of Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($classList->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">No classes available to promote</td>
                                </tr>
                            @else
                            @foreach($classList as $class)
                            <tr data-id="{{ $class->custom_class_id }}">
                                <td>{{ $class->classname }}-{{ $class->section }}</td>
                                <td>
                                    @if($class->id == 12)
                                        Transfer
                                    @else
                                        Class {{ $class->id + 1 }} - {{ $class->section }}
                                    @endif
                                </td>
                                <td>{{$class->student_count}}</td>
                            </tr>
                            @endforeach
                            @endif
                            
                        </tbody>
                    </table>
                </div>
                <div class="text-right mt-4">
                    <button id="promoteStudents" class="btn btn-primary px-5">
                        Promote
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).on('click', '#promoteStudents', function () {
        let classStudentMapping = [];
        let totalStudents = 0;

        $('#studentsPromotionTable tbody tr').each(function () {
            let customClassId = $(this).data('id');
            let studentCount = parseInt($(this).find('td:eq(2)').text()) || 0;

            if (customClassId) {
                classStudentMapping.push({
                    custom_class_id: customClassId,
                    student_count: studentCount
                });
            }

            totalStudents += studentCount;
        });

        if (totalStudents === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'No students found for promotion'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            html: `
                <p><strong>${totalStudents}</strong> students will be promoted.</p>
                <p style="font-size: 14px;">
                    <strong>Please note:</strong> Section and roll numbers will remain unchanged.
                    If you need to update them, go to <b>Manage Students</b> and make the required changes.
                </p>
            `,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Yes, Promote',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            reverseButtons: true,
            allowOutsideClick: false,
        }).then((result) => {

            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: "{{ route('promote-student') }}",
                type: "POST",
                data: {
                    class_data: classStudentMapping,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function () {
                    $('#promoteStudents')
                        .text('Promoting...')
                        .prop('disabled', true);
                },
                success: function (response) {
                    Swal.fire({
                        icon: response.status ? 'success' : 'info',
                        title: response.status ? 'Success' : 'Notice',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    let errorMessage = 'Something went wrong';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                },
                complete: function () {
                    $('#promoteStudents')
                        .text('Promote')
                        .prop('disabled', false);
                }
            });

        });

    });

    function promotionStatus() {
        document.getElementById('promotionHistoryContent').innerHTML =
            '<div class="text-center text-muted">Loading...</div>';

        $('#promotionHistoryModal').modal('show');

        fetch("{{ route('fetch.promotion.status') }}")
            .then(res => res.json())
            .then(data => {
                document.getElementById('promotionHistoryContent').innerHTML = data.html;
            })
            .catch(() => {
                document.getElementById('promotionHistoryContent').innerHTML =
                    '<p class="text-danger text-center">Failed to load data</p>';
            });
    }
</script>
@endsection

