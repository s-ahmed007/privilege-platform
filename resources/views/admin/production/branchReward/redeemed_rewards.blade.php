@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<style>
    .requested {
        display: inline-block;
        min-width: 10px;
        padding: 3px 7px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        float: right;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        background-color: #de8414;
        border-radius: 10px;
    }
    .dispatched {
        display: inline-block;
        min-width: 10px;
        padding: 3px 7px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        float: right;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        background-color: green;
        border-radius: 10px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        color: #337ab7 !important;background-color: #eee !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        color: #337ab7 !important; background-color: #ffffff !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current{
        color: #ffffff !important; background-color: #337ab7 !important}
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{
        color: #ffffff !important; background-color: #337ab7 !important}
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Redeemed {{$type == 'partner' ? 'Partner' : 'Royalty'}} Rewards</h3>
            <a class="btn btn-submit" href="{{url('admin/redeemed_reward/'.$type.'/all')}}">All</a>
            <a class="btn btn-guest" href="{{url('admin/redeemed_reward/'.$type.'/requested')}}">Requested</a>
            <a class="btn btn-submit" style="background-color: #13CE66" href="{{url('admin/redeemed_reward/'.$type.'/dispatched')}}">
                {{$type == 'partner' ? 'Used' : 'Dispatched'}}
            </a>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($redeems)
                        <table id="rewardsList" class="table table-bordered table-hover table-striped projects">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Reward</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Others</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $i=1; ?>
                            @foreach ($redeems as $key => $redeem)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>
                                        <b>{{ $redeem->reward->offer_description }}</b> <br>
                                        Quantity - {{$redeem->quantity}}<br>
                                        @if($redeem->reward->branch != null)
                                        {{$redeem->reward->branch->info->partner_name.', '.$redeem->reward->branch->partner_area}}
                                        @else

                                        @endif
                                    </td>
                                    <td><b>{{ $redeem->customer['customer_full_name'] }}</b><br>
                                        {{ $redeem->customer['customer_contact_number']}}<br>
                                    </td>
                                    <?php
                                    $posted_on = date("Y-M-d H:i:s", strtotime($redeem->created_at));
                                    $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                                    $exp_date = date("Y-M-d", strtotime($redeem->reward->date_duration[0]['to']));
                                    $exp_date = \Carbon\Carbon::createFromTimeStamp(strtotime($exp_date));
                                    ?>
                                    <td>
                                        @foreach((array)$redeem->required_fields as $field)
                                            @if($field['type'] == \App\Http\Controllers\Enum\RewardRequiredFieldsType::phone)
                                                Phone: {{$field['value']}}<br>
                                            @endif
                                            @if($field['type'] == \App\Http\Controllers\Enum\RewardRequiredFieldsType::email)
                                                Email: {{$field['value']}}<br>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach((array)$redeem->required_fields as $field)
                                            @if($field['type'] == \App\Http\Controllers\Enum\RewardRequiredFieldsType::del_add)
                                                Address: {{$field['value']}}<br>
                                            @endif
                                            @if($field['type'] == \App\Http\Controllers\Enum\RewardRequiredFieldsType::others)
                                                Others: {{$field['value']}}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        {{date("F d, Y h:i A", strtotime($redeem->created_at))}}<br>
                                        <b>Expiry: {{ date("F d, Y", strtotime($redeem->reward->date_duration[0]['to']))}}</b>
                                        @if(date('Y-m-d', strtotime($redeem->reward->date_duration[0]['to'])) < date('Y-m-d'))
                                            <span class="manual-label">Expired</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($status == 'all')
                                            @if($redeem->used == 0)
                                                <span class="guest-label">Requested</span>
                                            @elseif($redeem->used == 1)
                                                @if($type != 'partner')
                                                    <span class="admin-label">Dispatched</span>
                                                @else
                                                    <span class="admin-label">Used</span>
                                                @endif
                                            @endif
                                        @elseif($status == 'requested')
                                            @if($type != 'partner')
                                            <a class="btn btn-success" href="{{ url('admin/dispatch_reward/'.$redeem->id) }}"
                                               onclick="return confirm('Are you sure?')">Dispatch</a>
                                            @else
                                                <span class="guest-label">Requested</span>
                                            @endif
                                        @else
                                            @if($type != 'partner')
                                                <span class="admin-label">Dispatched</span>
                                            @else
                                                <span class="admin-label">Used</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No request found.' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.production.footer')
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

{{-- ============================================================================================================
  ========================Opening edit & delete====================
============================================================================================================= --}}
<script>
    $('.deleteBtn').on('click', function (event) {
        if (confirm("Are you sure to delete?")) {
            //fetch the opening id
            var offerId = $(this).attr('data-offer-id');
            var url = "{{ url('/branch-offers') }}";
            url += '/' + offerId;

            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}"/>' +
                '<input type="hidden" name="_method" value="DELETE"/>' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    });

    $('.editBtn').on('click', function (event) {
        //fetch the opening id
        var offerId = $(this).attr('data-offer-id');
        var url = "{{ url('/admin/reward') }}";
        url += '/' + offerId + '/edit';
        window.location.href = url;
    });

    $('.activeBtn').on('click', function (event) {
        if (confirm('Are you sure to activate?')) {
            //fetch the opening id
            var offerId = $(this).attr('data-offer-id');
            var url = "{{ url('/activate-reward') }}" + '/' + offerId;
            window.location.href = url;
        }
    });

    $('.deactiveBtn').on('click', function (event) {
        if (confirm('Are you sure to deactivate?')) {
            //fetch the opening id
            var offerId = $(this).attr('data-offer-id');
            var url = "{{ url('/deactivate-reward') }}" + '/' + offerId;
            window.location.href = url;
        }
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#rewardsList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>