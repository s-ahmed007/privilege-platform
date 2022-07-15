@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('ip_exists'))
                <div class="alert alert-warning">
                    {{ session('ip_exists') }}
                </div>
            @endif
            <h3>Edit Ip Address</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/update-branch-ip-address/'.$ipAddress->branch_id.'/'.$ipAddress->id) }}"
                          enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Ip Address:</label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <input type="text" class="form-control" placeholder="0.0.0.0" name="ip_address" value="{{$ipAddress->ip_address}}" required="required"/>
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
