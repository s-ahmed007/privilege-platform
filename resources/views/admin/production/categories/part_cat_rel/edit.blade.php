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
            <h3>Edit Partner Category Relation</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <hr>
            <form action="{{ url('admin/part_cat_relation/'.$relation->id) }}" class="form-horizontal" method="post">
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
                        <select class="form-control" name="main_cat" disabled>
                            <option value="{{$relation->id}}">{{$relation->categoryRelation->mainCategory->name}}</option>
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
                        <select class="form-control" name="sub_cat_1" disabled>
                            @if($relation->categoryRelation->sub_cat_1)
                            <option>{{$relation->categoryRelation->sub_cat_1->cat_name}}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('sub_cat_2'))
                            {{ $errors->getBag('default')->first('sub_cat_2') }}
                        @endif
                        </span>
                        <label for="">Sub Category 2</label>
                        <select class="form-control" name="sub_cat_2" disabled>
                            @if($relation->categoryRelation->sub_cat_2)
                            <option>{{$relation->categoryRelation->sub_cat_2->cat_name}}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('partner_id'))
                            {{ $errors->getBag('default')->first('partner_id') }}
                        @endif
                        </span>
                        <label for="">Partner</label>
                        <select class="form-control" name="partner_id">
                            <option selected disabled>Select Partner</option>
                            @foreach($partners as $partner)
                                <option value="{{$partner->partner_account_id}}" {{$relation->partner_id == $partner->partner_account_id ? 'selected':''}}>{{$partner->info->partner_name}}</option>
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
