{{-- history modal  --}}
<div class="modal fade" id="promotionHistoryModal" tabindex="-1" role="dialog" aria-labelledby="promotionHistoryModalTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-xl modal-dialog-centered">
        <div class="modal-content">            
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Class Promotion Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <strong>Note:</strong> Promotion history is retained for <strong>7 days</strong> only.  
                    After 7 days, all Promotion history will be permanently deleted from the system. 
                </p>
                <!-- AJAX content loads here -->
                <div id="promotionHistoryContent">
                    <div class="text-center text-muted">Loading...</div>
                </div>
            </div>
           
        </div>
    </div>
</div>
{{-- modal close --}}