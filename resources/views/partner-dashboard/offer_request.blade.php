@include('partner-dashboard.header')

<div class="container-fluid">
<div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
        <h3 class="d-inline-block">{{__('partner/common.request_new_offer')}}</h3>
                <h5 class="d-inline-block float-right">{{__('partner/offers.request_new_offer_msg')}}</h5>
        </div>
        <div class="col-md-5">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <form class="form-horizontal form-label-left" method="post" action="{{ url('/partner/branch/offer_request') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">{{__('partner/offers.comment')}}:</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                         <span style="color: red;">
                            @if ($errors->getBag('default')->first('comment'))
                                {{ $errors->getBag('default')->first('comment') }}
                            @endif
                         </span>
                        <textarea style="max-width:95%;padding:5px;" cols="80" rows="10" name="comment" required></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->


@include('partner-dashboard.footer')
