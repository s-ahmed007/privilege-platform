@include('admin.production.header')
{{--Crop image--}}
<script src="{{asset('js/imageCrop/jquery.js')}}"></script>
<script src="{{asset('js/imageCrop/croppie.js')}}"></script>
<link href="{{asset('admin/vendors/croppie/croppie.css')}}" rel="stylesheet">

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('exists_in_cod'))
                <div class="alert alert-warning">
                    {{ session('exists_in_cod') }}
                </div>
            @endif
            <h3>Edit Customer (Lost Card)</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($profileInfo))
            <form action="{{ url('lostUserEditDone/'. $profileInfo['customer_id']) }}" class="form-horizontal">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="first_name">Lost Card Number:</label>
                    <span style="color: red;">
                  @if ($errors->getBag('default')->first('old_customer_id'))
                            {{ $errors->getBag('default')->first('old_customer_id') }}
                        @endif
                      </span>
                    <div class="col-sm-10">
                        <input type="text" name="old_customer_id" class="form-control" id="old_customer_id"
                               value="{{ $profileInfo['customer_id'] }}"
                               pattern="[0-9]{16}" maxlength="16" minlength="16" disabled required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="first_name">New Card Number:</label>
                    <span style="color: red;">
                  @if ($errors->getBag('default')->first('new_customer_id'))
                            {{ $errors->getBag('default')->first('new_customer_id') }}
                        @endif
                       </span>
                    <div class="col-sm-10">
                        <input type="text" name="new_customer_id" class="form-control" id="new_customer_id"
                               pattern="[0-9]{16}" maxlength="16" minlength="16" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="name">Shipping Address:</label>
                    <span style="color: red;">
                    @if ($errors->getBag('default')->first('shipping_address'))
                            {{ $errors->getBag('default')->first('shipping_address') }}
                        @endif
                        </span>
                    <div class="col-sm-10">
                        <textarea name="shipping_address" class="form-control" id="shipping_address" rows="4" cols="50"
                                  required>{{ $profileInfo['shipping_address'] }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label style="margin: -5px 5px 10px -2px" class="control-label col-sm-2"
                           for="customization">Action:</label>
                    <span>
                           <input style="margin: 5px 5px 5px 5px" type="checkbox" name="customization">
                           <b style="color: green">customization required<b>
                        </span>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure?')">Submit
                            To COD
                        </button>
                    </div>
                </div>
            </form>

        @endif
    </div>
</div>

@include('admin.production.footer')