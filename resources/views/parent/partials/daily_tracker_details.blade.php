@if(!empty($dailyReportCard['reportCardDetails']) && collect($dailyReportCard['reportCardDetails'])->isNotEmpty())

    @foreach(collect($dailyReportCard['reportCardDetails'])->sortKeys() as $sport => $details)

        <div class="col-12"> <h2 class="mt-0 mb-4">{{ $sport }}</h2> </div>                                    
        @foreach(collect($details)->sortByDesc('date') as $detail)
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="card mb-4 mt-2">

                    <div class="activity-img" onclick="modelContent({{ $detail['activity_id'] }}, '{{ $detail['skillsport'] }}',  '{{ $sport }}', '{{ $detail['techniques'] }}', '{{ $dailyReportCard['studentProfile']['class']  }} - {{$dailyReportCard['studentProfile']['section'] ?? '' }}', true)">
                        <div class="class">
                            <div class="date col py-1">{{ date('d-M-Y', strtotime($detail['date'])) ?? 'N.A' }}</div>
                            <div class="prd col py-1">Period {{ $detail['period'] ?? 'N.A' }}</div>
                        </div>

                        @php 
                            if($detail['image'] == ''){
                               $imagepath = 'public/change-activities/default_activity_img.svg'; 
                            } else {
                                if(str_starts_with($detail['image'], 'https')){
                                    $imagepath = $detail['image'];
                                }else{
                                    $file = 'public/uploads/'.$detail['image'];
                                    if (file_exists($file)) {                                                        
                                        $imagepath = 'public/uploads/'.$detail['image'];
                                    } else {
                                       $imagepath = 'public/change-activities/default_activity_img.svg';
                                    }
                                }
                            } 
                        @endphp

                        <div class="img_overlay"></div>
                        <img class="card-img-top" src="{{ $imagepath }}" alt="Card image cap">
                    </div>

                    <div class="card-body">
                        <h5 class="card-title">{{ $detail['activity'] ?? 'N.A' }}</h5> 
                        <p class="card-text"><strong>Rating</strong>                            
                            <span class="rating" style="position: relative; top: -5px;">
                                <span class="stars" style="margin-right: 0px;">
                                    <?php for ($i=0; $i < $detail['level'] ; $i++) {  ?>
                                      <img alt="star" src="{{'public/change-activities/star_fill-o.svg'}}" class="img-fluid">
                                    <?php } ?>
                                      
                                    <?php for ($i=0 ; $i < 7-$detail['level'] ; $i++ ) { ?>
                                        <img alt="star" src="{{'public/change-activities/star_border-o.svg'}}" class="img-fluid">
                                    <?php } ?>
                                </span>
                            </span>
                        </p>

                        <p class="card-text"><strong>Skill/Sports</strong> {{ $detail['skillsport'] ?? 'N.A' }} </p>
                        <p class="card-text"><strong>Technique</strong>{{ $detail['techniques'] ?? 'N.A' }}  </p>
                        <p class="card-text"><strong>Level- {{ $detail['level'] ?? 'N.A' }}</strong>{{ $detail['level_name'] ?? 'N.A' }} </p>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach

@else
    <div class="col-12 col-md-12 mt-5">
        <div class="card py-5" style="text-align: center;">
            <h4>No data for this session</h4> 
        </div>
    </div>
@endif
