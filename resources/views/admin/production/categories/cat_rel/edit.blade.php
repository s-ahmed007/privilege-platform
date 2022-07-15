@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif (session('try_again'))
                <div class="alert alert-danger">
                    {{ session('try_again') }}
                </div>
            @endif
            <h3>Edit Category Relation</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <hr>
            <form action="{{ url('admin/category_relation/'.$relation->id) }}" class="form-horizontal" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">

                <div class="form-group">
                    <div class="col-sm-3">
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('main_cat'))
                                {{ $errors->getBag('default')->first('main_cat') }}
                                <br>
                            @endif
                        </span>
                        <label for="">Main Category</label>
                        <select class="form-control" name="main_cat" required>
                            <option value="" selected>Select</option>
                            @foreach($main_cats as $cats)
                                <option value="{{$cats->id}}" {{$relation->main_cat == $cats->id ? 'selected' : ''}}>{{$cats->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('sub_cat_1'))
                                {{ $errors->getBag('default')->first('sub_cat_1') }}
                                <br>
                            @endif
                        </span>
                        <label for="">Sub Category 1</label>
                        <select class="form-control" name="sub_cat_1">
                            <option value="" selected>Select</option>
                            @foreach($sub_cat_1 as $cats)
                                <option value="{{$cats->id}}" {{$relation->sub_cat_1_id == $cats->id ? 'selected' : ''}}>{{$cats->cat_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('sub_cat_2'))
                                {{ $errors->getBag('default')->first('sub_cat_2') }}
                            @endif
                        </span>
                        <label for="">Sub Category 2</label>
                        <select class="form-control" name="sub_cat_2">
                            <option value="" selected>Select</option>
                            @foreach($sub_cat_2 as $cats)
                                <option value="{{$cats->id}}" {{$relation->sub_cat_2_id == $cats->id ? 'selected' : ''}}>{{$cats->cat_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label for=""></label>
                        <button type="submit" class="btn btn-success form-control">Edit Relation</button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

@include('admin.production.footer')
