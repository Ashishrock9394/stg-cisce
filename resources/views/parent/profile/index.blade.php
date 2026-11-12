@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')
<style>
    .preview-img {
        max-width: 100px;
        height: 100px;
        border-radius: 50%;
    }
</style>

<div class="all-chaptr-cards">
    <div class="container">
        <div class="t-mrg2 mb-5 pb-5">
            <div class="row">
                <div class="col-12">
                    <div class="heading-rw mt-0 mt-md-1 mb-0 p-0">
                        <a href="javascript:history.back()" class="back-button">
                            <span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
                                </svg>
                            </span>
                        </a>
                        <h1 class="ml-md-4 mb-0">{{$title}}</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">               
                    <div class="all-chaptr-cards1 filter-bx1 from__bx mt-4">

                        <form id="updateStudentProfile" method="post" action="" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <!-- School Code (readonly) -->
                            <div class="form-row">
                                
                                <div class="form-group col-md-6">
                                    <label for="student_name">Your Name </label>
                                    <input type="text" class="form-control @error('student_name') is-invalid @enderror" 
                                        id="student_name" name="student_name" value="{{$student->student_name}}" readonly>
                                    @error('student_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="dob">Date of Birth </label>
                                    <input type="text" class="form-control @error('dob') is-invalid @enderror" 
                                        id="dob" name="dob" value="{{$student->dob}}" readonly>
                                    @error('dob')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="student_uid">Admission Number </label>
                                    <input type="text" class="form-control @error('student_uid') is-invalid @enderror" 
                                        id="student_uid" name="student_uid" value="{{$student->student_uid}}" readonly>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="class">Class </label>
                                    <input type="text" class="form-control @error('class') is-invalid @enderror" 
                                        id="class" name="class" value="Class - {{$student->class_id}}" readonly>
                                    @error('class')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="section">Section </label>
                                    <input type="text" class="form-control @error('section') is-invalid @enderror" 
                                        id="section" name="section" value="{{$student->section_id}}" readonly>
                                    @error('section')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="rollno">Roll Number </label>
                                    <input type="text" class="form-control @error('rollno') is-invalid @enderror" 
                                        id="rollno" name="rollno" value="{{$student->rollno}}" readonly>
                                    @error('rollno')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="studentEmail">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('studentEmail') is-invalid @enderror"  id="studentEmail" name="studentEmail" value="{{$student->email_id}}">
                                    @error('studentEmail') <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="mobile">Mobile <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{$student->mobile}}">
                                    @error('mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> 
                                <div class="form-group col-md-2">
                                    <label for="profilePicture">Profile Picture <span class="text-danger">*</span></label>
                                    {{-- Upload new image --}}
                                    <input type="file" class="form-control @error('profilePicture') is-invalid @enderror" id="profilePicture" name="profilePicture" type="file" accept=".jpg,.jpeg,.png" onchange="previewImage(event)">
                                    @error('profilePicture')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-2" style="text-align:center">
                                @if(!empty($student->profile_picture))
                                    <img src="{{ asset('assets/uploads/profilePictures/student/' .$student->profile_picture) }}" id="imagePreview" class="preview-img img-thumbnail">
                                @else .
                                    <img id="imagePreview" class="preview-img img-thumbnail d-none" />
                                @endif
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                                        <option value="3">Select</option>
                                        <option value="Male" {{$student->gender == 'Male' ? 'selected' : '' }}>Male</option>
										<option value="Female" {{$student->gender == 'Female' ? 'selected' : '' }}>Female</option>
										<option value="TransGender" {{ $student->gender == 'TransGender' ? 'selected' : '' }}>TransGender</option>
                                        </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="apaarId"> APAAR ID (12 digits) </label>
                                    <input type="text" name="apaarId" id="apaarId" class="form-control @error('apaarId') is-invalid @enderror" value="{{$student->apaarId}}">
                                    @error('apaarId')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="domicile"> Domicile </label>
                                    <input type="text" name="domicile" id="domicile" class="form-control @error('domicile') is-invalid @enderror" value="{{$student->domicile}}">
                                    @error('domicile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="hobbies">Hobbies</label>
                                    <input type="text" name="hobbies" id="hobbies" value="{{$student->hobbies}}" class="form-control @error('hobbies') is-invalid @enderror" placeholder="Separated by commas">
                                     @error('hobbies')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                </div>
                                
                            </div>

                            <!-- Submit Buttons -->
                                <div class="d-flex justify-content-end mt-5">
                                    <a type="button" class="btn btn-secondary mr-2" href="{{ route('student.dashboard')}}">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const input = event.target;
        if(event.target.id == 'profilePicture'){
            var preview = document.getElementById('imagePreview');
        }
        if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
        }
    }
	$(document).ready(function () {
		$('#updateStudentProfile').on('submit', function (e) {
			e.preventDefault();
			let form = $(this);
            let formData = new FormData(form[0]);
            formData.append('_method', 'PUT');   
			submitLoader();
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: "{{route('profile.update')}}",
                type: 'POST',
                data: formData,
                processData: false, 
                contentType: false,
                beforeSend: function () {
                    form.find("button[type='submit']").prop("disabled", true).text("Updating...");
                },
                success: function (response) {
                    Swal.close();
                    Swal.fire({
                        icon: "success",
                        title: "Profile Updated!",
                        text: response.message ?? "Your profile has been updated successfully."
                    }).then(() => {
                        location.reload();
                    });;
                },
                error: function (xhr) {
                    Swal.close();
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = Object.values(errors).flat().join("\n");
                        Swal.fire("Profile Update Fail", errorMessages, "warning");
                    } else {
                        Swal.fire("Error", "Something went wrong!", "error");
                    }
                },
                complete: function () {
                    form.find("button[type='submit']").prop("disabled", false).text("Update Profile");
                }
            });
		});
	});

</script>
@endsection