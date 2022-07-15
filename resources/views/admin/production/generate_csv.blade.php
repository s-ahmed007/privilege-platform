@include('admin.production.header')

<div class="right_col" role="main">

    <div class="page-title">
        <div class="title_left">
            <h3>Generate CSV</h3>
        </div>
        @if(session('try_again'))
            <div class="title_right alert alert-warning"
                 style="text-align: center;"> {{ session('try_again') }}
            </div>
        @endif
    </div>
    <div class="col-md-12 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <a class="btn btn-success" href="{{url('admin/generate/csv/app_version')}}">User App Version</a>
                <a class="btn btn-active" href="{{url('admin/generate/csv/email_verified')}}">Email Verified</a>
                <a class="btn btn-danger" href="{{url('admin/generate/csv/email_not_verified')}}">Email Not Verified</a>
                <a class="btn btn-premium" href="{{url('admin/generate/csv/profile_completed')}}">Profile Completed</a>
                <a class="btn btn-guest" href="{{url('admin/generate/csv/profile_not_completed')}}">Profile Not Completed</a>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
