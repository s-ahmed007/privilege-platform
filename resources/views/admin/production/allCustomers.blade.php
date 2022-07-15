@include('admin.production.header')
<style>
    .bg_color {
        background-color: #ffedc9;
        background-image: linear-gradient(to top right, #ffd3c9, #c9f7ff, #cdc9ff);
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/>
<?php use \App\Http\Controllers\functionController; ?>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left" style="width: 60%;">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @elseif (session('delete customer'))
                <div class="alert alert-danger">{{ session('delete customer') }}</div>
            @elseif(session('try_again'))
                <div class="alert alert-warning">{{ session('try_again') }}</div>
            @elseif(session('codPaymentClear'))
                <div class="alert alert-success">{{ session('codPaymentClear') }}</div>
            @elseif(session('cardActivated'))
                <div class="alert alert-success">{{ session('cardActivated') }}</div>
            @elseif(session('user_active'))
                <div class="alert alert-warning">{{ session('user_active') }}</div>
            @elseif(session('cod exists'))
                <div class="alert alert-warning">{{ session('cod exists') }}</div>
            @else @endif
            <h3>Members {{$tab_title != '' ? '('.$tab_title.')': ''}}</h3>
            <a class="btn btn-all" href="{{url('customers/card_users')}}">All</a>
{{--            <a class="btn btn-guest" href="{{url('customers/guest')}}">Guest</a>--}}
{{--            <a class="btn btn-trial" style="padding: 10px;" href="{{url('customers/trial')}}">Trial</a>--}}
            <a class="btn btn-premium" href="{{url('customers/card_holders')}}">Premium</a>
{{--            <a class="btn btn-spot" href="{{url('customers/spot')}}">Spot Purchase/Manual Reg</a>--}}
            <a class="btn btn-active" href="{{url('customers/active')}}">Active Members</a>
{{--            <a class="btn btn-inactive-trial" href="{{url('customers/inactive/trial')}}">Inactive Trial</a>--}}
            <a class="btn btn-inactive-premium" href="{{url('customers/inactive/premium')}}">Inactive Premium</a>
{{--            <a class="btn btn-expired" href="{{url('customers/expired/trial')}}">Expired Trial</a>--}}
{{--            <a class="btn btn-expired" href="{{url('customers/expired/premium')}}">Expired Premium</a>--}}
{{--            <a class="btn btn-expiring" href="{{url('customers/expiring')}}">Expiring</a>--}}
{{--            <a class="btn btn-upgrade" href="{{url('customers/upgraded')}}">Upgrade</a>--}}
{{--            <a class="btn btn-renew" href="{{url('customers/renewed')}}">Renew</a>--}}
            <a class="btn btn-influencer" style="padding: 10px;" href="{{url('customers/influencer')}}">Influencer</a>
                <form method="post" action="{{url('/pdf/generate/emails')}}">
                    {{csrf_field()}}
                    <input type="hidden" name="title" value="{{$tab_title}}">
                    <input type="hidden" name="emails" value="{{$emails_to_print}}" id="email_list">
                    <input class="btn btn-influencer" type="submit"  value="Generate Email List">
                </form>
{{--            <button class="btn btn-active" onclick="generateEmailPDF('{{$user_list_type}}')">Generate</button>--}}
            {{--                <a class="btn btn-recent" href="{{url('customers/recent')}}">Recent Members</a>--}}
        </div>
        <div class="title_right" style="width: 40%; float: right">
            <div class="col-md-8 col-sm-5 col-xs-12 form-group pull-right top_search">
                <form action="{{ url('customerById') }}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="customerSearchKey">Search Confirmed Member</label>
                        <br>
                        <input type="text" class="form-control" name="customerSearchKey" id="customerSearchKey"
                               placeholder="User with name, E-mail, phone or username" style="width: 100%;">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    @if($profileInfo)
                        <table class="table table-bordered table-hover table-striped projects">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th style="width: 10%">Image</th>
                                <th style="width: 10%">Customer ID</th>
                                <th style="width: 15%">Customer Info</th>
                                <th>Membership Plan</th>
                                <!-- <th>DOB</th>
                                <th>Gender</th> -->
{{--                                @if($customer_type != '(Guest)')--}}
{{--                                    <th>Purchase Type</th>--}}
{{--                                @endif--}}
                            <!-- @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                <th>Status</th>
@endif -->
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($profileInfo as $key => $customerInfo)
                                <?php
//                                if ($customerInfo->customerHistory != null && $customerInfo->customerHistory->type == \App\Http\Controllers\Enum\CustomerType::card_holder) {
//                                    $bg_color = 'bg_color';
//                                } else {
//                                    $bg_color = '';
//                                }
                                ?>
                                {{--<tr class="{{$bg_color}}">--}}
                                <tr>
                                    <td>{{ ($profileInfo->currentpage()-1) * $profileInfo->perpage() + $key + 1 }}</td>
                                    <td><img src="{{ $customerInfo->customer_profile_image }}" alt="Profile Image" width="100%"
                                             style="border-radius: 50%"></td>
                                    <td>{{ $customerInfo->customer_id }}<br>
{{--                                        <p>--}}
{{--                                            @if($customerInfo->customerHistory != null)--}}
{{--                                                @if($customerInfo->card_active==1)--}}
{{--                                                    <a href="{{url('card-active-by-admin/'.$customerInfo->customer_id)}}">--}}
{{--                                                        <button type="button" style="margin-left: unset;" class="btn btn-activate pull-right"--}}
{{--                                                                onclick="return confirm('Are you sure you want to activate membership of this customer?');">--}}
{{--                                                            <b>Activate</b>--}}
{{--                                                        </button>--}}
{{--                                                    </a>--}}
{{--                                                @elseif((new \App\Http\Controllers\functionController2())->getExpStatusOfCustomer($customerInfo->expiry_date)=='expired')--}}
{{--                                                    <b style="color: #F59B00">Expired on:--}}
{{--                                                        <br><i>{{date('d-m-Y',strtotime($customerInfo->expiry_date))}}</i></b>--}}
{{--                                                @elseif((new \App\Http\Controllers\functionController2())->getExpStatusOfCustomer($customerInfo->expiry_date)=='10 days remaining')--}}
{{--                                                    <b style="color: green"></b>--}}
{{--                                                @elseif($customerInfo->card_active==2)--}}
{{--                                                    <b style="color: green"></b>--}}
{{--                                                @endif--}}
{{--                                            @endif--}}
{{--                                        </p>--}}
                                        <?php $influencer = (new functionController)->influencersPromoUsed($customerInfo->customer_id);
                                        if($influencer['user'] != null){ ?>
                                        <p><span style="color: #12A03E"><i class="bx bxs-star yellow"></i><b>INFLUENCER</b></span></p>
                                        <?php } ?>
                                        <p>
                                            <?php $time1 = explode(' ', $customerInfo->member_since); ?>
                                            <span style="white-space: nowrap"> <b>Joined: </b> <i>
                                                 @if($time1[1] != '00:00:00')
                                                        {{date("M d, Y h:i A", strtotime($customerInfo->member_since))}}
                                                    @else
                                                        {{date("M d, Y", strtotime($customerInfo->member_since))}}
                                                    @endif
                                                </i>
                                            </span>
{{--                                            @if($customerInfo->customerHistory != null)--}}
{{--                                                @if($customerInfo->latestSSLTransaction != null)--}}
{{--                                                    @if($customerInfo->latestSSLTransaction->tran_date != null)--}}
{{--                                                        <?php $time = explode(' ', $customerInfo->latestSSLTransaction->tran_date); ?>--}}
{{--                                                        @if($time[1] != '00:00:00')--}}
{{--                                                            <span style="white-space: nowrap"> <b>Purchased: </b> <i>{{date("M d, Y h:i A", strtotime($customerInfo->latestSSLTransaction->tran_date))}}</i>--}}
{{--                                                            </span>--}}
{{--                                                        @else--}}
{{--                                                            <span style="white-space: nowrap"> <b>Purchased: </b> <i>{{date("M d, Y", strtotime($customerInfo->latestSSLTransaction->tran_date))}}</i>--}}
{{--                                                            </span>--}}
{{--                                                        @endif--}}
{{--                                                    @else--}}
{{--                                                        <span style="white-space: nowrap"> <b>Purchased: </b> <i>N/A</i></span>--}}
{{--                                                    @endif--}}
{{--                                                    <span style="white-space: nowrap"> <b>Expiry: </b> <i>{{date("M d, Y", strtotime($customerInfo->expiry_date))}}</i>--}}
{{--                                                        </span>--}}
{{--                                                @else--}}
{{--                                                    <span style="white-space: nowrap"> <b>Purchased: </b> N/A</span>--}}
{{--                                                @endif--}}
{{--                                            @endif--}}
                                        </p>
{{--                                        @if($customer_type == '(Guest)')--}}
{{--                                            <a href="{{url('admin/activate_trial/'.$customerInfo->customer_id)}}" class="btn btn-success"--}}
{{--                                               onclick="return confirm('Are you sure?');">Activate Trial</a>--}}
{{--                                        @endif--}}
                                    </td>
                                    <td>{{ $customerInfo->customer_full_name }}
                                        <br> <span class="{{$customerInfo->email_verified == 1 ? 'text-success' : 'text-danger'}}">
                                            {{$customerInfo->customer_email}}</span>
                                        <br> {{ $customerInfo->customer_contact_number }}<br>
                                        @if($customerInfo->account->platform == \App\Http\Controllers\Enum\PlatformType::web ||
                                            $customerInfo->account->platform == \App\Http\Controllers\Enum\PlatformType::rbd_admin)
                                            Via: <b>Website</b><br>
                                        @elseif($customerInfo->account->platform == \App\Http\Controllers\Enum\PlatformType::android)
                                            Via: <b>Android</b><br>

                                        @elseif($customerInfo->account->platform == \App\Http\Controllers\Enum\PlatformType::ios)
                                            Via: <b>IOS</b><br>

                                        @elseif($customerInfo->account->platform == \App\Http\Controllers\Enum\PlatformType::sales_app)
                                            Via: <b>Sales App</b><br>
                                        @else
                                            Via: <b>N/A</b><br>
                                        @endif
{{--                                        @if($customerInfo->customerHistory != null&&$customerInfo->customerHistory->sellerInfo != null)--}}
{{--                                        <br>--}}
{{--                                            Seller:--}}
{{--                                            <b>{{$customerInfo->customerHistory->sellerInfo->first_name}} {{$customerInfo->customerHistory->sellerInfo->last_name}}</b>--}}
{{--                                        @endif--}}
                                    </td>
                                    <td>
                                        <p><b>Premium Member</b></p>
{{--                                        @if ($customerInfo->customerHistory!=null)--}}
{{--                                            <p><b>{{ $customerInfo->month }} {{$customerInfo->month > 1 ? "Months":"Month"}}</b></p>--}}
{{--                                        @else--}}
{{--                                            <p class="card-type-guest">Guest</p>--}}
{{--                                        @endif--}}
                                        @if( $customerInfo->customerReferrer != null)
                                            <p class="middle">
                                                Referral: <br><b>{{$customerInfo->customerReferrer->customer_full_name}}<br>
                                                    {{$customerInfo->customerReferrer->customer_id}}</b><br>
                                                Total Refers: <b>{{$customerInfo->customerReferrer->reference_used}}
                                                </b>
                                            </p>
                                        @endif
                                        @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin)
                                            <p id="user_approval">Active:
                                                @if($customerInfo->account->moderator_status == 1)
                                                    <i class="cross-icon-admin" style="font-size: 2em; cursor: pointer"
                                                       id="statusSign_{{$customerInfo->customer_id}}"
                                                       onclick="userApproval(
                                                               '1',
                                                               '{{$customerInfo->customer_id}}',
                                                               'Are you sure you want to activate this customer?',
                                                               'moderator_status'
                                                               )">
                                                    </i>
                                                @else
                                                    <i class="check-icon" style="font-size: 2em; color: green; cursor: pointer"
                                                       id="statusSign_{{$customerInfo->customer_id}}"
                                                       onclick="userApproval(
                                                               '2',
                                                               '{{$customerInfo->customer_id}}',
                                                               'Are you sure you want to deactivate this customer?',
                                                               'moderator_status'
                                                               )">
                                                    </i>
                                                @endif
                                                <br>
                                                Suspend:
                                                @if($customerInfo->account->isSuspended == 1)
                                                    <i class="cross-icon-admin" style="font-size: 2em; cursor: pointer"
                                                       id="suspensionSign_{{$customerInfo->customer_id}}"
                                                       onclick="userApproval(
                                                               '1',
                                                               '{{$customerInfo->customer_id}}',
                                                               'Are you sure you want to unsuspend this customer?',
                                                               'suspension'
                                                               )">
                                                    </i>
                                                @else
                                                    <i class="check-icon" style="font-size: 2em; color: green; cursor: pointer"
                                                       id="suspensionSign_{{$customerInfo->customer_id}}"
                                                       onclick="userApproval(
                                                               '0',
                                                               '{{$customerInfo->customer_id}}',
                                                               'Are you sure you want to suspend this customer?',
                                                               'suspension'
                                                               )">
                                                    </i>
                                                @endif
                                            </p>
                                        @endif
                                    </td>
                                <!-- <td>{{ $customerInfo->customer_dob != null ? $customerInfo->customer_dob : 'N/A' }}</td>
                                    <td>{{ $customerInfo->customer_gender != null ? $customerInfo->customer_gender : 'N/A' }}</td> -->
{{--                                    @if($customer_type != '(Guest)')--}}
{{--                                        <td>--}}
{{--                                            <p>--}}
{{--                                                @if(!$customerInfo->latestSSLTransaction)--}}
{{--                                                    <span class="guest-label">Guest Member</span>--}}
{{--                                                @elseif(!$customerInfo->latestSSLTransaction->cardDelivery)--}}
{{--                                                    N/A--}}
{{--                                                @elseif($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==1)--}}
{{--                                                    <span class="upgrade-label">{{$customerInfo->isUpgrade() == true ? 'Upgrade' : 'Online'}}</span>--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==2)--}}
{{--                                                    Office Pickup--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==3)--}}
{{--                                                    Pre-Order<br>COD--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==4)--}}
{{--                                                    COD--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==5)--}}
{{--                                                    Customization--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==6)--}}
{{--                                                    COD<br>(Lost-card)--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==7)--}}
{{--                                                    Customization<br>(Lost-card)--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==9)--}}
{{--                                                    <span class="spot-label">Spot</span>--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==\App\Http\Controllers\Enum\DeliveryType::made_by_admin)--}}
{{--                                                    <span class="admin-label">Admin</span>--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==10)--}}
{{--                                                    <span class="influencer-label">Influencer</span>--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==11)--}}
{{--                                                    <span class="trial-label">Trial</span>--}}
{{--                                                @elseif ($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==12)--}}
{{--                                                    <span class="renew-label">Renew</span>--}}
{{--                                                @endif--}}
{{--                                                    <br>--}}
{{--                                                @if($customerInfo->latestSSLTransaction && $customerInfo->latestSSLTransaction->platform == \App\Http\Controllers\Enum\PlatformType::rbd_admin)--}}
{{--                                                    <span class="admin-label">Admin</span>--}}
{{--                                                @endif--}}
{{--                                            </p>--}}
{{--                                        </td>--}}
{{--                                    @endif--}}

                                    <td style="text-align: center;">
                                        <select id="customer_edit_{{$customerInfo->customer_id}}" class="selectChangeOff"
                                                onchange="customer_edit('{{$customerInfo->customer_id}}')">
                                            <option value="0" disabled selected>--Options--</option>
                                            <option value="1">Edit Info</option>
{{--                                            <option value="2">Lost Card</option>--}}
{{--                                            @if($customerInfo->customerHistory==null)--}}
{{--                                                <option value="5">Upgrade Membership</option>--}}
{{--                                            @elseif($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==11 &&--}}
{{--                                                (new \App\Http\Controllers\functionController2())->getExpStatusOfCustomer($customerInfo->expiry_date) == 'active'))--}}
{{--                                                <option value="5">Upgrade Membership</option>--}}
{{--                                            @elseif($customerInfo->latestSSLTransaction->cardDelivery->delivery_type==11 &&--}}
{{--                                                (new \App\Http\Controllers\functionController2())->getExpStatusOfCustomer($customerInfo->expiry_date) != 'active'))--}}
{{--                                                <option value="5">Renew Membership</option>--}}
{{--                                            @elseif($customerInfo->latestSSLTransaction->cardDelivery->delivery_type!=11 &&--}}
{{--                                                (new \App\Http\Controllers\functionController2())->getExpStatusOfCustomer($customerInfo->expiry_date) != 'active'))--}}
{{--                                            <option value="5">Renew Membership</option>--}}
{{--                                            @endif--}}
                                        </select>
{{--                                        @if(Session::get('admin') == \App\Http\Controllers\Enum\AdminRole::superadmin && $customerInfo->customerHistory==null)--}}
{{--                                            <button class="btn btn-delete" onclick="deleteUser('{{$customerInfo->customer_id}}')">Delete--}}
{{--                                            </button>--}}
{{--                                        @endif--}}
                                        <br>
                                        <span>Last visit: <br>
                                        @if($customerInfo->customerLastActivitySession)
                                            {{date("M d, Y h:i A",strtotime($customerInfo->customerLastActivitySession->created_at)).' by '}}
                                            <br>
                                            @if($customerInfo->customerLastActivitySession->platform == \App\Http\Controllers\Enum\PlatformType::web)
                                                Website
                                            @elseif($customerInfo->customerLastActivitySession->platform == \App\Http\Controllers\Enum\PlatformType::android)
                                                Android
                                            @elseif($customerInfo->customerLastActivitySession->platform == \App\Http\Controllers\Enum\PlatformType::ios)
                                                IOS
                                            @endif
                                            @if($customerInfo->customerLastActivitySession->version != null)
                                                Version: {{$customerInfo->customerLastActivitySession->version}}
                                            @else
                                                Version: N/A
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                            </tr>
                            </tfoot>
                        </table>
                        {{ $profileInfo->links() }} @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No customers found.' }}
                        </div>
                    @endif
                </div>
                <!--end of .table-responsive-->
            </div>
        </div>
    </div>
</div>
<div id="deactivateReasonModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">Please choose one reason</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
                
            </div>
            <div class="modal-body">
                <input type="radio" name="gender" value="{{\App\Http\Controllers\Enum\MiscellaneousType::lost_card}}">Lost card
                <br>
                <input type="radio" name="gender" value="{{\App\Http\Controllers\Enum\MiscellaneousType::miss_behave}}"> Misbehave
                <br>
                <input type="radio" name="gender" value="{{\App\Http\Controllers\Enum\MiscellaneousType::others}}">Others
                <input type="hidden" name="status">
                <input type="hidden" name="userId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="confirmApproval()">Go</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="currentPage" value="{{isset($_GET['page']) ? $_GET['page'] : '1'}}">
@include('admin.production.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(function () {
        $("#customerSearchKey").autocomplete({
            source: '{{url('/customerByKey')}}',
            autoFocus: true,
            delay: 500
        });
    });
</script>
{{-- =================customer approval with JavaScript & Ajax======================= --}}
<script>
    function userApproval(status, userId, prompt_text, activity) {
        if (activity == 'suspension') {//change user suspension status
            if (prompt_text === 'Are you sure you want to suspend this customer?') {
                if (confirm(prompt_text)) {
                    userSuspensionAjax(status, userId);
                }
            } else {
                if (confirm(prompt_text)) {
                    userSuspensionAjax(status, userId);
                }
            }
        } else {//change user moderator status
            if (prompt_text === 'Are you sure you want to activate this customer?') {
                if (confirm(prompt_text)) {
                    var misc = 0;
                    userApprovalAjax(status, userId, misc);
                }
            } else {
                if (confirm(prompt_text)) {
                    $("input[name=status]").val(status);
                    $("input[name=userId]").val(userId);
                    $("#deactivateReasonModal").modal('toggle');
                }
            }
        }
    }

    function confirmApproval() {
        var status = $('input[name=status]').val();
        var userId = $('input[name=userId]').val();
        var misc = $('input[name=gender]:checked').val();

        userApprovalAjax(status, userId, misc);
    }

    function userApprovalAjax(status, userId, misc) {
        var url = "{{ url('/customerApproval') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'userId': userId,
                'status': status,
                'misc': misc
            },
            success: function (data) {
                if (data[0] === '1') {
                    $('#statusSign_' + data[1]).removeClass('cross-icon-admin');
                    $('#statusSign_' + data[1]).addClass('check-icon');
                    document.getElementById('statusSign_' + data[1]).style.color = 'green';
                    $('#statusSign_' + data[1]).attr(
                        "onclick",
                        "userApproval(2," + data[1] +
                        ", 'Are you sure you want to deactivate this customer?', 'moderator_status)"
                    );
                } else {
                    $('#statusSign_' + data[1]).removeClass('check-icon');
                    $('#statusSign_' + data[1]).addClass('cross-icon-admin');
                    $('#statusSign_' + data[1]).attr(
                        "onclick",
                        "userApproval(1," + data[1] +
                        ", 'Are you sure you want to activate this customer?', 'moderator_status')"
                    );
                }
            }
        });
    }

    function userSuspensionAjax(status, userId) {
        var url = "{{ url('/customerSuspension') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'userId': userId,
                'status': status
            },
            success: function (data) {
                if (data[0] === '1') {
                    $('#suspensionSign_' + data[1]).removeClass('cross-icon-admin');
                    $('#suspensionSign_' + data[1]).addClass('check-icon');
                    document.getElementById('suspensionSign_' + data[1]).style.color = 'green';
                    $('#suspensionSign_' + data[1]).attr(
                        "onclick",
                        "userApproval(0," + data[1] +
                        ", 'Are you sure you want to suspend this customer?', 'suspension')"
                    );
                } else {
                    $('#suspensionSign_' + data[1]).removeClass('check-icon');
                    $('#suspensionSign_' + data[1]).addClass('cross-icon-admin');
                    $('#suspensionSign_' + data[1]).attr(
                        "onclick",
                        "userApproval(1," + data[1] +
                        ", 'Are you sure you want to unsuspend this customer?', 'suspension')"
                    );
                }
            }
        });
    }

    {{-- ====guest customer delete==== --}}

    function deleteUser(customerId) {
        if (confirm("Are you sure to delete this customer?")) {
            var url = "{{ url('/delete-user') }}";
            url += '/' + customerId;
            $('<form action="' + url + '" method="POST">' +
                '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                '</form>').appendTo($(document.body)).submit();
        }
        return false;
    }

    function customer_edit(customer_id) {
        var option_type = document.getElementById("customer_edit_" + customer_id).value;
        var cur_page = document.getElementById("currentPage").value;
        //return false;
        if (option_type == 1) {
            var url = "{{url('/edit-user')}}" + '/' + customer_id;
            window.location = url;
        } else if (option_type == 2) {
            //alert('This feature is not available now');
            var url = "{{url('/edit-lost-user')}}" + '/' + customer_id;
            window.location = url;
        } else if (option_type == 5) {
            var url = "{{url('/admin/upgrade-membership')}}" + '/' + customer_id;
            window.location = url;
        }
    }

    function b2b2c_user() {
        var client_id = document.getElementById("b2b2c_user").value;
        var url = "{{url('/customers/b2b2c')}}" + '/' + client_id;
        window.location = url;
    }


    //generate pdf
    function generateEmailPDF(type){
        $.ajax({
            type: "POST",
            url: "{{ url('admin/get_emails_to_print_test') }}",
            async: true,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'user_type': type
            },
            success: function (data) {
                generatingPDF(data, 0, 5);
            }
        });
    }

    function generatingPDF(emails, start_index, end_index){
        var emails_to_print = emails.slice(start_index, end_index);
        var url = "{{ url('/generate_email_pdf_test') }}";
        $.ajax({
            type: "POST",
            url: url,
            async: true,
            data: {
                '_token': '<?php echo csrf_token(); ?>',
                'emails': emails_to_print,
                'title': 'All Members'
            },
            success: function (data) {

            }
        });

        start_index += 5;
        end_index += 5;
        if(end_index < 50){
            generatingPDF(emails, start_index, end_index);
        }
    }
    //to keep select option unselected from prev page
    $(document).ready(function () {
        $(".selectChangeOff").each(function () {
            $(this).val($(this).find('option[selected]').val());
        });
    })
</script>