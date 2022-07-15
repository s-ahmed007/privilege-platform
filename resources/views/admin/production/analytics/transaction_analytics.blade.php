@include('admin.production.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <form class="form-inline" action="" method="post"
          style="margin-bottom: 10px">
        {{csrf_field()}}
        <h4><b>DAILY TRANSACTIONS</b></h4>
        <div class="form-group">
            <label for="daily_analytics_date">Date</label>
            <input type="date" id="daily_analytics_date" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}">
        </div>
        <div class="form-group" style="width: 40%;">
            <input type="text" class="form-control" name="transactionAnalyticsByPartnerBranch"
                   id="dailyPartnerSearchKeyInTransaction"
                   placeholder="Partner Branch with E-mail or name" style="width: 100%;" required>
        </div>
        <div class="form-group" style="width: 20%">
            <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
            <button type="button" class="btn btn-primary form-control" onclick="sortDailyTransactionAnalytics()">Sort
            </button>
        </div>
    </form>
    <div id="dailyTransactionChart"></div>
    <br><br>
    <form class="form-inline" action="" method="post"
          style="margin-bottom: 10px">
        {{csrf_field()}}
        <h4><b>WEEKLY TRANSACTIONS</b></h4>
        <div class="form-group">
            <label for="weekly_analytics_date">Date</label>
            <input type="date" id="weekly_analytics_date" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}">
        </div>
        <div class="form-group" style="width: 40%;">
            <input type="text" class="form-control" name="transactionAnalyticsByPartnerBranch"
                   id="weeklyPartnerSearchKeyInTransaction"
                   placeholder="Partner Branch with E-mail or name" style="width: 100%;" required>
        </div>
        <div class="form-group" style="width: 20%">
            <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
            <button type="button" class="btn btn-primary form-control" onclick="sortWeeklyTransactionAnalytics()">Sort
            </button>
        </div>
    </form>
    <div id="weeklyTransactionChart"></div>
    <br><br>
    <form class="form-inline" action="" method="post"
          style="margin-bottom: 10px">
        {{csrf_field()}}
        <h4><b>PERIODIC TRANSACTIONS</b></h4>
        <div class="form-group">
            <label for="peak_hour_from">Period (Month)</label>
            <select class="form-control" name="transactionAnalyticsByPeriod" id="transactionAnalyticsByPeriod">
                <option value="3">3</option>
                <option value="6">6</option>
                <option value="12">12</option>
            </select>
        </div>
        <div class="form-group" style="width: 40%;">
            <input type="text" class="form-control" name="transactionAnalyticsByPartnerBranch"
                   id="periodicPartnerSearchKeyInTransaction"
                   placeholder="Partner Branch with E-mail or name" style="width: 100%;" required>
        </div>
        <div class="form-group" style="width: 20%">
            <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
            <button type="button" class="btn btn-primary form-control" onclick="sortPeriodicTransactionAnalytics()">Sort
            </button>
        </div>
    </form>
    <div id="periodicTransactionChart"></div>
    <br>
</div>

@include('admin.production.footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="{{asset('js/admin/analytics/transaction.js')}}"></script>
<script src="{{asset('js/datepicker/moment.min.js')}}"></script>
