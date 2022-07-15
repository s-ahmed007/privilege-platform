@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif (session('try_again'))
                <div class="alert alert-warning">{{ session('try_again') }}</div>
            @endif
            <h3>Sub Category 1 Create/ Edit/ Display</h3>
            <a type="button" class="btn btn-create" href="{{ url('/admin/sub_cat_1/create') }}" style="margin-left: unset;">+ Create New Sub Category 1</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($categories)
                        <table id="subCat1List" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
<!--                                 <th>Icon</th> -->
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($categories as $category)
                                <tr class="blog_row" data-category-id='{{ $category->id }}'>
                                    <!-- <td><img src="{{asset($category->icon)}}" width="30%" height="100px" alt="icon"></td> -->
                                    <td>{{ $category->cat_name }}</td>
                                    <td>
                                        <button class="btn btn-edit editBtn" data-category-id='{{ $category->id }}' title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-delete deleteBtn" data-category-id='{{ $category->id }}'>
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
    $('.deleteBtn').on('click', function (event) {//not using right now
        if (confirm("Are you sure?")) {
            //fetch the blog id
            var catId = $(this).attr('data-category-id');
            var url = "{{ url('/admin/sub_cat_1/') }}";
            url += '/' + catId;

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
        var url = "{{ url('/admin/sub_cat_1/') }}";
        url += '/' + catId + '/edit';
        window.location.href = url;
    });

    $(document).ready(function () {
        $('#subCat1List').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>