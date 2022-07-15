@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')

<div class="right_col" role="main">
    <div>
        <div class="heading">
            <h3>Edit Branch Information</h3>
        </div>
        <div class="bar-long"></div>
        @if(session('try_again'))
            <div class="title_right alert alert-warning"
                 style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        @if (isset($branchInfo))
            <form action="{{ url('branchEditDone/'.$branchInfo->id) }}" class="form-horizontal" method="post">
                <div>
                    <div class="heading">
                        <h3>Basic Info</h3>
                    </div>
                    <div class="bar">
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-4 col-xs-12" for="name">Email:</label>
                            <span style="color: red;">
                           @if ($errors->getBag('default')->first('branch_email'))
                                    {{ $errors->getBag('default')->first('branch_email') }}
                                @endif
                        </span>
                            <div class="col-md-9 col-sm-6 col-xs-12">
                                <input type="text" name="branch_email" class="form-control" id="email"
                                       value="{{ $branchInfo->partner_email }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-4 col-xs-12" for="name">Contact:</label>
                            <span style="color: red;">
                           @if ($errors->getBag('default')->first('branch_mobile'))
                                    {{ $errors->getBag('default')->first('branch_mobile') }}
                                @endif
                        </span>
                            <div class="col-md-9 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <input type="text" name="branch_mobile" class="form-control"
                                           placeholder="phone number"
                                           value="{{ $branchInfo->partner_mobile }}" id="phone_number"
                                           maxlength="15">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-4 col-xs-12" for="name">Address:</label>
                            <span style="color: red;">
                            @if ($errors->getBag('default')->first('branch_address'))
                                    {{ $errors->getBag('default')->first('branch_address') }}
                                @endif
                        </span>
                            <div class="col-md-9 col-sm-6 col-xs-12">
                                <input type="text" name="branch_address" class="form-control" id="email"
                                       value="{{ $branchInfo->partner_address }}">
                            </div>
                        </div>
                        <div class="form-group">
                       <span style="color: #E74430;">
                           @if ($errors->getBag('default')->first('division'))
                               {{ $errors->getBag('default')->first('division') }}
                           @endif
                        </span>
                            <label class="control-label col-md-3 col-sm-4 col-xs-12">Select Division</label>
                            <div class="col-md-9 col-sm-6 col-xs-12">
                                <select class="form-control" name="division">
                                    <option <?php if ($branchInfo->partner_division == '') echo 'selected disabled';?>>
                                        -----
                                    </option>
                                    @foreach($all_divs as $division)
                                        <option
                                            <?php if ($branchInfo->partner_division == $division->name) echo 'selected';?>
                                            value="{{$division->name}}">{{$division->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                       <span style="color: #E74430;">
                           @if ($errors->getBag('default')->first('area'))
                               {{ $errors->getBag('default')->first('area') }}
                           @endif
                        </span>
                            <label class="control-label col-md-3 col-sm-4 col-xs-12">Select Area:</label>
                            <div class="col-md-9 col-sm-6 col-xs-12">
                                <select class="form-control" name="area">
                                    <?php if ($branchInfo->partner_area == '') echo ' <option selected disabled>-----</option>';?>
                                    @foreach($all_areas as $area)
                                        <option
                                            <?php if ($branchInfo->partner_area == $area['area_name']) echo 'selected';?>
                                            value="{{$area['area_name']}}">{{$area['area_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-4 col-xs-12" for="name">Location:</label>
                            <span style="color: red;">
                           @if ($errors->getBag('default')->first('branch_location'))
                                    {{ $errors->getBag('default')->first('branch_location') }}
                                @endif
                       </span>
                            <div class="col-md-9 col-sm-6 col-xs-12">
                                <input type="text" name="branch_location" class="form-control" id="email"
                                       value="{{ $branchInfo->partner_location }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-4 col-xs-12">Longitude:</label>
                            <span style="color: red;">
                           @if ($errors->getBag('default')->first('longitude'))
                                    {{ $errors->getBag('default')->first('longitude') }}
                                @endif
                       </span>
                            <div class="col-md-9 col-sm-6 col-xs-12">
                                <input type="text" class="form-control" value="{{$branchInfo->longitude}}"
                                       name="longitude">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-4 col-xs-12">Latitude:</label>
                            <span style="color: red;">
                           @if ($errors->getBag('default')->first('latitude'))
                                    {{ $errors->getBag('default')->first('latitude') }}
                                @endif
                       </span>
                            <div class="col-md-9 col-sm-6 col-xs-12">
                                <input type="text" class="form-control" value="{{$branchInfo->latitude}}"
                                       name="latitude">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Main Branch:</label>
                            <span style="color: red;">
                           @if ($errors->getBag('default')->first('main_branch'))
                                    {{ $errors->getBag('default')->first('main_branch') }}
                                @endif
                       </span>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                @if($branchInfo['main_branch'] == 1)
                                    <i class="check-icon" style="font-size: 1.5em; color: forestgreen;"></i>
                                    <input type="hidden" class="form-control" name="main_branch" value="1">
                                @else
                                    <input type="checkbox" class="form-control" name="main_branch"
                                           onchange="doalert(this)">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <!-- Opening Hours -->
                        <div>
                            <div class="heading">
                                <h3>Branch Opening Hours</h3>
                            </div>
                            <div class="bar">
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 col-sm-6 col-xs-12">SATURDAY</label>
                                <div class="col-md-8 col-sm-6 col-xs-12">
                                    @if ($errors->getBag('default')->first('sat'))
                                        <div style="color: red">Field is required</div>
                                    @endif
                                    <input type="text" class="form-control" value="{{$branchInfo->openingHours->sat}}"
                                           name="sat">
                                </div>
                                <label class="col-md-4 col-sm-6 col-xs-12">SUNDAY</label>
                                <div class="col-md-8 col-sm-6 col-xs-12">
                                    @if ($errors->getBag('default')->first('sun'))
                                        <div style="color: red">Field is required</div>
                                    @endif
                                    <input type="text" class="form-control" value="{{$branchInfo->openingHours->sun}}"
                                           name="sun" value="{{old('sun')}}">
                                </div>
                                <label class="col-md-4 col-sm-6 col-xs-12">MONDAY</label>
                                <div class="col-md-8 col-sm-6 col-xs-12">
                                    @if ($errors->getBag('default')->first('mon'))
                                        <div style="color: red">Field is required</div>
                                    @endif
                                    <input type="text" class="form-control" value="{{$branchInfo->openingHours->mon}}"
                                           name="mon" value="{{old('mon')}}">
                                </div>
                                <label class="col-md-4 col-sm-6 col-xs-12">TUESDAY</label>
                                <div class="col-md-8 col-sm-6 col-xs-12">
                                    @if ($errors->getBag('default')->first('tues'))
                                        <div style="color: red">Field is required</div>
                                    @endif
                                    <input type="text" class="form-control" value="{{$branchInfo->openingHours->tue}}"
                                           name="tues" value="{{old('tues')}}">
                                </div>
                                <label class="col-md-4 col-sm-6 col-xs-12">WEDNESDAY</label>
                                <div class="col-md-8 col-sm-6 col-xs-12">
                                    @if ($errors->getBag('default')->first('wed'))
                                        <div style="color: red">Field is required</div>
                                    @endif
                                    <input type="text" class="form-control" value="{{$branchInfo->openingHours->wed}}"
                                           name="wed" value="{{old('wed')}}">
                                </div>
                                <label class="col-md-4 col-sm-6 col-xs-12">THURSDAY</label>
                                <div class="col-md-8 col-sm-6 col-xs-12">
                                    @if ($errors->getBag('default')->first('thu'))
                                        <div style="color: red">Field is required</div>
                                    @endif
                                    <input type="text" class="form-control" value="{{$branchInfo->openingHours->thurs}}"
                                           name="thu" value="{{old('thu')}}">
                                </div>
                                <label class="col-md-4 col-sm-6 col-xs-12">FRIDAY</label>
                                <div class="col-md-8 col-sm-6 col-xs-12">
                                    @if ($errors->getBag('default')->first('fri'))
                                        <div style="color: red">Field is required</div>
                                    @endif
                                    <input type="text" class="form-control" value="{{$branchInfo->openingHours->fri}}"
                                           name="fri" value="{{old('fri')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <!-- Facilities -->
                        <div>
                            <div class="heading">
                                <h3>Branch Facilities</h3>
                            </div>
                            <div class="bar">
                            </div>
                            <!-- GET PARTNER FACILITIES -->
                            <?php
                            $card_payment = 0;
                            $kids_area = 0;
                            $outdoor_seating = 0;
                            $smoking_area = 0;
                            $reservation = 0;
                            $wifi = 0;
                            $concierge = 0;
                            $online_booking = 0;
                            $seating_area = 0;

                            if ($branchInfo->facilities->card_payment == 1) {
                                $card_payment = 1;
                            }

                            if ($branchInfo->facilities->kids_area == 1) {
                                $kids_area = 1;
                            }

                            if ($branchInfo->facilities->outdoor_seating == 1) {
                                $outdoor_seating = 1;
                            }

                            if ($branchInfo->facilities->smoking_area == 1) {
                                $smoking_area = 1;
                            }

                            if ($branchInfo->facilities->reservation == 1) {
                                $reservation = 1;
                            }

                            if ($branchInfo->facilities->wifi == 1) {
                                $wifi = 1;
                            }

                            if ($branchInfo->facilities->concierge == 1) {
                                $concierge = 1;
                            }

                            if ($branchInfo->facilities->online_booking == 1) {
                                $online_booking = 1;
                            }

                            if ($branchInfo->facilities->seating_area == 1) {
                                $seating_area = 1;
                            }

                            ?>
                            <div class="form-group">
                                <div class="row" style="text-align: center">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        @if($branchInfo->account->info->partner_category == '3')
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="card_payment"
                                                    <?php if ($card_payment == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Accepts Card Payment</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="kids_area"
                                                    <?php if ($kids_area == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Kids Area</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="outdoor_seating"
                                                    <?php if ($outdoor_seating == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Outdoor Seating</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="smoking_area"
                                                    <?php if ($smoking_area == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Smoking Area</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="reservation"
                                                    <?php if ($reservation == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Takes Reservations</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="wifi"
                                                    <?php if ($wifi == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Wi-Fi</label>
                                            </div>
                                        @elseif($branchInfo->account->info->partner_category == '6')
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="card_payment"
                                                    <?php if ($card_payment == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Accepts Card Payment</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="wifi"
                                                    <?php if ($wifi == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Wi-Fi</label>
                                            </div>
                                        @elseif($branchInfo->account->info->partner_category == '1')
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="card_payment"
                                                    <?php if ($card_payment == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Accepts Card Payment</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="reservation"
                                                    <?php if ($reservation == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Takes Reservations</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="wifi"
                                                    <?php if ($wifi == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Wi-Fi</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="seating_area"
                                                    <?php if ($seating_area == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Seating Area</label>
                                            </div>
                                        @elseif($branchInfo->account->info->partner_category == '5')
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="card_payment"
                                                    <?php if ($card_payment == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Accepts Card Payment</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="reservation"
                                                    <?php if ($reservation == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Takes Reservations</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="wifi"
                                                    <?php if ($wifi == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Wi-Fi</label>
                                            </div>
                                        @elseif($branchInfo->account->info->partner_category == '2')
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="card_payment"
                                                    <?php if ($card_payment == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Accepts Card Payment</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="smoking_area"
                                                    <?php if ($smoking_area == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Smoking Area</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="wifi"
                                                    <?php if ($wifi == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Wi-Fi</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="online_booking"
                                                    <?php if ($online_booking == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Online Booking</label>
                                            </div>
                                        @else
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="card_payment"
                                                    <?php if ($card_payment == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Accepts Card Payment</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="reservation"
                                                    <?php if ($reservation == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Takes Reservations</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="wifi"
                                                    <?php if ($wifi == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Wi-Fi</label>
                                            </div>
                                            <div class="checkbox">
                                                <label><input type="checkbox" class="facility" name="concierge"
                                                    <?php if ($concierge == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                                    Concierge Available</label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <p class="center">
                        <button type="submit" class="btn btn-activate pull-right">Submit</button>
                    </p>
                </div>
            </form>
    </div>
    @endif
</div>

@include('partner-admin.production.footer')
<script>
    function doalert(checkboxElem) {
        if (checkboxElem.checked) {
            alert("You are going to change the main branch of this partner.");
        }
    }
</script>