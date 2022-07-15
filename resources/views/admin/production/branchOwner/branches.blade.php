@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>
                <td>{{ $branches[0]->owner->name }} Branches</td>
            </h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <div class="">
                    <table id="branchList" class="table table-striped projects">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Partner Name</th>
                            <th>Address</th>
                            <th>Expiry</th>

                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($branches))
                            @foreach ($branches as $key => $value)
                                <tr>
                                    <td>{{ $value->partner_account_id }}</td>
                                    <td>{{ $value->info->partner_name }}</td>
                                    <td width="50%">{{ $value->partner_address }}</td>
                                    <td>{{ $value->info->expiry_date }}</td>
                                </tr>
                            @endforeach
                        @else
                            <div style="font-size: 1.4em; color: red;">
                                {{ 'Partner not found.' }}
                            </div>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script>
    $(function () {
        $("#partnerSearchKey").autocomplete({
            source: '{{url('/partnerByKeyName')}}',
            autoFocus: true,
            delay: 500
        });
    });
</script>
<script type="text/javascript">
    $('.changeStatusBtn').on('click', function (event) {

        if (confirm("Are you sure to change this partner status?")) {
            //fetch the partner branch id
            var partnerAccountId = $(this).attr('data-partner-account-id');
            var url = "{{ url('/partner-change-status/partner') }}";
            url += '/' + partnerAccountId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '</form>').appendTo($(document.body)).submit();

        }
        return false;
    });

    //script to delete partner
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure you want to delete this partner?")) {
            //fetch the partner branch id
            var partnerBranchId = $(this).attr('data-partner-id');
            var url = "{{ url('/partner-delete') }}";
            url += '/' + partnerBranchId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $(document).ready(function () {
        $('#branchList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>