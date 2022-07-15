var base_url = window.location.origin;
var cur_url = window.location.href;
var csrf_token = $('meta[name="csrf-token"]').attr("content");

//counter analytics
function allCounters() {
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/allCounters",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {_token: csrf_token},
        success: function (data) {
            //all counters
            // $(".guest_count").text(data.guest_user);
            // $(".card_holder_count").text(data.card_user);
            // $(".trial_count").text(data.trial_user);
            $(".all_user_count").text(data.allCustomers);
            $(".all_partner_count").text(data.allPartners);
            $(".all_branch_count").text(data.allBranches);
            $(".inactive_user_count").text(data.inactive_user);
            $(".active_user_count").text(data.active_user);
            // $(".expiring_user_count").text(data.expiring_user);
            // $(".expired_user_count").text(data.expired_user);
            $(".transaction_count").text(data.total_transaction);
            $(".offers_count").text(data.total_offers);
            $(".review_count").text(data.total_reviews);
            $(".verified_email").text(data.verified_email+"%");
            $(".completed_profile").text(data.completed_profile+"%");
        }
    });
}

//visit analytics
function visitAnalytics(year = null, month = null, partner = null, sort = false)
{
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/visit_analytics",
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'year': year,
            'month': month,
            'partner': partner,
            'sort': sort
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['bar']});
            google.charts.setOnLoadCallback(
                // Anonymous function that calls drawChart1 and drawChart2
                function () {
                    drawVisitChart(data);
                }
            );
        }
    });
}

function drawVisitChart(param) {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Partner');
    data.addColumn('number', 'Web');
    data.addColumn('number', 'Android');
    data.addColumn('number', 'iOS');
    param.forEach(function (row) {
        data.addRow([row.partner, row.web, row.android_app, row.ios_app]);
    });

    var options = {
        titleTextStyle: {bold: true, color: '#0000000'},
        bars: 'vertical', // Required for Material Bar Charts.
        vAxis: {format: '########'},
        is3D: true,

    };
    var chart = new google.charts.Bar(document.getElementById('partnerVisitsChart'));
    chart.draw(data, google.charts.Bar.convertOptions(options));
    stopPageLoader();
}

//transaction analytics
function transactionAnalytics(year = null, partner = null, sort = false) {
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/transaction_analytics",
        async: false,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'year': year,
            'partner': partner,
            'sort': sort
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawTransactionChart(data);
                });
        }
    });
}

function drawTransactionChart(param) {
    var data = google.visualization.arrayToDataTable([
        ['Month', 'Premium', 'Trial', 'All'],
        ['Jan', param.Jan.card, param.Jan.trial, param.Jan.all],
        ['Feb', param.Feb.card, param.Feb.trial, param.Feb.all],
        ['Mar', param.Mar.card, param.Mar.trial, param.Mar.all],
        ['Apr', param.Apr.card, param.Apr.trial, param.Apr.all],
        ['May', param.May.card, param.May.trial, param.May.all],
        ['Jun', param.Jun.card, param.Jun.trial, param.Jun.all],
        ['Jul', param.Jul.card, param.Jul.trial, param.Jul.all],
        ['Aug', param.Aug.card, param.Aug.trial, param.Aug.all],
        ['Sep', param.Sep.card, param.Sep.trial, param.Sep.all],
        ['Oct', param.Oct.card, param.Oct.trial, param.Oct.all],
        ['Nov', param.Nov.card, param.Nov.trial, param.Nov.all],
        ['Dec', param.Dec.card, param.Dec.trial, param.Dec.all]

    ]);

    var options = {
        title: 'Transactions of Users',
        hAxis: {title: 'Month', titleTextStyle: {color: '#333'}},
        vAxis: {minValue: 0},
        pointSize: 10,
        pointShape: {type: 'circle'}


    };

    var chart = new google.visualization.AreaChart(document.getElementById('transactionChart'));
    chart.draw(data, options);
}

//registration analytics
function registrationAnalytics(year = null, sort = false) {
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/registration_analytics",
        async: false,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'year': year,
            'sort': sort
        },
        success: function (data) {
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(
                function () { // Anonymous function that calls drawChart1 and drawChart2
                    drawRegistrationChart(data);
                });
        }
    });
}

function drawRegistrationChart(param) {
    var data = google.visualization.arrayToDataTable([
        ['Month', 'Trial', 'Card User'],//'Guest',
        ['Jan', param.Jan.trial_user, param.Jan.card_user],//param.Jan.guest_user,
        ['Feb', param.Feb.trial_user, param.Feb.card_user],//param.Feb.guest_user,
        ['Mar', param.Mar.trial_user, param.Mar.card_user],//param.Mar.guest_user,
        ['Apr', param.Apr.trial_user, param.Apr.card_user],//param.Apr.guest_user,
        ['May', param.May.trial_user, param.May.card_user],//param.May.guest_user,
        ['Jun', param.Jun.trial_user, param.Jun.card_user],//param.Jun.guest_user,
        ['Jul', param.Jul.trial_user, param.Jul.card_user],//param.Jul.guest_user,
        ['Aug', param.Aug.trial_user, param.Aug.card_user],//param.Aug.guest_user,
        ['Sep', param.Sep.trial_user, param.Sep.card_user],//param.Sep.guest_user,
        ['Oct', param.Oct.trial_user, param.Oct.card_user],//param.Oct.guest_user,
        ['Nov', param.Nov.trial_user, param.Nov.card_user],//param.Nov.guest_user,
        ['Dec', param.Dec.trial_user, param.Dec.card_user]//param.Dec.guest_user,
    ]);

    var options = {
        title: 'Analytics of registered Users',
        hAxis: {title: 'Month', titleTextStyle: {color: '#333'}},
        bars: 'vertical', // Required for Material Bar Charts.
        is3D: true,
        pointSize: 10,
        pointShape: {type: 'circle'}
    };

    var chart = new google.visualization.AreaChart(document.getElementById('registrationChart'));
    chart.draw(data, options);
}

//peak hour analytics
function peakHourAnalytics(from = null, to = null, sort = false) {
    $.ajax({
        type: "POST",
        url: base_url + "/" + "admin/peak_hour_analytics",
        async: true,
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
    data.addColumn('number', 'Web');
    data.addColumn('number', 'Android');
    data.addColumn('number', 'Ios');
    $.each(param, function (index, row) {
        var hrs = moment(index, "hh").format('h\tA');
        data.addRow([hrs, row.web, row.android, row.ios]);
    });

    var options = {
        is3D: true,
        pointSize: 10,
        pointShape: {type: 'circle'}
    };

    var chart = new google.visualization.AreaChart(document.getElementById('peakHourChart'));
    chart.draw(data, options);
    stopPageLoader();
}

function customerLeaderBoard() {
    var year = $("#userLeadYear").val();
    var month = $("#userLeadMonth").val();
    var url = base_url + "/" + "admin/dashboard_user_leaderboard";
    $.ajax({
        type: "POST",
        url: url,
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'year': year,
            'month': month
        },
        success: function (data) {
            var output = '';
            output += "<thead><tr><th>S/N</th><th>Customer Info</th><th>Transactions</th></tr></thead>";
            output += "<tbody>";
            $.each(data, function (index, value) {
                output += "<tr>";
                output += "<td>" + (index + 1) + " ";
                if (value.prev_index != null) {
                    if (value.prev_index == index) {
                        output += "<i style='font-size: .8rem; ' class=\"fas fa-circle\"></i>";
                    } else if (value.prev_index > index) {
                        output += "<i class=\"fas fa-arrow-up\"></i>";
                    } else if (value.prev_index < index) {
                        output += "<i class=\"fas fa-arrow-down\"></i>";
                    }
                } else {
                    output += "<i style='font-size: .8rem; ' class=\"fas fa-circle\"></i>";
                }
                output += "</td>";
                output += "<td>" + value.customer_full_name + "<br>";
                output += value.customer_contact_number + "<br>";

                if (value.user_type == 1) {
                    output += "<span class='premium-label'>Premium Member</span>";
                } else if (value.user_type == 2) {
                    output += "Virtual Member";
                } else {
                    output += "<span class='trial-label'>Trial Member</span>";
                }
                output += "</td>";
                output += "<td>" + value.transaction_count + "</td>";

                output += "</tr>";
            });
            output += "</tbody>";
            $('#userLeaderBoardList').empty().html(output);
        }
    });
}

//sorting customer leaderboard
function sortCustomerLeaderBoard() {
    customerLeaderBoard();
}

function partnerOutletLeaderBoard() {
    var year = $("#partLeadYear").val();
    var month = $("#partLeadMonth").val();
    var url = base_url + "/" + "admin/dashboard_partner_leaderboard";
    $.ajax({
        type: "POST",
        url: url,
        async: true,
        headers: {"X-CSRF-TOKEN": csrf_token},
        data: {
            '_token': csrf_token,
            'year': year,
            'month': month
        },
        success: function (data) {
            var output = '';
            output += "<thead><tr><th>S/N</th><th>Partner Branch</th><th>Scan Points</th></tr></thead>";
            output += "<tbody>";
            $.each(data, function (index, value) {
                output += "<tr>";
                output += "<td>" + (index + 1) + " ";

                if (value.prev_index != null) {
                    if (value.prev_index == index) {
                        output += "<i style='font-size: .8rem; ' class=\"fas fa-circle\"></i>";
                    } else if (value.prev_index > index) {
                        output += "<i class=\"fas fa-arrow-up\"></i>";
                    } else if (value.prev_index < index) {
                        output += "<i class=\"fas fa-arrow-down\"></i>";
                    }
                } else {
                    output += "<i style='font-size: .8rem; ' class=\"fas fa-circle\"></i>";
                }
                output += "</td>";
                output += "<td>" + value.partner_name + "<br>" + value.area + "</td>";
                output += "<td>" + value.point + "<br> Admin Point:" + value.admin_point + "</td>";
                output += "</tr>";
            });
            output += "</tbody>";
            $('#partnerLeaderBoardList').empty().html(output);
            stopPageLoader();
        }
    });
}

//sorting partner leaderboard
function sortPartnerOutletLeaderBoard() {
    partnerOutletLeaderBoard();
}

//sorting visit analytics
function sortRbdPartnerVisitsAnalytics() {
    startPageLoader();
    var year = $("#visitAnalyticsByYear").val();
    var month = $("#visitAnalyticsByMonth").val();
    var partner = $("#partnerSearchKeyInVisit").val();
    if (!year && !month) {
        alert('Please select values');
    } else if (!year && month) {
        alert('Please select year');
    } else {
        visitAnalytics(year, month, partner, true);
    }
}

//sorting transaction analytics
$(".rbdPartnerTransactionAnalyticsBtn").click(function () {
    var year = $("#transactionAnalyticsByYear").val();
    var partner = $("#partnerSearchKeyInTransaction").val();

    if ((year == null) && (partner == '')) {
        alert('Please select values!');
        return false;
    } else if ((year == null) && (partner != '')) {
        alert('Please select values!');
        return false;
    }
    transactionAnalytics(year, partner, true);
});
//sorting user registration analytics
$(".regUserAnalyticsBtn").click(function () {
    var year = $("#regUserAnalyticsByYear").val();

    if (!year) {
        alert('Please select year!');
        return false;
    }
    registrationAnalytics(year, true);
});

//sorting peak hour
function sortPeakHour() {
    startPageLoader();
    var from = $("#peak_hour_from").val();
    var to = $("#peak_hour_to").val();
    peakHourAnalytics(from, to, true);
}

//partner search suggestion
$(function () {
    $("#partnerSearchKeyInVisit").autocomplete({
        source: base_url + '/partnerByKey',
        autoFocus: true,
        delay: 500
    });
    $("#partnerSearchKeyInTransaction").autocomplete({
        source: base_url + '/partnerByKey',
        autoFocus: true,
        delay: 500
    });
});

//call function page wise
if (base_url + '/dashboard' === cur_url) {
    startPageLoader();
    allCounters();
    customerLeaderBoard();
    partnerOutletLeaderBoard();
} else {
    startPageLoader();
    // run functions on page loading to append chart
    sortRbdPartnerVisitsAnalytics();
    // transactionAnalytics();
    // registrationAnalytics();
    peakHourAnalytics();
}