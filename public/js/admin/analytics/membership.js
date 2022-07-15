var base_url = window.location.origin;
var cur_url = window.location.href;
var csrf_token = $('meta[name="csrf-token"]').attr("content");

//daily registration analytics
function perDayUserAnalytics() {
    'use strict';
    var date = $("#per_day_analytics_date").val();
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/get_per_day_user_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'date': date
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawDailyMembershipChart(data);
                });
        }
    });
}

function drawDailyMembershipChart(param) {
    'use strict';
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Hour');
    // data.addColumn('number', 'Trial');
    data.addColumn('number', 'Member');
    // data.addColumn('number', 'All');
    $.each(param, function (index, row) {
        var hrs = moment(index, "hh").format('h\tA');
        data.addRow([hrs, row.premium_user]);
        // data.addRow([hrs, row.trial_user, row.premium_user, row.all]);
    });

    var options = {
        bars: 'vertical', // Required for Material Bar Charts.
        is3D: true,
        pointSize: 10,
        pointShape: {type: 'circle'},
        colors: ['#FFC82C', '#01ace6', '#13CE66'],
        height: 500
    };

    var chart = new google.visualization.AreaChart(document.getElementById('perDayRegistrationChart'));
    chart.draw(data, options);
    stopPageLoader();

}

//daily registration analytics
function weeklyUserAnalytics() {
    'use strict';
    var date = $("#per_week_analytics_date").val();
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/get_weekly_user_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'date': date
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawWeeklyMembershipChart(data);
                });
        }
    });
}

function drawWeeklyMembershipChart(param) {
    'use strict';
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Date');
    // data.addColumn('number', 'Trial');
    data.addColumn('number', 'Member');
    // data.addColumn('number', 'All');
    for (var i = 7; i >= 1; i--) {
        // var all = param[i].trial_count + param[i].premium_count;
        // data.addRow([param[i].date, param[i].trial_count, param[i].premium_count, all]);
        data.addRow([param[i].date, param[i].premium_count]);
    }
    var options = {
        hAxis: {title: 'Date', titleTextStyle: {color: '#333'}},
        colors: ['#FFC82C', '#01ace6', '#13CE66'],
        height: 500
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('weeklyMembershipChart'));
    chart.draw(data, options);
    stopPageLoader();

}

//daily registration analytics
function periodicUserAnalytics() {
    'use strict';
    var period = $("#user_analytics_period").val();
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/get_periodic_user_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'period': period
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawPeriodicMembershipChart(data);
                });
        }
    });
}

function drawPeriodicMembershipChart(param) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Month');
    // data.addColumn('number', 'Trial');
    data.addColumn('number', 'Member');
    // data.addColumn('number', 'All');

    for (var i = param.length - 1; i >= 0; i--) {
        // var all = param[i].trial_count + param[i].premium_count;
        var month = param[i].month + " '" + param[i].year;
        data.addRow([month, param[i].premium_count]);
    }

    var options = {
        hAxis: {title: 'Month', titleTextStyle: {color: '#333'}},
        height: 500,
        colors: ['#FFC82C', '#01ace6', '#13CE66'],
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('periodicMembershipChart'));
    chart.draw(data, options);
    stopPageLoader();

}

// gender analytics
function genderAnalytics() {
    'use strict';
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/get_gender_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawGenderChart(data);
                });
        }
    });
}

function drawGenderChart(param) {
    var data = google.visualization.arrayToDataTable([
        ["Gender", "Percentage", {role: "style"}],
        ["Male", param.male_percentage, '#01ace6'],
        ["Female", param.female_percentage, '#13CE66']
    ]);

    var options = {
        height: 500,
        colors: ['#01ace6', '#13CE66'],
        is3D: true,
    };

    var chart = new google.visualization.BarChart(document.getElementById('genderPieChart'));
    chart.draw(data, options);
}

// platform wise registration analytics
function platformWiseRegAnalytics() {
    'use strict';
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/get_platform_wise_reg_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawPlatformWiseRegChart(data);
                });
        }
    });
}

function drawPlatformWiseRegChart(param) {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Platform');
    data.addColumn('number', 'Web');
    data.addColumn('number', 'Android');
    data.addColumn('number', 'iOS');

    data.addRow(['', param.web_percentage, param.android_percentage, param.ios_percentage]);


    var options = {
        height: 500,
        colors: ['#FFC82C', '#01ace6', '#13CE66'],
        is3D: true,
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('platformWiseRegistrationChart'));
    chart.draw(data, options);
}

// age analytics
function ageAnalytics() {
    'use strict';
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/get_age_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () {
                    drawAgeChart(data);
                });
        }
    });
}

function drawAgeChart(param) {

    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Range');
    data.addColumn('number', 'All');
    data.addColumn('number', 'Male');
    data.addColumn('number', 'Female');
    data.addColumn('number', 'Undefined Gender');

    data.addRow(['13-17', param.all['13-17'], param.male['13-17'], param.female['13-17'], param.undefined_gender['13-17']]);
    data.addRow(['18-24', param.all['18-24'], param.male['18-24'], param.female['18-24'], param.undefined_gender['18-24']]);
    data.addRow(['25-34', param.all['25-34'], param.male['25-34'], param.female['25-34'], param.undefined_gender['25-34']]);
    data.addRow(['35-44', param.all['35-44'], param.male['35-44'], param.female['35-44'], param.undefined_gender['35-44']]);
    data.addRow(['45-54', param.all['45-54'], param.male['45-54'], param.female['45-54'], param.undefined_gender['45-54']]);
    data.addRow(['55-64', param.all['55-64'], param.male['55-64'], param.female['55-64'], param.undefined_gender['55-64']]);
    data.addRow(['65+', param.all['65+'], param.male['65+'], param.female['65+'], param.undefined_gender['65+']]);

    var options = {
        height: 500,
        colors: ['#FFC82C', '#13CE66', '#01ace6', '#A23C3C'],
        is3D: true,
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('ageRangeChart'));
    chart.draw(data, options);
    stopPageLoader();

}

// age analytics
function appVersionAnalytics() {
    'use strict';
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/get_app_version_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () {
                    drawAppVersionChart(data);
                });
        }
    });
}

function drawAppVersionChart(param) {
    //android chart
    var androidData = google.visualization.arrayToDataTable([
        ['Type', 'User Number'],
        ['Up-to-Date', param.android.running_version],
        ['Out-of-Date', param.android.old_version]
    ]);
    var androidOptions = {
        title: 'Running version ' + param.android.running_version_code,
        height: 500,
        colors: ['#13CE66', '#b3504e'],
        is3D: true,
    };

    var androidChart = new google.visualization.PieChart(document.getElementById('androidVersionChart'));
    androidChart.draw(androidData, androidOptions);

    // iOS chart
    var iosData = google.visualization.arrayToDataTable([
        ['Type', 'User Number'],
        ['Up-to-Date', param.ios.running_version],
        ['Out-of-Date', param.ios.old_version]
    ]);
    var iosOptions = {
        title: 'Running version ' + param.ios.running_version_code,
        height: 500,
        colors: ['#13CE66', '#b3504e'],
        is3D: true,
    };

    var iosChart = new google.visualization.PieChart(document.getElementById('iosVersionChart'));
    iosChart.draw(iosData, iosOptions);
    stopPageLoader();

}

//sorting user registration analytics
function sortDailyMembershipAnalytics() {
    'use strict';
    startPageLoader();
    perDayUserAnalytics();
}

function sortWeeklyMembershipAnalytics() {
    'use strict';
    startPageLoader();
    weeklyUserAnalytics();
}

function sortPeriodicMembershipAnalytics() {
    'use strict';
    startPageLoader();
    periodicUserAnalytics();
}

//call function page wise
startPageLoader();
perDayUserAnalytics();
weeklyUserAnalytics();
periodicUserAnalytics();
genderAnalytics();
platformWiseRegAnalytics();
ageAnalytics();
appVersionAnalytics();