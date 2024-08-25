@extends('layouts.app')

@section('content')
    <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Season
                    </h5>

                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-muted">Tipster</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ url('admin/tipster/season') }}" class="text-muted">Season</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
		<div class=" container ">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-title">
                        <span class="card-icon"><i class="la la la-history icon-xl"></i></span>
                        <h3 class="card-label">List Season</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ url('admin/tipster/season/create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="flaticon2-add icon-md"></i> New Record
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-separate table-head-custom table-checkable" id="table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Initialize Balance</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<style>
.table.dataTable  {
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-size: 13px;
}
</style>
@section('script')
    <script>
        $(document).ready(function(){
            $('#table').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/tipster/season/datatable') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    }
                },
                columns: [
                    {data: 'rownum'},
                    {data: 'action', searchable: false, orderable: false},
                    {data: 'name'},
                    {data: 'start_date',
                        render: function(data, type, row){
                                return moment(data).format("DD-MMM-YYYY HH:mm:ss");
                        }
                    },
                    {data: 'end_date',
                        render: function(data, type, row){
                                return moment(data).format("DD-MMM-YYYY HH:mm:ss");
                        }
                    },
                    {data: 'initialize_balance', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '')},
                ]
            });
        });
    </script>
@endsection
