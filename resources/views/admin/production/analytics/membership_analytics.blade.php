@include('admin.production.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="title_left">
    </div>
    <form class="form-inline" action="">
        <h4><b>DAILY REGISTERED USERS</b></h4>
        <div class="form-group">
            <label for="peak_hour_from">Date</label>
            <input type="date" id="per_day_analytics_date" class="form-control" value="{{date('Y-m-d')}}" min="2019-11-01"
            max="{{date('Y-m-d')}}">
        </div>
{{--        <div class="form-group">--}}
{{--            <label for="peak_hour_to">To</label>--}}
{{--            <input type="date" id="peak_hour_to" class="form-control" value="{{date('Y-m-d')}}">--}}
{{--        </div>--}}
        <div class="form-group">
            <label></label>
            <button type="button" class="btn btn-primary form-control" onclick="sortDailyMembershipAnalytics()">Sort</button>
        </div>
    </form>
    <div id="perDayRegistrationChart"></div>
    <br>
    <hr><br>
    <h4><b>WEEKLY REGISTERED USERS</b></h4>
    <form class="form-inline" action="">
        <div class="form-group">
            <label for="peak_hour_from">Date</label>
            <input type="date" id="per_week_analytics_date" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}">
        </div>
        {{--        <div class="form-group">--}}
        {{--            <label for="peak_hour_to">To</label>--}}
        {{--            <input type="date" id="peak_hour_to" class="form-control" value="{{date('Y-m-d')}}">--}}
        {{--        </div>--}}
        <div class="form-group">
            <label></label>
            <button type="button" class="btn btn-primary form-control" onclick="sortWeeklyMembershipAnalytics()">Sort</button>
        </div>
    </form>
    <div id="weeklyMembershipChart"></div>
    <br>
    <hr><br>
    <h4><b>PERIODIC REGISTERED USERS</b></h4>
    <form class="form-inline" action="">
        <div class="form-group">
            <label for="peak_hour_from">Period (Month)</label>
            <select class="form-control" name="" id="user_analytics_period">
                <option value="3">3</option>
                <option value="6">6</option>
                <option value="12">12</option>
            </select>
        </div>
        <div class="form-group">
            <label></label>
            <button type="button" class="btn btn-primary form-control" onclick="sortPeriodicMembershipAnalytics()">Sort</button>
        </div>
    </form>
    <div id="periodicMembershipChart"></div>
    <br>
    <h4><b>GENDER ANALYTICS</b></h4>
    <div id="genderPieChart"></div>
    <br>
    <h4><b>REGISTRATION ANALYTICS</b></h4>
    <div id="platformWiseRegistrationChart"></div>
    <br>
    <h4><b>AGE ANALYTICS</b></h4>
    <div id="ageRangeChart"></div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <h4><b>ANDROID VERSION</b></h4>
            <div id="androidVersionChart"></div>
        </div>
        <div class="col-md-6">
            <h4><b>IOS VERSION</b></h4>
            <div id="iosVersionChart"></div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="{{asset('js/admin/analytics/membership.js')}}"></script>
<script src="{{asset('js/datepicker/moment.min.js')}}"></script>