@extends('layouts.app')

@section('content')
    <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Upcoming Football Match
                    </h5>

                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-muted">Setting</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ url('admin/setting/upcoming-football-match') }}" class="text-muted">Upcoming Football Match</a>
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
                        <span class="card-icon"><i class="la la-list-ul icon-xl"></i></span>
                        <h3 class="card-label">List Season</h3>
                    </div>
                </div>
                <div class="card-body" style="overflow-y: auto;height:auto">
                    <div class="table-responsive">
                        <table class="table table-separate table-head-custom table-checkable" id="table" >
                            <thead id="thead">
                                <tr>
                                    <th>No.</th>
                                    <th>#</th>
                                    <th>Season Name</th>
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
        async function openTableUpcomingMatchBet(id){
            await $('#table').DataTable().destroy();
            await $('.table-responsive').css({"width":"2800px"});
            await $(".dropdown-menu").hide();
            await $('#table tbody').empty();
            await $('.card-label').html("List Upcoming Football Match");
            await $('#table thead tr').empty();
            await $('#table thead tr').append("<th>No</th>");
            await $('#table thead tr').append("<th>Season Name</th>");
            await $('#table thead tr').append("<th>Football Match Date Time</th>");
            await $('#table thead tr').append("<th>Agg Score</th>");
            await $('#table thead tr').append("<th>Away Position Label</th>");
            await $('#table thead tr').append("<th>Away Scores</th>");
            $('#table').DataTable({
                            responsive: true,
                            searchDelay: 500,
                            processing: true,
                            serverSide: true,
                            destroy: true,
                            ajax: {
                                url: "{{ url('admin/setting/upcoming-football-match/datatable') }}",
                                type: "POST",
                                data: function ( d ) {
                                    d.idSession = id;
                                    d._token = "{{ csrf_token() }}"
                                },
                            },
                            columns: [
                                {data: 'rownum'},
                                {data: 'season_name', className: "text-center", orderable:true, sortable:true},
                                {data: 'unixdatetime', className: "text-center", orderable:true, sortable:true},
                                {data: 'agg_score', className: "text-right", orderable:true, sortable:true},
                                {data: 'away_position', className: "text-right", orderable:true, sortable:true},
                                {data: 'away_scores', className: "text-right", orderable:true, sortable:true}
                            ]
                        })
        }


        $(document).ready(function(){
            $('#table').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/setting/upcoming-football-match/dataSeason') }}",
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
