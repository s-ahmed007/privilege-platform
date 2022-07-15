@include('header')
<?php
   $mem_plan_renew = $card_prices->where('platform', \App\Http\Controllers\Enum\PlatformType::web)
       ->where('type', \App\Http\Controllers\Enum\MembershipPriceType::renew)
       ->where('month', 1)->first();
   $mem_plans_buy = $card_prices->where('platform', \App\Http\Controllers\Enum\PlatformType::web)
       ->where('type', \App\Http\Controllers\Enum\MembershipPriceType::buy)
       ->where('month', '!=', 1)
       ->sortBy('month');
   ?>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>FAQs</h2>
         <p>Frequently asked questions</p>
      </div>
   </div>
</section>
<section id="faq" class="faq section-bg">
   <div class="container">
      <div class="faq-list">
         <ul>
            <li data-aos="fade-up">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" class="collapsed" href="#faq-list-1">What is Royalty? How much does Royalty Premium Membership cost ?<i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-1" class="collapse" data-parent=".faq-list">
                  <p>
                     Royalty is a discount, offers & rewards platform.
                     The regular price of Royalty Premium Membership is BDT
                     {{$mem_plan_renew->price}} for {{$mem_plan_renew->month}}
                     {{$mem_plan_renew->month > 1 ? 'months':'month'}} {{$mem_plan_renew->price == 0 ? '(FREE for new users),':','}}
                     <?php $i=1; ?>
                     @foreach($mem_plans_buy as $mem_plan)
                     @if($i ==count($mem_plans_buy))
                     {{'BDT '.$mem_plan->price}} for {{$mem_plan->month}} {{$mem_plan->month > 1 ? 'months.':'month.'}}
                     @elseif(++$i ==count($mem_plans_buy))
                     {{'BDT '.$mem_plan->price}} for {{$mem_plan->month}} {{$mem_plan->month > 1 ? 'months':'month'}} and
                     @else
                     {{'BDT '.$mem_plan->price}} for {{$mem_plan->month}} {{$mem_plan->month > 1 ? 'months':'month'}},
                     @endif
                     @endforeach
                     <!-- FREE trial is applicable for new users only.  -->
                     More on how to get the membership can be found <span><a href="{{url('/blog')}}" target="_blank" class="faq-blog-link">on our blog</a></span>.
                  </p>
               </div>
            </li>
            <li data-aos="fade-up" data-aos-delay="100">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-2" class="collapsed"> How to use the app to avail offers? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-2" class="collapse" data-parent=".faq-list">
                  <p>
                     Royalty is quick and easy to use. Simply visit a partner outlet and scan partner QR/open partner profile on the app to see available offers on the app and ask the incharge/manager to give their PIN. Once the partner completes the process
                     you will get a notification on your account regarding the transaction.
                  </p>
               </div>
            </li>
            <!-- <li data-aos="fade-up" data-aos-delay="200">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-3" class="collapsed">What are Royalty Deals? How does it work? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-3" class="collapse" data-parent=".faq-list">
                  <p>
                  Royalty deals are pre-purchased offers that customers can redeem at partners’ physical stores in a given time period. After you buy a deal of your choice, the deal gets added to the “My Purchases” tab in the “More” section of the app/ user account on the web. After visiting the particular partner outlet, show the deal in your app from “My Purchases” “Available” tap on the deal and ask the manager to enter their PIN to redeem the deal.
                  </p>
               </div>
            </li> -->
            <!-- <li data-aos="fade-up" data-aos-delay="300">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-4" class="collapsed">What are Royalty Credits? What can I do with that? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-4" class="collapse" data-parent=".faq-list">
                  <p>
                     As you keep on using our service, you will earn Royalty Credits on the go! You can use it to redeem rewards or purchase deals on the platform.
                  </p>
               </div>
            </li> -->
            <li data-aos="fade-up" data-aos-delay="400">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-5" class="collapsed">Why should I download the Royalty Mobile App? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-5" class="collapse" data-parent=".faq-list">
                  <p>
                     Royalty mobile app is a light-weight and easy to use app, that would give you the freedom to do what you love, anywhere you are. You can download it on both Google Play Store and iOS App Store. Get it now. <br>
                     <a href="{{url('http://bit.ly/RBDIOSAPP')}}" target="_blank" class="faq-store-img">
                  <img class="applelogo lazyload" src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/appstore.png" width="15%" alt="Royalty Applestore Icon"/>
                  </a>
                  <a href="{{url('http://bit.ly/RBDANDROID')}}" target="_blank" class="faq-store-img">
                  <img class="lazyload" src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/playstore.png" width="15%" alt="Royalty Playstore Icon"/>
                  </a><br>
                   The mobile app will allow you to:
                  <ul class="list-in-faq">
                     <li>• Access your user profile</li>
                     <li>• Track your loyalty credits</li>
                     <li>• Search offers by category</li>
                     <li>• Search for New Offers</li>
                     <li>• Find information about our partners</li>
                     <!-- <li>• Follow your favorite places and businesses</li> -->
                     <li>• Rate & review your favorite places</li>
                     <li>• Can locate nearby partners</li>
                     <li>• Can book an Uber directly to the location</li>
                     <li>•  Can call the outlet to book a table beforehand</li>
                  </ul>
            
                  </p>
               </div>
            </li>
            <li data-aos="fade-up" data-aos-delay="500">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-6" class="collapsed">What are the payment methods? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-6" class="collapse" data-parent=".faq-list">
                  <p>
                     You can pay by using VISA, MASTERCARD, American Express, Bkash or Rocket on our platfrom.
                  </p>
               </div>
            </li>
            <li data-aos="fade-up" data-aos-delay="600">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-7" class="collapsed">I forgot my PIN. How do I reset it? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-7" class="collapse" data-parent=".faq-list">
                  <p>
                     If you’ve forgotten your PIN, click on "Forgot your PIN" on the login screen and follow the on-screen instructions. You will receive a text on your registered number with a link to reset your PIN.
                  </p>
               </div>
            </li>
            <li data-aos="fade-up" data-aos-delay="700">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-8" class="collapsed">How can I refer my friends & family? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-8" class="collapse" data-parent=".faq-list">
                  <p>
                     You will find your referral code under Personal Info. Send this code to your friends & family
                     to earn Royalty Credit up to {{$other_amounts->where('type', 'refer_bonus')->first()->price}} per refer.              If your friend or family member subscribes using your referral code, both of you will
                     earn Royalty Credit up to {{$other_amounts->where('type', 'refer_bonus')->first()->price}} credits per refer. Each time any of your friends or family members get one of our plans and avails two offers at our partner outlets,
                     both of you will earn the credits. You will be able to use the credits to avail more rewards.
                  </p>
               </div>
            </li>
            <li data-aos="fade-up" data-aos-delay="800">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-9" class="collapsed">How can I earn Royalty Credits? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-9" class="collapse" data-parent=".faq-list">
                  <p>
                     Royalty credits can be earned through:<br>
                     1. Availing Offers<br>
                     2. Referring F&F<br>
                     3. Rating & Reviewing Partners<br>
                     4. Activities On The Platform
                     <br>
                     Go to the rewards section on your account to find the available rewards.
                  </p>
               </div>
            </li>
         </ul>
      </div>
   </div>
</section>
@include('footer')