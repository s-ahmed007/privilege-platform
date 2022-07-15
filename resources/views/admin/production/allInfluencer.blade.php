@include('admin.production.header')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"/>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left" style="width: 60%;">
            @if(session('try_again'))
                <div class="alert alert-warning">{{ session('try_again') }}</div>
            @endif
            <h3>Confirmed Members ({{$tab_title}})</h3>
{{--            <a class="btn btn-all" href="{{url('customers/card_users')}}">All</a>--}}
{{--            <a class="btn btn-premium" href="{{url('customers/card_holders')}}">Card Holder</a>--}}
{{--            <a class="btn btn-guest" href="{{url('customers/guest')}}">Guest</a>--}}
{{--            <a class="btn btn-spot" href="{{url('customers/spot')}}">Spot/Manual</a>--}}
            <a class="btn btn-primary" style="padding: 10px;" href="{{url('customers/influencer')}}">Influencer</a>
            <a class="btn btn-primary" style="padding: 10px;" href="{{url('customers/influencer-payment')}}">Influencer Payment</a>
            <form method="post" action="{{url('/pdf/generate/emails')}}">
                {{csrf_field()}}
                <input type="hidden" name="title" value="{{$tab_title}}">
                <input type="hidden" name="emails" value="{{$emails_to_print}}">
                <input class="btn btn-influencer" type="submit"  value="Generate Email List">
            </form>
        </div>
        <div class="title_right">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    @if($profileInfo)
                        <table class="table table-striped projects" id="influencerList">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th style="width: 15%">Image</th>
                                <th style="width: 15%">Customer ID</th>
                                <th style="width: 15%">Customer Info</th>
                                <th style="width: 15%">Promo</th>
                                <th style="width: 15%">Promo Used</th>
                                <th style="width: 15%">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($profileInfo as $customerInfo)
                                <tr>
                                    <th>{{ $customerInfo->serial }}</th>
                                    <td><img src="{{ $customerInfo->customer_profile_image }}" width="100%" style="border-radius: 50%"></td>
                                    <td>{{ $customerInfo->customer_id }}<br>
                                        <br>
                                        <p>
                                            @if($customerInfo->exp_status=='expired')
                                                <b style="color: #F59B00">Premium Membership Expired</b>
                                            @elseif($customerInfo->exp_status=='10 days remaining')
                                                <b style="color: green"></b>
                                            @elseif($customerInfo->card_active==2)
                                                <b style="color: green"></b>
                                            @elseif($customerInfo->card_active==1)
                                                <a href="{{url('card-active-by-admin/'.$customerInfo->customer_id)}}">
                                                    <button type="button" style="margin-left: unset;" class="btn btn-activate pull-right"
                                                            onclick="return confirm('Are you sure you want to activate membership of this customer?');">
                                                        <b>Activate</b>
                                                    </button>
                                                </a>
                                            @endif
                                        </p>
                                    </td>
                                    <td>{{ $customerInfo->customer_full_name }}<br>
                                        <span class="{{$customerInfo->email_verified == 1 ? 'text-success' : 'text-danger'}}">
                                        {{$customerInfo->customer_email}}
                                    </span><br>
                                        {{ $customerInfo->customer_contact_number }}<br>
                                        Via: <b>
                                            @if($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::web)
                                                Website
                                            @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::android)
                                                Android
                                            @elseif($customerInfo->platform == \App\Http\Controllers\Enum\PlatformType::ios)
                                                IOS
                                            @else
                                                N/A
                                            @endif
                                        </b>
                                        @if(isset($customerInfo->referrar))
                                            Refer: <b>{{$customerInfo->referrar->referral_number}}</b>
                                        @endif
                                    </td>
                                    <td>{{$customerInfo->promo_code}}<br>{{'Exp date :'.$customerInfo->promo_expiry}}</td>
                                    <td>
                                        @if($customerInfo->total_promo_used > 1)
                                            {{$customerInfo->total_promo_used. ' times'}}
                                        @elseif($customerInfo->total_promo_used > 0)
                                            {{$customerInfo->total_promo_used. ' time'}}
                                        @else
                                            Never
                                        @endif
                                    </td>
                                    <td>
                                        @if($customerInfo->promo_expiry < date('Y-m-d'))
                                            <span class="badge" style="background-color: red;">Expired</span>
                                        @elseif($customerInfo->promo_active ==1)
                                            <span class="badge" style="background-color:forestgreen;">Active</span>
                                        @elseif($customerInfo->promo_active ==0)
                                            <span class="badge" style="background-color:red;">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="font-size: 1.4em; color: red;">
                            {{ 'No customers found.' }}
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
    $(document).ready(function () {
        $('#influencerList').DataTable({
            //"paging": false
            "order": []
        });
    });
</script>
<script>
    function b2b2c_user() {
        var client_id = document.getElementById("b2b2c_user").value;
        var url = "{{url('/customers/b2b2c')}}" + '/' + client_id;
        window.location = url;
    }
</script>