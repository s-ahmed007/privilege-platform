@include('admin.production.header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @elseif (session('delete branch'))
                <div class="alert alert-danger">
                    {{ session('delete branch') }}
                </div>
            @elseif (session('delete partner'))
                <div class="alert alert-danger">
                    {{ session('delete partner') }}
                </div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">
                    {{ session('try_again') }}
                </div>
            @elseif(session('can-not-delete'))
                <div class="alert alert-warning">
                    {{ session('can-not-delete') }}
                </div>
            @endif
            <h3>Total Partners - <?php if (isset($allPartnersCount)) { echo $allPartnersCount; }?>
            </h3>
            <h2>Active Partners - <?php if (isset($activePartnersCount)) { echo $activePartnersCount; }?></h2>
            <h2>Deactive Partners - <?php if (isset($deactivePartnersCount)) { echo $deactivePartnersCount; }?>
            </h2>
        </div>
        <div class="title_left">
            <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('changePartnerStatus') }}" method="get">
                    <div class="form-group">
                        <label for="partnerSearchKey">Search Current Partners</label><br>
                        <input type="text" class="form-control" name="partnerName" id="partnerSearchKey"
                               placeholder="Partner with name" style="width: 100%;border-radius: 25px">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <div class="">
                    @if(isset($allPartners))
                    <table class="table table-striped projects">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Partner Name</th>
                            <th>Change Main Partner Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($allPartners as $key => $value)
                            <tr>
                                <td>{{ $value->serial }}</td>
                                <td>{{ $value->info->partner_name }}</td>
                                <td>
                                    <?php
                                    if ($value->active == 1) {
                                        $change_to = 0;
                                        $change_to_text = "Deactivate";
                                        echo '<button class="btn btn-deactivate changeStatusBtn" data-partner-account-id="' . $value->partner_account_id . '">' . $change_to_text . '</button>';
                                    } else {
                                        $change_to = 1;
                                        $change_to_text = "Activate";
                                        echo '<button class="btn btn-activate changeStatusBtn" data-partner-account-id="' . $value->partner_account_id . '">' . $change_to_text . '</button>';
                                    }
                                    ?>
                                    @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                        <button class="btn btn-delete deleteBtn"
                                                data-partner-id='{{ $value->partner_account_id }}'>Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                        {{$allPartners->links()}}
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'Partner not found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
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
</script>