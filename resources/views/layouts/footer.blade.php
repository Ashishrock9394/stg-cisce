<footer>
    <div class="container">
        <div class="row" style="text-align: center;">
            <div class="col">
                <div class="ftr-row">
                <span>©2025 CISCE, All rights reserved. <a href="{{ route('privacy.policy')}}"  style="text-decoration: underline">Privacy Policy</a> </span>
                <span>
                    <!-- <a href="{{ route('terms.conditions')}}">Terms & Conditions</a> |  -->
                    
                </span>
                <span>powered by <img src="{{ asset('logo/fitness365-logo-web.jpg') }}" alt="Fitness 365" class="img-fluid" style="max-width: 90px; height: 28px;"></span>
                </div>
            <div>

        </div>
    </div>
</footer>


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<!-- DataTable CSS -->
<script src="{{ asset('assets/DataTables/datatables.js') }}"></script>
<script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>

<!-- DataTables Buttons JS -->    
<script src="https://cdn.datatables.net/select/2.0.3/js/dataTables.select.js"></script>
<script src="https://cdn.datatables.net/select/2.0.3/js/select.bootstrap4.js"></script>
<script src="https://cdn.datatables.net/datetime/1.5.3/js/dataTables.dateTime.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/2.0.3/js/dataTables.colReorder.js"></script>
<script src="https://cdn.datatables.net/colreorder/2.0.3/js/colReorder.bootstrap4.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.0/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.bootstrap4.js"></script>




<!-- JSZip for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.0/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>


{{-- @stack('scripts') --}}



<script>

   /**
     * Handle all messages.
     * */
    function showMessages($icon, $title, $message) {
        Swal.fire({
            icon: $icon,
            title: $title,
            text: $message,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    }


    function handleResponseMessages(icon, title, message, options = {}) {

        let config = {
            icon: icon,
            title: title,
            text: message,
            allowOutsideClick: false,
            allowEscapeKey: false,
            
            showConfirmButton: true,
            confirmButtonText: options.confirmText || 'OK',
            
            showCancelButton: options.showCancel || false,
            cancelButtonText: options.cancelText || 'Cancel',

        };

        if (options.showCancel) {
            config.showCancelButton = true;
            config.cancelButtonText = options.cancelText || 'Cancel';
        }


        if (options.timer) {
            config.timer = options.timer;
            config.showConfirmButton = false;
        }

        return Swal.fire(config).then((result) => {
            if (result.isConfirmed && typeof options.onConfirm === 'function') {
                options.onConfirm();
            }
            if (result.dismiss === Swal.DismissReason.cancel && typeof options.onCancel === 'function') {
                options.onCancel();
            }
        });
    }

    function submitLoader(){
        Swal.fire({
            icon: 'info',
            title: 'Loading',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

</script>