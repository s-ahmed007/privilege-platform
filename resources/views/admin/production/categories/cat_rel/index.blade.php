@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif (session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @endif
            <h3>Category Relation Create/ Edit/ Display</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <hr>
            <form action="{{ url('admin/category_relation/') }}" class="form-horizontal" method="post">
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
                                <option value="{{$cats->id}}" {{old('main_cat') == $cats->id ? 'selected' : ''}}>{{$cats->name}}</option>
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
                                <option value="{{$cats->id}}" {{old('sub_cat_1') == $cats->id ? 'selected' : ''}}>{{$cats->cat_name}}</option>
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
                                <option value="{{$cats->id}}" {{old('sub_cat_2') == $cats->id ? 'selected' : ''}}>{{$cats->cat_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label for=""></label>
                        <button type="submit" class="btn btn-success form-control">Create Relation</button>
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
            <hr>
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($cat_rels)
                        <table id="catRelList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>Main Category</th>
                                <th>Sub Category 1</th>
                                <th>Sub Category 2</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($cat_rels as $cat)
                                <tr class="blog_row" data-category-id='{{ $cat->id }}'>
                                    <td>{{$cat->mainCategory->name}}</td>
                                    <td>
                                        @if($cat->sub_cat_1)
                                            {{ $cat->sub_cat_1->cat_name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($cat->sub_cat_2)
                                            {{ $cat->sub_cat_2->cat_name }}
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-edit editBtn" data-category-id='{{ $cat->id }}' title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-accept assignBtn" data-category-id='{{ $cat->id }}' title="Assign Partner">
                                            <i class="glyphicon glyphicon-upload"></i>
                                        </button>
                                        <button class="btn btn-delete deleteBtn" data-category-id='{{ $cat->id }}' title="Delete">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No category found.' }}
                        </div>
                    @endif
                </div>
                <!--end of .table-responsive-->
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
{{-- ============================================================================================================
========================category edit & delete====================
============================================================================================================= --}}
<script type="text/javascript">
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure?")) {
            //fetch the blog id
            var relId = $(this).attr('data-category-id');
            var url = "{{ url('/admin/category_relation/') }}";
            url += '/' + relId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $('.editBtn').on('click', function (event) {
        //fetch the category id
        var catId = $(this).attr('data-category-id');
        var url = "{{ url('/admin/category_relation/') }}";
        url += '/' + catId + '/edit';
        window.location.href = url;
    });

    $('.assignBtn').on('click', function (event) {
        //fetch the category id
        var catId = $(this).attr('data-category-id');
        var url = "{{ url('/admin/category_relation/assign_partner') }}";
        url += '/' + catId;
        window.location.href = url;
    });

    $(document).ready(function () {
        $('#catRelList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>