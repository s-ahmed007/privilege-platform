@include('b2b2c.layout.header')
<!-- bootstrap-progressbar -->
<link href="{{ asset('admin/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}"
      rel="stylesheet">
<!-- JQVMap -->
<link href="{{ asset('admin/vendors/jqvmap/dist/jqvmap.min.css') }}" rel="stylesheet"/>
<!-- bootstrap-daterangepicker -->
<link href="{{ asset('admin/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<style>
    .stats-count-box-1, .stats-count-box-2, .stats-count-box-3, .stats-count-box-4, .stats-count-box-5, .stats-count-box-6 {
    padding: 15px;
    border-radius: 5px;
    color: white;
    }
    .stats-count-box-1{
        background-color: #041c71;
    }
    .stats-count-box-2{
        background-color: #0047D0;
    }
    .stats-count-box-3{
        background-color: #087ce8;
    }
    .stats-count-box-4{
        background-color: #0094d2;
    }
    .stats-count-box-5{
        background-color: #01ace6;
    }
    .stats-count-box-6{
        background-color: #FFE166;
    }

</style>

<div class="right_col" role="main">
    <div class="title_left">
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @elseif(session('try_again'))
            <div class="title_right alert alert-warning" style="text-align: center;"> {{ session('try_again') }} </div>
        @endif
    </div>
    <div class="row tile_count">
        <div class="col-md-4 col-sm-6 col-xs-6 tile_stats_count">
            <div class="stats-count-box-1">
                <div class="count">{{ count($users) }}</div>
                <p class="count_top">Users</p>
            </div>
        </div>
    </div>
</div>
@include('b2b2c.layout.footer')