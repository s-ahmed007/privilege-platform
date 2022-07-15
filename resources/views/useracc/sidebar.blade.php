   <div class="img__wrap">
      <a href="{{url('edit-profile')}}" style="padding: unset;border-radius:50%">
         <div class="img__description_layer" style="width: 100%; border-radius: 50%">
            <p class="img__description">Update</p>
         </div>
         <img src="{{ session('customer_profile_image') }}" alt="Royalty user"
            class="img-circle customer-acc-profile-image lazyload" width="100%"/>
      </a>
   </div>
   <br>
   <div class="center">
      <p class="customer-acc-navbar-name mtb-10">
         {{ session('customer_full_name') }}
      </p>
      <p class="customer-acc-navbar-username">
         {{session('customer_username')}}
      </p>
      <p class="customer-acc-navbar-card-type">
         @if(session('user_type')== 2)
         @if($customer_data->card_active == 2 && $customer_data->delivery_type == 11)
         Trial Member
         @else
         Premium Member
         @endif
         @else Guest
         @endif
      </p>
   </div>
   <div class="customer-acc-card-status mtb-10 center">
      {{--If guest--}}
      @if($customer_data->card_active == 0)
      @if(!empty($info_at_buy_card) && $info_at_buy_card->delivery_type == 4)
      <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#cod_pending_card">
      Activate card
      </button>
      <br>
      @else
      <button class="btn btn-warning" onclick="location.href='{{url("select-card")}}'">
      {{$customer_data->can_get_trial == true ? 'Activate your Free Trial':'Get Premium Membership'}}</button>
      <br>
      @endif
      {{--not activated yet--}}
      @elseif($customer_data->card_active == 1)
      <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#active_card_modal">
      Activate card
      </button>
      <br>
      {{-- expired --}}
      @elseif($customer_data->exp_status == 'expired')
      <span class="btn btn-danger mtb-10">
      Expired
      </span>
      <br>
      <button class="btn btn-success" type="button" onclick="location.href='{{url("renew_subscription")}}'">
      Renew Membership
      </button>
      <br>
      {{--If 10 days remaining--}}
      @elseif($customer_data->exp_status == '10 days remaining')
      <?php
         $title = session('days_remaining') > 1 ? session('days_remaining').' days left' : session('days_remaining').' day left';
         ?>
      <span class="btn btn-warning mtb-10" title="{{$title}}">
      Active
      </span>
      <br>
      <button class="btn btn-success" type="button" onclick="location.href='{{url("renew_subscription")}}'">    
      {{--              @if($customer_data->customer_status == 3)--}}
      {{--                &nbsp;Upgrade Membership--}}
      {{--              @else--}}
      Renew Membership
      {{--              @endif--}}
      </button>
      <br>
      {{--free trial user--}}
      @elseif($customer_data->card_active == 2 && $customer_data->delivery_type == 11)
      <span class="btn btn-primary mtb-10">
      Active
      </span>
      <br>
      <button class="btn btn-success" type="button" onclick="location.href='{{url("renew_subscription")}}'">
      Upgrade Membership
      </button>
      <br>
      {{--active--}}
      @elseif($customer_data->card_active == 2)
      <span class="btn btn-success mtb-10">
      Active
      </span>
      <br>
      @endif
      {{-- Email Verification Section --}}
      @if($customer_data->email_verified == 0)
      <br>
      <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#email_verify_modal">
      Verify your e-mail
      </button>
      <br>
      @endif
      {{-- promo used section --}}
      @if($promo_used['user'] != null)
      <br>
      <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#total_promo_used">
      Promo Stats({{$promo_used['usage']}})
      </button>
      @endif
   </div>
<!-- <a href="#" data-toggle="modal" data-target="#followingModal">
   <div>
      <button class="btn btn-primary">
      <i class="user-plus-icon"></i>&nbsp;
      following
      </button>
   </div>
</a>
<a href="#" data-toggle="modal" data-target="#followersModal">
<button class="btn btn-primary">
<i class="user-icon"></i>&nbsp;
lalala
</button>
</a> -->
@php
$route = \Request::route()->getName();
@endphp
<ul>
   <li>
   <a class="{{$route == 'profileNewsfeed' ? 'active' : ''}}"
   href="{{url('users/'.session('customer_username'))}}"><i class='bx bx-news' ></i>&nbsp;&nbsp;News Feed</a>
</li>
<li>
<a class="{{$route == 'profileInfo' ? 'active' : ''}}"
   href="{{url('users/'.session('customer_username').'/info')}}"><i class='bx bx-info-circle' ></i>&nbsp;&nbsp;Personal Info</a>
</li>
<li>
<a class="{{$route == 'profileStat' ? 'active' : ''}}"
   href="{{url('users/'.session('customer_username').'/statistics')}}"><i class='bx bx-stats' ></i>&nbsp;&nbsp;Stats</a>
</li>
<li>
<a class="{{$route == 'profileRewards' ? 'active' : ''}}"
   href="{{url('users/'.session('customer_username').'/rewards')}}"><i class='bx bx-gift' ></i>&nbsp;&nbsp;Rewards</a>
</li>
<li>
<a class="{{$route == 'profileReviews' ? 'active' : ''}}"
   href="{{url('users/'.session('customer_username').'/reviews')}}"><i class='bx bx-edit' ></i>&nbsp;&nbsp;Reviews</a>
</li>
<li>
<a class="{{$route == 'profileOffers' ? 'active' : ''}}"
   href="{{url('users/'.session('customer_username').'/offers')}}"><i class='bx bxs-offer'></i>&nbsp;&nbsp;Offers History</a>
</li>
{{--<li>--}}
{{--<a class="{{$route == 'profileDeals' ? 'active' : ''}}"--}}
{{--   href="{{url('users/'.session('customer_username').'/deals')}}"><i class='bx bxs-discount' ></i>&nbsp;&nbsp;My Purchases</a>--}}
{{--</li>--}}
</ul>