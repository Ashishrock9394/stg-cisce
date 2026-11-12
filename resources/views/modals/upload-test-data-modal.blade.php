{{-- upload test data modal --}}
<div class="modal fade" id="uploadtestdata" tabindex="-1" role="dialog"
     aria-labelledby="testDataModal" data-backdrop="static" data-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="testDataModal">Upload Test Data</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">

                <form id="uploadTestFile" action="javascript:void(0);">
                    @csrf
                    <input type="hidden" name="event" value="preview">

                    <!-- Drag & Drop Area -->
                    <div class="drop-zone mb-3" id="dropZone">
                        <i class="fa fa-cloud-upload fa-3x text-secondary mb-2"></i>
                        <p class="mb-1">
                            Drag & drop your Excel file here
                        </p>
                        <small class="text-muted">or click to browse</small>

                        <input type="file" name="test_score" id="fileInput" hidden>
                    </div>

                    <!-- File Name -->
                    <p id="fileName" class="text-success d-none"></p>

                    <!-- Upload Button -->
                    <button class="btn btn-primary px-4 mt-2 uploadfile" type="submit">
                        <i class="fa fa-upload mr-1"></i> Upload File
                    </button>

                    <!-- Progress Bar -->
                    <div class="progress mt-4 d-none" id="uploadProgress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             style="width: 100%">Uploading...</div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
{{-- modal close --}}
<style>
    .drop-zone {
        border: 2px dashed rgb(128, 128, 128);;
        border-radius: 8px;
        padding: 30px;
        cursor: pointer;
        transition: 0.3s;
        background-color: #f8f9fa;
    }

    .drop-zone:hover,
    .drop-zone.dragover {
        background-color: #e9f2ff;
        border-color: rgb(128, 128, 128);
    }
</style>
<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');

    const allowedExtensions = ['xlsx'];

    function isExcelFile(file) {
        const extension = file.name.split('.').pop().toLowerCase();
        return allowedExtensions.includes(extension);
    }

    function showInvalidFileAlert() {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File',
            text: 'Please upload a valid Excel file (.xlsx).',
            confirmButtonColor: '#3085d6',
            allowOutsideClick: false            
        });
    }

    dropZone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (!file) return;

        if (!isExcelFile(file)) {
            fileInput.value = '';
            fileName.classList.add('d-none');
            showInvalidFileAlert();
            return;
        }

        fileName.textContent = file.name;
        fileName.classList.remove('d-none');
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        const file = e.dataTransfer.files[0];
        if (!file) return;

        if (!isExcelFile(file)) {
            showInvalidFileAlert();
            return;
        }

        fileInput.files = e.dataTransfer.files;
        fileName.textContent = file.name;
        fileName.classList.remove('d-none');
    });
</script>

