@include('admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>
<script>tinymce.init({selector: '#textarea1', plugins: "lists, advlist"});</script>
<script>tinymce.init({selector: '#textarea2', plugins: "lists, advlist"});</script>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <?php
            $partner_name = $partner_info == null ? 'Royalty' : $partner_info->info->partner_name;
            $partner_address = $partner_info == null ? '' : $partner_info->partner_address;
            ?>
            <h3>Create A New Reward of {{$partner_name}}</h3>
            <small>{{$partner_address}}</small>
        </div>
        <div class="title_right">
            <h5 style="color: red; float:right;">Fields with * are required</h5>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/admin/reward/') }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="branch_id" value="{{ $_GET['branch_id'] }}">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Date Duration:
                                        @if(isset($partner_info))
                                            <?php $exp_date = date("d-m-Y", strtotime($partner_info->info->expiry_date)); ?>
                                            (exp date: {{$exp_date}})<span style="color:red;font-size: 1.5em">*</span>
                                        @endif
                                    </label>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <input type="date" class="form-control" name="date_from2" required>
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <input type="date" class="form-control" name="date_to2" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Required Fields:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12" style="margin: 10px 0px -10px 0px">
                                        <label><input type="checkbox" name="phone"> Phone</label>
                                        <label><input type="checkbox" name="email"> Email</label>
                                        <label><input type="checkbox" name="del_add"> Delivery Address</label>
                                        <label><input type="checkbox" name="others" onclick="otherReqField(this.checked)"> Others</label>
                                        <input type="text" class="form-control" name="others_value" placeholder="Others" style="display: none;"><br>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Credits Required:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" min="1" name="selling_points" placeholder="Put the credit amount customers will buy it with" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Cost Price:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" min="1" name="actual_price" placeholder="Put price Royalty bought it with" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Reward Heading:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" name="reward_description" placeholder="Write the product name only"
                                               value="{{old('reward_description')}}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Available Rewards:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <p style="color: red;">Put nothing if its unlimited</p>
                                        <input type="number" class="form-control" min="1" name="counter_limit"
                                               placeholder="Counter limit">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Redeem Limit:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" min="1" name="scan_limit" placeholder="Redeem limit">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Reward Description:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <textarea id="textarea1" name="reward_full_description">{{old('reward_full_description')}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Terms & Conditions:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <textarea id="textarea2" name="tnc">{{old('tnc')}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-3 col-xs-12">Image(1:1):<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-8 col-xs-12">
                                        <input type="file" class="form-control" style="height: unset;" name="offerImage" required/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-xs-12">
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
<script>
    function otherReqField(value) {
        if(value){
            $('input[name$="others_value"]').css('display', 'block').prop('required', true);
        }else{
            $('input[name$="others_value"]').css('display', 'none').prop('required', false);
        }
    }
</script>