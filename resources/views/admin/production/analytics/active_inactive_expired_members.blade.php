@include('admin.production.header')

<div class="right_col" role="main">
    <div class="row">
        <h3>Expired Members ({{$tab_title}})</h3>
        <a href="{{url('admin/expired_members/active')}}" class="btn btn-active">Active</a>
        <a href="{{url('admin/expired_members/inactive')}}" class="btn btn-inactive-premium">Inactive</a>
        <table id="memberList" class="table table-bordered table-hover table-striped projects">
            <thead>
            <tr>
                <th>S/N</th>
                <th>Member Info</th>
                <th>Last Membership Plan</th>
                @if($status == 'active')
                    <th>Total Transaction</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $key => $user)
                <tr>
                    <td>{{ ($users->currentpage()-1) * $users->perpage() + $key + 1 }}</td>
                    <td>{{$user->customer_full_name}}<br>
                        {{$user->customer_contact_number}}<br>
                        <b style="color: #F59B00">Expired on:
                            <br><i>{{date('d-m-Y',strtotime($user->expiry_date))}}</i></b>
                    </td>
                    <td>
                        @if(!$user->latestSSLTransaction)
                            <span class="guest-label">Guest Member</span>
                        @elseif(!$user->latestSSLTransaction->cardDelivery)
                            N/A
                        @elseif($user->latestSSLTransaction->cardDelivery->delivery_type==1)
                            <span class="upgrade-label">  {{$user->isUpgrade() == true ? 'Upgrade' : 'Online'}}</span>
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==2)
                            Office Pickup
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==3)
                            Pre-Order<br>COD
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==4)
                            COD
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==5)
                            Customization
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==6)
                            COD<br>(Lost-card)
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==7)
                            Customization<br>(Lost-card)
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==9)
                            <span class="spot-label">Spot</span>
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==\App\Http\Controllers\Enum\DeliveryType::made_by_admin)
                            <span class="admin-label">Admin</span>
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==10)
                            <span class="influencer-label">Influencer</span>
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==11)
                            <span class="trial-label">Trial</span>
                        @elseif ($user->latestSSLTransaction->cardDelivery->delivery_type==12)
                            <span class="renew-label">Renew</span>
                        @endif
                    </td>
                    @if($status == 'active')
                        <td>{{$user->branch_transactions_count}}</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
        {{$users->links()}}
    </div>
</div>

@include('admin.production.footer')
