@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')


<style>
	.preview-img {
		max-width: 100px;
		height: auto;
	}
  	.hide{
		pointer-events: none;   
		opacity: 0.6;    
		cursor: not-allowed;
	}
</style>

<div class="container">
	<div class="t-mrg">	
		<div class="row">
			<div class="col">
				@if(Auth::user()->role_id == '4')
					<a href="{{route('filldart.dashboard')}}" class="back-button">
					<span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
						</svg></span> 
					</a>
					@elseif(Auth::user()->role_id == '9')
					<a href="{{route('cicse.schoollist')}}" class="back-button">
					<span class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5" />
						</svg></span> 
					</a>
				@endif
				<div class="heading-rw mt-0 mb-0 pt-0">
					<h1>{{$title}}</h1>
				</div>


			</div>

			<div class="col">
				@if(session('success'))
					<script>
						Swal.fire({
							  title: `{{ session('success') }}`,
							  icon: "success",
							  draggable: true
							});
					</script>
				@endif

				@if ($errors->any())
				    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-alert">
				        <ul>
				            @foreach ($errors->all() as $error)
				                <li>{{ $error }}</li>
				            @endforeach
				        </ul>
				        <button type="button" class="close" data-dismiss="alert">&times;</button>
				    </div>
				@endif
			</div>
		</div>
		@php
			$readonly = Auth::user()->role_id != 9 ? 'readonly' : '';
			$disabled = Auth::user()->role_id != 9 ? 'disabled' : '';
		@endphp
			
		
        <div class="row">
            <div class="col-12">               
                <div class="all-chaptr-cards filter-bx mt-4">

                    <form id="schoolSelfRegistrationForm" method="post" action="{{ 
								auth()->user()->role_id == 4 
								? route('school.profile.update', $schoolData->id)
								: route('cisce.school.profile.update', $schoolData->id)}}"  enctype="multipart/form-data">
					    @csrf
					    @method('PUT')
					    
					    <h5 class="card-title text-center mb-0">School Details</h5> 
					    <!-- School Code (readonly) -->
						<div class="form-row">
							<div class="form-group col-md-3">
								<label for="schoolCode">School Code (U-DISE Code) <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="schoolCode" name="school_code"
									value="{{ $schoolData->school_code }}" {{ $readonly }}>
							</div>
						</div>

					    <!-- Region, State, District, City -->
						
						<!-- Region -->
						<div class="form-row">
							<div class="form-group col-md-3">
								<label for="selectRegion">Region <span class="text-danger">*</span></label>
								<select id="selectRegion" name="region" class="form-control @error('region') is-invalid @enderror" {{ $disabled }}>
									<option value="">Select...</option>
									@foreach($regions as $region)
										<option value="{{ $region->name }}|{{ $region->id }}"
											{{ (old('region') ?? $schoolData->region) == $region->name ? 'selected' : '' }}>
											{{ $region->name }}
										</option>
									@endforeach
								</select>
								@error('region') <div class="invalid-feedback">{{ $message }}</div> @enderror
							</div>

							<div class="form-group col-md-3">
								<label for="selectZone">Zone <span style="color: red; font-weight: bold;">*</span></label>
								<select id="selectZone" name="zonename" class="form-control @error('zonename') is-invalid @enderror" {{ $disabled }}>
									<option value="" {{ (old('zonename') ?? $schoolData->zonename ?? '') == '' ? 'selected' : '' }}>Select</option>
									@foreach($zones as $zone)
										<option value="{{ $zone->name ?? ''}}"
											{{ (old('zonename') ?? $schoolData->zonename) == $zone->name ? 'selected' : '' }}>
											{{ $zone->name }}
										</option>
									@endforeach
								</select>
								@error('zonename')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="form-group col-md-3">
								<label for="selectState">State <span style="color: red; font-weight: bold;" >*</span></label>
								<select id="selectState" name="state" class="form-control @error('state') is-invalid @enderror" {{ $disabled }}>
									<option value="" {{ old('state', $schoolData->state_id) ? '' : 'selected' }}>Select</option>
									@foreach($states as $state)
										<option value="{{ $state->id ?? '' }}|{{ $state->name }}"
											{{ (old('state') ?? $schoolData->state) == $state->name ? 'selected' : '' }}>
											{{ $state->name }}
										</option>
									@endforeach
								</select>

								@error('state')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<div class="form-group col-md-3">
								<label for="selectDistrict">District <span style="color: red; font-weight: bold;">*</span></label>
								<select id="selectDistrict" name="district" class="form-control @error('district') is-invalid @enderror" {{ $disabled }}>
									<option value="" {{ (old('district') ?? $schoolData->district ?? '') == '' ? 'selected' : '' }}>Select</option>
									@foreach($districts as $district)
										<option value="{{ $district->id ?? ''}}"
											{{ (old('district') ?? $schoolData->district) == $district->name ? 'selected' : '' }}>
											{{ $district->name }}
										</option>
									@endforeach
								</select>
								@error('district')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>

							<!-- City -->
							<div class="form-group col-md-3">
								<label for="city">City </label>
								<input type="text" class="form-control @error('city') is-invalid @enderror"
									id="city" name="city" value="{{ old('city', $schoolData->city) }}" {{ $readonly }}>
								@error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
							</div>
							<!-- Address -->
							<div class="form-group col-md-6">
								<label for="address">Address </label>
								<input type="text" class="form-control @error('address') is-invalid @enderror"
									id="address" name="address" value="{{ old('address', $schoolData->address) }}" {{ $readonly }}>
								@error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
							</div>
							<div class="form-group col-md-3">
								<label for="pincode">Pincode </label>
								<input type="text" class="form-control @error('pincode') is-invalid @enderror"
									id="pincode" name="pincode" value="{{ old('pincode', $schoolData->pincode) }}" {{ $readonly }}>
								@error('pincode') <div class="invalid-feedback">{{ $message }}</div> @enderror
							</div>
						</div>

					    <!-- School Name & Shift -->
					    <div class="form-row">
					        <div class="form-group col-md-9">
					            <label for="schoolName">School Name <span class="text-danger">*</span></label>
					            <input type="text" class="form-control @error('schoolName') is-invalid @enderror" 
					                   id="schoolName" name="school_name" value="{{ old('school_name', $schoolData->school_name) }}" {{ $readonly }}>
					            @error('schoolName')
					                <div class="invalid-feedback">{{ $message }}</div>
					            @enderror
					        </div>

					        <div class="form-group col-md-3">
					            <label for="schoolShift">Shift <span class="text-danger">*</span></label>
					            <select name="school_shift" id="schoolShift" class="form-control @error('schoolShift') is-invalid @enderror">
					                <option value="">Select</option>
					                <option value="Day" {{ (old('schoolShift') ?? $schoolData->shift ?? '') == 'Day' ? 'selected' : '' }}>Morning</option>
					                <option value="Evening" {{ (old('schoolShift') ?? $schoolData->shift ?? '') == 'Evening' ? 'selected' : '' }}>Evening</option>
					            </select>
					            @error('schoolShift')
					                <div class="invalid-feedback">{{ $message }}</div>
					            @enderror
					        </div>
					    </div>

					    <!-- Email & Mobile -->
					    <div class="form-row">
					        <div class="form-group col-md-4">
					            <label for="headEmail">Head Email <span class="text-danger">*</span></label>
					            <input type="email" class="form-control @error('headEmail') is-invalid @enderror" id="headEmail" name="head_email" value="{{ old('headEmail') ?? $schoolData->head_email ?? '' }}" required>
					            @error('school_email')
					                <div class="invalid-feedback">{{ $message }}</div>
					            @enderror
					        </div>

							<div class="form-group col-md-4">
					            <label for="schoolEmail">School Email <span class="text-danger">*</span></label>
					            <input type="email" class="form-control @error('schoolEmail') is-invalid @enderror" id="schoolEmail" name="school_email" value="{{ old('schoolEmail') ?? $schoolData->s_email ?? '' }}" required>
					            @error('school_email')
					                <div class="invalid-feedback">{{ $message }}</div>
					            @enderror
					        </div>

					        <div class="form-group col-md-4">
					            <label for="schoolPhone">School Contact Number </label>
					            <input type="text" class="form-control @error('mobile') is-invalid @enderror"  id="schoolPhone" name="school_phone" value="{{ old('school_phone') ?? $schoolData->s_contact ?? '' }}">
					            @error('school_phone')
					                <div class="invalid-feedback">{{ $message }}</div>
					            @enderror
					        </div>
					    </div>

					    <!-- Website & Logo -->
					    <div class="form-group">
					        <div class="row align-items-end">
					            <div class="col-md-6">
					                <label for="schoolWebsite">School Website Address</label>
					                <input type="text" class="form-control @error('website') is-invalid @enderror" id="schoolWebsite" name="school_url" value="{{ old('website') ?? $schoolData->school_url ?? '' }}">
					                @error('school_url') 
					                    <div class="invalid-feedback">{{ $message }}</div> 
					                @enderror
					            </div>

					            <div class="col-md-4">
									<label for="imageUpload">Upload Logo</label>
									<input class="form-control @error('school_logo') is-invalid @enderror" type="file" accept=".jpg,.jpeg,.png" id="imageUpload" name="school_logo" onchange="previewImage(event); validateFile(this, 1024);">
									<span class="img-error text-danger"></span>
									@error('school_logo')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>


								<div class="col-md-2">
									@if($schoolData->logo)
										<img src="{{ asset('assets/uploads/logos/' . $schoolData->logo) }}" id="imagePreview" class="preview-img img-thumbnail" />
									@else .
										<img id="imagePreview" class="preview-img img-thumbnail d-none" />
									@endif
								</div>					           
					        </div>
					    </div>

					    <!-- Address -->
					    <div class="form-group">
					        <label for="schoolAddress">School Address <span class="text-danger">*</span></label>
					        <textarea id="schoolAddress" name="school_address" rows="2"  class="form-control @error('school_address') is-invalid @enderror">{{ old('school_address') ?? $schoolData->address ?? 'N.A.' }}</textarea>
					        @error('school_address')
					            <div class="invalid-feedback">{{ $message }}</div>
					        @enderror
					    </div>

				
					 
					    {{-- School Admin / Principal Contact & Signature --}}

					    <h5 class="mt-4">School Admin Details</h5>

					    <div class="form-row">
							<div class="form-group col-md-3">
								<label for="principalName">HM/Principal <span class="text-danger">*</span></label>
								<input type="text" class="form-control @error('principalName') is-invalid @enderror" 
									id="principalName" name="principalName" value="{{ old('principalName') ?? $schoolData->p_name ?? '' }}">
								@error('principalName')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror	
							</div>

							<div class="form-group col-md-3">
								<label for="principalEmail">Email <span class="text-danger">*</span></label>
								<input type="email" class="form-control @error('principalEmail') is-invalid @enderror"  id="principalEmail" name="principalEmail" value="{{ old('principalEmail') ?? $schoolData->p_email ?? '' }}" required>
								@error('principalEmail') <div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
							<div class="col-md-3">
								<label for="principalContact">Principal Contact Number</label>
								<input type="text" class="form-control @error('principal_contact') is-invalid @enderror"  id="principalContact" name="principal_contact"    value="{{ old('p_contact') ?? $schoolData->p_contact ?? '' }} ">
								@error('principal_contact') 
									<div class="invalid-feedback">{{ $message }}</div> 
								@enderror
							</div>
							<div class="form-group col-md-3">
								<label for="gender">Gender <span class="text-danger">*</span></label>
								<select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
									<option value="3">Select</option>
									<option value="Male" {{$schoolData->p_gender == 'Male' ? 'selected' : '' }}>Male</option>
									<option value="Female" {{$schoolData->p_gender == 'Female' ? 'selected' : '' }}>Female</option>
									<option value="TransGender" {{ $schoolData->p_gender == 'TransGender' ? 'selected' : '' }}>TransGender</option>
								</select>
								@error('gender')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="form-group">
							<div class="row align-items-end">
								
								<div class="form-group col-md-2">
									<label for="schoolAdminDesignation">Designation <span class="text-danger">*</span></label>
									<select name="schoolAdminDesignation" id="schoolAdminDesignation" 
											class="form-control @error('schoolAdminDesignation') is-invalid @enderror">
										<option value="0">Select</option>
										<option value="HM" {{ $schoolData->p_designation == 'HM' ? 'selected' : '' }}>HM</option>
										<option value="Principal" {{ $schoolData->p_designation == 'Principal' ? 'selected' : '' }}>Principal</option>
									</select>
									@error('schoolAdminDesignation')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>								

								<div class="form-group col-md-3">
									<label for="profilePicture">Profile Picture</label>
									<input class="form-control @error('profile_picture') is-invalid @enderror" type="file" accept=".jpg,.jpeg,.png" id="profilePicture" name="profilePicture" onchange="previewImage(event)">
									@error('profile_picture')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								<div class="form-group col-md-2" style="text-align:center">
									@if($schoolData->profile_picture)
										<img src="{{ asset('assets/uploads/profilePictures/users/' . $schoolData->profile_picture) }}" id="profilePicturePreview" class="preview-img img-thumbnail" />
									@else .
										<img id="profilePicturePreview" class="preview-img img-thumbnail d-none" />
									@endif
								</div>

								<div class="form-group col-md-3">
									<label for="principalSign">Upload Signature</label>
									<input class="form-control @error('principal_signature') is-invalid @enderror" type="file" accept=".jpg,.jpeg,.png" id="principalSign" name="principal_signature" onchange="previewImage(event)">
									@error('principal_signature')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								<div class="form-group col-md-2" style="text-align:center">
									@if($schoolData->signature)
										<img src="{{ asset('assets/uploads/signatures/' . $schoolData->signature) }}" id="signaturePreview" class="preview-img img-thumbnail" />
									@else .
										<img id="signaturePreview" class="preview-img img-thumbnail d-none" />
									@endif
								</div>
							</div>
						</div>
						
						<h5 class="mt-4">P.E. Head Details</h5>
						<div class="form-group">
							<div class="row align-items-end">
								<div class="col-md-2">
									<label for="hodName">HOD Name <span class="text-danger">*</span></label>
									<input type="text" class="form-control @error('hodName') is-invalid @enderror" 
										id="hodName" name="hodName" value="{{ $hod['name'] ?? '' }}">
									@error('hodName')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror	
								</div>

								<div class="col-md-3">
									<label for="hodEmail">Email </label>
									<input type="email" class="form-control @error('hodEmail') is-invalid @enderror"
										id="hodEmail" name="hodEmail" value="{{ $hod['email'] ?? '' }}">
									@error('hodEmail')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								<div class="col-md-2">
									<label for="hodContact">HOD Contact</label>
									<input type="text" class="form-control @error('hodContact') is-invalid @enderror"
										id="hodContact" name="hodContact" value="{{ $hod['contact'] ?? '' }}">
									@error('hodContact')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								<div class="col-md-3">
									<label for="hodSignature">PE Signature</label>
									<input class="form-control @error('hod_signature') is-invalid @enderror"
										type="file" accept=".jpg,.jpeg,.png"
										id="hodSignature" name="hod_signature"
										onchange="previewImage(event); validateFile(this, 300);">
									<span class="img-error text-danger"></span>
									@error('hod_signature')
										<div class="invalid-feedback">{{ $message }}</div>
									@enderror
								</div>

								<div class="col-md-2 d-flex justify-content-center">
									@if($schoolData->hod_sign)
										<img src="{{ asset('assets/uploads/hod-signatures/' . $schoolData->hod_sign) }}"
											id="hodsignaturePreview"
											class="preview-img img-thumbnail"
											style="max-height: 80px;">
									@else
										<img id="hodsignaturePreview"
											class="preview-img img-thumbnail d-none"
											style="max-height: 80px;">
									@endif
								</div>

							</div>					
						</div>


					    <!-- Submit Buttons -->
					    <div class="d-flex justify-content-end mt-5">
							@if(Auth::user()->role_id == '4')
								<a type="button" class="btn btn-secondary mr-2" href="{{ route('filldart.dashboard') }}">Cancel</a>								
							@elseif(Auth::user()->role_id == '9')
								<a type="button" class="btn btn-secondary mr-2" href="{{route('cicse.schoollist')}}">Cancel</a>
							@endif
					        <button type="submit" id="submitBtn" class="btn btn-primary">Update Profile</button>
					    </div>
					</form>

                </div>
            </div>
        </div>
    </div>
</div>


<script>


  	function previewImage(event) {
		const input = event.target;
		if(event.target.id == 'imageUpload'){
			var preview = document.getElementById('imagePreview');
		}

		if(event.target.id == 'principalSign'){
			var preview = document.getElementById('signaturePreview');
		} 
		if(event.target.id == 'profilePicture'){
			var preview = document.getElementById('profilePicturePreview');		
		} 
		if(event.target.id == 'hodSignature'){
			var preview = document.getElementById('hodsignaturePreview')			
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

	$('#selectState').on('change', function() {
        const stateId = $(this).val();
		console.log(stateId);
        const selectDistrict = $('#selectDistrict');
        selectDistrict.empty().append('<option value="">Select District</option>');

        if (stateId) {
            $.ajax({
                type: 'POST',
                url: "{{ route('school.getDistrict') }}",
                data: {
                    stateId: stateId,
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',

                success: function(response) {
                    $.each(response, function(index, city) {
                        selectDistrict.append(
                            $('<option>', {
                                value: city.id,
                                text: city.name
                            })
                        );
                    });
                },

                error: function(xhr, status, error) {
                    console.error("AJAX Error:", xhr.responseText);
                    $('#message').text('Error loading districts!');
                }
            });
        }
    });

	$("#selectRegion").on("change", function () {
		let regionValue = $(this).val();
		const selectZone = $('#selectZone');
		selectZone.empty().append('<option value="">Select Zone</option>');
		if(regionValue != ''){
			let regionArr = regionValue.split('|');
			let regionId = regionArr[1];
			$.ajax({
				url: "{{ route('school.getZones') }}",
				type: "POST",
				data: {
					regionId: regionId,
					_token: "{{ csrf_token() }}"
				},
				dataType: 'json',
				success: function(response) {
					$.each(response, function(index, zones) {
						selectZone.append(
							$('<option>', {
								value: zones.name,
								text: zones.name
							})
						);
					});
				},
				error: function(xhr, status, error) {
					console.error("AJAX Error:", xhr.responseText);
				}
			});
		}
	});

$(document).ready(function () {
	$('#schoolPhone').on('input', function() {
        let phone = $(this).val().replace(/\D/g, '');
        if (phone.length != 10) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
            $(this).after('<div class="invalid-feedback">Contact number must be 10 digits</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
});
function validateFile(input, maxKB) {
    const file = input.files[0];
    const errorSpan = input.nextElementSibling;
	const submitBtn = document.getElementById('submitBtn');
    if (!errorSpan || !errorSpan.classList.contains('img-error')) return;

    errorSpan.textContent = '';

    if (file) {
        const sizeKB = file.size / 1024;
        if (sizeKB > maxKB) {
            errorSpan.textContent = `File size must not exceed ${maxKB} KB.`;
            input.value = '';
			submitBtn.classList.add("hide");
            return false;
        }else{
			submitBtn.classList.remove("hide");
		}
    }
    return true;
}
</script>



@endsection