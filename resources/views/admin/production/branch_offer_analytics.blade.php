@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
   <div class="page-title">
      <div class="title_left">
         <h3>Scan Analytics</h3>
          <span>{{$partner->info->partner_name.', '.$partner->partner_area}}</span>
         <h4>Total scan: {{$grand_total}}</h4>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="container">
      <div class="row">
         <div class="col-xs-12">
            <div class="table-responsive">
                @if($offers)
                    <form class="form-horizontal" action="{{url('branch_offer_analytics/'.$offers[0]->branch_id)}}"
                        onsubmit="return checkYearMonth()" method="GET">
                        <div class="form-group center m-0">
                            <div class="col-md-4 sort-year">
                                <select class="form-control" name="year" id="year">
                                    <option value="all">Year</option>
                                    <?php
                                    for ($i = 2018; $i <= date('Y'); $i++) {
                                        if($i == $year){
                                            echo "<option value='$i' selected>$i</option>";
                                        }else{
                                            echo "<option value='$i'>$i</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 sort-month">
                                <select class="form-control" name="month" id="month">
                                    <option value="all">Month</option>
                                    <option value="01" {{$month == '01' ? 'selected' : ''}}>January</option>
                                    <option value="02" {{$month == '02' ? 'selected' : ''}}>February</option>
                                    <option value="03" {{$month == '03' ? 'selected' : ''}}>March</option>
                                    <option value="04" {{$month == '04' ? 'selected' : ''}}>April</option>
                                    <option value="05" {{$month == '05' ? 'selected' : ''}}>May</option>
                                    <option value="06" {{$month == '06' ? 'selected' : ''}}>June</option>
                                    <option value="07" {{$month == '07' ? 'selected' : ''}}>July</option>
                                    <option value="08" {{$month == '08' ? 'selected' : ''}}>August</option>
                                    <option value="09" {{$month == '09' ? 'selected' : ''}}>September</option>
                                    <option value="10" {{$month == '10' ? 'selected' : ''}}>October</option>
                                    <option value="11" {{$month == '11' ? 'selected' : ''}}>November</option>
                                    <option value="12" {{$month == '12' ? 'selected' : ''}}>December</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary" style="width:100%">Sort</button>
                            </div>
                        </div>
                    </form>
                        <table id="offerList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                                <tr>
                                    <th>Offer</th>
                                    <th>Total Transaction</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($offers as $offer)
                                    <tr>
                                        <td>{{$offer->offer_description}}</td>
                                        <td>{{$offer->total_transaction}}</td>
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
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#offerList').DataTable({
            //"paging": false
            "order": []
        });
    });

    function checkYearMonth(){
        var year = $("#year").val();
        var month = $("#month").val();
        if (year === 'all' || month === 'all') {
            alert('Please select year & month to see result');
            return false;
        }
    }
</script>