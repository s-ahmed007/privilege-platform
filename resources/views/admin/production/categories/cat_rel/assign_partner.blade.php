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
            <h3>Category Relation Create/ Edit/ Display</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <hr>
            <form action="{{ url('admin/category_relation/assign_partner/'.$cat_rel->id) }}" class="form-horizontal" method="post">
                <div class="form-group">
                    <div class="col-sm-3">
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('main_cat'))
                                {{ $errors->getBag('default')->first('main_cat') }}
                                <br>
                            @endif
                        </span>
                        <label for="">Main Category</label>
                        <input type="text" class="form-control" value="{{$cat_rel->mainCategory->name}}" disabled>
                    </div>
                    <div class="col-sm-3">
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('sub_cat_1'))
                            {{ $errors->getBag('default')->first('sub_cat_1') }}
                            <br>
                        @endif
                        </span>
                        <label for="">Sub Category 1</label>
                        <input type="text" class="form-control" value="{{$cat_rel->sub_cat_1 ? $cat_rel->sub_cat_1->cat_name : ''}}" disabled>
                    </div>
                    <div class="col-sm-3">
                        <span style="color: red;">
                        @if ($errors->getBag('default')->first('sub_cat_2'))
                                {{ $errors->getBag('default')->first('sub_cat_2') }}
                            @endif
                        </span>
                        <label for="">Sub Category 2</label>
                        <input type="text" class="form-control" value="{{$cat_rel->sub_cat_2 ? $cat_rel->sub_cat_2->cat_name : ''}}" disabled>
                    </div>
                    <div class="col-sm-3">
                        <label for="">Partner</label>
                        <select class="form-control" name="partner" required>
                            <option value="" selected>Select</option>
                            @foreach($partners as $partner)
                                <option value="{{$partner->partner_account_id}}" {{old('partner') == $partner->partner_account_id ? 'selected' : ''}}>
                                    {{$partner->partner_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label for=""></label>
                        <button type="submit" class="btn btn-success form-control">Assign partner</button>
                    </div>
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
            <hr>
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($assigned_partners)
                        <table id="catRelList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>Partner</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($assigned_partners as $partner)
                                <tr class="blog_row">
                                    <td>{{$partner->info->partner_name}}</td>
                                    <td>
                                        <button class="btn btn-delete deleteBtn" data-rel-id="{{$partner->id}}" title="Remove">
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
            var relId = $(this).attr('data-rel-id');
            var url = "{{ url('/admin/category_relation/remove_assigned_partner') }}";
            url += '/' + relId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $(document).ready(function () {
        $('#catRelList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>