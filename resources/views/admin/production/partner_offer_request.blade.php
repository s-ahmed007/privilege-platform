@include('admin.production.header')

<div class="right_col" role="main">
    <div class="page-title">
        @if(session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <div class="title_left">
            <h3>All Offer Requests</h3>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped projects">
                        <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Partner Info</th>
                            <th>Offer Request</th>
                            <th>Requested On</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($wishes as $key => $wish)
                            <tr>
                                <th>{{$key+1}}</th>
                                <td>
                                    {{ $wish->branchUser->branchScanner->full_name}}<br>
                                    {{ $wish->branchUser->branchScanner->branch->info->partner_name.', '.
                                     $wish->branchUser->branchScanner->branch->partner_area}}<br>
                                    {{ $wish->branchUser->branchScanner->branch->partner_mobile }}
                                </td>
                                <td>{{ $wish->comment }}</td>
                                <td>
                                    {{date("F d, Y h:i A", strtotime($wish->posted_on))}}
                                </td>
                                <td><input type="button" class="btn btn-delete" value="Delete"
                                           onclick="delete_wish('{{ $wish->id }}')"></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.production.footer')
<script>
    function delete_wish(wish_id) {
        if (confirm('Are you sure to delete the wish?')) {
            var url = "{{ url('/delete_wish') }}" + '/' + wish_id;
            window.location = url;
        }
    }
</script>