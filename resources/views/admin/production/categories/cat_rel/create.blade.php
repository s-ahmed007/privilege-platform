@include('admin.production.header')
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-warning">
                    {{ session('status') }}
                </div>
            @endif
            <h3>Create Main Category</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <form action="{{ url('admin/category_relation/') }}" class="form-horizontal" method="post">
            <div class="form-group">
{{--                <label class="control-label col-sm-2" for="first_name">Main Category:</label>--}}
                <span style="color: red;">
                    @if ($errors->getBag('default')->first('category_name'))
                        {{ $errors->getBag('default')->first('category_name') }}
                    @endif
                </span>
                <div class="col-sm-4">
                    Main Category
                    <select class="form-control" name="main_cat" id="">
                        @foreach($main_cats as $cats)
                            <option value="{{$cats->id}}">{{$cats->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    Sub Category 1
                    <select class="form-control" name="main_cat" id="">
                        @foreach($sub_cat_1 as $cats)
                            <option value="{{$cats->id}}">{{$cats->cat_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    Sub Category 2
                    <select class="form-control" name="main_cat" id="">
                        @foreach($sub_cat_2 as $cats)
                            <option value="{{$cats->id}}">{{$cats->cat_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-activate pull-right">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('admin.production.footer')