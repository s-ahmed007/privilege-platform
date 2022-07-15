@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')

<!-- page content -->
<div class="right_col" role="main">
    <div class="heading">
        <h3>Analytics of {{Session::get('partner_name')}} ({{ $partnerInfo->branches->where('id',$branch_id)->first()->partner_area }})</h3>
    </div>
    <div class="bar"></div>
      <div class="title_left">
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @elseif(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
      </div>

    <div class="clearfix"></div>
    <div class="panel-body">

        @if(isset($statistics))
            <form class="form-inline" action="{{url('sort-sales-analytics')}}" method="post" style="margin-bottom: 10px">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="salesAnalyticsByYear" id="salesAnalyticsByYear" required>
                        <option selected disabled value="">Select year</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="salesAnalyticsByMonth" id="salesAnalyticsByMonth" required>
                        <option selected disabled value="">Select month</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="salesAnalyticsByBranch" id="salesAnalyticsByBranch" required>
                        <option selected disabled value="">Select Branch</option>
                        @if(count($partnerInfo->branches)>0)
                            @foreach($partnerInfo->branches as $branch)
                            <option value="{{$branch->id}}">{{substr($branch->partner_address, 0,20).'... => '.$branch->partner_area}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <button type="button" class="btn btn-primary sortSalesAnalyticsBtn">Sort</button>
                </div>
            </form>
           
            <div id="SalesChart"></div><hr>
            
            <form class="form-inline" action="{{url('sort-transaction-analytics')}}" method="post" style="margin-bottom: 10px">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="transactionAnalyticsByYear" id="transactionAnalyticsByYear" required>
                        <option selected disabled value="">Select year</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="transactionAnalyticsByBranch" id="transactionAnalyticsByBranch" required>
                        <option selected disabled value="">Select Branch</option>
                        @if(count($partnerInfo->branches)>0)
                            @foreach($partnerInfo->branches as $branch)
                            <option value="{{$branch->id}}">{{substr($branch->partner_address, 0,20).'... => '.$branch->partner_area}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <button type="button" class="btn btn-primary form-control sortTransactionAnalyticsBtn">Sort</button>
                </div>
            </form>
            <div id="transactionChart"></div><hr>

            <form class="form-inline" action="{{url('sort-gender-analytics')}}" method="post" style="margin-bottom: 10px">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="genderAnalyticsByYear" id="genderAnalyticsByYear" required>
                        <option selected disabled value="">Select year</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="genderAnalyticsByMonth" id="genderAnalyticsByMonth">
                        <option selected disabled>Select month</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="genderAnalyticsByBranch" id="genderAnalyticsByBranch" required>
                        <option selected disabled value="">Select Branch</option>
                        @if(count($partnerInfo->branches)>0)
                            @foreach($partnerInfo->branches as $branch)
                            <option value="{{$branch->id}}">{{substr($branch->partner_address, 0,20).'... => '.$branch->partner_area}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <button type="button" class="btn btn-primary form-control genderTransactionAnalyticsBtn">Sort</button>
                </div>
            </form>
            <div>
                <div id="pieChart"></div><hr>
            </div>

            <form class="form-inline" action="{{url('sort-ageGender-analytics')}}" method="post" style="margin-bottom: 10px">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="ageGenderAnalyticsByYear" id="ageGenderAnalyticsByYear" required>
                        <option selected disabled value="">Select year</option>
                        <option value="2017">2017</option>
                        <option value="2018">2018</option>
                        <option value="2019">2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="ageGenderAnalyticsByMonth" id="ageGenderAnalyticsByMonth">
                        <option selected disabled>Select month</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="ageGenderAnalyticsByBranch" id="ageGenderAnalyticsByBranch" required>
                        <option selected disabled value="">Select Branch</option>
                        @if(count($partnerInfo->branches)>0)
                            @foreach($partnerInfo->branches as $branch)
                            <option value="{{$branch->id}}">{{substr($branch->partner_address, 0,20).'... => '.$branch->partner_area}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <button type="button" class="btn btn-primary form-control ageGenderAnalyticsBtn">Sort</button>
                </div>
            </form>

            <div id="ageAndGenderChart"></div>

            <?= $statistics->render("AreaChart", "Sales", "SalesChart");?>
            <?= $statistics->render("AreaChart", "Transaction", "transactionChart");?>
            <?= $statistics->render("PieChart", "visitPartner", "pieChart");?>
            <?= $statistics->render("ColumnChart", "ageGender", "ageAndGenderChart");?>

        @elseif(isset($sortedSales))
            <form class="form-inline" action="{{url('sort-sales-analytics')}}" method="post" style="margin-bottom: 10px">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="salesAnalyticsByYear" id="salesAnalyticsByYear">
                        <option selected disabled>Select year</option>
                        <option value="2017" {{$year == 2017 ? 'selected' : ''}}>2017</option>
                        <option value="2018" {{$year == 2018 ? 'selected' : ''}}>2018</option>
                        <option value="2019" {{$year == 2019 ? 'selected' : ''}}>2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="salesAnalyticsByMonth" id="salesAnalyticsByMonth">
                        <option selected disabled>Select month</option>
                        <option value="01" {{$month == 1 ? 'selected' : ''}}>January</option>
                        <option value="02" {{$month == 2 ? 'selected' : ''}}>February</option>
                        <option value="03" {{$month == 3 ? 'selected' : ''}}>March</option>
                        <option value="04" {{$month == 4 ? 'selected' : ''}}>April</option>
                        <option value="05" {{$month == 5 ? 'selected' : ''}}>May</option>
                        <option value="06" {{$month == 6 ? 'selected' : ''}}>June</option>
                        <option value="07" {{$month == 7 ? 'selected' : ''}}>July</option>
                        <option value="08" {{$month == 8 ? 'selected' : ''}}>August</option>
                        <option value="09" {{$month == 9 ? 'selected' : ''}}>September</option>
                        <option value="10" {{$month == 10 ? 'selected' : ''}}>October</option>
                        <option value="11" {{$month == 11 ? 'selected' : ''}}>November</option>
                        <option value="12" {{$month == 12 ? 'selected' : ''}}>December</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="salesAnalyticsByBranch" id="salesAnalyticsByBranch" required>
                        <option selected disabled value="">Select Branch</option>
                        @if(count($partnerInfo->branches)>0)
                            @foreach($partnerInfo->branches as $branch)
                            <option value="{{$branch->id}}" {{$branch->id == $branch_id ? 'selected' : ''}}>{{substr($branch->partner_address, 0,20).'... => '.$branch->partner_area}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <button type="submit" class="btn btn-primary form-control">Sort</button>
                </div>
            </form>
            <?= $sortedSales->render("AreaChart", "Sales", "SalesChart");?>
            <hr><div id="SalesChart"></div>
        @elseif(isset($sortedTransactions))
            <form class="form-inline" action="{{url('sort-transaction-analytics')}}" method="post" style="margin-bottom: 10px">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="transactionAnalyticsByYear">
                        <option selected disabled>Select year</option>
                        <option value="2017" {{$year == 2017 ? 'selected' : ''}}>2017</option>
                        <option value="2018" {{$year == 2018 ? 'selected' : ''}}>2018</option>
                        <option value="2019" {{$year == 2019 ? 'selected' : ''}}>2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="transactionAnalyticsByBranch" required>
                        <option selected disabled value="">Select Branch</option>
                        @if(count($partnerInfo->branches)>0)
                            @foreach($partnerInfo->branches as $branch)
                            <option value="{{$branch->id}}" {{$branch->id == $branch_id ? 'selected' : ''}}>{{substr($branch->partner_address, 0,20).'... => '.$branch->partner_area}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <button type="submit" class="btn btn-primary form-control">Sort</button>
                </div>
            </form>
            <?= $sortedTransactions->render("AreaChart", "Transaction", "transactionChart");?>
            <hr><div id="transactionChart"></div>
        @elseif(isset($sortedGender))
            <form class="form-inline" action="{{url('sort-gender-analytics')}}" method="post" style="margin-bottom: 10px">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="genderAnalyticsByYear">
                        <option selected disabled>Select year</option>
                        <option value="2017" {{$year == 2017 ? 'selected' : ''}}>2017</option>
                        <option value="2018" {{$year == 2018 ? 'selected' : ''}}>2018</option>
                        <option value="2019" {{$year == 2019 ? 'selected' : ''}}>2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="genderAnalyticsByMonth">
                        <option selected disabled>Select month</option>
                        <option value="01" {{$month == 1 ? 'selected' : ''}}>January</option>
                        <option value="02" {{$month == 2 ? 'selected' : ''}}>February</option>
                        <option value="03" {{$month == 3 ? 'selected' : ''}}>March</option>
                        <option value="04" {{$month == 4 ? 'selected' : ''}}>April</option>
                        <option value="05" {{$month == 5 ? 'selected' : ''}}>May</option>
                        <option value="06" {{$month == 6 ? 'selected' : ''}}>June</option>
                        <option value="07" {{$month == 7 ? 'selected' : ''}}>July</option>
                        <option value="08" {{$month == 8 ? 'selected' : ''}}>August</option>
                        <option value="09" {{$month == 9 ? 'selected' : ''}}>September</option>
                        <option value="10" {{$month == 10 ? 'selected' : ''}}>October</option>
                        <option value="11" {{$month == 11 ? 'selected' : ''}}>November</option>
                        <option value="12" {{$month == 12 ? 'selected' : ''}}>December</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="genderAnalyticsByBranch" required>
                        <option selected disabled value="">Select Branch</option>
                        @if(count($partnerInfo->branches)>0)
                            @foreach($partnerInfo->branches as $branch)
                            <option value="{{$branch->id}}" {{$branch->id == $branch_id ? 'selected' : ''}}>{{substr($branch->partner_address, 0,20).'... => '.$branch->partner_area}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <button type="submit" class="btn btn-primary form-control">Sort</button>
                </div>
            </form>
            <?= $sortedGender->render("PieChart", "visitPartner", "pieChart");?>
            <hr><div id="pieChart"></div>
        @else
            <form class="form-inline" action="{{url('sort-ageGender-analytics')}}" method="post" style="margin-bottom: 10px">
                {{csrf_field()}}
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="ageGenderAnalyticsByYear">
                        <option selected disabled>Select year</option>
                        <option value="2017" {{$year == 2017 ? 'selected' : ''}}>2017</option>
                        <option value="2018" {{$year == 2018 ? 'selected' : ''}}>2018</option>
                        <option value="2019" {{$year == 2019 ? 'selected' : ''}}>2019</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="ageGenderAnalyticsByMonth">
                        <option selected disabled>Select month</option>
                        <option value="01" {{$month == 1 ? 'selected' : ''}}>January</option>
                        <option value="02" {{$month == 2 ? 'selected' : ''}}>February</option>
                        <option value="03" {{$month == 3 ? 'selected' : ''}}>March</option>
                        <option value="04" {{$month == 4 ? 'selected' : ''}}>April</option>
                        <option value="05" {{$month == 5 ? 'selected' : ''}}>May</option>
                        <option value="06" {{$month == 6 ? 'selected' : ''}}>June</option>
                        <option value="07" {{$month == 7 ? 'selected' : ''}}>July</option>
                        <option value="08" {{$month == 8 ? 'selected' : ''}}>August</option>
                        <option value="09" {{$month == 9 ? 'selected' : ''}}>September</option>
                        <option value="10" {{$month == 10 ? 'selected' : ''}}>October</option>
                        <option value="11" {{$month == 11 ? 'selected' : ''}}>November</option>
                        <option value="12" {{$month == 12 ? 'selected' : ''}}>December</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <select class="form-control" name="ageGenderAnalyticsByBranch" required>
                        <option selected disabled value="">Select Branch</option>
                        @if(count($partnerInfo->branches)>0)
                            @foreach($partnerInfo->branches as $branch)
                            <option value="{{$branch->id}}" {{$branch->id == $branch_id ? 'selected' : ''}}>{{ substr($branch->partner_address, 0,20).'... => '.$branch->partner_area}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3 col-md-offset-6 col-sm-3 col-sm-offset-6 col-xs-3 col-xs-offset-6"></label>
                    <button type="submit" class="btn btn-primary form-control">Sort</button>
                </div>
            </form>
            <?= $sortedAgeGender->render("ColumnChart", "ageGender", "ageAndGenderChart");?>
            <hr><div id="ageAndGenderChart"></div>
        @endif

    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
      $(".sortSalesAnalyticsBtn").click(function(){
            var salesAnalyticsByBranch = $("#salesAnalyticsByBranch").val();
            var salesAnalyticsByMonth = $("#salesAnalyticsByMonth").val();
            var salesAnalyticsByYear = $("#salesAnalyticsByYear").val();
            var salesAnalyticsByMonthName = $("#salesAnalyticsByMonth option:selected").text();

            if((salesAnalyticsByYear==null)||(salesAnalyticsByMonth==null)||(salesAnalyticsByBranch==null)){
                alert('Year, Month And Branch Must Be Set!');
                return false;
            }

            //fetch data 
            var url = "{{ url('/sort-sales-analytics-json') }}";
            var dataObj = {'_token':'<?php echo csrf_token() ?>', 'salesAnalyticsByYear':salesAnalyticsByYear, 'salesAnalyticsByMonth':salesAnalyticsByMonth, 'salesAnalyticsByBranch':salesAnalyticsByBranch};
            var e = this;

            $.ajax({
                type: 'POST',
                url: url,
                data: dataObj,
            }).done(function(data){
                // console.log(data);
                if(data=="missing_params"){
                    alert("Missing Parameters");
                    return false;
                }

                var data = new google.visualization.DataTable($.parseJSON(data));

                var options = {
                  title: "Daily sales analysis of "+salesAnalyticsByMonthName+" \n(Amount of money in BDT against number of days)",
                  titleTextStyle: {fontSize: 14},
                };

                var chart = new google.visualization.AreaChart(
                document.getElementById('SalesChart'));
                chart.draw(data,options);
            });
        });
</script>
<script type="text/javascript">
      $(".sortTransactionAnalyticsBtn").click(function(){
            var transactionAnalyticsByBranch = $("#transactionAnalyticsByBranch").val();
            var transactionAnalyticsByYear = $("#transactionAnalyticsByYear").val();

            console.clear();
            console.log(transactionAnalyticsByBranch);
            console.log(transactionAnalyticsByYear);

            if((transactionAnalyticsByBranch==null)||(transactionAnalyticsByYear==null)){
                alert('Year And Branch Must Be Set!');
                return false;
            }


            
            //fetch data 
            var url = "{{ url('/sort-transaction-analytics-json') }}";
            var dataObj = {'_token':'<?php echo csrf_token() ?>', 'transactionAnalyticsByBranch':transactionAnalyticsByBranch, 'transactionAnalyticsByYear':transactionAnalyticsByYear};
            var e = this;

            $.ajax({
                type: 'POST',
                url: url,
                data: dataObj,
            }).done(function(data){
                console.log(data); 
                if(data=="missing_params"){
                    alert("Missing Parameters");
                    return false;
                }

                

                var data = new google.visualization.DataTable($.parseJSON(data));

                 var options = {
                  title: "Monthly transaction analysis of "+transactionAnalyticsByYear+" \n(Number of customers against Months)",
                  titleTextStyle: {fontSize: 14},
                };

                var chart = new google.visualization.AreaChart(
                document.getElementById('transactionChart'));
                chart.draw(data,options);


            });

        });
</script>
<script type="text/javascript">
      $(".genderTransactionAnalyticsBtn").click(function(){
            var genderAnalyticsByBranch = $("#genderAnalyticsByBranch").val();
            var genderAnalyticsByMonth = $("#genderAnalyticsByMonth").val();
            var genderAnalyticsByYear = $("#genderAnalyticsByYear").val();
            console.clear();
            console.log(genderAnalyticsByYear);
            console.log(genderAnalyticsByMonth);
            console.log(genderAnalyticsByBranch);

            if((genderAnalyticsByBranch==null)||(genderAnalyticsByYear==null)){
                alert('Year And Branch Must Be Set!');
                return false;
            }


            
            //fetch data 
            var url = "{{ url('/sort-gender-analytics-json') }}";
            var dataObj = {'_token':'<?php echo csrf_token() ?>', 'genderAnalyticsByBranch':genderAnalyticsByBranch, 'genderAnalyticsByMonth':genderAnalyticsByMonth, 'genderAnalyticsByYear':genderAnalyticsByYear};
            var e = this;

            $.ajax({
                type: 'POST',
                url: url,
                data: dataObj,
            }).done(function(data){
                console.log(data); 
                if(data=="missing_params"){
                    alert("Missing Parameters");
                    return false;
                }

                var data = new google.visualization.DataTable($.parseJSON(data));

                var options = {
                    title: 'Gender Demographics',
                    titleTextStyle: {fontSize: 14},
                    is3D: true
                };

                var chart = new google.visualization.PieChart(
                document.getElementById('pieChart'));
                chart.draw(data,options);


            });

        });
</script>
<script type="text/javascript">
      $(".ageGenderAnalyticsBtn").click(function(){
            var ageGenderAnalyticsByBranch = $("#ageGenderAnalyticsByBranch").val();
            var ageGenderAnalyticsByMonth = $("#ageGenderAnalyticsByMonth").val();
            var ageGenderAnalyticsByYear = $("#ageGenderAnalyticsByYear").val();
            console.clear();
            console.log(ageGenderAnalyticsByYear);
            console.log(ageGenderAnalyticsByMonth);
            console.log(ageGenderAnalyticsByBranch);

            if((ageGenderAnalyticsByBranch==null)||(ageGenderAnalyticsByYear==null)){
                alert('Year And Branch Must Be Set!');
                return false;
            }


            
            //fetch data 
            var url = "{{ url('/sort-ageGender-analytics-json') }}";
            var dataObj = {'_token':'<?php echo csrf_token() ?>', 'ageGenderAnalyticsByBranch':ageGenderAnalyticsByBranch, 'ageGenderAnalyticsByMonth':ageGenderAnalyticsByMonth, 'ageGenderAnalyticsByYear':ageGenderAnalyticsByYear};
            var e = this;

            $.ajax({
                type: 'POST',
                url: url,
                data: dataObj,
            }).done(function(data){
                console.log(data); 
                if(data=="missing_params"){
                    alert("Missing Parameters");
                    return false;
                }

                var data = new google.visualization.DataTable($.parseJSON(data));

                var options = {
                  title: 'Age & gender statistics (Percentage of Gender ratio against Age)',
                  vAxis: {titleTextStyle: {fontSize: 14}}
                };


                var chart = new google.visualization.ColumnChart(
                document.getElementById('ageAndGenderChart'));
                chart.draw(data, options);

            });

        });
</script>
@include('partner-admin.production.footer')