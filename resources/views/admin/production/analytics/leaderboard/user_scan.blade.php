@include('admin.production.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="title_left">
    </div>
    <div class="row">
        <div class="col-md-12">
            {{--    customer leaderboard--}}
            <h3>Customer Leaderboard</h3>
            <div class="row">
                <div class="col-md-12">
                    <form class="form-inline" action="">
                        <div class="form-group">
                            <label for="peak_hour_from">From</label>
                            <input type="date" id="cus_lead_from" class="form-control" value="{{date('Y-m-01')}}">
                        </div>
                        <div class="form-group">
                            <label for="peak_hour_to">To</label>
                            <input type="date" id="cus_lead_to" class="form-control" value="{{date('Y-m-d')}}">
                        </div>
                        <div class="form-group">
                            <label></label>
                            <button type="button" class="btn btn-primary form-control" onclick="sortCustomerLeaderBoard()">Sort</button>
                        </div>
                    </form>
                </div>
                <br><br><br>
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table id="userLeaderBoardList" class="table table-bordered table-hover table-striped projects">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script>
    var base_url = window.location.origin;
    var cur_url = window.location.href;
    var csrf_token = $('meta[name="csrf-token"]').attr("content");

    function customerLeaderBoard() {
        var from = $("#cus_lead_from").val();
        var to = $("#cus_lead_to").val();
        var url = base_url + "/" + "admin/sort_user_scan_leaderboard";
        $.ajax({
            type: "POST",
            url: url,
            async: true,
            headers: {"X-CSRF-TOKEN": csrf_token},
            data: {
                '_token': csrf_token,
                'from': from,
                'to': to,
                'user_count_to_show': null
            },
            success: function (data) {
                var output = '';
                output += "<thead><tr><th>S/N</th><th>Customer Info</th><th>Transactions</th></tr></thead>";
                output += "<tbody>";
                $.each(data, function (index, value) {
                    output += "<tr>";
                    output += "<td>" + (index + 1) + "</td>";
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
                stopPageLoader();
            }
        });

    }

    //sorting customer leaderboard
    function sortCustomerLeaderBoard() {
        startPageLoader();
        customerLeaderBoard();
    }
    startPageLoader();
    customerLeaderBoard();

</script>
