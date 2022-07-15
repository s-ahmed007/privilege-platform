@include('partner-dashboard.header')

<div class="container-fluid">
<div class="row bg-title">
      <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
         <h3 class="d-inline-block">{{__('partner/profile.profile_visit_stats')}}</h3>
         <h5 class="d-inline-block float-right">{{__('partner/profile.find_profile_visit')}}</h5>
      </div>
   </div>
    <div class="row">
        <div class="col-md-12">
            <form class="form-inline" action="">
                <div class="form-group">
                    <label for="peak_hour_from">{{__('partner/common.period_monthly')}}</label>
                    <select class="form-control" name="" id="ProfileVisitAnalyticsByPeriod">
                        <option value="3">3</option>
                        <option value="6">6</option>
                        <option value="12">12</option>
                    </select>
                </div>
                <div class="form-group">
                    <label></label>
                    <button type="button" class="btn btn-primary form-control" onclick="sortPeriodicProfileVisitAnalytics()" style="color:white;border-radius: 3px;">Sort</button>
                </div>
            </form>
            <br>
            <div id="profileVisitChart"></div>
        </div>
    </div>

</div>

@include('partner-dashboard.footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="{{asset('js/partner_dashboard/statistics.js')}}"></script>
<script src="{{asset('js/datepicker/moment.min.js')}}"></script>