@include('admin.production.header')

<style>
    input[type="date"].form-control {
        line-height: 20px;
    }

    input[type="time"].form-control {
        line-height: 20px;
    }
</style>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Points Customization -> {{$partner_info->partner_name}}</h3>
        </div>
    </div>
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            @if (Session::has('updated'))
                <div class="alert alert-success title_right"
                     style="text-align: center">{{ Session::get('updated') }}</div>
            @elseif(session('try_again'))
                <div class="alert alert-warning"> {{ session('try_again') }}</div>
            @endif
            <div class="x_content">
                <form class="form-horizontal form-label-left" method="post" action="{{ url('update_points/'.$partner_info->partner_account_id) }}">
                    <div class="row">
                        <!--PRICES-->
                        <div class="col-md-9">
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
                                        @if(isset($customize_point[0]->point_type) && $customize_point[0]->point_type == \App\Http\Controllers\Enum\PointCustomizeType::weekly)
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::weekly}}"
                                                    selected>Weekly
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::daily}}">Daily
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::hourly}}">
                                                Hourly
                                            </option>
                                        @elseif(isset($customize_point[0]->point_type) && $customize_point[0]->point_type == \App\Http\Controllers\Enum\PointCustomizeType::daily)
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::weekly}}">
                                                Weekly
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::daily}}"
                                                    selected>Daily
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::hourly}}">
                                                Hourly
                                            </option>
                                        @elseif(isset($customize_point[0]->point_type) && $customize_point[0]->point_type == \App\Http\Controllers\Enum\PointCustomizeType::hourly)
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::weekly}}">
                                                Weekly
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::daily}}">Daily
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::hourly}}"
                                                    selected>Hourly
                                            </option>
                                        @else
                                            <option disabled selected>Select</option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::weekly}}">
                                                Weekly
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::daily}}">Daily
                                            </option>
                                            <option value="{{\App\Http\Controllers\Enum\PointCustomizeType::hourly}}">
                                                Hourly
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                    <label class="control-label col-md-4 col-sm-6 col-xs-12">Date Duration
                                @if(isset($partner_info)):
                                    <?php $exp_date = date("d-m-Y", strtotime($partner_info->expiry_date)); ?>
                                    (exp date: {{$exp_date}})
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
                                    @if(isset($customize_point[0]->date_duration))
                                        <?php
                                        $point_details = json_decode($customize_point[0]->date_duration);
                                        $from_date = date("d-m-Y", strtotime($point_details[0]->from));
                                        ?>
                                        <input style="
                            " type="text" class="form-control" placeholder="DD-MM-YYYY" name="date_from" value="{{$from_date}}">
                                    @else
                                        <input style="
                            " type="text" class="form-control" placeholder="DD-MM-YYYY" name="date_from">
                                    @endif
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-12">
                               <span style="color: red;">
                                    @if ($errors->getBag('default')->first('date_to'))
                                       {{ $errors->getBag('default')->first('date_to') }}
                                   @else
                                       <br>
                                   @endif
                                </span>
                                    @if(isset($customize_point[0]->date_duration))
                                        <?php
                                        $point_details = json_decode($customize_point[0]->date_duration);
                                        $to_date = date("d-m-Y", strtotime($point_details[0]->to));
                                        ?>
                                        <input style="
                            " type="text" class="form-control" placeholder="DD-MM-YYYY" name="date_to" value="{{$to_date}}">
                                    @else
                                        <input style="
                            " type="text" class="form-control" placeholder="DD-MM-YYYY" name="date_to">
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4 col-sm-6 col-xs-12">Weekdays</label>
                                <div class="col-md-8 col-sm-6 col-xs-12" style="margin: 10px 0px -10px 0px">

                                    @if(isset($customize_point[0]->weekdays))
                                        <?php $point_details = json_decode($customize_point[0]->weekdays); ?>
                                        {{--$customize_point->weekdays[0]['sat']--}}
                                        <label><input type="checkbox"
                                                      name="sat" <?php if ($point_details[0]->sat == 1) {
                                                echo 'checked';
                                            }?> > Sat</label>
                                        <label><input type="checkbox"
                                                      name="sun" <?php if ($point_details[0]->sun == 1) {
                                                echo 'checked';
                                            }?> > Sun</label>
                                        <label><input type="checkbox"
                                                      name="mon" <?php if ($point_details[0]->mon == 1) {
                                                echo 'checked';
                                            }?> > Mon</label>
                                        <label><input type="checkbox"
                                                      name="tue" <?php if ($point_details[0]->tue == 1) {
                                                echo 'checked';
                                            }?> > Tue</label>
                                        <label><input type="checkbox"
                                                      name="wed" <?php if ($point_details[0]->wed == 1) {
                                                echo 'checked';
                                            }?> > Wed</label>
                                        <label><input type="checkbox"
                                                      name="thu" <?php if ($point_details[0]->thu == 1) {
                                                echo 'checked';
                                            }?> > Thu</label>
                                        <label><input type="checkbox"
                                                      name="fri" <?php if ($point_details[0]->fri == 1) {
                                                echo 'checked';
                                            }?> > Fri</label>
                                    @else
                                        <label><input type="checkbox" name="sat" checked> Sat</label>
                                        <label><input type="checkbox" name="sun" checked> Sun</label>
                                        <label><input type="checkbox" name="mon" checked> Mon</label>
                                        <label><input type="checkbox" name="tue" checked> Tue</label>
                                        <label><input type="checkbox" name="wed" checked> Wed</label>
                                        <label><input type="checkbox" name="thu" checked> Thu</label>
                                        <label><input type="checkbox" name="fri" checked> Fri</label>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-offset-4 col-md-8">
                            <div class="form-group">
                                <div id="time_durations">
                                    <button style="margin: 20px 0 10px 0" type="button" name="add" id="add" class="btn btn-primary">Add Time Durations
                                    </button>
                                    <?php for($i = 0; $i < $time_duration_count; $i++){?>
                                    <div id="row{{$i}}" class="col-md-12">
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="time" class="form-control" name="time_duration_from[]" value="{{ $time_from_array[$i] }}">
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <input type="time" class="form-control" name="time_duration_to[]" value="{{ $time_to_array[$i] }}">
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-12">
                                            <button name="remove" id="{{$i}}" class="btn btn-danger btn_remove">Remove
                                            </button>
                                        </div>
                                    </div>
                                    <?php }?>
                                    <input type="hidden" name="time_duration_count" id="time_duration_count"
                                           value="{{$time_duration_count}}">
                                </div>
                            </div>
                            </div>

                            <div class="form-group">
                                <span style="color: red;">
                                    @if ($errors->getBag('default')->first('points'))
                                        {{ $errors->getBag('default')->first('points') }}
                                    @else
                                        <br>
                                    @endif
                                </span>
                                <label class="control-label col-md-4 col-sm-6 col-xs-12">Point Multiplier</label>
                                <div class="col-md-8 col-sm-6 col-xs-12">
                                    @if(isset($customize_point[0]->point_multiplier))
                                        <input type="number" class="form-control" name="points" min="1" placeholder="Put numeric value except zero"
                                               value="{{$customize_point[0]->point_multiplier}}">
                                    @else
                                        <input type="number" class="form-control" min="1" name="points" placeholder="Put numeric value except zero" value="">
                                    @endif
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
                                    @if(isset($customize_point[0]->description))
                                        <input type="text" class="form-control" name="description"
                                               value="{{$customize_point[0]->description}}">
                                    @else
                                        <input type="text" class="form-control" name="description" value="">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                        <!--DURATIONS-->
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="ln_solid"></div>
                    <div class="form-group">
                        <div class="col-xs-12">
                                <button type="button" class="btn btn-delete deleteCustomizedPoint"
                                        data-partner-account-id="{{$partner_info->partner_account_id}}">Delete</button>
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')

<script type="text/javascript">
    $('.deleteCustomizedPoint').on('click', function (event) {

        if (confirm("Are you sure?")) {
            //fetch the partner branch id
            var partnerAccountId = $(this).attr('data-partner-account-id');
            var url = "{{ url('delete-customized-point') }}";
            url += '/' + partnerAccountId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });
</script>

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