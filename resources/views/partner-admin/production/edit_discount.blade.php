@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>

<script>tinymce.init({ selector:'#textarea1', plugins: "lists, advlist" });</script>
<script>tinymce.init({ selector:'#textarea2', plugins: "lists, advlist" });</script>
<script>tinymce.init({ selector:'#textarea3', plugins: "lists, advlist" });</script>
<!-- page content -->
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Edit Discount Details</h3>
        </div>
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @elseif(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="panel-body">
        <form class="form-horizontal form-label-left" method="post" action="{{ url('editDiscounts') }}">
            {{csrf_field()}}
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Discount %:</label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                      <span style="color: red;">
                        @if ($errors->getBag('default')->first('discount_for_gold'))
                          {{ $errors->getBag('default')->first('discount_for_gold') }}
                        @endif
                      </span>
                    <input type="text" class="form-control" value="{{$discountInfo[0]['discount_percentage']}}" name="discount_for_gold" value="{{old('discount_for_gold')}}">
                </div>
                <div class="col-md-5 col-sm-5 col-xs-12">
                     <span style="color: red;">
                        @if ($errors->getBag('default')->first('discount_for_platinum'))
                             {{ $errors->getBag('default')->first('discount_for_platinum') }}
                         @endif
                    </span>
                    <input type="text" class="form-control" value="{{$discountInfo[1]['discount_percentage']}}" name="discount_for_platinum" value="{{old('discount_for_platinum')}}">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Discount details:</label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <span style="color: red;">
                      @if ($errors->getBag('default')->first('discount_details_for_gold'))
                            {{ $errors->getBag('default')->first('discount_details_for_gold') }}
                        @endif
                    </span>
                    <textarea id="textarea1" name="discount_details_for_gold" value="{{$discountInfo[0]['discount_details']}}">{{$discountInfo[0]['discount_details']}}</textarea>
{{--                    <input type="text" class="form-control" value="{{$discountInfo[0]['discount_details']}}" name="discount_details_for_gold" value="{{old('discount_details_for_gold')}}">--}}
                </div>
                <div class="col-md-5 col-sm-5 col-xs-12">
                     <span style="color: red;">
                     @if ($errors->getBag('default')->first('discount_details_for_platinum'))
                             {{ $errors->getBag('default')->first('discount_details_for_platinum') }}
                         @endif
                    </span>
                    <textarea id="textarea2" name="discount_details_for_platinum" value="{{old('discount_details_for_platinum')}}">{{$discountInfo[1]['discount_details']}}</textarea>
                    {{--<input type="text" class="form-control" value="{{$discountInfo[1]['discount_details']}}" name="discount_details_for_platinum" value="{{old('discount_details_for_platinum')}}">--}}
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Terms&Condition</label>
                <div class="col-md-9 col-sm-4 col-xs-12">
                    <span style="color: red;">
                     @if ($errors->getBag('default')->first('tnc_for_partner'))
                            {{ $errors->getBag('default')->first('tnc_for_partner') }}
                        @endif
                    </span>
                    <textarea id="textarea3" name="tnc_for_partner">{{$discountInfo['tnc']}}</textarea>
{{--                    <input type="text" class="form-control" value="{{$discountInfo[0]['expiry_date']}}" name="discount_expiry_for_gold" value="{{old('discount_expiry_for_gold')}}">--}}
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Discount expiry:</label>
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <span style="color: red;">
                     @if ($errors->getBag('default')->first('discount_expiry_for_gold'))
                            {{ $errors->getBag('default')->first('discount_expiry_for_gold') }}
                        @endif
                    </span>
                    <input type="text" class="form-control" value="{{$discountInfo[0]['expiry_date']}}" name="discount_expiry_for_gold" value="{{old('discount_expiry_for_gold')}}">
                </div>
                <div class="col-md-5 col-sm-5 col-xs-12">
                    <span style="color: red;">
                     @if ($errors->getBag('default')->first('discount_expiry_for_platinum'))
                            {{ $errors->getBag('default')->first('discount_expiry_for_platinum') }}
                        @endif
                    </span>
                    <input type="text" class="form-control" value="{{$discountInfo[1]['expiry_date']}}" name="discount_expiry_for_platinum" value="{{old('discount_expiry_for_platinum')}}">
                </div>
            </div>
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