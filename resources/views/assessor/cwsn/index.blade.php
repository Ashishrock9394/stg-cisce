@extends('layouts.filldart-app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style>

    /* Small devices (Large phones, 576px and up)  576px and 768px  */
    @media (min-width: 576px) {
     
    }

    /* Medium devices (Tablets, 768px and up)    768px and  992px */
    @media (min-width: 768px) {
      
    }


    /* Large devices (Desktops, 992px and up)  992px and above*/
    @media (min-width: 992px) {
      
    }

    /* 0px to 767px  */
    @media (max-width: 767px) {   


      .scanner-conatiner{
            margin-top: 0px !important;
      }
    }

    #scanner_btn{
        min-width: 56px !important;
    }

</style>
@stack('cwsn-style')

<div class="container">
    <div class="t-mrg2 mb-5 pb-5">            
        <div class="all-chaptr-cards">
            <div class="row">
                <div class="col">
                    <div class="heading-rw mt-0 mt-md-1 mb-0 p-0"> 
                     <x-back-button :title="$title" /> 
                    </div>
                </div>
            </div>

            @php  $type = "cwsnlist"; @endphp
            
            <x-get-student-list 
                :classes="$classes" 
                :type="$type"
                :cwsn-type="$pwd_category_id"
                :title="$title"
            />
            
            <div class="col-12"> @yield('cwsnform') </div>
        </div>
    </div>
</div>


@stack('cwsn-module-script')

<script>
    function SubmitForm(formid, formData, route) {

        submitLoader();

        $.ajax({
            url: route,
            method: 'POST',
            data: formData, 
            success: function(response) {   
                Swal.close();   
                $(`#${formid}`)[0].reset();

                if (response.status === 'success' || response.success == true) {
                    handleResponseMessages( 'success',  '', response.message, {
                        confirmText: 'OK',
                        onConfirm: function () {
                            location.reload();
                        }
                    });
                }
            },

            error: function (xhr) {
                Swal.fire({
                    title: "Error!",
                    text: xhr.responseJSON?.message ?? 'Unexpected error occurred',
                    icon: "error"
                });
            }
        });
    }
</script>

@endsection