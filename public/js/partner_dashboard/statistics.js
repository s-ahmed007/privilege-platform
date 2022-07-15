var base_url = window.location.origin;
var cur_url = window.location.href;
var page_loader = $(".page_loader");
var csrf_token = $('meta[name="csrf-token"]').attr("content");

//peak hour
function peakHourAnalytics(from = null, to = null, sort = false) {
    $.ajax({
        type: "POST",
        url: base_url + "/" + "partner/branch/get_peak_hour_data",
        async: false,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'from': from,
            'to': to,
            'sort': sort
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawPeakHourChart(data);
                });
        }
    });
}

function drawPeakHourChart(param) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Hour');
    data.addColumn('number', 'Transactions');
    $.each(param, function (index, row) {
        if (row != null){
            var hrs = moment(index, "hh").format('h [\n] A');
            data.addRow([hrs, row]);
        }
    });

    var options = {
        title: 'Overall Peak Hours',
        is3D: true,
        colors: ['#01ace6'],
        height: 500
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('peakHourChart'));
    chart.draw(data, options);
}

//sorting peak hour
function sortPeakHour() {
    var from = $("#peak_hour_from").val();
    var to = $("#peak_hour_to").val();
    peakHourAnalytics(from, to, true);
}
function resetPeakHourAnalytics() {
    $("#peak_hour_from").val("");
    $("#peak_hour_to").val("");
    peakHourAnalytics();
}

//call function
if(cur_url == base_url+"/partner/branch/dashboard"){
    peakHourAnalytics();
}

//profiile visit
function profileVisitAnalytics() {
    var period = $("#ProfileVisitAnalyticsByPeriod").val();
    $.ajax({
        type: "POST",
        url: base_url + "/" + "partner/branch/get_profile_visit_data",
        async: false,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'period': period
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawProfileVisitChart(data);
                });
        }
    });
}

function drawProfileVisitChart(param) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Month');
    data.addColumn('number', 'Visit');

    for (var i=param.length-1; i>=0; i--){
        var month = param[i].month+" '"+param[i].year;
        data.addRow([month,  param[i].visit_count]);
    }

    var options = {
        is3D: true,
        colors: ['#01ace6'],
        height: 500,
        pointSize: 10,
        pointShape: {type: 'circle'},
    };

    var chart = new google.visualization.AreaChart(document.getElementById('profileVisitChart'));
    chart.draw(data, options);
}

function sortPeriodicProfileVisitAnalytics(){
    "use strict";
    profileVisitAnalytics();
}

if(cur_url == base_url+"/partner/branch/profile_visit"){
    profileVisitAnalytics();
}

//periodic transaction analytics
function periodicTransactionAnalytics() {
    "use strict";
    var period = $("#transactionAnalyticsByPeriod").val();

    $.ajax({
        type: "POST",
        url: base_url + "/" + "partner/branch/get_transaction_statistics",
        async: false,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'period': period
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
    data.addColumn('number', 'Royalty Users');

    for (var i=param.length-1; i>=0; i--){
        var month = param[i].month+" '"+param[i].year;
        data.addRow([month,  param[i].trial_count + param[i].premium_count]);
    }

    var options = {
        hAxis: {title: 'Month', titleTextStyle: {color: '#333'}},
        vAxis: {minValue: 0},
        pointSize: 10,
        pointShape: {type: 'circle'},
        colors: ['#01ace6'],
        height: 500
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('periodicTransactionChart'));
    chart.draw(data, options);

    page_loader.css('display', 'none');
}
//sorting periodic transaction analytics
function sortPeriodicTransactionAnalytics(){
    "use strict";
    periodicTransactionAnalytics();
}

if(cur_url == base_url+"/partner/branch/transaction_statistics"){
    periodicTransactionAnalytics();
}