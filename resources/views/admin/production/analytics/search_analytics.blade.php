@include('admin.production.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-6">
            <h4><b>SEARCHED KEYS WITHOUT RESULTS</b></h4>
            <br>
            <div class="col-xs-12">
                <div class="table-responsive">
                    <table id="searchKeysWithoutResultList" class="table table-bordered table-hover table-striped projects">
                    </table>
                </div>
            </div>
            </div>
        <div class="col-md-6">
            <h4 style="display: inline-block;"><b>SEARCHED PARTNERS</b></h4>
            <div class="col-xs-12">
                <br>
                <div class="table-responsive">
                    <table id="searchedPartnerList" class="table table-bordered table-hover table-striped projects">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@include('admin.production.footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    var base_url = window.location.origin;
    var cur_url = window.location.href;
    var csrf_token = $('meta[name="csrf-token"]').attr("content");

    function searchedKeysWithoutResultBox() {
        var url = base_url + "/" + "admin/analytics/search/keys";
        $.ajax({
            type: "POST",
            url: url,
            headers: {"X-CSRF-TOKEN": csrf_token},
            data: {
                '_token': csrf_token,
            },
            success: function (data) {
                var output = '';
                output += "<thead><tr><th>Search Counts</th><th>Keys</th></tr></thead>";
                output += "<tbody>";
                $.each(data, function (index, value) {
                    output += "<tr>";
                    output += "<td>" + value.search_key_count + "</td>";
                    output += "<td>" + value.key + "</td>";

                    output += "</tr>";
                });
                output += "</tbody>";
                $('#searchKeysWithoutResultList').empty().html(output);
            }
        });
    }


    function searchedPartnersBox() {
        var url = base_url + "/" + "admin/analytics/search/partners";
        var partner_key_url = base_url + "/" + "admin/analytics/search_keys_of_partner";
        $.ajax({
            type: "POST",
            url: url,
            async: false,
            headers: {"X-CSRF-TOKEN": csrf_token},
            data: {
                '_token': csrf_token,
            },
            success: function (data) {
                var output = '';
                output += "<thead><tr><th>Partner Branch</th><th>Search Count</th><th>Action</th></tr></thead>";
                output += "<tbody>";
                $.each(data, function (index, value) {
                    output += "<tr>";
                    output += "<td>" + value.partner_name + "<br>" + value.partner_address + "</td>";
                    output += "<td>" + value.search_count + "</td>";
                    output += "<td> <a href=\""+partner_key_url+"/"+value.branch_id+"\" class=\"btn btn-primary\">Search Keys</a> </td>";
                    output += "</tr>";
                });
                output += "</tbody>";
                $('#searchedPartnerList').empty().html(output);
            }
        });
    }

    searchedKeysWithoutResultBox();
    searchedPartnersBox();
</script>
