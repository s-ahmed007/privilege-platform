@include('admin.production.header')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Customer Leaderboard</h3>
            <select class="form-control" id="userLeaderboardSort" name="userLeaderboardSort">
                <option selected disabled>Select month</option>
                <option value="01" {{date('m') == '01' ? 'selected' : ''}}>January</option>
                <option value="02" {{date('m') == '02' ? 'selected' : ''}}>February</option>
                <option value="03" {{date('m') == '03' ? 'selected' : ''}}>March</option>
                <option value="04" {{date('m') == '04' ? 'selected' : ''}}>April</option>
                <option value="05" {{date('m') == '05' ? 'selected' : ''}}>May</option>
                <option value="06" {{date('m') == '06' ? 'selected' : ''}}>June</option>
                <option value="07" {{date('m') == '07' ? 'selected' : ''}}>July</option>
                <option value="08" {{date('m') == '08' ? 'selected' : ''}}>August</option>
                <option value="09" {{date('m') == '09' ? 'selected' : ''}}>September</option>
                <option value="10" {{date('m') == '10' ? 'selected' : ''}}>October</option>
                <option value="11" {{date('m') == '11' ? 'selected' : ''}}>November</option>
                <option value="12" {{date('m') == '12' ? 'selected' : ''}}>December</option>
            </select><br>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    <table id="leaderBoardList" class="table table-bordered table-hover table-striped projects">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Customer Info</th>
                            <th>Amount of Transactions</th>
                            <!-- <th>Points</th> -->
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($leaderboard_data as $key => $value)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{ $value->customer_id }}<br>
                                    {{ $value->customer_full_name }}<br>
                                    {{ $value->customer_contact_number }}
                                </td>
                                <td>{{ $value->transaction_count }}</td>
                                <!-- <td>{{ $value->total_point }}</td> -->
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
<script type="text/javascript">
    //sorting leaderboard by month
    $("#userLeaderboardSort").change(function () {
        var month = $(this).val();
        var url = "{{ url('/admin/sort_user_leaderboard') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'month' : month
            },
            success: function (data) {
                var output = '';
                output += "<thead><tr><th>S/N</th><th>Customer Info</th><th>Transactions Number</th><th>Points</th></tr></thead>";
                output += "<tbody>";
                $.each(data, function(index, value) {
                    output += "<tr>";
                    output += "<td>"+(index+1)+"</td>";
                    output += "<td>"+value.customer_id +"<br>";
                    output += value.customer_full_name +"<br>";
                    output += value.customer_contact_number;
                    output += "</td>";
                    output += "<td>"+value.transaction_count +"</td>";
                    output += "<td>"+value.total_point +"</td>";
                    output += "</tr>";
                });
                output += "</tbody>";
                $('#leaderBoardList').empty().html(output);
            }
        });
    });
</script>