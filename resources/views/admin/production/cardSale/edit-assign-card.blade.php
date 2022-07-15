@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('ip_exists'))
                <div class="alert alert-warning">
                    {{ session('ip_exists') }}
                </div>
            @endif
            <h3>Edit</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/update-assigned-card/'.$card->id) }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <div id="row" class="total_card">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <span style="color: #E74430;">
                                        @if ($errors->getBag('default')->first('card_number'))
                                            {{ $errors->getBag('default')->first('card_number') }}
                                        @endif
                                    </span>
                                    <input type="text" class="form-control" placeholder="Card Number" value="{{$card->card_number}}"
                                       name="card_number" maxlength="16" minlength="16" required>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select class="form-control" name="card_type">
                                        <option value="2" {{$card->card_type == 2 ? 'selected':''}}>Royalty Premium Membership</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
