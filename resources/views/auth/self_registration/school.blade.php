@extends('layouts.filldart-login-app-header')
@section('content')

<div class="container">
    <div class="t-mrg my-5">
         <div class="cred-bx text-center">
            <img src="{{ asset('resources/images/gofor-fit-logo.png') }}" height="40" class="d-inline-block align-top mb-2" alt="">
        </div>
        <div class="row">
            <div class="col-12">               
                <div class="all-chaptr-cards filter-bx mt-4">

                    <form id="schoolSelfRegistrationForm" method="post" action="{{ route('register.school') }}">
                        @csrf
                        <h5 class="card-title text-center mb-0">{{ $title }}</h5>

                        <!-- Section: School Details -->
                        <div class="row mt-1">
                            <div class="col-12">
                                <h5 class="mt-0">School Details</h5>
                            </div>
                        </div>

                        <!-- Join Type -->
                        <div class="form-row ml-0">
                            <label>Wants to Join through</label>
                            <div class="form-group col-sm-4 col-md-auto">
                                <div class="form-check pl-2 mx-4">
                                    <input class="form-check-input" type="radio" name="joinType" id="joinByBoard" value="Board" checked required>
                                    <label class="form-check-label" for="joinByBoard"> Board </label>
                                </div>
                            </div>
                            <div class="form-group col-sm-4 col-md-auto my-0">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="joinType" id="joinByChain" value="SchoolChain" >
                                    <label class="form-check-label" for="joinByChain"> School Chain </label>
                                </div>
                            </div>
                        </div>

                        <!-- Board and Code -->
                        <div class="form-row">
                            <!-- Board Dropdown -->

                            {{--
                            <div class="form-group col-md-6" id="boardContainer">
                                <label for="selectBoard">Select Board *</label>
                                <select id="selectBoard" name="board" class="form-control @error('board') is-invalid @enderror">
                                    <option selected>Select...</option>
                                    @foreach($board_list as $board)
                                        <option value="{{ $board->id }}" {{ old('board') == $board->id ? 'selected' : '' }}>{{ $board->boardname }}</option>
                                    @endforeach
                                </select>
                                @error('board')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            --}}


                            <div class="form-group col-md-6" id="boardContainer">
                                <label for="selectBoard">Select Board <span class="text-danger">*</span></label>
                                <select id="selectBoard" name="board" class="form-control @error('board') is-invalid @enderror">
                                    <option value="">Select...</option> <!-- Empty value triggers validation -->
                                    @foreach($board_list as $board)
                                        <option value="{{ $board->id }}" {{ old('board') == $board->id ? 'selected' : '' }}>
                                            {{ $board->boardname }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('board')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                           

                            <!-- Chain Dropdown (Initially hidden) -->
                            <div class="form-group col-md-6 d-none" id="chainContainer">
                                <label for="selectByChain">Select School Chain <span class="text-danger">*</span></label>
                                <select id="selectByChain" name="schoolChain" class="form-control">
                                    <option selected>Select...</option>
                                    <option value="DAV">DAV</option>
                                    <option value="DelhiPublicSchool">Delhi Public School</option>
                                </select>
                            </div>

                
                            <!-- School Type -->
                            <div class="form-group col-md-3">
                                <label for="ddlSchoolType">School Type *</label>
                                <select name="ddlSchoolType" id="ddlSchoolType" class="form-control @error('ddlSchoolType') is-invalid @enderror">
                                    <option value="0">Select</option>
                                    <option value="1" {{ old('ddlSchoolType') == '1' ? 'selected' : '' }}>Indian School</option>
                                    <option value="2" {{ old('ddlSchoolType') == '2' ? 'selected' : '' }}>Foreign School</option>
                                </select>
                                @error('ddlSchoolType')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="form-group col-md-3">
                                <label for="schoolCode" data-toggle="tooltip" title="U-DISE code stands for UNIFIED DISTRICT INFORMATION SYSTEM for EDUCATION"> School Code ( U-DISE Code ) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('schoolCode') is-invalid @enderror" id="schoolCode"  name="schoolCode" value="{{ old('schoolCode') }}" placeholder="Enter U-DISE code">

                                @error('schoolCode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- Zone, Region, State, City -->
                        <div class="form-row">
                            
                            <!-- <div class="form-group col-md-3">
                                <label for="selectZone">Zone *</label>
                                <select id="selectZone" name="zone" class="form-control">
                                    <option selected>Choose...</option>
                                    <option>...</option>
                                </select>
                            </div> --> 

                            <div class="form-group col-md-3">
                                <label for="selectRegion">Region <span class="text-danger">*</span></label>
                                <select id="selectRegion" name="region" class="form-control @error('region') is-invalid @enderror">
                                    <option value="">Select...</option>
                                    @foreach($regions as $list)
                                        <option value="{{ $list->id }}" {{ old('region') == $list->id ? 'selected' : '' }}>
                                            {{ $list->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('region')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="selectState">State <span class="text-danger">*</span></label>
                                <select id="selectState" name="state" class="form-control @error('state') is-invalid @enderror">
                                    <option value="">Choose...</option>
                                    @foreach($states as $list)
                                        <option value="{{ $list->id }}" {{ old('state') == $list->id ? 'selected' : '' }}>
                                            {{ $list->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="form-group col-sm-6 col-md-6 col-lg-3">
                                <label for="selectDistrict">District <span class="text-danger">*</span></label>
                                <select id="selectDistrict" name="district" class="form-control @error('district') is-invalid @enderror">
                                    <option value="">Select</option>
                                    @if(old('district'))
                                        <option value="{{ old('district') }}" selected>{{ old('district_name') ?? 'Previously selected' }}</option>
                                    @endif
                                    <!-- Dynamic district options should be appended via JavaScript/AJAX -->
                                </select>
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="form-group col-sm-6 col-md-6 col-lg-3">
                                <label for="selectCity">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" placeholder="" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <!-- School Name & Shift -->
                        <div class="form-row">
                
                            <!-- School Name -->
                            <div class="form-group col-md-9">
                                <label for="schoolName">School Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('schoolName') is-invalid @enderror" id="schoolName" name="schoolName" value="{{ old('schoolName') }}">
                                @error('schoolName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="schoolShift">Shift <span class="text-danger">*</span></label>
                                <select name="schoolShift" id="schoolShift" class="form-control @error('schoolShift') is-invalid @enderror">
                                    <option value="">Select</option> {{-- Use empty string to trigger required validation --}}
                                    <option value="1" {{ old('schoolShift') == '1' ? 'selected' : '' }}>Morning</option>
                                    <option value="2" {{ old('schoolShift') == '2' ? 'selected' : '' }}>Evening</option>
                                </select>

                                @error('schoolShift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                        </div>

                        <!-- Email & Mobile -->
                        <div class="form-row">
                          
                            <!-- Email -->
                            <div class="form-group col-md-9">
                                <label for="schoolEmail">Email *</label>
                                <input type="schoolEmail" class="form-control @error('schoolEmail') is-invalid @enderror" id="schoolEmail" name="schoolEmail" value="{{ old('schoolEmail') }}">
                                @error('schoolEmail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-3">
                                <label for="schoolPhone">School Mobile Number *</label>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="schoolPhone" name="mobile" value="{{ old('mobile') }}">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                           
                        </div>

                        <!-- Address -->


                        <div class="form-group ">
                            <label for="schoolAddress">School Address <span style="color: red; font-weight: bold;" >*</span></label>
                            <textarea id="schoolAddress" name="address" rows="2" class="form-control  @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <!-- Website -->
                        <div class="form-group">
                            <label for="schoolWebsite">School Website Address </label>
                            <input type="schoolWebsite" class="form-control " id="schoolWebsite" name="website" value="{{ old('website') }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="schoolDescription">School Description</label>
                            <textarea id="schoolDescription" name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <!-- Section: School Admin Details -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="mt-4">School Admin Details</h5>
                            </div>
                        </div>

                        <!-- Principal, Designation, Gender -->
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="principalName">HM/Principal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('principalName') is-invalid @enderror" id="principalName" name="principalName" value="{{ old('principalName') }}">
                                @error('principalName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label for="principalEmail">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('principalEmail') is-invalid @enderror" id="principalEmail" 
                                    name="principalEmail" placeholder="sc_bosh@gmail.com" value="{{ old('principalEmail') }}">
                                @error('principalEmail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-2">
                                <label for="designation">Designation <span class="text-danger">*</span></label>
                                <select name="schoolAdminDesignation" id="schoolAdminDesignation" class="form-control @error('schoolAdminDesignation') is-invalid @enderror">
                                    <option value="2">Select</option>
                                    <option value="0" {{ old('schoolAdminDesignation') == '0' ? 'selected' : '' }} >HM</option>
                                    <option value="1" {{ old('schoolAdminDesignation') == '1' ? 'selected' : '' }}>Principal</option>
                                </select>
                                @error('schoolAdminDesignation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-2">
                                <label for="gender">Gender <span class="text-danger">*</span></label>
                                <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                                    <option value="3">Select</option>

                                    <option value="0" {{ old('gender') == '0' ? 'selected' : '' }} >Male</option>
                                    <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>Female</option>
                                    <option value="2" {{ old('gender') == '0' ? 'selected' : '' }} >TransGender</option>

                    
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Consent -->

                        <div class=" mb-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="marketingConsent" name="marketingConsent">
                                <label class="form-check-label @error('marketingConsent') is-invalid @enderror" for="marketingConsent">
                                    I agree to receive newsletters, or marketing and promotional content. <span class="text-danger">*</span>
                                </label>
                                 @error('marketingConsent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="d-flex justify-content-end">
                            <a type="button" class="btn btn-sm btn-secondary mr-2" href="{{ route('login') }}"><span>Cancel</span></a>
                            <button type="submit" class="btn btn-sm btn-primary"><span>Submit</span></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const joinByBoard = document.getElementById('joinByBoard');
        const joinByChain = document.getElementById('joinByChain');

        const boardContainer = document.getElementById('boardContainer');
        const chainContainer = document.getElementById('chainContainer');

        function toggleDropdowns() {
            if (joinByBoard.checked) {
                boardContainer.classList.remove('d-none');
                chainContainer.classList.add('d-none');
            } else if (joinByChain.checked) {
                boardContainer.classList.add('d-none');
                chainContainer.classList.remove('d-none');
            }
        }

        joinByBoard.addEventListener('change', toggleDropdowns);
        joinByChain.addEventListener('change', toggleDropdowns);
    });



    $(document).ready(function() {
        $('#registrationSuccessModal').modal('show');

        var oldState = '{{ old("state") }}';
        var oldDistrict = '{{ old("district") }}';

        if (oldState) {
            $('#selectState').val(oldState); 

            $.ajax({
                type: 'POST',
                url: "{{ route('getDistrict') }}",
                data: {
                    stateId: oldState,
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    $('#selectDistrict').empty().append('<option>Select</option>');
                    
                    $.each(response, function (key, value) {
                        $('#selectDistrict').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });

                    if (oldDistrict) {
                        $('#selectDistrict').val(oldDistrict);
                    }
                },

            });
        }

        $('#selectState').on('change', function() {
            const stateId = $(this).val();
            const selectDistrict = $('#selectDistrict');
            selectDistrict.empty().append('<option value="">Select District</option>');

            if (stateId) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('getDistrict') }}",
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
    });
</script>



@endsection