@include('admin.production.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="title_left">
    </div>
    <a href="{{url('admin/all_partner_visits')}}" class="btn btn-primary" style="float: right;">Show All</a>
    <form class="form-inline"
          style="margin-bottom: 10px">
        {{csrf_field()}}
        <h4><b>PARTNER PROFILE VISIT</b></h4>
        <div class="form-group">
            <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
            <select class="form-control" id="visitAnalyticsByYear" name="visitAnalyticsByYear" required="required">
                @for($i = 2018; $i <= date('Y'); $i++)
                    <option value="{{$i}}" {{date('Y') == $i ? 'selected' : ''}}>{{$i}}</option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
            <select class="form-control" id="visitAnalyticsByMonth" name="visitAnalyticsByMonth">
                <option value="01" {{date('m') == '01' ? 'selected' : ''}}>January</option>
                <option value="02" {{date('m') == '02' ? 'selected' : ''}}>February</option>
                <option value="03" {{date('m') == '03' ? 'selected' : ''}}>March</option>
                <option value="04" {{date('m') == '04' ? 'selected' : ''}}>April</option>
                <option value="05" {{date('m') == '05' ? 'selected' : ''}}>May</option>
                <option value="06" {{date('m') == '06' ? 'selected' : ''}}>June</option>
                <option value="07" {{date('m') == '07' ? 'selected' : ''}}>July</option>
                <option value="08" {{date('m') == '08' ? 'selected' : ''}}>August</option>
                <option value="09" {{date('m') == '09' ? 'selected' : ''}}>September</option>
                <option value="10" {{date('m') == '10' ? 'selected' : ''}}>October</option>
                <option value="11" {{date('m') == '11' ? 'selected' : ''}}>November</option>
                <option value="12" {{date('m') == '12' ? 'selected' : ''}}>December</option>
            </select>
        </div>
        <div class="form-group" style="width: 40%;">
            <input type="text" class="form-control" name="visitAnalyticsByPartner" id="partnerSearchKeyInVisit"
                   placeholder="Partner with name or E-mail" style="width: 100%;">
        </div>
        <div class="form-group" style="width: 20%">
            <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
            <button type="button" class="btn btn-primary form-control" onclick="sortRbdPartnerVisitsAnalytics()">Sort</button>
        </div>
    </form>
    <div id="partnerVisitsChart"></div>
    <br>
    <h4><b>PEAK HOURS</b></h4>
    <form class="form-inline" action="">
        <div class="form-group">
            <label for="peak_hour_from">From</label>
            <input type="date" id="peak_hour_from" class="form-control" value="{{date('Y-m-d')}}">
        </div>
        <div class="form-group">
            <label for="peak_hour_to">To</label>
            <input type="date" id="peak_hour_to" class="form-control" value="{{date('Y-m-d')}}">
        </div>
        <div class="form-group">
            <label></label>
            <button type="button" class="btn btn-primary form-control" onclick="sortPeakHour()">Sort</button>
        </div>
    </form>
    <div id="peakHourChart"></div>
</div>

@include('admin.production.footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="{{asset('js/admin/analytics/statistics.js')}}"></script>
<script src="{{asset('js/datepicker/moment.min.js')}}"></script>
