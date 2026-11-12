@extends('layouts.app')
@section('title', 'CISCE | ' . $title)
@section('content')

<style>
    a {
        color: orangered;
    }
    a:hover {
        color: orangered;
        text-decoration: underline;
    }

    /* ── Shared card base ── */
    .cluster-card,
    .esc-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 12px;
    }
    .cluster-card:last-child,
    .esc-card:last-child {
        margin-bottom: 0;
    }

    /* ── Yellow header (used by both tables) ── */
    .card-header-yellow {
        background-color: #ffcb08;
        color: #000;
        font-weight: bold;
        font-size: 14px;
        padding: 8px 14px;
    }

    /* ── Two-column label/value row ── */
    .card-field-row {
        display: flex;
        border-bottom: 1px solid #dee2e6;
        font-size: 14px;
    }
    .card-field-row:last-child {
        border-bottom: none;
    }
    .card-field-label {
        width: 135px;
        flex-shrink: 0;
        padding: 8px 12px;
        font-weight: 600;
        font-size: 12px;
        color: #495057;
        background-color: #f8f9fa;
        border-right: 1px solid #dee2e6;
        line-height: 1.5;
        display: flex;
        align-items: flex-start;
        padding-top: 10px;
    }
    .card-field-value {
        flex: 1;
        padding: 8px 12px;
        color: #212529;
        line-height: 1.5;
    }

    /* ── Show/hide logic ── */
    .cards-mobile      { display: block; }
    .table-desktop     { display: none; }

    @media (min-width: 768px) {
        .cards-mobile  { display: none; }
        .table-desktop { display: block; }
        p{
            margin-top:10px;
        }
        
    }
     @media (max-width: 768px) {
        p {
            font-size: 13px;
            margin-top: 10px;
            margin-bottom: 6px;
        }
        h4 {
            margin-top: 10px;
            margin-bottom: 10px;
        }
    }
</style>

<div class="container">
    <div class="t-mrg2 pb-5">
        <div class="container-fluid p-0 pt-3">
            <div class="row my-5">

                {{-- ══════════════════════════════════
                     TECHNICAL HELPDESK
                ══════════════════════════════════ --}}
                <div class="col-md-12">
                    <h4 class="mt-3 mb-3">Technical Helpdesk</h4>

                    {{-- Mobile cards --}}
                    <div class="cards-mobile">

                        <div class="cluster-card">
                            <div class="card-header-yellow">Cluster 1</div>
                            <div class="card-field-row">
                                <div class="card-field-label">School States</div>
                                <div class="card-field-value">Uttar Pradesh and Uttarakhand</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:fitness.cluster1@cisce.org">fitness.cluster1@cisce.org</a></div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Mobile Number</div>
                                <div class="card-field-value">7303225696</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Contact Person</div>
                                <div class="card-field-value">Mr. Hareram Singh</div>
                            </div>
                        </div>

                        <div class="cluster-card">
                            <div class="card-header-yellow">Cluster 2</div>
                            <div class="card-field-row">
                                <div class="card-field-label">School States</div>
                                <div class="card-field-value">West Bengal, Arunachal Pradesh, Assam, Manipur, Meghalaya, Mizoram, Nagaland, Sikkim, Tripura and Odisha</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:fitness.cluster2@cisce.org">fitness.cluster2@cisce.org</a></div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Mobile Number</div>
                                <div class="card-field-value">7303323397</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Contact Person</div>
                                <div class="card-field-value">Mr. Abdulla Muneer</div>
                            </div>
                        </div>

                        <div class="cluster-card">
                            <div class="card-header-yellow">Cluster 3</div>
                            <div class="card-field-row">
                                <div class="card-field-label">School States</div>
                                <div class="card-field-value">Delhi, Himachal Pradesh, Chandigarh, Haryana, Punjab, Jammu &amp; Kashmir, Bihar, Jharkhand, Madhya Pradesh, Gujarat and Rajasthan</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:fitness.cluster3@cisce.org">fitness.cluster3@cisce.org</a></div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Mobile Number</div>
                                <div class="card-field-value">7303322098</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Contact Person</div>
                                <div class="card-field-value">Mr. Shamsher Ali</div>
                            </div>
                        </div>

                        <div class="cluster-card">
                            <div class="card-header-yellow">Cluster 4</div>
                            <div class="card-field-row">
                                <div class="card-field-label">School States</div>
                                <div class="card-field-value">Andhra Pradesh, Telangana, and Karnataka &amp; Goa </div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:fitness.cluster4@cisce.org">fitness.cluster4@cisce.org</a></div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Mobile Number</div>
                                <div class="card-field-value">9910457600</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Contact Person</div>
                                <div class="card-field-value">Miss Arti Gaur</div>
                            </div>
                        </div>

                        <div class="cluster-card">
                            <div class="card-header-yellow">Cluster 5</div>
                            <div class="card-field-row">
                                <div class="card-field-label">School States</div>
                                <div class="card-field-value">Chhattisgarh, Kerala, Maharashtra, Tamil Nadu, Puducherry, Andaman &amp; Nicobar Island, Schools Abroad</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:fitness.cluster5@cisce.org">fitness.cluster5@cisce.org</a></div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Mobile Number</div>
                                <div class="card-field-value">9910457500</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Contact Person</div>
                                <div class="card-field-value">Miss Deepika</div>
                            </div>
                        </div>

                    </div>{{-- end mobile cards --}}

                    {{-- Desktop table (unchanged) --}}
                    <div class="table-desktop">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="auto-style1">Cluster Name</th>
                                        <th style="width: 418px;">School States</th>
                                        <th>Email ID</th>
                                        <th>Mobile Number</th>
                                        <th>Contact Person</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="auto-style1">Cluster 1</th>
                                        <td>Uttar Pradesh and Uttarakhand</td>
                                        <td><a href="mailto:fitness.cluster1@cisce.org">fitness.cluster1@cisce.org</a></td>
                                        <td>7303225696</td>
                                        <td>Mr. Hareram Singh</td>
                                    </tr>
                                    <tr>
                                        <th class="auto-style1">Cluster 2</th>
                                        <td>West Bengal, Arunachal Pradesh, Assam, Manipur, Meghalaya, Mizoram, Nagaland, Sikkim, Tripura and Odisha</td>
                                        <td><a href="mailto:fitness.cluster2@cisce.org">fitness.cluster2@cisce.org</a></td>
                                        <td>7303323397</td>
                                        <td>Mr. Abdulla Muneer</td>
                                    </tr>
                                    <tr>
                                        <th class="auto-style1">Cluster 3</th>
                                        <td>Delhi, Himachal Pradesh, Chandigarh, Haryana, Punjab, Jammu &amp; Kashmir, Bihar, Jharkhand, Madhya Pradesh, Gujarat and Rajasthan</td>
                                        <td><a href="mailto:fitness.cluster3@cisce.org">fitness.cluster3@cisce.org</a></td>
                                        <td>7303322098</td>
                                        <td>Mr. Shamsher Ali</td>
                                    </tr>
                                    <tr>
                                        <th class="auto-style1">Cluster 4</th>
                                        <td>Andhra Pradesh, Telangana and Karnataka &amp;  Goa</td>
                                        <td><a href="mailto:fitness.cluster4@cisce.org">fitness.cluster4@cisce.org</a></td>
                                        <td>9910457600</td>
                                        <td>Miss Arti Gaur</td>
                                    </tr>
                                    <tr>
                                        <th class="auto-style1">Cluster 5</th>
                                        <td>Chhattisgarh, Kerala, Maharashtra, Tamil Nadu, Puducherry, Andaman &amp; Nicobar Island, Schools Abroad</td>
                                        <td><a href="mailto:fitness.cluster5@cisce.org">fitness.cluster5@cisce.org</a></td>
                                        <td>9910457500</td>
                                        <td>Miss Deepika</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>{{-- end desktop table --}}

                    <p><strong>Timing:</strong> Mon–Fri, 9:00 AM–6:00 PM, except holidays</p>
                    <p><strong>Note:</strong> Helpdesks are state-specific. Kindly contact only your designated cluster personnel.</p>
                </div>

                {{-- ══════════════════════════════════
                     ESCALATION PROTOCOL
                ══════════════════════════════════ --}}
                <div class="col-md-12">
                    <h4 class="mt-3">Escalation Protocol</h4>
                    <p>If your concern is not resolved within three (3) working days, you may escalate it to the relevant authority below:</p>

                    {{-- Mobile cards --}}
                    <div class="cards-mobile">

                        <div class="esc-card">
                            <div class="card-header-yellow"><b>Active CISCE Registration, Assessment related Issues</b></div>
                            <div class="card-field-row">
                                <div class="card-field-label">Contact Person</div>
                                <div class="card-field-value">Mr. Shiv Kumar (CISCE Sports Department)</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Mobile Number</div>
                                <div class="card-field-value">7650045212</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:activecisce@cisce.org">activecisce@cisce.org</a></div>
                            </div>
                        </div>

                        <div class="esc-card">
                            <div class="card-header-yellow"><b>CISCE Games & Sports related Issues</b></div>
                            <div class="card-field-row">
                                <div class="card-field-label">Contact Person</div>
                                <div class="card-field-value">Mr. Arnav Kumar (CISCE Games &amp; Sports Manager)</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Mobile Number</div>
                                <div class="card-field-value">8800025295, 9220441573</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:sports@cisce.org">sports@cisce.org</a></div>
                            </div>
                        </div>
                        <div class="esc-card">
                            <div class="card-header-yellow">Active CISCE Payment related</div>
                            <div class="card-field-row">
                                <div class="card-field-label">Contact Person</div>
                                <div class="card-field-value">Mr. Arijit Basu (Deputy Secretary, Finance)</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Mobile Number</div>
                                <div class="card-field-value">9831133606</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:dsfin@cisce.org">dsfin@cisce.org</a></div>
                            </div>
                        </div>
                        <div class="esc-card">
                            <div class="card-header-yellow">Active CISCE Portal related Issues</div>
                            <div class="card-field-row">
                                <div class="card-field-label">Contact Person</div>
                                <div class="card-field-value">Mr. Sujit Panigrahi (CEO, Sequoia Fitness and Sports Technology Pvt Ltd)</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Mobile Number</div>
                                <div class="card-field-value">9810259395</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:fitness.assessment@cisce.org">fitness.assessment@cisce.org</a></div>
                            </div>
                        </div>

                        <div class="esc-card">
                            <div class="card-header-yellow">All other queries</div>
                            <div class="card-field-row">
                                <div class="card-field-label">Info</div>
                                <div class="card-field-value">Queries related to Board Exams, School Affiliation etc. / Queries not related to Active CISCE — send mail here</div>
                            </div>
                            <div class="card-field-row">
                                <div class="card-field-label">Email ID</div>
                                <div class="card-field-value"><a href="mailto:council@cisce.org">council@cisce.org</a></div>
                            </div>
                        </div>

                    </div>{{-- end escalation mobile cards --}}

                    {{-- Desktop table (unchanged) --}}
                    <div class="table-desktop">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="auto-style1">Query Type</th>
                                    <th style="width: 418px;">Contact Person</th>
                                    <th>Mobile Number</th>
                                    <th>Email ID</th>
                                </tr>
                            </thead>
                            <tbody>                                
                                <tr>
                                    <td class="auto-style2"><b>Active CISCE Registration, Assessment related Issues</b></td>
                                    <td class="auto-style2">Mr. Shiv Kumar (CISCE Sports Department)</td>
                                    <td class="auto-style2">7650045212</td>
                                    <td class="auto-style2"><a href="mailto:activecisce@cisce.org">activecisce@cisce.org</a></td>
                                </tr>
                                <tr>
                                    <td class="auto-style2"><b>CISCE Games & Sports related Issues</b></td>
                                    <td class="auto-style2">Mr. Arnav Kumar (CISCE Games &amp; Sports Manager)</td>
                                    <td class="auto-style2">8800025295, 9220441573</td>
                                    <td class="auto-style2"><a href="mailto:sports@cisce.org">sports@cisce.org</a></td>
                                </tr>
                                <tr>
                                    <td class="auto-style2">Active CISCE Payment related</td>
                                    <td class="auto-style2">Mr. Arijit Basu (Deputy Secretary, Finance)</td>
                                    <td class="auto-style2">9831133606</td>
                                    <td class="auto-style2"><a href="mailto:dsfin@cisce.org">dsfin@cisce.org</a></td>
                                </tr>
                                <tr>
                                    <td class="auto-style2">Active CISCE Portal related Issues</td>
                                    <td class="auto-style2">Mr. Sujit Panigrahi (CEO, Sequoia Fitness and Sports Technology Pvt Ltd)</td>
                                    <td class="auto-style2">9810259395</td>
                                    <td class="auto-style2"><a href="mailto:fitness.assessment@cisce.org">fitness.assessment@cisce.org</a></td>
                                </tr>
                                <tr>
                                    <td class="auto-style2">All other queries</td>
                                    <td class="auto-style2" colspan="2">
                                        Queries related to Board Exams, School Affiliation etc./
                                        Queries not related to Active CISCE - send mail here
                                    </td>
                                    <td class="auto-style2"><a href="mailto:council@cisce.org">council@cisce.org</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>{{-- end escalation desktop table --}}

                </div>

            </div>
        </div>
    </div>
</div>

@endsection