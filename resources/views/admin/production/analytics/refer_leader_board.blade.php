@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
<div class="title_left">
    </div>

        <div class="row">
            <div class="col-md-12">
            <h3>Refer LeaderBoard</h3>
                <form class="form-inline" action="">
                    <div class="form-group">
                        <label for="peak_hour_from">From</label>
                        <input type="date" id="refer_lead_from" class="form-control" value="{{date('Y-m-01')}}">
                    </div>
                    <div class="form-group">
                        <label for="peak_hour_to">To</label>
                        <input type="date" id="refer_lead_to" class="form-control" value="{{date('Y-m-d')}}">
                    </div>
                    <div class="form-group">
                        <label></label>
                        <button type="button" class="btn btn-primary form-control" onclick="sortReferLeaderBoard()">Sort</button>
                    </div>
                </form>
            </div>
            <div class="col-xs-12">
                <div class="table-responsive">
                    <table id="referLeaderBoardList" class="table table-bordered table-hover table-striped projects">

                    </table>
                </div>
                <input type="hidden" id="previous_delivery_type" name="previous_delivery_type" value="0"/>
            </div>
        </div>
</div>

@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    var base_url = window.location.origin;
    var cur_url = window.location.href;
    var csrf_token = $('meta[name="csrf-token"]').attr("content");

    function referLeaderBoard() {
        var from = $("#refer_lead_from").val();
        var to = $("#refer_lead_to").val();
        var url = base_url + "/" + "admin/refer/leader-board";
        $.ajax({
            type: "POST",
            url: url,
            async: true,
            headers: {"X-CSRF-TOKEN": csrf_token},
            data: {
                '_token': "{{ csrf_token() }}",
                'from': from,
                'to': to
            },
            success: function (data) {
                var output = '';
                output += "<thead><tr><th>S/N</th><th>Member Information</th><th>Membership Type</th><th>Reference Used</th></tr></thead>";
                output += "<tbody>";
                $.each(data, function (index, value) {
                    output += "<tr>";
                    output += "<td>" + (index +1)+ "</td>";
                    output += "<td>" + "<b>Card No:</b>" + value.customer_id + "<br>";
                    output += "<b>Name:</b>" + value.customer_full_name + "<br>";
                    output += "<b>Mobile:</b>" + value.customer_contact_number + "<br>";
                    output += "<b>Email:</b>" + value.customer_email + "<br>";
                    output += "<b>Referral Code:</b>" + value.referral_number + "<br>";
                    output += "</td>";

                    output += "<td>";
                    if (value.latest_s_s_l_transaction.card_delivery.delivery_type != 11) {
                        output += "<span class='premium-label'>Premium Member</span>";
                    } else if (value.latest_s_s_l_transaction.card_delivery.delivery_type == 11) {
                        output += " <span class=\"trial-label\">Trial Member</span>";
                    }
                    output += "</td>";
                    output += "<td><b>Total: " + value.reference_used + "</b><br>";
                    output += "<b>Current: "+value.cur_referred+"</b></td>";
                    output += "</tr>";
                });
                output += "</tbody>";
                $('#referLeaderBoardList').empty().html(output);
                stopPageLoader();
            }
        });
    }

    //sorting refer leaderboard
    function sortReferLeaderBoard() {
        startPageLoader();
        referLeaderBoard();
    }

    referLeaderBoard();

</script>