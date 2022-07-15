<?php use App\Http\Controllers\Enum\ValidFor; ?>
@include('admin.production.header')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=37yoj87gdrindjk3ksaos96cpb8uwpwlf8nyk2rmrqa37n3v"></script>
<script>tinymce.init({selector: '#textarea1', plugins: "lists, advlist"});</script>
<script>tinymce.init({selector: '#textarea2', plugins: "lists, advlist"});</script>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif(session('try_again'))
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
            <h3>Create A New Offer of {{$partner_info->info->partner_name}}</h3>
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
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/branch-offers/') }}">
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
                                        <input type="date" name="date_from2" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <input type="date" name="date_to2" class="form-control" required>
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
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Credit:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" min="1" name="points" placeholder="Points" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Offer Heading:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" name="offer_description"
                                               value="{{old('offer_description')}}" required>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Old Price:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" name="actual_price"
                                           placeholder="Old price">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Discounted Price:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" name="price" placeholder="Discounted Price">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Available Rewards:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" min="1" name="counter_limit" placeholder="Available rewards">
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Offer Limit Per User:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" min="1" name="scan_limit" placeholder="Scan limit">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Offer Description:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <textarea id="textarea1" name="offer_full_description">{{old('offer_full_description')}}</textarea>
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