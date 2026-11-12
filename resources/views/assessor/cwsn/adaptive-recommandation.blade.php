@switch($pwd_category_id)
    @case(1)
        <div class="mt-4">
	        <label class="form-label d-block fw-bold text-dark mb-3"><strong>CWSN Adaptive Accommodations Provided for the visually impaired.</strong></label>
	        <div class="row g-2">
	            <div class="col-md-6 mb-4">
	               <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="sighted_guide_tether">Sighted Human Guide Runner (With Tether) </label>
	                <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="guide_wire_system">Physical Guide-Wire / Structural Tracking Carabiner</label>	                
	            </div>

	            <div class="col-md-6 mb-4">
	                <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="audio_localization">Directional Sound Optimization Speakers. </label>

	               <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="none">No Specific Tool (Partial Sight Lane Bound)</label>
	            </div>
	        </div>
	    </div>
        @break

    @case(2)
        <div class="mt-4">
	        <label class="form-label d-block fw-bold text-dark mb-3"><strong>CWSN Adaptive Accommodations Provided for the hearing impaired.</strong></label>
	        <div class="row g-2">
	            <div class="col-md-6 mb-4">
	               <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="visual_flags">Visual Flag Drop </label>
	                <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="strobe_light">Electronic Strobe Light Signal</label>	                
	            </div>

	            <div class="col-md-6 mb-4">
	                <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="tactile_tap">Physical Shoulder Tap Cue </label>

	               <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="none">Standard Visual Line-of-sight</label>
	            </div>
	        </div>
	    </div>
        @break

    @case(4)
        <div class="mt-4">   
	        <label class="form-label d-block fw-bold text-dark mb-3"><strong>CWSN Adaptive Accommodations for Cognitive Support and Pacing Interventions</strong></label>
	        <div class="row g-2">
	            <div class="col-md-6 mb-4">
	               <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="pace_partner">Dedicated Pace Partner (Stride Mirroring)</label>
	                <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="token_bucket">Physical Tokens & Lap Bucket System</label>	                
	            </div>

	            <div class="col-md-6 mb-4">
	                <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="cone_milestones">Short Horizon Cone-to-Cone Markers</label>

	               <label class="list-group-item pr-3">
	                  <input class="form-check-input me-1" name="accommodations_used[]" type="checkbox" value="none">Visual Schedule Chart Only</label>
	            </div>
	        </div>
	    </div>
        @break

    @default
        <span>Something went wrong, please try again</span>
@endswitch


<!-- Trainer Notes -->
<div class="mb-4">
    <label class="form-label fw-bold text-secondary"><strong>Assessor Observation Notes</strong></label>
    <textarea name="assessor_notes" rows="3" placeholder="Describe lap pacing adjustments, behavioral observations, or guide details..." class="form-control bg-light"></textarea>
</div>