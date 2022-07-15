@include('admin.production.header')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h3>Create A New Link</h3>
        </div>
        <div class="title_right">
            <h5 style="color: red; float:right;">Fields with * are required</h5>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <form class="form-horizontal form-label-left" method="post" action="{{ url('/admin/dynamic_links/') }}" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @include('admin.production.dynamicLink.fields')
                        
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-xs-12">
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