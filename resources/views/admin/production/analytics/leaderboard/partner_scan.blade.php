@include('admin.production.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="title_left">
    </div>
    <div class="row">
        <div class="col-md-12">
            {{--    partner leaderboard--}}
            <h3 style="display: inline-block;">Partner Leaderboard</h3>
            <div class="row">
                <div class="col-md-12">
                    <form class="form-inline" action="">
                        <div class="form-group">
                            <label for="peak_hour_from">From</label>
                            <input type="date" id="part_lead_from" class="form-control" value="{{date('Y-m-01')}}">
                        </div>
                        <div class="form-group">
                            <label for="peak_hour_to">To</label>
                            <input type="date" id="part_lead_to" class="form-control" value="{{date('Y-m-d')}}">
                        </div>
                        <div class="form-group">
                            <label></label>
                            <button type="button" class="btn btn-primary form-control" onclick="sortPartnerOutletLeaderBoard()">Sort</button>
                        </div>
                    </form>
                </div>
                <br><br><br>
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table id="partnerLeaderBoardList" class="table table-bordered table-hover table-striped projects">
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

    function partnerOutletLeaderBoard() {
        var from = $("#part_lead_from").val();
        var to = $("#part_lead_to").val();
        var url = base_url + "/" + "admin/sort_partner_scan_leaderboard";
        $.ajax({
            type: "POST",
            url: url,
            async: true,
            headers: {"X-CSRF-TOKEN": csrf_token},
            data: {
                '_token': csrf_token,
                'from': from,
                'to': to,
                'partner_count_to_show': null
            },
            success: function (data) {
                var output = '';
                output += "<thead><tr><th>S/N</th><th>Partner Branch</th><th>Scan Points</th></tr></thead>";
                output += "<tbody>";
                $.each(data, function (index, value) {
                    output += "<tr>";
                    output += "<td>" + (index + 1) + " ";
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
        startPageLoader();
        partnerOutletLeaderBoard();
    }
    startPageLoader();
    partnerOutletLeaderBoard();

</script>
