@extends('layouts.filldart-app')
 @section('title', 'CISCE | ' . $title)
 @section('content')

 <style>
    @media (max-width: 768px) {

        .mobile-card-table thead {
            display: none;
        }

        .mobile-card-table,
        .mobile-card-table tbody,
        .mobile-card-table tr,
        .mobile-card-table td {
            display: block;
            width: 100%;
        }

        .mobile-card-table tr {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 10px;
            background: #fff;
        }

        .mobile-card-table td {
            border: none !important;
            padding: 8px 10px;
            text-align: left;
            word-break: break-word;
        }

        .mobile-card-table td::before {
            content: attr(data-label);
            display: block;
            font-weight: 600;
            color: #495057;
            margin-bottom: 4px;
        }

        .table-responsive {
            border: 0;
        }

        h3 {
            font-size: 20px;
        }

        .container,
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }
    }
</style>


    <div class="container">
        <div class="t-mrg2 pb-5">
            <div class="container-fluid">

                <div class="all-chaptr-cards mb-4" style="margin: 0;">
                    <div class="row">
                        <x:back-button title="{{ $title }}" />
                    </div>
                </div>

                <div class="row">
                    @if($contacts->isNotEmpty())
                    <div class="col-md-12">
                        <h3 class="mt-3 mb-3">Regional & Zonal Sports Coordinator</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered mobile-card-table">
                                <thead>
                                    <tr>
                                        <th>Designation</th>
                                        <th>Areas Covered</th>
                                        <!-- <th>School Code</th> -->
                                        <th>Helpdesk Email ID to Contact</th>
                                        <th>Helpdesk Mobile No to Call</th>
                                        <th>Contact Person</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contacts as $contact)
                                        <tr>
                                            <td data-label="Designation">
                                                @if ($contact->role_id == 5)
                                                    PRINCIPAL SPORTS COORDINATOR
                                                @elseif($contact->role_id == 6)
                                                    ZONAL SPORTS COORDINATOR
                                                @endif
                                            </td>

                                            <td data-label="Areas Covered">
                                                @if ($contact->role_id == 5)
                                                    Region
                                                @elseif($contact->role_id == 6)
                                                    {{ $contact->zone_name }}
                                                @endif
                                            </td>

                                            <!-- <td data-label="School Code">
                                                {{ $contact->school_code }}
                                            </td> -->

                                            <td data-label="Email">
                                                <a href="mailto:{{ $contact->email }}">
                                                    {{ $contact->email }}
                                                </a>
                                            </td>

                                            <td data-label="Mobile">
                                                <a href="tel:{{ $contact->phone }}">
                                                    {{ $contact->phone }}
                                                </a>                                                
                                            </td>
                                            
                                            <td data-label="Contact Person">
                                                {{ $contact->name }}                                                
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif


                    <div class="col-md-12">
                        <h3 class="mt-3 mb-3">Technical Helpdesk</h3>

                        <div class="table-responsive">
                            <table class="table table-bordered mobile-card-table">
                                <thead>
                                    <tr>
                                        <th>Cluster Name</th>
                                        <th>School States</th>
                                        <th>Helpdesk Email ID</th>
                                        <th>Helpdesk Mobile No</th>
                                        <th>Contact Person</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-label="Cluster">Cluster 1</td>
                                        <td data-label="School States">Uttar Pradesh and Uttarakhand</td>
                                        <td data-label="Email">
                                            <a href="mailto:fitness.cluster1@cisce.org">
                                                fitness.cluster1@cisce.org
                                            </a>
                                        </td>
                                        <td data-label="Mobile">
                                            <a href="tel:7303225696">7303225696</a>
                                        </td>
                                        <td data-label="Contact Person">Mr.Hareram Singh</td>
                                    </tr>

                                    <tr>
                                        <td data-label="Cluster">Cluster 2</td>
                                        <td data-label="School States">
                                            West Bengal, Arunachal Pradesh, Assam, Manipur,
                                            Meghalaya, Mizoram, Nagaland, Sikkim, Tripura and Odisha
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:fitness.cluster2@cisce.org">
                                                fitness.cluster2@cisce.org
                                            </a>
                                        </td>
                                        <td data-label="Mobile">
                                            <a href="tel:7303323397">7303323397</a>
                                        </td>
                                        <td data-label="Contact Person">Mr. Abdulla Muneer</td>
                                    </tr>

                                    <tr>
                                        <td data-label="Cluster">Cluster 3</td>
                                        <td data-label="School States">
                                            Delhi, Himachal Pradesh, Chandigarh, Haryana, Punjab,
                                            Jammu & Kashmir, Bihar, Jharkhand, Madhya Pradesh,
                                            Gujarat, Goa and Rajasthan
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:fitness.cluster3@cisce.org">
                                                fitness.cluster3@cisce.org
                                            </a>
                                        </td>
                                        <td data-label="Mobile">
                                            <a href="tel:7303322098">7303322098</a>
                                        </td>
                                        <td data-label="Contact Person">Mr. Shamsher Ali</td>
                                    </tr>

                                    <tr>
                                        <td data-label="Cluster">Cluster 4</td>
                                        <td data-label="School States">
                                            Andhra Pradesh, Telangana, Karnataka and Chhattisgarh
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:fitness.cluster4@cisce.org">
                                                fitness.cluster4@cisce.org
                                            </a>
                                        </td>
                                        <td data-label="Mobile">
                                            <a href="tel:9910457600">9910457600</a>
                                        </td>
                                        <td data-label="Contact Person">Miss Arti Gaur</td>
                                    </tr>

                                    <tr>
                                        <td data-label="Cluster">Cluster 5</td>
                                        <td data-label="School States">
                                            Kerala, Maharashtra, Tamil Nadu, Puducherry,
                                            Andaman & Nicobar Island
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:fitness.cluster5@cisce.org">
                                                fitness.cluster5@cisce.org
                                            </a>
                                        </td>
                                        <td data-label="Mobile">
                                            <a href="tel:9910457500">9910457500</a>
                                        </td>
                                        <td data-label="Contact Person">Miss Deepika</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p><strong>Timing:</strong> Mon–Fri, 9:00 AM–6:00 PM, except holidays</p>
                        <p>
                            <strong>Note:</strong>
                            Helpdesks are state-specific. Kindly contact only your designated
                            cluster personnel.
                        </p>
                    </div>

                    <div class="col-md-12">
                        <h3 class="mt-3">Escalation Protocol</h3>

                        <p>
                            If your concern is not resolved within three (3) working days,
                            you may escalate it to the relevant authority below:
                        </p>

                        <div class="table-responsive">
                            <table class="table table-bordered mobile-card-table">
                                <thead>
                                    <tr>
                                        <th>Query Type</th>
                                        <th>Contact Person</th>
                                        <th>Mobile Number</th>
                                        <th>Email ID</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    

                                    <tr>
                                        <td data-label="Query Type">
                                            <b>Active CISCE Registration, Assessment related Issues</b>
                                        </td>
                                        <td data-label="Contact Person">
                                            Mr. Shiv Kumar (CISCE Sports Department)
                                        </td>
                                        <td data-label="Mobile">
                                            <a href="tel:7650045212">7650045212</a>                                             
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:activecisce@cisce.org">
                                                activecisce@cisce.org
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td data-label="Query Type">
                                            <b>CISCE Games & Sports related Issues</b>
                                        </td>
                                        <td data-label="Contact Person">
                                            Mr. Arnav Kumar (CISCE Games & Sports Manager)
                                        </td>
                                        <td data-label="Mobile">
                                            <a href="tel:8800025295,9220441573">8800025295,9220441573</a>  
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:sports@cisce.org">
                                                sports@cisce.org
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-label="Query Type">
                                            Active CISCE Payment related
                                        </td>
                                        <td data-label="Contact Person">
                                            Mr. Arijit Basu (Deputy Secretary, Finance)
                                        </td>
                                        <td data-label="Mobile">
                                            <a href="tel:9831133606">9831133606</a>                                             
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:dsfin@cisce.org">
                                                dsfin@cisce.org
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td data-label="Query Type">
                                            Active CISCE Portal related Issues
                                        </td>
                                        <td data-label="Contact Person">
                                            Mr. Sujit Panigrahi (CEO, Sequoia Fitness and Sports Technology Pvt Ltd)
                                        </td>
                                        <td data-label="Mobile">
                                            <a href="tel:9810259395">9810259395</a>                                            
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:fitness.assessment@cisce.org">
                                                fitness.assessment@cisce.org
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td data-label="Query Type">
                                            All other queries
                                        </td>
                                        <td data-label="Contact Person" colspan="2">
                                            Queries related to Board Exams, School Affiliation etc.
                                            Queries not related to Active CISCE.
                                        </td>
                                        <td data-label="Email">
                                            <a href="mailto:council@cisce.org">
                                                council@cisce.org
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>  

            </div>
        </div>
    </div>


    </div>

 @endsection