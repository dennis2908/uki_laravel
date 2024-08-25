@extends('layouts.app')

@section('content')
    <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Transaction Cancel
                    </h5>

                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-muted">Tipster</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ url('admin/tipster/transaction-cancel') }}" class="text-muted">Transaction Cancel</a>
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
                        <span class="card-icon"><i class="la la-window-close icon-xl"></i></span>
                        <h3 class="card-label">List Match Bet</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ url('admin/tipster/transaction-cancel/create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="flaticon2-add icon-md"></i> New Record
                        </a>
                    </div>
                </div>
                <div class="card-body" style="overflow-y: auto;height:auto">
                    <div class="table-responsive" style="width:3800px;">
                        <table class="table table-separate table-head-custom table-checkable" id="table" >
                            <thead id="thead">
                                <tr>
                                    <th>No</th>
                                    <th>#</th>
                                    <th>Odds Over</th>
                                    <th>Odds Under</th>
                                    <th>Season Name</th>
                                    <th>Football Match Date Time</th>
                                    <th>Bet Price</th>
                                    <th>Big Bet Price</th>
                                    <th>Football Match</th>
                                    <th>Away Team Odds</th>
                                    <th>Away Team Handicap</th>
                                    <th>Away Team Football Team</th>
                                    <th>Home Team Odds</th>
                                    <th>Home Team Handicap</th>
                                    <th>Home Team Football Team</th>
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
        async function openTableTransactionCancel(id){
            await $('#table').DataTable().destroy();
            await $('.table-responsive').css({"width":"1800px"});
            await $(".dropdown-menu").hide();
            await $('#table tbody').empty();
            await $('.card-label').html("List Transaction Cancel");
            await $('#table thead tr').empty();
            await $('#table thead tr').append("<th>No</th>");
            await $('#table thead tr').append("<th>Action</th>");
            await $('#table thead tr').append("<th>Username</th>");
            await $('#table thead tr').append("<th>Match Bet</th>");
            await $('#table thead tr').append("<th>Place Bet Time</th>");
            await $('#table thead tr').append("<th>Status</th>");
            await $('#table thead tr').append("<th>Win Prize</th>");
            $('#table').DataTable({
                destroy: true,
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/tipster/transaction-cancel/datatable') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "idMatchBet" : id
                    }
                },
                columns: [
                    {data: 'rownum'},
                    {data: 'action', searchable: false, orderable: false},
                    {data: 'username', className: "text-center"},
                    {data: 'tipster_match_bet',  className: "text-right"},
                    {data: 'place_bet_time', className: "text-center"},
                    {data: 'status', className: "text-center"},
                    {data: 'win_prize' , className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '')},
                ]
            });
        }


        $(document).ready(function(){
            $('#table').DataTable({
                responsive: true,
                destroy: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/tipster/transaction-cancel/dataMatchBet') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    }
                },
                columns: [
                    {data: 'rownum'},
                    {data: 'action', searchable: false, orderable: false},
                    {data: 'odds_over', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '')},
                    {data: 'odds_under', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '')},
                    {data: 'tipster_season_name', className: "text-center", orderable:true, sortable:true},
                    {data: 'unixdatetime', className: "text-center", orderable:true, sortable:true},
                    {data: 'bet_price', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '')},
                    {data: 'big_bet_price', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '')},
                    {data: 'football_match_id', className: "text-center"},
                    {data: 'away_team_odds', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '')},
                    {data: 'away_team_handicap', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '') },
                    {data: 'away_team_football_team_id', className: "text-center"},
                    {data: 'home_team_odds', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '')},
                    {data: 'home_team_handicap', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 3, '')},
                    {data: 'home_team_football_team_id', className: "text-right"},
                ]
            });
        });
    </script>
@endsection
