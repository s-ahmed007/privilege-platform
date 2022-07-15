@php use \App\Http\Controllers\Enum\DynamicLinkType; @endphp
@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('delete'))
                <div class="alert alert-danger">
                    {{ session('delete') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @endif
            <h3>Dynamic link /Create /Edit /Delete</h3>
             <a type="button" class="btn btn-create" href="{{ url('/admin/dynamic_links/create') }}"
                style="margin-left: unset;">+ Create A New Link</a>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    @if($links)
                        <table id="offersList" class="table table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Image</th>
                                <th>Redirect URL</th>
                                <th>Tag</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $i=1; @endphp
                            @foreach ($links as $link)
                                <?php $values = $link->values[0]; ?>
                                <tr class="opening_row">
                                    <td>{{ $i }}</td>
                                    <td>
                                        <img src="{{ $values['image_url'] }}" alt="Image" width="100" height="100">
                                    </td>
                                    <td>{{ $values['redirect_url'] }}</td>
                                    <td>
                                        @if($link->tag == DynamicLinkType::HOMEPAGE_BANNER)
                                            Homepage Banner
                                        @elseif($link->tag == DynamicLinkType::APP_HOMEPAGE_POPUP)
                                            App Homepage Popup
                                        @endif
                                    </td>
                                    <td>
                                    @if($values['active'] == 1)
                                        <span class="all-label">Active</span>
                                    @else
                                        <span class="trial-label">Inactive</span>
                                    @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-edit editBtn" title="Edit"
                                                data-link-id='{{ $link->id }}'><i class="fa fa-edit"></i></button>
                                        <button class="btn btn-delete deleteBtn" title="Delete"
                                                data-link-id='{{ $link->id }}'><i class="fa fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            @php $i++; @endphp
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No link found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure to delete?")) {
            var linkId = $(this).attr('data-link-id');
            var url = "{{ url('/admin/dynamic_links') }}";
            url += '/' + linkId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $('.editBtn').on('click', function (event) {
        var linkId = $(this).attr('data-link-id');
        var url = "{{ url('/admin/dynamic_links') }}";
        url += '/' + linkId + '/edit';
        window.location.href = url;
    });

    $(document).ready(function () {
        $('#linkList').DataTable({
            "order": []
        });
    });
</script>