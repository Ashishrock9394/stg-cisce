@extends('layouts.filldart-app')
@section('title', $title)
@section('content')
<style>
    #deletedSchoolsTable thead th {
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }
    #deletedSchoolsTable tbody td {
        position: sticky;
        color: #000;
    }

    table#deletedSchoolsTable th.dt-orderable-none .dt-column-order {
        display: none !important;
    }

</style>

<div class="container">
    <div class="t-mrg2 mb-5 pb-5">
        <div class="datatable-actions-outside d-flex justify-content-between align-items-center mb-3">
            <div class="all-chaptr-cards mb-4" style="margin: 0;">
                <div class="row">
                    <x:back-button title="{{$title}}" />
                </div>
            </div>            
        </div>

        <table id="deletedSchoolsTable" class="table table-bordered tbl-style display mb-4" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>School Code</th>
                    <th>School Name</th>
                    <th>Deleted By</th>
                    <th>Deleted Students</th>
                    <th>View</th>
                </tr>
            </thead>
        </table>

        
    </div>
</div>
<script>
    $(document).ready(function() {
        var table = $('#deletedSchoolsTable').DataTable({
            info: true,
            processing: true,
            serverSide: true,
            searchable: true,
            pageLength: 100, // default rows per page
            lengthMenu: [[100, 200, 300, -1], [100, 200, 300 , "All"]],
            dom: `<"top"lf><"filter-right">rt<"bottom"ip><"clear">`,
            
            ajax: {
                url: "{{ route('admin.restore.index') }}",
            },

            columns: [
                {
                    data: null,
                    name: 'serial_no',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'school_code', name: 'school_code' },
                { data: 'school_name', name: 'school_name' },
                { data: 'deleted_by_username', name: 'deleted_by_username' },
                { data: 'total_deleted', name: 'total_deleted' },

                {
                    data: 'view_link',
                    title: 'View Details',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        if (row.total_deleted > 0) {
                            return `<div><a href="${row.view_link}">View</a> </div>`;
                        } else {
                            return `<span class="text-muted">No record found</span>`;
                        }
                    }
                }
            ],

            order: [[0, 'asc']],
            
            initComplete: function () {
                $('.dt-search input[type="search"]').attr('placeholder', 'Search here...');
            },

            language: {
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                }
            }
        });

        function getTimestamp() {
            const now = new Date();
            return `Schools Assessment List ${now.getDate()}-${now.getMonth()+1}-${now.getFullYear()}`;
        }
    });

</script>

@endsection
