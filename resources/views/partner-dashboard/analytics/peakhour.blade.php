@include('partner-dashboard.header')

<div class="container-fluid">
<div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3 class="d-inline-block">Analytics of transaction peak hour</h3>
                <h5 class="d-inline-block float-right">Here you will find the peak hour when most transactions are done </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form class="form-inline" action="">
                <div class="form-group">
                    <label for="peak_hour_from">From</label>
                    <input type="date" id="peak_hour_from" class="form-control">
{{--                    <input type="date" id="peak_hour_from" class="form-control" value="{{date('Y-m-d')}}">--}}
                </div>
                <div class="form-group">
                    <label for="peak_hour_to">To</label>
                    <input type="date" id="peak_hour_to" class="form-control">
{{--                    <input type="date" id="peak_hour_to" class="form-control" value="{{date('Y-m-d')}}">--}}
                </div>
                <div class="form-group">
                    <label></label>
                    <button type="button" class="btn btn-primary" onclick="sortPeakHour()">Sort</button>
                </div>
            </form>
            <br>
            <div id="peakHourChart"></div>
        </div>
    </div>

</div>

@include('partner-dashboard.footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{{asset('js/partner_dashboard/statistics.js')}}"></script>
<script src="{{asset('js/datepicker/moment.min.js')}}"></script>


