@include('admin.production.header')

<div class="right_col" role="main">
    @if (Session::has('operation failed'))
        <div class="title_right alert alert-danger"
             style="text-align: center; margin-top: 60px;">{{ Session::get('operation failed') }}</div>
    @endif
    @if (Session::has('trending partners added'))
        <div class="title_right alert alert-success"
             style="text-align: center; margin-top: 60px;">{{ Session::get('trending partners added') }}</div>
    @endif
    @if (Session::has('brand partners added'))
        <div class="title_right alert alert-success"
             style="text-align: center; margin-top: 60px;">{{ Session::get('brand partners added') }}</div>
    @endif
    <div class="page-title">
        <div class="title_left">
            <h3>Add Blog Categories</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <form class="form-horizontal form-label-left" method="post"
                          action="{{ url('/addBlogCategories') }}">
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                @foreach($allCategories as $category)
                                    <input id="quantity" name="quantity" width="196px" required/>
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                <button type="submit" class="btn btn-done">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')