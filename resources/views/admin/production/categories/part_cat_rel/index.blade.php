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
                <div class="alert alert-danger">
                    {{ session('try_again') }}
                </div>
            @endif
            <h3>Partner Category Relation Edit</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($relations)
                        <table id="partCatRelList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>Main Category</th>
                                <th>Sub Category 1</th>
                                <th>Sub Category 2</th>
                                <th>Partner</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($relations as $row)
                                <tr class="blog_row" data-category-id='{{ $row->id }}'>
                                    <td>{{$row->categoryRelation->mainCategory->name}}</td>
                                    <td>
                                        @if($row->categoryRelation->sub_cat_1_id)
                                            {{ $row->categoryRelation->sub_cat_1->cat_name }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->categoryRelation->sub_cat_2_id)
                                            {{ $row->categoryRelation->sub_cat_2->cat_name }}
                                        @endif
                                    </td>
                                    <td>{{$row->info->partner_name}}</td>
                                    <td>
                                        <button class="btn btn-edit editBtn" data-relation-id='{{ $row->id }}' title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-delete deleteBtn" data-relation-id='{{ $row->id }}' title="Assign Partner">
                                            <i class="glyphicon glyphicon-trash"></i>
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
========================relation edit & delete====================
============================================================================================================= --}}
<script type="text/javascript">
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure?")) {
            //fetch the relation id
            var relId = $(this).attr('data-relation-id');
            var url = "{{ url('/admin/part_cat_relation/') }}";
            url += '/' + relId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $('.editBtn').on('click', function (event) {
        //fetch the relation id
        var relId = $(this).attr('data-relation-id');
        var url = "{{ url('/admin/part_cat_relation/') }}";
        url += '/' + relId + '/edit';
        window.location.href = url;
    });

    $(document).ready(function () {
        $('#partCatRelList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>