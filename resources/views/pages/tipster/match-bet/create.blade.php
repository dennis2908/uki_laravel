@extends('layouts.app')

@section('content')
    <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Create Match Bet
                    </h5>

                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-muted">Tipster</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('admin/tipster/match-bet') }}" class="text-muted">Match Bet</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ url('admin/tipster/match-bet/create') }}" class="text-muted">Create Match Bet</a>
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
                        <h3 class="card-label">Create Match Bet</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ url('admin/tipster/match-bet') }}" class="btn btn-danger font-weight-bolder">
                            <span class="svg-icon svg-icon-md"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-12.000003, -11.999999) "/>
                                </g>
                            </svg></span> Back
                        </a>
                    </div>
                </div>
                <form action="{{ url('admin/tipster/match-bet/create') }}" method="POST" id="formSubmit">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12 h-100 card p-3 bg-secondary">
                                <b><label style="font-weight: bold;">Season</label></b>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Tipster Season</label>
                                        <select class="form-control" name="tipster_season_id" required>
                                                <option value="" selected>== Select Season ==</option>
                                            @foreach ($DataSeason as $V)
                                                <option value="{{$V->id}}">{{$V->name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Start Date Match</label>
                                        <input type="date" class="form-control" name="start_date_match" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>End Date Match</label>
                                        <input type="date" class="form-control" name="end_date_match"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Football Match</label>
                                <select class="form-control" name="football_match_id" required>
                                        <option value="" selected>== Select Football Match ==</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12 h-100 card p-3 bg-secondary" id="away_team">
                                <b><label style="font-weight: bold;">Away Team</label></b>
                                <div class="row">
                                   <div class="col-md-6">
                                        <label>Odds</label>
                                        <input type="text" class="form-control" placeholder="Ex : 200.456" id="away_team_odds" value="{{ old('away_team_odds') }}" required/>
                                        <input type="hidden" class="form-control" name="away_team_odds" value="{{ old('away_team_odds') }}"/>
                                        <input type="hidden" class="form-control" name="away_team_football_team_id" value="{{ old('away_team_football_team_id') }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Handicap</label>
                                        <input type="text" class="form-control" placeholder="Ex : 2.3343" id="away_team_handicap" value="{{ old('away_team_handicap') }}" required/>
                                        <input type="hidden" class="form-control" name="away_team_handicap" value="{{ old('away_team_handicap') }}" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12 h-100 card p-3 bg-secondary" id="home_team">
                                <b><label style="font-weight: bold;">Home Team</label></b>
                                <div class="row">
                                   <div class="col-md-6">
                                        <label>Odds</label>
                                        <input type="text" class="form-control" placeholder="Ex : 500.456" id="home_team_odds" value="{{ old('home_team_odds') }}" required/>
                                        <input type="hidden" class="form-control" name="home_team_odds" value="{{ old('home_team_odds') }}"/>
                                        <input type="hidden" class="form-control" name="home_team_football_team_id" value="{{ old('home_team_football_team_id') }}"/>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Handicap</label>
                                        <input type="text" class="form-control" placeholder="Ex : 1.5672" id="home_team_handicap" value="{{ old('home_team_handicap') }}" required/>
                                        <input type="hidden" class="form-control" name="home_team_handicap" value="{{ old('home_team_handicap') }}" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Odds Over</label>
                                <input type="text" class="form-control" placeholder="Ex : 0.123" id="odds_over" value="{{ old('odds_over') }}" required/>
                                <input type="hidden" class="form-control" name="odds_over" value="{{ old('odds_over') }}"/>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Odds Under</label>
                                <input type="text" class="form-control" placeholder="Ex : 1.541" id="odds_under" value="{{ old('odds_under') }}" required/>
                                <input type="hidden" class="form-control" name="odds_under" value="{{ old('odds_under') }}"/>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Handicap</label>
                                <input type="text" class="form-control" placeholder="Ex : 1.5414" id="handicap" value="{{ old('handicap') }}" required/>
                                <input type="hidden" class="form-control" name="handicap" value="{{ old('handicap') }}"/>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Bet Price</label>
                                <input type="text" class="form-control" placeholder="Ex : 1500.167" id="bet_price" value="{{ old('bet_price') }}" required/>
                                <input type="hidden" class="form-control" name="bet_price" value="{{ old('bet_price') }}"/>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Big Bet Price</label>
                                <input type="text" class="form-control" placeholder="Ex : 15999.167" id="big_bet_price" value="{{ old('big_bet_price') }}" required/>
                                <input type="hidden" class="form-control" name="big_bet_price" value="{{ old('big_bet_price') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <button type="reset" class="reset btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<script src={{ asset('assets/js/jquery.min.js') }}></script>
<script src={{ asset('assets/js/jquery.inputmask.bundle.js') }}></script>
<script src={{ asset('assets/js/function.js') }}></script>
<script src={{ asset('assets/js/pages/crud/forms/save-edit/match-bet/create-edit.js') }}></script>
<script type='text/javascript'>

    $(document).ready(function(){
        tipsterSeasonChange("{{ route('football.bet.get.match.time')}}");
        footballMatchChange("{{ route('football.bet.get.match') }}");
        resetInput();
        createEdit();

    });

</script>