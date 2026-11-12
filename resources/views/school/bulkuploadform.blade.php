<style>
   .dropdown-checkbox-menu {  max-height: 300px; overflow-y: auto; width: 100%;font-size: 0.875rem; padding: 5px 0;  }
   .dropdown-checkbox-menu.show {  width: 90%;  }
   .dropdown-menu label, .dropdown-menu input[type="text"] { width: 100%; padding: 6px 10px; font-size: 0.85rem; }
   .dropdown-menu input[type="checkbox"] { margin-right: 8px; transform: scale(1.1); appearance: auto !important;  vertical-align: middle; }
   .list-group-item { padding: 0; margin-left: 28px; border: none;}
   .dropdown-menu input, .dropdown-menu label { cursor: default; }
   .dropdown-toggle { text-align: left; }
   .dropdown-menu label {display: flex; align-items: center; font-size: 0.875rem; padding: 5px 10px; }
   li.list-group-item.nomeclature_list { margin: 3px; }

   .row.class_nomenclature_dropdown {
      margin-right: 36px;
   }
    .nomenclature-input.highlighted {
        background-color: #e7f3ff; /* Light blue highlight */
        border: 1px solid #007bff;
    }

   span.class-badge.selected_classs {
       background-color: #292775;
       color: #ffffff;
       padding: 8px 11px 8px 11px;
       margin-bottom: 4px;
       border-radius: 8px;
   }

   div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line][class$=right] {
       right: 0.75em;
   }

   div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line][class$=left] {
       left: 0.75em;
   }
   div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line] {
       top: 1.4125em;
       width: 1.4555em;

   }
   .upload-file {
          background: #fff;
          border-radius: 5px;
          height: 42px;
   }


   .class-badge {
    display: inline-block;
    background-color: #f0f0f0;
    padding: 6px 12px;
    margin: 4px;
    border-radius: 20px;
    position: relative;
    font-size: 14px;
}

.remove-class {
    color: #ffffff;
    margin-left: 8px;
    cursor: pointer;
    font-weight: bold;
}
.custom-dropdown .dropdown-toggle {
   position: relative;
}
.custom-dropdown .dropdown-toggle:after {
   right: 10px;
}



</style>


<div class="">
   <div class="mt-0">
      <div class="row s-code">
         <div class="col-12 col-xl-6">
            <div class="heading">
               <h6>
                  School&nbsp;Code: <div><span>{{ $schoolCode }}</span></div>
               </h6>
                <span>Match "School Code" before upload students data</span>
            </div>



            <div class="action-btns">
               <a href="{{ route('download-template') }}" class="btn btn-link px-0 mr-3 txt-btn">
                  <span>
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-file-text" viewBox="0 0 16 16">
                        <path
                           d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5M5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1z" />
                        <path
                           d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1" />
                     </svg>
                  </span>
                  Download Template
               </a>
               <a href="{{ route('sample-data') }}" class="btn btn-link px-0 txt-btn">
                  <span>
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-arrow-down-square" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                           d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z" />
                     </svg>
                  </span>
                  Sample Data
               </a>

               <button type="button" class="btn btn-link px-0 txt-btn" onclick="downloadStudentProfile()">
                   <span>
                       <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                           class="bi bi-arrow-down-square" viewBox="0 0 16 16">
                           <path fill-rule="evenodd"
                               d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z" />
                       </svg>
                   </span>
                   Download All Students
               </button>

            </div>


            <div class="uploadform mb-4 mt-3">
               <form id="bulkuploadform" class="frms d-flex" action="javascript::void(0);">
                  @csrf
                  <input type="file" name="upload_student_profile" class="p-2 mr-3 upload-file">
                  <input type="hidden" name="event" value="preview">
                  <button class="btn btn-primary uploadfile" type="submit"><i class="fa fa-upload" aria-hidden="true"></i> Upload</button>
               </form>
            </div>


            <div class="col-xl-12 d-none text-center mb-3">
               <img src="{{ asset('assets/imgs/upload-data.svg') }}">
            </div>
            <div class="instructions col-12 col-xl-12">
               <div class="i-rules">
                  <h6>Instructions to fill the excel for promotion</h6>
                  <div class="i-rules-container d-flex">
                     <div class="list-group p-2">
                        <ul>
                           <li>
                              <b>Download All Students Excel Template:</b>
                              Ensure that all Class 12 students and any other students who need to be transferred
                              are marked as <strong>Transfer</strong> before downloading the template.
                           </li>
                           <li>
                              <strong>Student Class:</strong> Enter the updated class in Roman numerals only 
                              (e.g., I, II, III, IV, V, VI, VII, VIII, IX, X, XI, XII).
                           </li>
                           <li>
                              <strong>Student Section:</strong> Enter the updated section using letters such as 
                              A, B, C, etc. If there is only one section, enter <strong>'A'</strong>.
                           </li>
                           <li>
                              <strong>Student Roll Number:</strong> Update the student's roll number if required.
                           </li>
                           <li>
                              <strong>Transfer Students:</strong> Ensure that all Class 12 students and any other 
                              students who need to be transferred are excluded from the Excel template.
                           </li>
                           <li>
                              <strong>Important:</strong> Verify all details carefully before uploading the Excel file, 
                              as changes made through upload may not be reversible.
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>


         <div class="instructions col-12 col-xl-6">
            <div class="i-rules">
               <h6>Instructions to fill excel for new students</h6>
               <div class="i-rules-container d-flex">
                  <div class="list-group p-2">
                     <ul>
                        <li>Download the template to import student data.</li>
                        <li>Keep the Excel heading as it is; don't change it.</li>
                        <li>You can download the Sample Data to view the format required for import.</li>
                         <li><strong>Worksheet Requirement:</strong> The Excel file must contain only one worksheet.</li>
                         <li><strong>Remove Extra Worksheets:</strong> Delete any additional sheets like <em>SampleData</em>, <em>Important Guidelines</em>, or any others.</li>
                        <li>Excel columns highlighted in orange are mandatory.</li>
                        <li>Excel columns highlighted in yellow are optional.</li>
                        <li>The Excel sheet should not contain any blank rows.</li>
                        <li>School code: Your school code is <strong>{{ $schoolCode }}</strong>.(Spaces not allowed).</li>
                        <li>AdmissionNumber: Student Registration Number.</li>
                        <li>Name: Full name of the student (only letters, spaces, dots, and apostrophes are allowed).</li>
                        <li>Gender: Accepted values are Male, Female, M, or F only.</li>
                        <li>Class: Should be in Roman numerals only (e.g., I, II, III, IV, V, VI, VII, VIII, IX, X, XI, XII).</li>
                        <li>Section: Use letters like A, B, C, etc. (If there is only one section, please fill "A").</li>
                        <li>Roll No: Should be a numeric value (e.g., 1, 2, 3... 33, 34, 35) and cannot be left blank.</li>
                        <li>DOB: Format should be DD/MM/YYYY (e.g., 15/03/2014). This is a critical field — please ensure the date format is correct in the Excel sheet before uploading.</li>
                       <li>Email: Email ID of the parent (this will be used for communication purposes).</li>
                       <li>CWSN(Children with Special Needs): Fill <b>YES</b> if the student is differently abled; otherwise fill <b>NO</b>.</li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>


      {{--
      <div class="mt-4">          
          @if($logs->isNotEmpty())             
                 <a class="btn btn-link" data-toggle="collapse" href="#multiCollapseExample1" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">View Upload History</a>
             
               <div class="row mt-4">
                 <div class="col">
                   <div class="collapse multi-collapse" id="multiCollapseExample1">
                     
                        <table class="table table-bordered table-striped table-hover">
                           <thead class="table-dark">
                               <tr>
                                   <th>#</th>
                                   <th>Uploaded By</th>
                                   <th>Upload Time</th>
                                   <th>Status</th>
                                   <th>Message</th>
                                   <th>Completed At</th>
                                   <th>File</th>
                                   <th>Error File</th>
                               </tr>
                           </thead>
                           <tbody>
                               @foreach($logs as $index => $log)
                               <tr>
                                   <td>{{ $index + 1 }}</td>
                                   <td>{{ $log->user->name ?? 'N/A' }}</td>
                                   <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y h:i A') }}</td>
                                   <td>
                                       @if($log->status === 'completed')
                                           <span class="badge bg-success">Completed</span>
                                       @elseif($log->status === 'processing')
                                           <span class="badge bg-warning text-dark">Processing</span>
                                       @elseif($log->status === 'queued')
                                           <span class="badge bg-info text-dark">Queued</span>
                                       @else
                                           <span class="badge bg-danger">Failed</span>
                                       @endif
                                   </td>
                                   <td>{!! nl2br(e($log->message)) !!}</td>
                                   <td>{{ $log->completed_at ? \Carbon\Carbon::parse($log->completed_at)->format('d M Y h:i A') : '-' }}</td>
                                   
                                    <td>
                                       @if($log->file_path)
                                           <a href="{{ route('download.uploadedfile', $log->id) }}" class="btn btn-sm btn-primary">Uploaded</a>
                                       @endif
                                   </td>

                                   <td>              
                                    @if($log->error_file)
                                        <a href="{{ route('download.errorfile', $log->id) }}" class="btn btn-sm btn-primary">Error File</a>
                                    @endif                       
                                   </td>

                                 </tr>

                               @endforeach
                           </tbody>
                       </table>
                   </div>
                 </div>
               </div>
          @endif
      </div>

      --}}
   </div>
</div>


<script>

   $(document).ready(function () {

      const defaultSelectedClassIds = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
      $('input[name="class_options[]"]').each(function () {
         if (defaultSelectedClassIds.includes($(this).val())) {
            $(this).prop('checked', true);
             // $(this).prop('disabled', true);
         }
      });


      $('body').on('blur', 'input[name="class_nomenclature[]"]', function () {
         const originalValue = $(this).data('original');
         const currentValue = $(this).val().trim();

         if (currentValue === '') {
            $(this).val(originalValue);  // Restore original
         }
      });

      $('body').on("click", ".dropdown-menu", function (e) {
         e.stopPropagation();
      });

      function updateNomenclatureHighlight(classId, highlight) {
         const input = $('input[name="class_nomenclature[]"][data-class-id="' + classId + '"]');
         input.toggleClass('highlighted', highlight);
      }


      $('.form-group').each(function () {
         const container = $(this);
         const selectAll = container.find('.selectall');
         const checkboxes = container.find('.justone');
         const dropdownText = container.find('.dropdown-text');
         const selectText = container.find('.select-text');


         selectAll.on('change', function () {
            const isChecked = $(this).is(':checked');

            // checkboxes.each(function () {
            //    if (!defaultSelectedClassIds.includes($(this).val())) {
            //       $(this).prop('checked', isChecked);
            //    }
            // });

            checkboxes.prop('checked', isChecked);

            if (dropdownText.length) {
               dropdownText.text(`(${isChecked ? checkboxes.length : 0}) Selected`);
            }

            if (selectText.length) {
               selectText.text(isChecked ? ' Deselect' : ' Select');
            }

            // Only update highlight for actual class list checkboxes
            if (container.find('input[name="class_options[]"]').length) {
               checkboxes.each(function () {
                  updateNomenclatureHighlight($(this).val(), isChecked);
               });
            }
         });


         checkboxes.on('change', function () {

            const checkedCount = checkboxes.filter(':checked').length;
            const allChecked = checkedCount === checkboxes.length;

            selectAll.prop('checked', allChecked);
            if (selectText.length) {
               selectText.text(allChecked ? ' Deselect' : ' Select');
            }
            if (dropdownText.length) {
               dropdownText.text(`(${checkedCount}) Selected`);
            }

            // Highlight corresponding input if part of class list
            if ($(this).attr('name') === 'class_options[]') {
               updateNomenclatureHighlight($(this).val(), $(this).is(':checked'));
            }
         });
      });


      $('input[name="class_options[]"]:checked').each(function () {
         const classId = $(this).val();

         updateNomenclatureHighlight(classId, true);
      }); 

      $('.form-group').each(function () {
         const checkboxes = $(this).find('.justone');
         const checkedCount = checkboxes.filter(':checked').length;
         const dropdownText = $(this).find('.dropdown-text');

         if (dropdownText.length) {
            dropdownText.text(`(${checkedCount}) Selected`);
         }
      });


      $('#applyChangesBtn').off('click').on('click', function () {

          if (!confirm('Are you sure you want to save these classes?')) return;

         const selectedData = [];
         $('input[name="class_options[]"]').each(function () {
            const classId = $(this).val();
            const isChecked = $(this).is(':checked');
            const input = $('input[name="class_nomenclature[]"][data-class-id="' + classId + '"]');
            const className = input.val().trim();

            selectedData.push({
               id: classId,
               nomenclature: className,
               selected: isChecked
            });
         });


          const uniqueData = selectedData.filter((value, index, self) =>
            index === self.findIndex((t) => t.id === value.id)
         );

         const finalSelected = uniqueData.filter(item => item.selected === true);

         if (finalSelected.length === 0) {
            alert('You have to select at least one class.');
            return; // stop further execution
         }

         $.ajax({
            url: '{{route('saveclassnomenclature')}}',
            method: 'POST',
            headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            contentType: 'application/json',
            data: JSON.stringify({ classes: finalSelected }),

            success: function (response) {
               if (response.status === 'success') {
                  // Show confirmation
                  alert('Classes saved successfully!');
                  location.reload();
               }
            },
            error: function (xhr) {
               alert('An error occurred while saving.');
               console.error(xhr.responseText);
            }
         });

         
      });
   });



   function deleteClass(classId) {
       if (!confirm('Are you sure you want to delete this class?')) return;

       fetch("{{ route('class.delete') }}", {
           method: 'POST',
           headers: {
               'Content-Type': 'application/json',
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
           },
           body: JSON.stringify({ class_id: classId })
       })
       .then(response => response.json())
       .then(data => {
           if (data.success) {
               const badge = document.getElementById('badge-' + classId);
               if (badge) {
                   badge.remove();
                   console.log(`Badge #${classId} removed`);

                  
                   setTimeout(() => {
                       const remainingBadges = document.querySelectorAll('.class-badge');
                       console.log('Remaining badges:', remainingBadges.length);

                       if (remainingBadges.length === 0) {
                           location.reload(); 
                       }
                   }, 100); 
               }
           } else {
               alert(data.message || 'Could not delete class.');
           }
       })
       .catch(error => {
           console.error('Error:', error);
           alert('Something went wrong.');
       });
   }



   function resetSelectedClasses() {

      if (!confirm("Are you sure you want to reset all selected classes?")) return;
      $.ajax({
         url: "{{ route('classes.reset') }}",
         method: 'POST',
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
         data: {}, 
         success: function(response) {
            if (response.success) {            
               $('.class-badge.selected_classs').remove();
               $('#restbutton').hide();
               $('input[name="class_options[]"]').prop('checked', false);
               location.reload();                 
            } else {
               alert('Something went wrong. Please try again.');
            }
         },
         error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            alert('Request failed.');
         }
      });
   }

   function downloadStudentProfile() {

       Swal.fire({
            icon:'info',
           title: "Generating Excel...",
           text: "Please wait",
           allowOutsideClick: false,
           didOpen: () => Swal.showLoading()
       });

       $.ajax({
           url: "{{ route('download.student.profile') }}",
           method: "POST",
           data: {
               _token: "{{ csrf_token() }}"
           },
           xhrFields: {
               responseType: "blob"
           },

           success: function (data, status, xhr) {

               Swal.close();

               let filename = "StudentDataUploadFormat.xlsx";

               const blob = new Blob([data]);
               const url = window.URL.createObjectURL(blob);

               const a = document.createElement("a");
               a.href = url;
               a.download = filename;
               document.body.appendChild(a);
               a.click();
               a.remove();

               window.URL.revokeObjectURL(url);
           },

           error: function () {

               Swal.close();

               Swal.fire({
                   icon: "error",
                   title: "Export Failed"
               });
           }
       });
   }


</script>

