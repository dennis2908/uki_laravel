@extends('layouts.app')

@section('content')
    <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Match Bet
                    </h5>

                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-muted">Tipster</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ url('admin/tipster/match-bet') }}" class="text-muted">Match Bet</a>
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
                        <span class="card-icon"><i class="la la-money-bill-wave-alt icon-xl"></i></span>
                        <h3 class="card-label">List Season</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ url('admin/tipster/match-bet/create') }}" class="btn btn-primary font-weight-bolder">
                            <i class="flaticon2-add icon-md"></i> New Record
                        </a>
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
        async function openTableMatchBet(id){
            await $('#table').DataTable().destroy();
            await $('.table-responsive').css({"width":"2800px"});
            await $(".dropdown-menu").hide();
            await $('#table tbody').empty();
            await $('.card-label').html("List Match Bet");
            await $('#table thead tr').empty();
            await $('#table thead tr').append("<th>No</th>");
            await $('#table thead tr').append("<th>#</th>");
            await $('#table thead tr').append("<th>Odds Over</th>");
            await $('#table thead tr').append("<th>Odds Under</th>");
            await $('#table thead tr').append("<th>Season Name</th>");
            await $('#table thead tr').append("<th>Football Match Date Time</th>");
            await $('#table thead tr').append("<th>Bet Price</th>");
            await $('#table thead tr').append("<th>Big Bet Price</th>");
            await $('#table thead tr').append("<th>Football Match</th>");
            await $('#table thead tr').append("<th>Away Team Odds</th>");
            await $('#table thead tr').append("<th>Away Team Handicap</th>");
            await $('#table thead tr').append("<th>Away Team Football Team</th>");
            await $('#table thead tr').append("<th>Home Team Odds</th>");
            await $('#table thead tr').append("<th>Home Team Handicap</th>");
            await $('#table thead tr').append("<th>Home Team Football Team</th>");
            $('#table').DataTable({
                            responsive: true,
                            searchDelay: 500,
                            processing: true,
                            serverSide: true,
                            destroy: true,
                            ajax: {
                                url: "{{ url('admin/tipster/match-bet/datatable') }}",
                                type: "POST",
                                data: function ( d ) {
                                    d.idSession = id;
                                    d._token = "{{ csrf_token() }}"
                                },
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
                        })
        }


        $(document).ready(function(){
            $('#table').DataTable({
                responsive: true,
                searchDelay: 500,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('admin/tipster/match-bet/dataSeason') }}",
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
