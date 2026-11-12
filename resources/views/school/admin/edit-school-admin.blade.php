<form id="edit_viewer_details" method="POST" action="">
    @csrf               
    <input type="hidden" id="viewerId" name="viewerId">
    <div class="form-row">      
        <div class="form-group col-md-3">
            <label for="uid">Admin ID <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="text" class="form-control @error('uid') is-invalid @enderror" id="uid" name="uid" value="{{ old('uid') }}" readonly>
            @error('uid')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group col-md-3">
            <label for="trainerName">Name <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="text" class="form-control @error('trainerName') is-invalid @enderror" id="trainerName" name="trainerName" value="" placeholder="Your name" required>
            @error('trainerName')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-3">
            <label for="gender">Gender <span style="color: red; font-weight: bold;">*</span></label>
            <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Choose...</option>
                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Transgender" {{ old('gender') == 'Transgender' ? 'selected' : '' }}>Transgender</option>
            </select>

            @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group col-md-3">
            <label for="designation">Designation <span style="color: red; font-weight: bold;">*</span></label>
            <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation') }}" placeholder="Enter designation" maxlength="20" required>
           

            @error('designation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <!-- Qualification, Email, Mobile -->
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="qualification">Qualification <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="text" class="form-control @error('qualification') is-invalid @enderror" name="qualification" id="qualification" placeholder="Type here" value="{{ old('qualification') }}" required>
            @error('qualification')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-3">
            <label for="trainerEmail">Email <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="trainerEmail" name="email" placeholder="sc_bosh@gmail.com" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group col-md-3">
            <label for="number">Mobile <span style="color: red; font-weight: bold;">*</span></label>
            <input 
                type="text" 
                class="form-control @error('mobile') is-invalid @enderror" 
                id="number" 
                name="mobile" 
                placeholder="Enter 10 digits mobile number" 
                value="{{ old('mobile') }}" 
                pattern="\d{10}" 
                maxlength="10" 
                required
            >
            @error('mobile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group col-md-3">
            <label for="dob">DOB <span style="color: red; font-weight: bold;" >*</span></label>
            <input type="date" name="dob" id="dob" class="form-control @error('dob') is-invalid @enderror" 
                value="{{ old('dob') }}" 
                max="{{ date('Y-m-d', strtotime('-18 years')) }}" 
                required>
            @error('dob')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <!-- Module Access -->
    <div class="form-row">
        <div class="col-12">
            <h5 class="mb-3">Assign Module Access <span style="color: red; font-weight: bold;">*</span></h5>          
            <div class="row">
                @foreach($DashboardModule as $key => $label)
                    <div class="col-sm-6 col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" 
                            type="checkbox" 
                            name="module_access[]" 
                            id="roleId_{{ $label->role_id }}-{{ $key }}"
                            value="{{ $label->route_name }}" 
                            {{ (is_array(old('module_access')) && in_array($label->route_name, old('module_access'))) ? 'checked' : '' }} >
                            <label class="form-check-label" for="roleId_{{ $label->role_id }}-{{ $key }}"> &nbsp {{ $label->name }} </label>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('module_access')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror                                      
        </div>
    </div>

    <!-- Submit -->
    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary"><span>Submit</span></button>
    </div>
</form>


<script>
    $(document).ready(function() {
        $('#edit_viewer_details').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            let form = $(this);
            let checkedModules = form.find('input[name="module_access[]"]:checked');
                        
            if (checkedModules.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Module Selected',
                    text: 'Please select at least one module access before submitting.',
                    confirmButtonText: 'OK'
                });

                return;
            }

            $.ajax({
                url: "{{ route('update.admin.details') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success',
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    let errorMessage = '';

                    if (errors) {
                        for (const key in errors) {
                            errorMessage += errors[key].join(', ') + '\n';
                        }
                    } else {
                        errorMessage = xhr.responseText;
                    }

                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                    });
                }                
            });
        });
    });
</script>
