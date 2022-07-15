@include('admin.production.header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
    <div class="row">
        <form class="form-inline" action="" method="post">
            <div class="form-group">
                <select name="category" id="category" class="form-control">
                    <option selected disabled>Category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <select name="area" id="area" class="form-control">
                    <option selected disabled>Area</option>
                    @foreach($area as $value)
                        <option value="{{$value->partner_area}}">{{$value->partner_area}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
               <button type="button" class="btn btn-primary" onclick="merchantTransactionPercentageAnalytics(true)">Sort</button>
            </div>
        </form>
    </div>
    <h3><b>MERCHANT TRANSACTION PERCENTAGE</b></h3>
    <div id="merchantTransactionPercentageChart"></div>
   <div class="page-title">
      <div class="title_left">
         <h4><b>TRANSACTION (Total Scan:{{$grand_total}})</b></h4>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
                @if($branches)
                <form class="form-inline" action="{{url('admin/scan_analytics')}}" method="GET">
                    <div class="form-group">
                        <label for="from">From</label>
                        @if(isset($from))
                            <input type="date" id="from" name="from" class="form-control" value="{{$from}}">
                        @else
                            <input type="date" id="from" name="from" class="form-control" value="{{date('Y-m-01')}}">
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="to">To</label>
                        @if(isset($from))
                            <input type="date" id="to" name="to" class="form-control" value="{{$to}}">
                        @else
                            <input type="date" id="to" name="to" class="form-control" value="{{date('Y-m-d')}}">
                        @endif
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="active">
                            <option value="all" {{$active == 'all' ? 'selected' : ''}}>All</option>
                            <option value="1" {{$active == '1' ? 'selected' : ''}}>Partners with Scans</option>
                            <option value="0" {{$active == '0' ? 'selected' : ''}}>Partners without Scans</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="status">
                            <option value="all" {{$status == 'all' ? 'selected' : ''}}>All</option>
                            <option value="1" {{$status == '1' ? 'selected' : ''}}>Current Partners</option>
                            <option value="0" {{$status == '0' ? 'selected' : ''}}>Old Partners</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="area" id="area" class="form-control">
                            <option value="all" >All</option>
                            @foreach($area as $value)
                                <option value="{{$value->partner_area}}" {{$selected_area == $value->partner_area ? 'selected':''}}>
                                    {{$value->partner_area}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary form-control">Sort</button>
                    </div>
                </form>
                    <br>
                <table id="partnerlist" class="table table-bordered table-hover table-striped projects">
                <thead>
                    <tr>
                        <th>Partner</th>
                        <th>QR Scan</th>
                        <th>Card Scan</th>
                        <th>Total Scan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branches as $branch)
                        <tr>
                            <td>{{$branch->info->partner_name}}<br>{{$branch->partner_area}}</td>
                            <td>{{$branch->qr_transaction}}</td>
                            <td>{{$branch->card_transaction}}</td>
                            <td>{{$branch->total_transaction}}</td>
                            <td>
                                <a href="{{url('branch_offer_analytics/'.$branch->id)}}" class="btn btn-primary">Offer Analytics</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
                @else
                <div style="font-size: 1.4em; color: red;">
                    {{ 'No Branch found.' }}
                </div>
                @endif
            </div>
            </div>
        </div>
   </div>
</div>

@include('admin.production.footer')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    var base_url = window.location.origin;
    var cur_url = window.location.href;
    var csrf_token = $('meta[name="csrf-token"]').attr("content");

    //partner transaction Percentage analytics
    function merchantTransactionPercentageAnalytics(sort=false) {
        startPageLoader();
        var category = $("#category").val();
        var area = $("#area").val();
        if (area && !category){
            alert('Please select category');
            stopPageLoader();
            return false;
        }
        $.ajax({
            type: "POST",
            url: base_url + "/" + "admin/merchant_transaction_percentage_analytics",
            async: true,
            headers: {"X-CSRF-TOKEN": csrf_token},
            data: {
                '_token': csrf_token,
                'sort': sort,
                'category': category,
                'area': area
            },
            success: function (data) {
                google.charts.load('current', {'packages': ['corechart']});
                google.charts.setOnLoadCallback(
                    function () { // Anonymous function that calls drawChart1 and drawChart2
                        drawMerchantTransactionPercentageChart(data);
                    });
                stopPageLoader();
            }
        });
    }

    function drawMerchantTransactionPercentageChart(param) {
        "use strict";
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Partner');
        data.addColumn('number', 'Transaction Percentage');

        $.each(param, function (index, row) {
            data.addRow([row.info.partner_name, row.percentage]);
        });

        var options = {
            colors: ['#01ace6', '#13CE66'],
            height: 500,
            hAxis: { format: '#\'%\'',
                viewWindow: {
                    max:100,
                    min:0
                }
            },
        };

        //add
        var formatter = new google.visualization.NumberFormat(
            {suffix: '%', negativeColor: 'red', negativeParens: true});
        formatter.format(data, 1);

        var chart = new google.visualization.BarChart(document.getElementById('merchantTransactionPercentageChart'));
        chart.draw(data, options);
    }

    merchantTransactionPercentageAnalytics();

    $(document).ready(function () {
        $('#partnerlist').DataTable({
           //"paging": false
            "order": []
        });
    });
</script>