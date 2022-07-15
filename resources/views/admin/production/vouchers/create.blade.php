<?php use App\Http\Controllers\Enum\ValidFor; ?>
@include('admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>
<script>tinymce.init({selector: '#textarea1', plugins: "lists, advlist"});</script>
<script>tinymce.init({selector: '#textarea2', plugins: "lists, advlist"});</script>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h3>Create A New Deal of {{$partner_info->info->partner_name}}</h3>
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
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/admin/vouchers/') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="branch_id" value="{{ $_GET['id'] }}">
                        <div class="row">
                            <!--PRICES-->
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Date Duration
                                        @if(isset($partner_info))
                                            <?php $exp_date = date("d-m-Y", strtotime($partner_info->info->expiry_date)); ?>
                                            (exp date: {{$exp_date}}):<span style="color:red;font-size: 1.5em">*</span>
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
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Redeem Duration</label>                                    
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <select class="form-control" name="redeem_duration">
                                            <option selected disabled>Days</option>
                                            @for($i=1; $i<=31; $i++)
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Weekdays:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12" style="margin: 10px 0px -10px 0px">
                                        <label><input type="checkbox" name="sun2" checked> Sun</label>
                                        <label><input type="checkbox" name="mon2" checked> Mon</label>
                                        <label><input type="checkbox" name="tue2" checked> Tue</label>
                                        <label><input type="checkbox" name="wed2" checked> Wed</label>
                                        <label><input type="checkbox" name="thu2" checked> Thu</label>
                                        <label><input type="checkbox" name="fri2" checked> Fri</label>
                                        <label><input type="checkbox" name="sat2" checked> Sat</label>
                                    </div>
                                </div>

                                <div class="col-md-offset-4 col-md-8">
                                    <div class="form-group">
                                        <div id="time_durations2">
                                            <button style="margin: 20px 0 10px 0" type="button" name="add2" id="add2" class="btn btn-primary">Add Time Durations
                                            </button>
                                            <input type="hidden" name="time_duration_count2" id="time_duration_count2">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Credit:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" min="1" name="points" placeholder="Credit">
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Heading:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" name="heading"
                                               value="{{old('heading')}}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Actual Price:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" name="actual_price" id="actual_price" 
                                           placeholder="Actual price">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Discount type:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <select class="form-control" name="discount_type" id="discount_type">
                                            <option value="1">Flat</option>
                                            <option value="2">Percentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Discount:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" name="discount" id="discount" placeholder="Discount">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Selling Price:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" name="selling_price" id="selling_price" placeholder="Selling Price" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Commission type:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <select class="form-control" name="commission_type">
                                            <option value="1">Flat</option>
                                            <option value="2">Percentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Commission:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" name="commission" placeholder="Commission">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Available Deals:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" name="counter_limit" placeholder="Write available deal amount" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Purchase Limit:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" min="1" name="scan_limit" placeholder="Purchase limit">
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Description:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <textarea id="textarea1" name="description">{{old('description')}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Terms & Conditions:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <textarea id="textarea2" name="tnc">{{old('tnc')}}</textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Valid For:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <select class="form-control" name="valid_for">
                                            <option value="{{ValidFor::ALL_MEMBERS}}">All Members</option>
                                            <option value="{{ValidFor::PREMIUM_MEMBERS}}">Premium Members</option>
                                        </select>
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
<script type="text/javascript">
    $(document).ready(function () {
        var time_duration_count = $("#time_duration_count2").val();
        var i = time_duration_count - 1;
        //if there is no previously added extra discounts
        if (isNaN(i)) {
            var i = 0;
        }
        $('#add2').click(function () {
            i++;
            $('#time_durations2').append(
                '<div id="row2' + i + '" class="col-md-12">'
                + '<div class="col-md-4 col-sm-4 col-xs-12">'
                + '<input type="time" class="form-control" name="time_duration_from2[]">'
                + '</div>'
                + '<div class="col-md-4 col-sm-4 col-xs-12">'
                + '<input type="time" class="form-control" name="time_duration_to2[]">'
                + '</div>'
                + '<div class="col-md-2 col-sm-2 col-xs-12">'
                + '<button name="remove2" id="' + i + '" class="btn btn-danger btn_remove2">Remove</button>'
                + '</div>'
                + '</div>'
            );
            //Following script to remove newly add discount blocks
            $(document).on('click', '.btn_remove2', function () {
                var button_id = $(this).attr("id");
                $('#row2' + button_id + '').remove();
            });
        });
        //Following script to remove previous discount blocks
        $(document).on('click', '.btn_remove2', function () {
            var button_id = $(this).attr("id");
            $('#row2' + button_id + '').remove();
        });
    });
</script>

<script type="text/javascript">
    function calculateSellingPrice(argument) {
        var actual_price = $('#actual_price').val();
        var discount_type = $('#discount_type').val();
        var discount = $('#discount').val();
        var selling_price = 0;

        if(actual_price && discount_type && discount){
            if (discount_type == 1) {
                selling_price = actual_price - discount;
            }else if(discount_type == 2){
                selling_price = actual_price - ((actual_price * discount) / 100);
            }
            $('#selling_price').val(selling_price);
        }else{
            console.log('not set');
        }
    }
    $('#actual_price').keyup(function () {
        calculateSellingPrice();
    });
    $('#discount_type').change(function () {
        calculateSellingPrice();
    });
    $('#discount').keyup(function () {
        calculateSellingPrice();
    });
</script>