<div class="grid">                        
   
   @if(!empty($reportDetail['reportCardDetails']) && collect($reportDetail['reportCardDetails'])->isNotEmpty())

      <table cellspacing="0" cellpadding="0" class="tbl">
         <tr class="s3">
            <th width="170px;">Skill / Sports</th>
            <th width="170px;">Technique</th>
            <th>Activity</th>
            <th colspan="7" width="130px;">Rating</th>
            <th width="140px;">Level</th>
         </tr>

         @foreach(collect($reportDetail['reportCardDetails'])->sortKeys() as $skillName => $skillarea)

            @php
               $sportRowspan = 0;
               foreach($skillarea as $technique){
                  $sportRowspan += count($technique); 
               }
               $sportFirstRow = true; 
            @endphp

            @foreach(collect($skillarea)->sortKeys() as $techniqueName => $activities)
               @php $techniqueRowspan = count($activities); @endphp

               @foreach(collect($activities)->sortKeys() as $activity_title => $activity)

                  @foreach($activity as $outcomes)

                     <tr>
                        @if($sportFirstRow)
                           <td rowspan="{{ $sportRowspan }}" style="text-align: center;"><p class="s4">{{ $skillName ?? 'N.A' }}</p></td>
                           @php $sportFirstRow = false; @endphp
                        @endif

                        @if(!isset($techniqueFirstRow))
                           @php $techniqueFirstRow = true; @endphp
                        @endif

                        @if($techniqueFirstRow)
                        <td rowspan="{{ $techniqueRowspan }}" style="text-align: center;"><p class="s4">{{ $techniqueName ?? 'N.A' }}</p></td>
                          @php $techniqueFirstRow = false; @endphp
                        @endif

                        <td><p class="s4">{{ $activity_title ?? 'N.A' }}</p></td>
                        <td class="star" colspan="7" style="text-align: left;">
                           @for ($i = 0; $i < $outcomes['level']; $i++)
                              <span class="s5 filled">&#9733;</span>   
                           @endfor

                           @for ($i = 0; $i < 7 - $outcomes['level']; $i++)
                              <span class="s5 ">&#9734;</span>
                           @endfor
                        </td>
                        <td><p class="s4">{{ $outcomes['level_name'] ?? 'N.A' }}</p></td>
                     </tr>
                  @endforeach   
               @endforeach

               @php unset($techniqueFirstRow); @endphp

            @endforeach
         @endforeach
      </table>
   </div>


    <button class="btn btn-link collapsed mt-4 px-0" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"> Rubric Descriptions </button>

    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
         <div class="card-body p-0">
            <table class="table table-bordered ">
               <thead>
                  <tr>
                     <th scope="col">Level</th>
                     <th scope="col">Level Name</th>
                     <th scope="col">Description</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($levels as $level)
                  <tr>
                     <td>L-{{$level->level_value}} </td>
                     <td> {{$level->level_name}} </td>
                     <td> {{$level->description}} </td>
                  </tr>
                  @endforeach              
               </tbody>
            </table>
         </div>
      </div>

   @else

      <div class=" mt-4">
         <div class="col-12 student-report">
            <div class="row activity_cards mb-5">
               <div class="col-12 col-md-12 mt-5 p-0">
                  <div class="card" style="text-align: center; min-height: auto; box-shadow: none;">
                     <h4>No Report Available</h4>
                  </div>
               </div>
            </div>
         </div>
      </div>

   @endif


