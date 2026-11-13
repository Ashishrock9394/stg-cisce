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


{{-- @stack('scripts') --}}

<script>
    document.addEventListener('change', function (event) {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || !input.matches('[data-toggle="buttons"] input[type="checkbox"], [data-toggle="buttons"] input[type="radio"]')) {
            return;
        }

        const group = input.closest('[data-toggle="buttons"]');
        if (!group) {
            return;
        }

        if (input.type === 'radio' && input.checked) {
            group.querySelectorAll('input[type="radio"][name="' + CSS.escape(input.name) + '"]').forEach(function (radio) {
                radio.closest('.btn')?.classList.toggle('active', radio.checked);
            });
        } else {
            input.closest('.btn')?.classList.toggle('active', input.checked);
        }
    });
</script>

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