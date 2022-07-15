@include('admin.production.header')
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
            <h3>Create A New Offer</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/store-custom-point') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="offer_id" value="{{ $partner_info->id }}">
                        <div class="row">
                            <!--PRICES-->
                            <div class="col-md-9">
                                <h3 style="text-align: center">Customized Point</h3>
                                <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('point_type'))
                                        {{ $errors->getBag('default')->first('point_type') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Point Type</label>
                                    <div class="col-md-8 col-sm-9 col-xs-12">
                                        <select class="form-control" name="point_customize_type" required>
                                            <option disabled selected>Select</option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::weekly}}">
                                                Weekly
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::daily}}">Daily
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::hourly}}">
                                                Hourly
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Date Duration
                                        @if(isset($partner_info))
                                            <?php $exp_date = date("d-m-Y", strtotime($partner_info->branch->info->expiry_date)); ?>
                                            (exp date: {{$exp_date}}):
                                        @endif
                                    </label>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                        <span style="color: red;">
                                            @if ($errors->getBag('default')->first('date_from'))
                                                {{ $errors->getBag('default')->first('date_from') }}
                                            @else
                                                <br>
                                            @endif
                                        </span>
                                        <input style="
                            " type="text" class="form-control" placeholder="DD-MM-YYYY" name="date_from">
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12">
                                       <span style="color: red;">
                                            @if ($errors->getBag('default')->first('date_to'))
                                               {{ $errors->getBag('default')->first('date_to') }}
                                           @else
                                               <br>
                                           @endif
                                        </span>
                                        <input style="
                            " type="text" class="form-control" placeholder="DD-MM-YYYY" name="date_to">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Weekdays</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12" style="margin: 10px 0px -10px 0px">
                                        <label><input type="checkbox" name="sun" checked> Sun</label>
                                        <label><input type="checkbox" name="mon" checked> Mon</label>
                                        <label><input type="checkbox" name="tue" checked> Tue</label>
                                        <label><input type="checkbox" name="wed" checked> Wed</label>
                                        <label><input type="checkbox" name="thu" checked> Thu</label>
                                        <label><input type="checkbox" name="fri" checked> Fri</label>
                                        <label><input type="checkbox" name="sat" checked> Sat</label>
                                    </div>
                                </div>

                                <div class="col-md-offset-4 col-md-8">
                                    <div class="form-group">
                                        <div id="time_durations">
                                            <button style="margin: 20px 0 10px 0" type="button" name="add" id="add" class="btn btn-primary">Add Time Durations
                                            </button>
                                            <input type="hidden" name="time_duration_count" id="time_duration_count">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <span style="color: red;">
                                        @if ($errors->getBag('default')->first('point_multiplier'))
                                            {{ $errors->getBag('default')->first('point_multiplier') }}
                                        @else
                                            <br>
                                        @endif
                                    </span>
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Point Multiplier</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="number" class="form-control" min="1" name="point_multiplier" placeholder="Put numeric value except zero" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <span style="color: red;">
                                        @if ($errors->getBag('default')->first('description'))
                                            {{ $errors->getBag('default')->first('description') }}
                                        @else
                                            <br>
                                        @endif
                                    </span>
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Point Description</label>
                                    <div class="col-md-8 col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" name="description" value="{{old('description')}}">
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
        var time_duration_count = $("#time_duration_count").val();
        var i = time_duration_count - 1;
        //if there is no previously added extra discounts
        if (isNaN(i)) {
            var i = 0;
        }
        $('#add').click(function () {
            i++;
            $('#time_durations').append(
                '<div id="row' + i + '" class="col-md-12">'
                + '<div class="col-md-4 col-sm-4 col-xs-12">'
                + '<input type="time" class="form-control" name="time_duration_from[]" value="12:00">'
                + '</div>'
                + '<div class="col-md-4 col-sm-4 col-xs-12">'
                + '<input type="time" class="form-control" name="time_duration_to[]" value="12:00">'
                + '</div>'
                + '<div class="col-md-2 col-sm-2 col-xs-12">'
                + '<button name="remove" id="' + i + '" class="btn btn-danger btn_remove">Remove</button>'
                + '</div>'
                + '</div>'
            );
            //Following script to remove newly add discount blocks
            $(document).on('click', '.btn_remove', function () {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });
        });
        //Following script to remove previous discount blocks
        $(document).on('click', '.btn_remove', function () {
            var button_id = $(this).attr("id");
            $('#row' + button_id + '').remove();
        });
    });
</script>