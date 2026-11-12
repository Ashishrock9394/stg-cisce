@extends('layouts.filldart-app')
@section('title', $title)
@section('content')
<style>
    #SchoolAuditLogs thead th {
		position: sticky;
		top: 0;
		box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
	}
    #SchoolAuditLogs tbody td {
		position: sticky;
		color: #000;
	}

    table#SchoolAuditLogs th.dt-orderable-none .dt-column-order {
	    display: none !important;
	}

</style>

<div class="container">
    <div class="t-mrg2 mb-5 pb-5">
        <div class="all-chaptr-cards mb-4" style="margin: 0;">
			<div class="row">
				<x:back-button title="{{$title}}" >
				</x:back-button>
			</div>
		</div>

        <table id="SchoolAuditLogs" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>action_type</th>
                    <th>description</th>                   
                    <th>user_agent</th>
                    <th>created_at</th>
                    <th>updated_at</th>
                    <th>Ip Address</th>
                </tr>
            </thead>
            <tbody>

            	@foreach($data as $value)


            	<tr>
            		
					<td>{{ $value->user_id }}</td> 
					<td>{{ $value->action_type }}</td>
					<td>{{ $value->description }}</td>

					<td>{{ $value->user_agent }}</td>     		
					<td>{{ $value->created_at->setTimezone('Asia/Kolkata')->format('d-m-Y h:i A') }}</td>
					<td>{{ $value->updated_at }}</td>
					<td>{{ Crypt::decryptString($value->ip_address) }}</td>
            	</tr>
            	@endforeach 
            </tbody>
        </table>

        
    </div>
</div>


@endsection