<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <input type="hidden" id="cwsn_type" value="{{ $cwsnType ?? 'None Specified' }}">
            
            <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close"
                style="left:20px; z-index:5; background:#fff;"></button>

            <div class="form-row my-2 p-5">
                
                <div class="col-12 col-md-4">
                    <div class="form mt-1 mt-md-3">
                        <label for="class_id" class="form-label">Select Class</label>
                        <div class="input-group mb-3">
                            <select name="class_id" id="class_id" class="form-control">
                            <option value="">-- Select Class --</option>

                                @foreach ($classes as $customcls)
                            <option value="{{ $customcls->id . '-' . $customcls->class_id.'-'.$customcls->section }}">
                                {{ $customcls->classname . '-' . $customcls->section}}
                            </option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-8">
                    <div class="form mt-1 mt-md-3">
                        <label for="student_id" class="form-label">Select Student</label>
                        <div class="input-group mb-3">
                            <select name="student_id" id="student_id" data-grade="speed" data-test-type="{{ $type }}" class="form-control">
                                <option value="">-- Select Student --</option>
                            </select>
                        </div>
                    </div>
                </div>  
                {{-- <div class="col-4 col-sm-3 col-md-2 col-lg-1">
                    <div class="form mt-1 mt-md-3">
                        <div class="mb-3" style="margin-top:32px;">
                        <a href="{{ route('scan') }}"
                                class="btn btn-outline-secondary px-3 ml-0 d-flex justify-content-center align-items-center border-btn"
                                style="gap: 5px" data-bs-toggle="modal" data-bs-target=".bd-scan-modal-lg"><span
                                    class="d-flex"><i class="bi bi-qr-code"></i></span>
                                <span>Scan</span>
                            </a>

                        </div>
                    </div>
                </div>        --}}

            </div>

        </div>
    </div>

</div>
<div class="modal fade bd-scan-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <button type="button" class="close btn-close" data-bs-dismiss="modal" aria-label="Close" style="left:20px; z-index:5; background:#fff;"></button>
            <div id="my-qr-reader" style="position: relative; padding: 0px; border: 1px solid silver;">                
            </div>
        </div>
    </div>

</div>


@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>


{{-- <script>
    function domReady(fn) {
        if (document.readyState === "complete" || document.readyState === "interactive") {
            setTimeout(fn, 1000);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }
    let htmlscanner; 
    let testType = '';

    domReady(function () {
        
        $('.bd-scan-modal-lg').on('shown.bs.modal', function () {
        console.log("hello");
        testType = $('#student_id').data('test-type');

            testType = $('#student_id').data('test-type');
            if (!htmlscanner) {
                htmlscanner = new Html5QrcodeScanner( "my-qr-reader", { fps: 10, qrbos: 240 } );
                htmlscanner.render(onScanSuccess);
            }
        });

        $('.bd-scan-modal-lg').on('hidden.bs.modal', function () {
            if (htmlscanner && typeof htmlscanner.clear === 'function') {
                htmlscanner.clear().then(() => {
                    htmlscanner = null;
                    $('#my-qr-reader').html("");
                }).catch(err => {
                    console.error("Scanner cleanup error:", err);
                });
            }
        });

        function extractUserId(decodeText) {
            const match = decodeText.match(/Goforfit Id\s*:\s*([A-Z0-9]+)/i);
            return match ? match[1] : null;
        }
        function onScanSuccess(decodeText, decodeResult) {
            if (!htmlscanner || typeof htmlscanner.clear !== 'function') {
                console.warn('Scanner not initialized');
                return;
            }
            htmlscanner.clear().then(() => {
                $('.bd-scan-modal-lg').modal('hide');
                // submitLoader();

                const student_reg_no = extractUserId(decodeText);

                if (!student_reg_no) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid QR Code',
                        text: 'User ID not found in QR code.'
                    });
                    return;
                }

                let school_id = $('#SchoolId').val();
                let skillReportId = $("input[name='skillReportId']").val();
                let cwsn_type = $('#cwsn_type').val();

                $.ajax({
                    url: '{{ route("fetch.student.detail") }}',
                    method: 'GET',
                    data: {
                        student_reg_no: student_reg_no,
                        skillReportId: skillReportId,
                        testType: testType,
                        school_id: school_id,
                        cwsn_type:cwsn_type,
                    },
                    success: function(response) {
                        Swal.close();
                        if (!response.success || !response.data) {
                            Swal.fire({
                                icon: 'info',
                                title: 'Student Not Found',
                                text: response.message || 'No data returned for this student.'
                            });
                            return;
                        }
                        const studentData = response.data;

                        if (studentData.test_already_given === true) {
                            clearExistingRecords(response, skillReportId, school_id, response.data.class_name, testType);
                            return;
                        }
                        const studentId = response.data.student_id;
                        stdArray = toggleValue(stdArray, studentId, selectedRank);

                        const existingStudent = document.querySelector(`.student-id[value="${studentId}"]`);
                        if (existingStudent) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Student Already Selected',
                                text: 'This student has already been assigned to another rank.',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        if (selectedRank) { // only update the current lane
                            let li = $(`#laneList li[data-rank='${selectedRank}']`);
                            li.find('.student-name').text(response.data.name);
                            li.find('.student-class').text(response.data.class_name);                        
                            li.find('.student_roll_no').text(response.data.student_roll_no);
                            li.find('.student-reg').text(response.data.student_registration_no);
                            li.find('.student-id').val(response.data.student_id);
                            localStorage.setItem(studentli, JSON.stringify(stdArray));
                            $('#class_id').trigger('change');

                        }

                        $('.bd-example-modal-lg').modal('hide');                        
                        selectedRank = null;
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        console.error('AJAX error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Request Failed',
                            text: 'Not authorized to take test for this student'
                        });
                    }
                });

            }).catch(err => {
                console.error('Failed to clear htmlscanner:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Camera Error',
                    text: `Failed to stop camera after scanning: ${err.message || err}`
                });
            });
        }
    });  

</script> --}}

<script>
    let stdArray = [];
    const studentli = "selected_student";
    $(document).ready(function () {
        $('#class_id').on('change', function () {
            let classCustom = this.value;       
            let skillReportId = $("input[name='skillReportId']").val();
            let testType = $('#student_id').data('test-type');
            const testStatus = localStorage.getItem("testStatus");

            let cwsn_type = $('#cwsn_type').val();

            $('#student_name').text('Student Name');
            $('#student_class').text('Class');
            $('#student_registration_no').text('Registration Number');
            $('#selected_student_id').val('');
            $('#AGE_GENDER_ID').text('');
            $('#student_roll_no').text('');
            $('#roll_no_id').val('');

            let studentDropdown = document.getElementById('student_id');
            studentDropdown.innerHTML = '<option value="">Loading...</option>';

            if (classCustom) {
                fetch(`{{ route('studentRollNo.autocomplete') }}?class_id=${classCustom}&test_status=${testStatus}&skillReportId=${skillReportId}&testType=${testType}&cwsn_type=${cwsn_type}&query=`)
                    .then(response => response.json())
                    .then(data => {
                        studentDropdown.innerHTML = '';
                        let localData = localStorage.getItem("selected_student");
                        let localIds = [];

                        if (localData) {
                            try {
                                const parsed = JSON.parse(localData);
                                localIds = parsed.map(item => item.studentId);
                            } catch (e) {
                                console.error("Error parsing localStorage:", e);
                            }
                        }

                        let filteredData = data.filter(item => !localIds.includes(item.id));

                        if (Array.isArray(filteredData) && filteredData.length > 0) {
                            studentDropdown.innerHTML = '<option value="">-- Select Student --</option>';
                            filteredData.forEach(student => {
                                let option = document.createElement('option');
                                option.value = student.rollno;
                                option.setAttribute("data-id", student.id);
                                option.text = `Roll No: ${student.rollno} | Name: ${student.student_name}`;
                                studentDropdown.appendChild(option);
                            });
                        } 
                        else if (data.data && data.data.length > 0) {
                            studentDropdown.innerHTML = '<option value="">-- Select Student --</option>';
                            data.data.forEach(student => {
                                let option = document.createElement('option');
                                option.value = student.rollno;
                                option.setAttribute("data-id", student.id);
                                option.text = `Roll No: ${student.rollno} | Name: ${student.student_name}`;
                                studentDropdown.appendChild(option);
                            });
                        }
                        else {
                            $('#class_id option[value="'+classCustom+'"]').hide(); 
                            studentDropdown.innerHTML = '<option value="">No students found in the selected class</option>';
                        }
                    })
                    .catch(() => {
                        studentDropdown.innerHTML = '<option value="">Error loading students</option>';
                    });
            } else {
                studentDropdown.innerHTML = '<option value="">-- Select Class First --</option>';
            }
        });

        window.clearExistingRecords = function(response, skillReportId, school_id, class_id, testType) {
            let title = @json($title);
            const selectElement = document.getElementById('student_id');
            const selectedStudent = selectElement.options[selectElement.selectedIndex];
            const studentId = selectedStudent ? selectedStudent.getAttribute('data-id') : null;

            let html = `<strong>${response.data.name}</strong> has already completed this test. Would you like to retake it?`; 

            const existingTest = response.data.existingTestName;
            const hasExistingTest = Array.isArray(existingTest) ? existingTest.length > 0 : existingTest;

            if (response.data.test_already_given && hasExistingTest ) {
               if (response.data.existingTestName.test_name !== title ) {                  
                    html = `<strong>${response.data.name}</strong> has already completed a test in the <strong>${response.data.existingTestName.test_category}</strong> category (previous: <strong>${response.data.existingTestName.test_name}</strong>). Starting a new test will permanently delete all previous records under this category. Would you like to proceed?`;
                }
            }

            Swal.fire({
                title: 'Test Already Completed',
                html:html,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Retake&nbsp;Test',
                cancelButtonText: 'Cancel',
                allowOutsideClick: false,
                allowEscapeKey: false,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('delete-student-test') }}", 
                        type: "POST",
                        data: {
                            student_id: response.data.student_id,
                            skillReportId: skillReportId,
                            school_id: school_id,
                            class_id: class_id,
                            testType: testType,
                            _token: $('meta[name="csrf-token"]').attr("content")
                        },
                        beforeSend: function () {
                            Swal.fire({
                                icon:"info",
                                title: "Deleting Record...",
                                text: "Please wait",
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(deleteResponse) {
                            Swal.close();

                            if (deleteResponse.success) {

                                Swal.fire("Deleted!", deleteResponse.message , "success")

                            .then(() => {

                                $('#student_name').text(response.data.name);
                                $('#student_class').text(response.data.class_name);
                                $('#student_registration_no').text(response.data.student_registration_no);
                                $('#selected_student_id').val(response.data.student_id);
                                $('#student_roll_no').text(response.data.student_roll_no);
                                $('#AGE_GENDER_ID').attr('placeholder', response.data.Age+ '/' + response.data.Gender);
                                
                                if (selectedRank) {
                                    const studentId = response.data.student_id;
                                    stdArray = toggleValue(stdArray, studentId, selectedRank);

                                     let li = $(`#laneList li[data-rank='${selectedRank}']`);
                                    li.find('.student-name').text(response.data.name);
                                    li.find('.student-class').text(response.data.class_name);                        
                                    li.find('.student_roll_no').text(response.data.student_roll_no);
                                    li.find('.student-reg').text(response.data.student_registration_no);
                                    li.find('.student-id').val(response.data.student_id);
                                    localStorage.setItem(studentli, JSON.stringify(stdArray));
                                    $('#class_id').trigger('change');
                                }
                                    $('#class_id').trigger('change');
                                    $('.bd-example-modal-lg').modal('hide'); 
                                    selectedRank = null;
                                }); 
                                

                            } else {
                                Swal.fire("Error", deleteResponse.message, "error");
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    $('#class_id').trigger('change');
                }
            });
        };        

    });

    $('#student_id').on('change', function () {
        let rollNo = this.value;
        
        let selectedOption = $('#class_id option:selected');
        let classValue = $('#class_id').val();                                 
        let displayText = selectedOption.text();
        
        let skillReportId = $("input[name='skillReportId']").val();
        let school_id = $('#SchoolId').val();
        
        // for 50m dash and 600m speed 
        const selectElement = document.getElementById('student_id');
        const grade = selectElement.getAttribute('data-grade');
        const selectedStudent = selectElement.options[selectElement.selectedIndex];
        const studentId = selectedStudent.getAttribute('data-id');

        let [custom_class_id, class_id] = classValue.split('-');
        let testType = $('#student_id').data('test-type');
        $.ajax({
            url: '{{ route("fetch.student.detail") }}',
            method: 'GET',
            data: {
                class_id: class_id,
                custom_class_id: custom_class_id,
                studentId: studentId,
                roll_no: rollNo,
                class_name: displayText,
                skillReportId:skillReportId,
                testType:testType,
                school_id:school_id
            },

            beforeSend: function () {
                Swal.fire({
                    icon:'info',
                    title: 'Getting Student Data...',
                    text: 'Please wait while we load the student details.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },


            success: function(response) {

                Swal.close();

                if (response.success) {

                    if (response.data.test_already_given === true) {
                        clearExistingRecords(response, skillReportId,school_id, class_id, testType);
                        return; 
                    }
                    
                    if(grade=="speed"){

                        const studentId = response.data.student_id;
                        stdArray = toggleValue(stdArray, studentId, selectedRank);

                        const existingStudent = document.querySelector(`.student-id[value="${studentId}"]`);
                        if (existingStudent) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Student Already Selected',
                                text: 'This student has already been assigned to another rank.',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        if (selectedRank) { // only update the current lane
                            let li = $(`#laneList li[data-rank='${selectedRank}']`);
                            li.find('.student-name').text(response.data.name);
                            li.find('.student-class').text(response.data.class_name);                        
                            li.find('.student_roll_no').text(response.data.student_roll_no);
                            li.find('.student-reg').text(response.data.student_registration_no);
                            li.find('.student-id').val(response.data.student_id);
                            localStorage.setItem(studentli, JSON.stringify(stdArray));
                            $('#class_id').trigger('change');

                        }

                        $('.bd-example-modal-lg').modal('hide');
                        document.getElementById('student_id').innerHTML = '<option value="">--Selected Students --</option>';                        
                        selectedRank = null;

                    }else{                        
                        $('#student_name').text(response.data.name);
                        $('#student_class').text(response.data.class_name);
                        $('#student_registration_no').text(response.data.student_registration_no);
                        $('#selected_student_id').val(response.data.student_id);
                        $('#student_roll_no').text(response.data.student_roll_no);
                        $('#AGE_GENDER_ID').attr('placeholder', response.data.Age+ '/' + response.data.Gender);
                    }


                } else {

                    // showMessages('info', 'Student not found', response.message);
                }
            },
            error: function() {
                 Swal.close();
                 handleResponseMessages('warning', 'Error', 'Something went wrong while fetching student. Please try later');
            }
        });
        
    });

    function toggleValue(arr, studentId, selectedRank) {
        arr = arr.filter(item => item.studentId !== studentId);
        arr = arr.filter(item => item.selectedRank !== selectedRank);
        arr.push({selectedRank, studentId});
        
        return arr;
    }
</script>

@endpush

