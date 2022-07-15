var base_url = window.location.origin;
var cur_url = window.location.href;
var csrf_token = $('meta[name="csrf-token"]').attr("content");

//daily transaction analytics
function dailyTransactionAnalytics() {
    "use strict";
    var date = $("#daily_analytics_date").val();
    var partner = $("#dailyPartnerSearchKeyInTransaction").val();
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/daily_user_transaction_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'date': date,
            'partner': partner
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawDailyTransactionChart(data);
                });
        }
    });
}

function drawDailyTransactionChart(param) {
    'use strict';
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Hour');
    // data.addColumn('number', 'Trial');
    data.addColumn('number', 'Member');
    // data.addColumn('number', 'All');
    $.each(param, function (index, row) {
        var hrs =  moment(index, "hh").format('h\tA');
        data.addRow([hrs, row.premium_user]);
        // data.addRow([hrs, row.trial_user, row.premium_user, row.all]);
    });
    var options = {
        vAxis: {minValue: 0},
        is3D: true,
        pointSize: 10,
        pointShape: {type: 'circle'},
        colors: ['#FFC82C', '#01ace6', '#13CE66']
    };

    var chart = new google.visualization.AreaChart(document.getElementById('dailyTransactionChart'));
    chart.draw(data, options);
    stopPageLoader();
}

//weekly transaction analytics
function weeklyTransactionAnalytics() {
    "use strict";
    var date = $("#weekly_analytics_date").val();
    var partner = $("#weeklyPartnerSearchKeyInTransaction").val();
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/weekly_user_transaction_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'date': date,
            'partner': partner
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['bar']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawWeeklyTransactionChart(data);
                });
        }
    });
}

function drawWeeklyTransactionChart(param) {
    'use strict';
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Date');
    // data.addColumn('number', 'Trial');
    data.addColumn('number', 'Member');
    // data.addColumn('number', 'All');
    for (var i=7; i>=1; i--){
        // var all = param[i].trial_count + param[i].premium_count;
        data.addRow([param[i].date, param[i].premium_count]);
    }
    var options = {
        bars: 'vertical',
        colors: ['#FFC82C', '#01ace6', '#13CE66']
    };

    var chart = new google.charts.Bar(document.getElementById('weeklyTransactionChart'));
    chart.draw(data, options);
    stopPageLoader();
}

//periodic transaction analytics
function periodicTransactionAnalytics() {
    "use strict";
    var period = $("#transactionAnalyticsByPeriod").val();
    var partner = $("#periodicPartnerSearchKeyInTransaction").val();

    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/periodic_user_transaction_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'period': period,
            'partner': partner
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawPeriodicTransactionChart(data);
                });
        }
    });
}

function drawPeriodicTransactionChart(param) {
    "use strict";
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Month');
    // data.addColumn('number', 'Trial');
    data.addColumn('number', 'Member');
    // data.addColumn('number', 'All');

    for (var i=param.length-1; i>=0; i--){
        // var all = param[i].trial_count + param[i].premium_count;
        var month = param[i].month+" '"+param[i].year;
        data.addRow([month, param[i].premium_count]);
    }

    var options = {
        hAxis: {title: 'Month', titleTextStyle: {color: '#333'}},
        vAxis: {minValue: 0},
        pointSize: 10,
        pointShape: {type: 'circle'},
        colors: ['#FFC82C','#01ace6', '#13CE66']
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('periodicTransactionChart'));
    chart.draw(data, options);
    stopPageLoader();
}

//sorting daily transaction analytics
function sortDailyTransactionAnalytics(){
    "use strict";
    startPageLoader();
    dailyTransactionAnalytics();
}
//sorting weekly transaction analytics
function sortWeeklyTransactionAnalytics(){
    "use strict";
    startPageLoader();
    weeklyTransactionAnalytics();
}
//sorting periodic transaction analytics
function sortPeriodicTransactionAnalytics(){
    "use strict";
    startPageLoader();
    periodicTransactionAnalytics();
}

//partner search suggestion
$(function () {
    "use strict";
    $("#dailyPartnerSearchKeyInTransaction").autocomplete({
        source: base_url + '/partnerByKey',
        autoFocus: true,
        delay: 500
    });
    $("#weeklyPartnerSearchKeyInTransaction").autocomplete({
        source: base_url + '/partnerByKey',
        autoFocus: true,
        delay: 500
    });
    $("#periodicPartnerSearchKeyInTransaction").autocomplete({
        source: base_url + '/partnerByKey',
        autoFocus: true,
        delay: 500
    });
});

startPageLoader();
periodicTransactionAnalytics();
dailyTransactionAnalytics();
weeklyTransactionAnalytics();