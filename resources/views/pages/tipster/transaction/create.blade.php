@extends('layouts.app')

@section('content')
    <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold my-1 mr-5">
                        Create Transaction
                    </h5>

                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-muted">Setting</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('admin/tipster/transaction') }}" class="text-muted">Transaction</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="{{ url('admin/tipster/transaction/create') }}" class="text-muted">Create Transaction</a>
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
                        <span class="card-icon"><i class="la la-money-bill icon-xl"></i></span>
                        <h3 class="card-label">Create Transaction</h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="{{ url('admin/tipster/transaction') }}" class="btn btn-danger font-weight-bolder">
                            <span class="svg-icon svg-icon-md"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000003, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-12.000003, -11.999999) "/>
                                </g>
                            </svg></span> Back
                        </a>
                    </div>
                </div>
                <form action="{{ url('admin/tipster/transaction/create') }}" method="POST" id="formSubmit">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Tipster User</label>
                                <select class="form-control" name="user_id" required>
                                        <option value="" selected>== Select User ==</option>
                                    @foreach ($DataUser as $V)
                                        <option value="{{$V->id}}">{{$V->username}}</option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Match Bet</label>
                                <select class="form-control" name="tipster_match_bet_id" required>
                                        <option value="" selected>== Select Match Bet ==</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Type</label>
                                <select class="form-control" name="type" required>
                                        <option value="home">Home</option>
                                        <option value="away">Away</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Bet Type</label>
                                <select class="form-control" name="bet_type" required>
                                        <option value="big">Big</option>
                                        <option value="normal">Normal</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Win Prize</label>
                                <input type="text" class="form-control" placeholder="Ex : 999.456" id="win_prize" value="{{ old('win_prize') }}" required/>
                                <input type="hidden" class="form-control" name="win_prize" value="{{ old('win_prize') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        <button type="reset" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<script src={{ asset('assets/js/jquery.min.js') }}></script>
<script src={{ asset('assets/js/jquery.inputmask.bundle.js') }}></script>
<script src={{ asset('assets/js/function.js') }}></script>
<script src={{ asset('assets/js/pages/crud/forms/save-edit/transaction/create-edit.js') }}></script>
<script type='text/javascript'>

    $(document).ready(function(){

        createEdit();

        $('[name="user_id"]').on('change',function(e){

                var url = "{{ route('match.bet.get.data.by.user.id.no.redun') }}";
                $.ajax({
                    url: url,
                    type: 'POST',
                    data : { id : this.value },
                    dataType: 'json', // added data type
                    success: function(res) {
                        $('[name="tipster_match_bet_id"]').find('option')
                        .remove()
                        .end().append($('<option>', {
                            value: '',
                            text : "== Select Match Bet =="
                        }));
                        res.data.forEach(function(data) {
                            $('[name="tipster_match_bet_id"]').append($('<option>', {
                                value: data.id,
                                text : data.odds_over+ " - " +data.odds_under+ " - " +data.bet_price+ "-" + data.big_bet_price
                            }));
                        });
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $('[name="tipster_match_bet_id"]').find('option')
                        .remove()
                        .end().append($('<option>', {
                            value: '',
                            text : "== Select Match Bet =="
                        }));
                    }
                });
            })
    });

</script>