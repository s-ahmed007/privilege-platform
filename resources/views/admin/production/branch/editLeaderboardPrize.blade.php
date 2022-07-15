@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('ip_exists'))
                <div class="alert alert-warning">
                    {{ session('ip_exists') }}
                </div>
            @endif
            <h3>Edit Leader Board Prize</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/update-leaderboard-prize/'.$prize->id) }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Prize Text:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="Prize text" value="{{$prize->prize_text}}" name="prize_text" required="required"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Month:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <select class="form-control" name="month_number">
                                    <option selected disabled>Select Month</option>
                                    @for ($m=1; $m<=12; $m++)
                                        <option value="{{$m}}" {{$prize->month == $m ? 'selected' : ''}}>{{ date('F', mktime(0,0,0,$m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="checkbox" name="change_prize" style="width: 30px; height: 30px;">
                                <span style="font-size: 20px; vertical-align: text-bottom;">Change for next month</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-activate pull-right">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
