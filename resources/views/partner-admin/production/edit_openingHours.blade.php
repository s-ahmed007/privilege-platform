@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')
<!-- page content -->
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Edit Opening Hours</h3>
        </div>
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @elseif(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="panel-body">
        <form class="form-horizontal form-label-left" method="post" action="{{ url('editOpeningHours') }}">
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                <label class="col-md-1">SAT</label>
                <div class="col-md-8 col-sm-3 col-xs-6">
                    @if ($errors->getBag('default')->first('sat'))
                        <div style="color: red">Field is required</div>
                    @endif
                    <input type="text" class="form-control" value="{{$openingHours['sat']}}" name="sat">
                </div>
                <div class="col-md-3"></div>
                <label class="col-md-1">SUN</label>
                <div class="col-md-8 col-sm-3 col-xs-6">
                    @if ($errors->getBag('default')->first('sun'))
                        <div style="color: red">Field is required</div>
                    @endif
                    <input type="text" class="form-control" value="{{$openingHours['sun']}}" name="sun" value="{{old('sun')}}">
                </div>
                <div class="col-md-3"></div>
                <label class="col-md-1">MON</label>
                <div class="col-md-8 col-sm-3 col-xs-6">
                    @if ($errors->getBag('default')->first('mon'))
                        <div style="color: red">Field is required</div>
                    @endif
                    <input type="text" class="form-control" value="{{$openingHours['mon']}}" name="mon" value="{{old('mon')}}">
                </div>
                <div class="col-md-3"></div>
                <label class="col-md-1">TUES</label>
                <div class="col-md-8 col-sm-3 col-xs-6">
                    @if ($errors->getBag('default')->first('tues'))
                        <div style="color: red">Field is required</div>
                    @endif
                    <input type="text" class="form-control" value="{{$openingHours['tue']}}" name="tues" value="{{old('tues')}}">
                </div>
                <div class="col-md-3"></div>
                <label class="col-md-1">WED</label>
                <div class="col-md-8 col-sm-3 col-xs-6">
                    @if ($errors->getBag('default')->first('wed'))
                        <div style="color: red">Field is required</div>
                    @endif
                    <input type="text" class="form-control" value="{{$openingHours['wed']}}" name="wed" value="{{old('wed')}}">
                </div>
                <div class="col-md-3"></div>
                <label class="col-md-1">THU</label>
                <div class="col-md-8 col-sm-3 col-xs-6">
                    @if ($errors->getBag('default')->first('thu'))
                        <div style="color: red">Field is required</div>
                    @endif
                    <input type="text" class="form-control" value="{{$openingHours['thurs']}}" name="thu" value="{{old('thu')}}">
                </div>
                <div class="col-md-3"></div>
                <label class="col-md-1">FRI</label>
                <div class="col-md-8 col-sm-3 col-xs-6">
                    @if ($errors->getBag('default')->first('fri'))
                        <div style="color: red">Field is required</div>
                    @endif
                    <input type="text" class="form-control" value="{{$openingHours['fri']}}" name="fri" value="{{old('fri')}}">
                </div>
            </div>

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="ln_solid"></div>
            <div class="form-group">
                <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                    <button type="submit" class="btn btn-activate pull-right">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@include('partner-admin.production.footer')