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
            <h3>Edit Offer</h3>
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
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/branch-offers/'.$offer_details->id) }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT"/>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Date Duration
                                        @if(isset($partner_info))
                                            <?php $exp_date = date("d-m-Y", strtotime($partner_info->info->expiry_date)); ?>
                                            (exp date: {{$exp_date}}):<span style="color:red;font-size: 1.5em">*</span>
                                        @endif
                                    </label>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->date_duration))
                                            <?php
                                            $point_details = $offer_details->date_duration;
                                            $from_date = date("Y-m-d", strtotime($point_details[0]['from']));
                                            ?>
                                           <input type="date" name="date_from2" class="form-control" value="{{$from_date}}" required>
                                        @else
                                           <input type="date" name="date_from2" class="form-control" required>
                                        @endif
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->date_duration))
                                            <?php
                                            $point_details = $offer_details->date_duration;
                                            $to_date = date("Y-m-d", strtotime($point_details[0]['to']));
                                            ?>
                                            <input type="date" name="date_to2" value="{{$to_date}}" class="form-control" required>
                                        @else
                                            <input type="date" name="date_to2" class="form-control" required>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Weekdays:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12" style="margin: 10px 0px -10px 0px">
                                        @if(isset($offer_details->weekdays))
                                            <?php $point_details = $offer_details->weekdays;
                                                 $point_details = $point_details[0];
                                            ?>
                                            <label><input type="checkbox" name="sun2"
                                                <?php if ($point_details['sun'] == 1) {
                                                    echo 'checked';
                                                }?> > Sun</label>
                                            <label><input type="checkbox" name="mon2"
                                                <?php if ($point_details['mon']== 1) {
                                                    echo 'checked';
                                                }?> > Mon</label>
                                            <label><input type="checkbox" name="tue2"
                                                <?php if ($point_details['tue'] == 1) {
                                                    echo 'checked';
                                                }?> > Tue</label>
                                            <label><input type="checkbox" name="wed2"
                                                <?php if ($point_details['wed'] == 1) {
                                                    echo 'checked';
                                                }?> > Wed</label>
                                            <label><input type="checkbox" name="thu2"
                                                <?php if ($point_details['thu'] == 1) {
                                                    echo 'checked';
                                                }?> > Thu</label>
                                            <label><input type="checkbox" name="fri2"
                                                <?php if ($point_details['fri'] == 1) {
                                                    echo 'checked';
                                                }?> > Fri</label>
                                            <label><input type="checkbox" name="sat2"
                                                <?php if ($point_details['sat'] == 1) {
                                                    echo 'checked';
                                                }?> > Sat</label>
                                        @else
                                            <label><input type="checkbox" name="sat2" checked> Sat</label>
                                            <label><input type="checkbox" name="sun2" checked> Sun</label>
                                            <label><input type="checkbox" name="mon2" checked> Mon</label>
                                            <label><input type="checkbox" name="tue2" checked> Tue</label>
                                            <label><input type="checkbox" name="wed2" checked> Wed</label>
                                            <label><input type="checkbox" name="thu2" checked> Thu</label>
                                            <label><input type="checkbox" name="fri2" checked> Fri</label>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-offset-4 col-md-8">
                                    <div class="form-group">
                                        <div id="time_durations2">
                                            <button style="margin: 20px 0 10px 0" type="button" name="add2" id="add2" class="btn btn-primary">Add Time Durations
                                            </button>

                                            <?php for($i = 0; $i < $time_duration_count2; $i++){?>
                                            <div id="row2{{$i}}" class="col-md-12">
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input type="time" class="form-control" name="time_duration_from2[]" value="{{ $time_from_array2[$i] }}">
                                                </div>
                                                <div class="col-md-4 col-sm-4 col-xs-12">
                                                    <input type="time" class="form-control" name="time_duration_to2[]" value="{{ $time_to_array2[$i] }}">
                                                </div>
                                                <div class="col-md-2 col-sm-2 col-xs-12">
                                                    <button name="remove2" id="{{$i}}" class="btn btn-danger btn_remove2">Remove
                                                    </button>
                                                </div>
                                            </div>
                                            <?php }?>
                                            <input type="hidden" name="time_duration_count2" id="time_duration_count2"
                                                   value="{{$time_duration_count2}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Credit:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->point))
                                            <input type="text" class="form-control" name="points"
                                                   value="{{$offer_details->point}}" required>
                                        @else
                                            <input type="text" class="form-control" name="points" value="" required>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Offer Heading<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->offer_description))
                                            <input type="text" class="form-control" name="offer_description"
                                                   value="{{$offer_details->offer_description}}" required>
                                        @else
                                            <input type="text" class="form-control" name="offer_description" value="" required>
                                        @endif
                                    </div>
                                </div>
<!--                                 <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Old Price:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->actual_price))
                                            <input type="number" class="form-control" name="actual_price"
                                                   value="{{$offer_details->actual_price}}" placeholder="If N/A then put 0">
                                        @else
                                            <input type="number" class="form-control" name="actual_price" value="" placeholder="If N/A then put 0">
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Discounted Price:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->price))
                                            <input type="number" class="form-control" name="price"
                                                   value="{{$offer_details->price}}" placeholder="If N/A then put 0">
                                        @else
                                            <input type="number" class="form-control" name="price" value="" placeholder="If N/A then put 0">
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Available Rewards:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->counter_limit))
                                            <input type="number" class="form-control" min="1" name="counter_limit"
                                                   value="{{$offer_details->counter_limit}}">
                                        @else
                                            <input type="number" class="form-control" min="1" name="counter_limit" value="Available rewards">
                                        @endif
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Offer Limit Per User:</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->scan_limit))
                                            <input type="number" class="form-control" min="1" name="scan_limit"
                                                   value="{{$offer_details->scan_limit}}">
                                        @else
                                            <input type="number" class="form-control" min="1" name="scan_limit" value="">
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Offer Description:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->tnc))
                                            <textarea id="textarea1" name="offer_full_description">{{$offer_details->offer_full_description}}</textarea>
                                        @else
                                            <textarea id="textarea1" name="offer_full_description">Terms</textarea>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Terms & Conditions:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->tnc))
                                            <textarea id="textarea2" name="tnc">{{$offer_details->tnc}}</textarea>
                                        @else
                                            <textarea id="textarea2" name="tnc">Terms</textarea>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Valid For:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <select class="form-control" name="valid_for">
                                            @if($offer_details->valid_for == ValidFor::ALL_MEMBERS)
                                            <option value="{{ValidFor::ALL_MEMBERS}}" selected>All Members</option>
                                            <option value="{{ValidFor::PREMIUM_MEMBERS}}">Premium Members</option>
                                            @else
                                            <option value="{{ValidFor::ALL_MEMBERS}}">All Members</option>
                                            <option value="{{ValidFor::PREMIUM_MEMBERS}}" selected>Premium Members</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Priority:<span style="color:red;font-size: 1.5em">*</span></label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        @if(isset($offer_details->priority))
                                           <input type="text" name="priority" class="form-control"
                                                  value="{{$offer_details->priority}}" required>
                                        @else
                                            <input type="text" name="priority" class="form-control" placeholder="Priority" required>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                    {{--<button type="button" class="btn btn-delete deleteCustomizedPoint"--}}
                                    {{--data-partner-account-id="{{$partner_info->partner_account_id}}">Delete</button>--}}
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
                + '<input type="time" class="form-control" name="time_duration_from2[]" value="12:00">'
                + '</div>'
                + '<div class="col-md-4 col-sm-4 col-xs-12">'
                + '<input type="time" class="form-control" name="time_duration_to2[]" value="12:00">'
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