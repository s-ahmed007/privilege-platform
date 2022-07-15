@include('header')
<link rel="stylesheet" type="text/css" href="{{asset('css/owl.carousel.min.css')}}"/>
<?php use \App\Http\Controllers\functionController; 
   use App\Http\Controllers\Enum\ValidFor;
   ?>
<script src="//code.jquery.com/jquery.min.js"></script>
<script src="{{asset('js/partner-profile/images-grid.js')}}"></script>
<style>
   .tab-pane ul li {list-style-type: unset !important}
   .floating {
   margin-left: -2px;
   }
   .floating::after {
   display: block;
   content: "";
   clear: both;
   }
   .floating .bx bxs-star yellow, .floating .bx bx-star, .floating .bx bxs-star-half yellow {
   padding-left: 2px;
   float: left;
   }
   .prof-nav-active{position: fixed; top: 50px; z-index: 1000; margin-left: 10.8rem}
</style>
<?php $allBranches = (new functionController)->branchesOfPartner($partnerInfo->info->partner_account_id); ?>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <?php $branch_count = (new functionController)->branchCount($partnerInfo->info->partner_account_id) ?>
         <h2>
            @foreach($categories as $category)
            @if($category->id == $partnerInfo->info->partner_category)
            <!-- <a href="{{ url('offers/'.$category->type) }}"
               target="_blank">  -->
            {{ $category->name }}
            <!-- </a> -->
            @endif
            @endforeach
         </h2>
         <div class="partner-details">
            <img src="{{ $partnerInfo->profileImage->partner_profile_image }}" class="img-100"/>
            <h3 class="partner-profile-name">
               {{ $partnerInfo->info->partner_name }}
               <span style="font-size: 12px">{{ $partnerInfo->info->partner_type }}</span>
               <span style="font-size: 12px">{{$partnerBranch->partner_address}}</span>
               @if($ratings != NULL)
               <span class="floating">
               @if($ratings['average_rating'] == 1)
               <i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] == 2)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] == 3)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] == 4)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] == 5)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
               @elseif($ratings['average_rating']>1.0 && $ratings['average_rating'] <=1.5)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] >1.5 && $ratings['average_rating'] < 2.0)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] > 2.0 && $ratings['average_rating'] <=2.5)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] > 2.5 && $ratings['average_rating'] < 3.0)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] > 3 && $ratings['average_rating'] <= 3.5)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] > 3.5 && $ratings['average_rating'] < 4.0)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bx-star yellow"></i>
               @elseif($ratings['average_rating'] > 4.0 && $ratings['average_rating'] <= 4.5)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star-half yellow"></i>
               @elseif($ratings['average_rating'] > 4.5 && $ratings['average_rating'] <= 5.0)
               <i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i><i class="bx bxs-star yellow"></i>
               @else
               <i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i><i class="bx bx-star yellow"></i>
               @endif
               </span>
               @endif
            </h3>
         </div>
      </div>
   </div>
</section>
<!-- {{--Hidden Form to get Partner ID for Loadmore--}} -->
<form>
   <input type="hidden" id="partner_id_lm" name="partner_id_lm" value="{{ $branch_id }}"/>
</form>
<div class="container">
   <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-12">
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
               <li class="breadcrumb-item"><a href="{{url('/offers/all')}}">All Offers</a></li>
               <?php $cur_cat = $categories->where('id', $partnerInfo->info->partner_category)->first(); ?>
               <li class="breadcrumb-item"><a href="{{url('/offers/'.$cur_cat->type)}}">{{$cur_cat->name}}</a></li>
               <?php $pname = str_replace("'", "", $partnerInfo->info->partner_name); ?>
               <li class="breadcrumb-item active">{{$partnerInfo->info->partner_name}}</li>
            </ol>
         </nav>
      </div>
   </div>
</div>
<section class="counts" style="padding: 10px 0;">
   <div class="container partner-profile-nav-box">
      <ul class="nav nav-pills partner-profile-navbar whitebox">
         @if(count($sorted_offers) > 0)
         <li><a href="#offers">Offers</a></li>
         @endif
         @if(count($allBranches->branches)>1)
         <li><a href="#branches">Branches</a></li>
         @endif
         <li><a href="#about">About</a></li>
         <li><a href="#gallery">Gallery</a></li>
         @if(count($partnerInfo->menuImages) > 0)
         <li><a href="#menu">Menu</a></li>
         @endif
         <li><a href="#reviews">Ratings & Reviews</a></li>
         <li><a href="#nearbyPartners">Nearby</a></li>
      </ul>
   </div>
   <div class="container">
      {{--      @if(count($vouchers) > 0)--}}
      {{--      
      <div id="deals" class="whitebox">
         --}}
         {{--         
         <div class="whitebox-header-text">DEALS--}}
            {{--         
         </div>
         --}}
         {{--         @foreach($vouchers as $voucher)--}}
         {{--            <?php $dates = $voucher->date_duration[0]; ?>--}}
         {{--            @if(new DateTime($dates['from']) <= new DateTime(date("d-m-Y"))--}}
         {{--            && new DateTime($dates['to']) >= new DateTime(date("d-m-Y")))--}}
         {{--            
         <div class="whitebox-inner-box whitebox-inner-box-deals">
            --}}
            {{--               
            <div class="whitebox-inner-box-name">
               --}}
               {{--                  
               <p>{{$voucher->heading}}</p>
               --}}
               {{--                  @if($voucher->point != null && $voucher->point != 0)--}}
               {{--                     <span class="savings-label">--}}
               {{--                        EARN {{$voucher->point}}{{$voucher->point > 1 ? ' CREDITS':' CREDIT'}}  --}}
               {{--                     </span>--}}
               {{--                  @endif--}}
               {{--                  
               <p class="deals-left bold">{{$voucher->counter_limit - $voucher->purchased}} left</p>
               --}}
               {{--                  
               <div class="partner-offer-timings">
                  --}}
                  {{--                     
                  <p>Valid for - <span>{{$voucher->valid_for == ValidFor::ALL_MEMBERS ? 'Everyone':'Premium Members'}}</span></p>
                  <br>--}}
                  {{--                     @if(!session('customer_id'))--}}
                  {{--                     <button class="btn btn-primary m-0" onclick="location.href='{{url("login")}}'">Get Premium Membership</button>--}}
                  {{--                     @else--}}
                  {{--                     @if(session('user_type') == 3 && $voucher->valid_for == ValidFor::PREMIUM_MEMBERS)--}}
                  {{--                     <button class="btn btn-primary m-0" onclick="buyMembershipBeforeDealPurchase()">Get Premium Membership</button>--}}
                  {{--                     @endif--}}
                  {{--                     @endif--}}
                  {{--                  
               </div>
               --}}
               {{--               
            </div>
            --}}
            {{--               
            <div class="whitebox-inner-box-buy">
               --}}
               {{--                  
               <p>--}}
                  {{--                     <span class="original-price">&#x9f3;{{intval($voucher->actual_price)}}</span>--}}
                  {{--                     <span class="discounted-price">&#x9f3;{{intval($voucher->selling_price)}}</span>--}}
                  {{--                  
               </p>
               --}}
               {{--                  <a href="{{url('deals/'.$voucher->branch_id)}}">--}}
               {{--                  <button class="btn btn-success">BUY</button>--}}
               {{--                  </a>--}}
               {{--               
            </div>
            --}}
            {{--            
         </div>
         --}}
         {{--            @endif--}}
         {{--         @endforeach--}}
         {{--      
      </div>
      --}}
      {{--      @endif--}}
      @if(count($sorted_offers) > 0)
      <div id="offers" class="whitebox">
         <div class="whitebox-header-text">OFFERS
         </div>
         @foreach($sorted_offers as $offer)
         <div class="whitebox-inner-box whitebox-inner-box-offers">
            <div class="whitebox-inner-box-name">
               <p>{{$offer->offer_description}}</p>
               @if($offer->point != null && $offer->point != 0)
               <span class="savings-label">
               EARN {{$offer->point}}{{$offer->point > 1 ? ' CREDITS':' CREDIT'}} 
               </span>
               @endif
               <?php
                  if($offer->actual_price != 0){
                      $deducted_price = $offer->actual_price - $offer->price;
                      $percentage = floor(($deducted_price * 100)/$offer->actual_price);
                  }else{
                      $percentage = 0;
                  }
                  ?>
               <div class="partner-offer-timings">
                  <p>Valid for - <span>{{$offer->valid_for == ValidFor::ALL_MEMBERS ? 'Everyone':'Premium Members'}}</span></p>
                  <p>Valid on -
                     <span>
                     <?php
                        $weekdays = $offer->weekdays[0];
                        ?>
                     @if($weekdays['sat'] == '1' && $weekdays['sun'] == '1' && $weekdays['mon'] == '1' && $weekdays['tue'] == '1' &&
                     $weekdays['wed'] == '1' && $weekdays['thu'] == '1' && $weekdays['fri'] == '1')
                     All days
                     @else
                     @if($weekdays['sun'] == '1')
                     Sun
                     @endif
                     @if($weekdays['mon'] == '1')
                     Mon
                     @endif
                     @if($weekdays['tue'] == '1')
                     Tue
                     @endif
                     @if($weekdays['wed'] == '1')
                     Wed
                     @endif
                     @if($weekdays['thu'] == '1')
                     Thu
                     @endif
                     @if($weekdays['fri'] == '1')
                     Fri
                     @endif
                     @if($weekdays['sat'] == '1')
                     Sat
                     @endif
                     @endif
                     </span>
                  </p>
                  @if($offer->time_duration != null)
                  <p>Timing - <span>
                     <?php $i=0; ?>
                     @foreach($offer->time_duration as $duration)
                     {{date('h:i a', strtotime($duration['from'])).' - '.date('h:i a', strtotime($duration['to']))}}
                     <?php
                        echo $i != count($offer->time_duration)-1 ? ',' : '';
                        $i++; ?>
                     @endforeach
                     </span>
                  </p>
                  @endif
                  {{--
                  <p>Valid up to {{$offer->date_duration[0]['to']}}</p>
                  --}}
               </div>
               <div class="pp-offer-btn">
                  @if($offer->offer_full_description != null)
                  <button class="btn btn-primary-thin m-0 offerDetails" data-offer-id="{{$offer->id}}" data-offer-tab="details">Details</button>
                  @endif
                  <button class="btn btn-primary-thin m-0 offerDetails" data-offer-id="{{$offer->id}}" data-offer-tab="tnc">T&C</button>
                  {{--                  @if(!session('customer_id'))--}}
                  {{--                     <br><br>--}}
                  {{--                     <button class="btn btn-primary m-0" onclick="location.href='{{url("login")}}'">--}}
                  {{--                     Get Premium Membership</button>--}}
                  {{--                  @else--}}
                  {{--                     @if(session('user_type') == 3 && $offer->valid_for == ValidFor::PREMIUM_MEMBERS)--}}
                  {{--                        <br><br>--}}
                  {{--                        <button class="btn btn-primary m-0" onclick="location.href='{{url("select-card")}}'">--}}
                  {{--                           Get Premium Membership</button>--}}
                  {{--                     @endif--}}
                  {{--                  @endif--}}
               </div>
               <!-- <div class="pp-offer-pricedif">
                  <p>
                     <span class="pp-offer-oldprice">&#x9f3;{{$offer->actual_price}}</span>
                     <span class="pp-offer-disc-price">&#x9f3;</i>{{$offer->price}}</span>
                  </p>
                  <p class="pp-offer-vatsc">Excludes VAT & SC (If applicable)</p>
                  </div> -->                
            </div>
            <!-- @if($offer->point != null && $offer->point != 0)
               <div class="whitebox-inner-box-buy">
                  <span class="savings-label">
                     EARN {{$offer->point}}{{$offer->point > 1 ? ' CREDITS':' CREDIT'}} 
                  </span>
               </div>
               @endif -->
         </div>
         <div class="modal" id="offerDetails_{{$offer->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <h4 class="modal-title">{{ $partnerInfo->info->partner_name }}</h4>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="cross-icon"></i>
                     </button>
                  </div>
                  <div class="modal-body">
                     <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                           <li role="presentation" class="active"><a href="#detailsTab{{$offer->id}}" aria-controls="detailsTab{{$offer->id}}"
                              role="tab" data-toggle="tab">Details</a>
                           </li>
                           <li role="presentation"><a href="#tncTab{{$offer->id}}" aria-controls="tncTab{{$offer->id}}" role="tab"
                              data-toggle="tab">T&C</a>
                           </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content offer-tab-content">
                           <div role="tabpanel" class="tab-pane active" id="detailsTab{{$offer->id}}">
                              {!! html_entity_decode($offer->offer_full_description) !!}
                           </div>
                           <div role="tabpanel" class="tab-pane" id="tncTab{{$offer->id}}">
                              {!! html_entity_decode($offer->tnc) !!}
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         @endforeach
      </div>
      @endif
      @if(count($allBranches->branches)>1)
      <div id="branches" class="whitebox">
         <div class="whitebox-header-text">BRANCHES</div>
         <?php if($branch_count > 1){ ?>
         <div class="partner-details row">
            <?php $allBranches = (new functionController)->branchesOfPartner($partnerInfo->info->partner_account_id); ?>
            @for($i=0; $i<count($allBranches->branches); $i++)
            @if($allBranches->branches[$i]->id != $branch_id)
            <!-- {{--other branches of that partner--}} -->
            <?php $pname = str_replace("'", "", $partnerInfo->info->partner_name); ?>
            <div class="col-md-4 col-sm-4 col-xs-6 branch-container">
               <a href="{{ url('partner-profile/'. $pname .'/'.$allBranches->branches[$i]->id) }}">
                  <div class="other-branch">
                     {{$partnerInfo->info->partner_name.' - '.$allBranches->branches[$i]['partner_area'] }}
                     <p class="dots">{{$allBranches->branches[$i]['partner_address'] }}</p>
                  </div>
               </a>
            </div>
            @else
            <!-- {{--when on that specific branch--}} -->
            <div class="col-md-4 col-sm-4 col-xs-6 branch-container">
               <div class="active-branch">
                  {{$partnerInfo->info->partner_name.' - '.$allBranches->branches[$i]['partner_area'] }}
                  <p class="dots">{{$allBranches->branches[$i]['partner_address'] }}</p>
               </div>
            </div>
            @endif
            @endfor
         </div>
         <?php } ?>
      </div>
      @endif
      <div id="about" class="whitebox">
         <div class="whitebox-header-text">ABOUT</div>
         <div class="partner-details">
            <p>{{ $partnerInfo->info->about}}</p>
         </div>
         <hr>
         <div class="row">
            <div class="col-md-4 col-sm-12 col-xs-12">
               <div class="info-box">
                  <p class="partner-profile-f-s-h1">Timings</p>
                  <table class="partner-profile-opening-hours-table">
                     <tbody>
                        @if(isset($openingHours) && $openingHours != null)
                        @for($i=0; $i<7; $i++)
                        <tr <?php if (substr(date("l"), 0, 3) == $days[$i])
                           echo 'style="border-left: 3px solid #007bff;background-color: #dcdcdc;"';?>>
                           <td class="timing_days">{{$days[$i]}}</td>
                           <td class="timing_hours">{{$openingHours[$i]}}</td>
                        </tr>
                        @endfor
                        @else
                        @for($i=0; $i<7; $i++)
                        <tr>
                           <td>----</td>
                           <td>----</td>
                        </tr>
                        @endfor
                        @endif
                     </tbody>
                  </table>
               </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
               <div class="info-box">
                  <p class="partner-profile-f-s-h1">Facilities</p>
                  <div class="partner-profile-social-zone-div">
                     @if($partnerBranch['facilities'])
                     @foreach($partnerBranch['facilities'] as $facility)
                     <div class="partner-profile-facilities-div">
                        <img src="{{$facility['icon']}}" width="20" height="20">
                        {{$facility['name']}}
                     </div>
                     @endforeach
                     @else
                     <div class="partner-profile-facilities-div">No facility available</div>
                     @endif
                  </div>
               </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
               <div class="info-box">
                  <p class="partner-profile-f-s-h1">Connect</p>
                  <div class="partner-profile-social-zone-div">
                     <div class="partner-profile-social-zone-div">
                        <a href="tel:{{$partnerBranch->partner_mobile}}">
                        <i class='bx bxs-phone-call'></i>
                        {{$partnerBranch->partner_mobile}}
                        </a>
                     </div>
                     @if($partnerInfo->info->facebook_link != '#')
                     <div class="partner-profile-social-zone-div">
                        <a target="_blank" href="{{ url($partnerInfo->info->facebook_link) }}" class="socials_link" rel="noreferrer">
                        <i class='bx bxl-facebook-square' ></i>
                        Facebook
                        </a>
                     </div>
                     @endif
                     @if($partnerInfo->info->website_link != '#')
                     <div class="partner-profile-social-zone-div">
                        <a target="_blank" href="{{ url($partnerInfo->info->website_link) }}" class="socials_link" rel="noreferrer">
                        <i class='bx bx-world' ></i>
                        Website
                        </a>
                     </div>
                     @endif
                     @if($partnerInfo->info->instagram_link != '#')
                     <div class="partner-profile-social-zone-div">
                        <a target="_blank" href="{{ url($partnerInfo->info->instagram_link) }}"
                           class="socials_link" rel="noreferrer">
                        <i class='bx bxl-instagram'></i>
                        Instagram
                        </a>
                     </div>
                     @endif
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
               <div class="partner-map">
                  <iframe src="{{ $partnerBranch->partner_location }}" height="300" style="border:0; width: 100%;"
                     allowfullscreen></iframe>
               </div>
            </div>
         </div>
      </div>
      <div id="gallery" class="whitebox">
         <div class="whitebox-header-text">GALLERY</div>
         <div id="multipleGallery"></div>
      </div>
      <script>
         (function ($) {
             $(document).ready(function () {
                 $('#multipleGallery').imagesGrid({
                     images: [
                             <?php
            foreach($partnerInfo->galleryImages as $galleryImage){?>
                         {
                             src: '<?php echo $galleryImage->partner_gallery_image; ?>', // url
                             alt: 'Gallery Image',// alternative text
                             title: 'Partner Gallery',// title
                             caption: "<?php echo $galleryImage->image_caption; ?>",// modal caption
                             thumbnail: '<?php echo $galleryImage->partner_gallery_image; ?>'
                         },
                         <?php } ?>
                     ],
                     align: true,
                     cells: 3,
                     getViewAllText: function (imgsCount) {
                         return 'View All Photos'
                     }
                 });
             });
         })(jQuery);
      </script>
      @if(count($partnerInfo->menuImages) > 0)
      <div id="menu" class="whitebox">
         <div class="whitebox-header-text">MENU</div>
         <div id="multipleMenu"></div>
      </div>
      <script>
         (function ($) {
             $(document).ready(function () {
                 $('#multipleMenu').imagesGrid({
                     images: [
                             <?php
            foreach($partnerInfo->menuImages as $menuImage){?>
                         {
                             src: '<?php echo $menuImage->partner_menu_image; ?>', // url
                             alt: 'Menu Image',// alternative text
                             title: 'Partner Menu',// title
                             caption: "Menu",// modal caption
                             thumbnail: '<?php echo $menuImage->partner_menu_image; ?>'
                         },
                         <?php } ?>
                     ],
                     align: true,
                     cells: 3,
                     getViewAllText: function (imgsCount) {
                         return 'View All Photos'
                     }
                 });
             });
         })(jQuery);
      </script>
      @endif
      <div id="reviews" class="whitebox">
         <div class="row rating_table">
            @if(($total_review_count || $total_rating_count) > 0)
               <div class="col-md-12 px-0">
                  <div class="whitebox-header-text">RATINGS</div>
               </div>
            <div class="col-md-3 col-sm-4 col-xs-12">
               @php
               $rate_color = '';
               if($ratings['average_rating'] == 0.00 || $ratings['average_rating'] < 1.50){
               $rate_color = '#e74c3c';
               }elseif($ratings['average_rating'] < 2.50){
               $rate_color = '#F3A712';
               }elseif($ratings['average_rating'] < 3.50){
               $rate_color = '#f5d233';
               }elseif($ratings['average_rating'] < 4.50){
               $rate_color = '#CDDC39';
               }elseif($ratings['average_rating'] <= 5.00){
               $rate_color = '#2fab5d';
               }
               @endphp
               <div class="center"><br><br><br>
                  <span class="overall-rating" style="background-color: {{$rate_color}}">
                  <span>{{ isset($ratings['average_rating']) ? round($ratings['average_rating'],1) : '0' }}</span>
                  <span>/ 5</span>
                  </span>
               </div>
               <br>
               <div>
                  <div class="overall-rating-star" style="color:{{$rate_color}}; text-align: center;">
                     @if(isset($ratings['average_rating']))
                     @if($ratings['average_rating'] == 1)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] == 2)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] == 3)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] == 4)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] == 5)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     @elseif($ratings['average_rating'] > 1.0 && $ratings['average_rating'] <= 1.5)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star-half yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] > 1.5 && $ratings['average_rating'] < 2.0)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] > 2.0 && $ratings['average_rating'] <= 2.5)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star-half yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] > 2.5 && $ratings['average_rating'] < 3.0)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] > 3 && $ratings['average_rating'] <= 3.5)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star-half yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] > 3.5 && $ratings['average_rating'] < 4.0)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @elseif($ratings['average_rating'] > 4.0 && $ratings['average_rating'] <= 4.5)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star-half yellow"></i>
                     @elseif($ratings['average_rating'] > 4.5 && $ratings['average_rating'] <= 5.0)
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     @else
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     @endif
                     @endif
                  </div>
                  <p style="text-align: center;">
                     @if($total_review_count > 0)
                     {{$total_review_count > 1 ? $total_review_count.' reviews' : '1 review' }}
                     @endif
                     @if($total_rating_count > 0)
                     <br>{{$total_review_count > 0 ? 'and':''}}
                     {{$total_rating_count > 1 ? $total_rating_count.' ratings' : '1 rating' }}
                     @endif
                  </p>
               </div>
            </div>
            <div class="col-md-5 col-sm-7 col-xs-12">
               <div class="ratebox">
                  <div>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                  </div>
                  <span style="float: right;margin-top: -7px;">&nbsp;({{$ratings['rating_counter']['5_star']}})</span>
                  <div class="progress">
                     <div class="progress-bar progress-bar-striped rating-5" role="progressbar" aria-valuenow="70" aria-valuemin="0"
                        aria-valuemax="100" style="width:{{ round($ratings['5_star']).'%' }};
                        {{round($ratings['5_star']) == 0 ? 'background-color: unset' : ''}}">
                     </div>
                  </div>
                  <div>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                  </div>
                  <span style="float: right;">&nbsp;({{$ratings['rating_counter']['4_star']}})</span>
                  <div class="progress">
                     <div class="progress-bar progress-bar-striped rating-4" role="progressbar" aria-valuenow="70" aria-valuemin="0"
                        aria-valuemax="100" style="width:{{ round($ratings['4_star']).'%' }};
                        {{round($ratings['4_star']) == 0 ? 'background-color: unset' : ''}}">
                     </div>
                  </div>
                  <div>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                  </div>
                  <span style="float: right;">&nbsp;({{$ratings['rating_counter']['3_star']}})</span>
                  <div class="progress">
                     <div class="progress-bar progress-bar-striped rating-3" role="progressbar" aria-valuenow="70" aria-valuemin="0"
                        aria-valuemax="100" style="width:{{ round($ratings['3_star']).'%' }};
                        {{round($ratings['3_star']) == 0 ? 'background-color: unset' : ''}}">
                     </div>
                  </div>
                  <div>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                  </div>
                  <span style="float: right;">&nbsp;({{$ratings['rating_counter']['2_star']}})</span>
                  <div class="progress">
                     <div class="progress-bar progress-bar-striped rating-2" role="progressbar" aria-valuenow="70" aria-valuemin="0"
                        aria-valuemax="100" style="width:{{ round($ratings['2_star']).'%' }};
                        {{round($ratings['2_star']) == 0 ? 'background-color: unset' : ''}}">
                     </div>
                  </div>
                  <div>
                     <i class="bx bxs-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                     <i class="bx bx-star yellow"></i>
                  </div>
                  <span style="float: right;">&nbsp;({{$ratings['rating_counter']['1_star']}})</span>
                  <div class="progress">
                     <div class="progress-bar progress-bar-striped rating-1" role="progressbar" aria-valuenow="70" aria-valuemin="0"
                        aria-valuemax="100" style="width:{{ round($ratings['1_star']).'%' }};
                        {{round($ratings['1_star']) == 0 ? 'background-color: unset' : ''}}">
                     </div>
                  </div>
               </div>
            </div>
            @else
               <div class="col-md-12 px-0">
               <div class="whitebox-header-text">No rating yet</div>
               </div>
            @endif
         </div>
      </div>
      <div class="whitebox">
         <div class="whitebox-header-text"> 
            @if(count($reviews) > 0)
            REVIEWS ({{$total_review_count > 1 ? $total_review_count.' Reviews' : '1 Review' }})
            @else
            No reviews yet
            @endif
         </div>
         <div id="containerloadmore">
            @if(isset($reviews))
            <?php $row = count($reviews);
               if ($row > $review_loadmore){
                   $all_reviews = $reviews->take($review_loadmore);
               }else{
                   $all_reviews = $reviews;
               }
               ?>
            @foreach($all_reviews as $review)
            @if(!empty($review))
            <div class="whitebox-inner-box-inner" id="review-id-{{$review['id']}}">
               <!-- User review -->
               <div class="row">
                  <div class="col-md-2 col-sm-2 col-xs-3">
                     <div class="comment-avatar center">
                        {{--
                        <a href="{{url('user-profile/'.$reviews[$i]['customer_username'])}}" target="_blank">
                           --}}
                           <img src="{{ asset($review['customerInfo']['customer_profile_image'])}}"
                              class="img-circle img-40 primary-border-1 lazyload" alt="Royalty user-pic">
                           <!--  </a> -->
                           <p class="comment-name reviewer-name mt">{{ $review['customerInfo']['customer_full_name'] }}</p>
                           <p>
                              <!-- <a href="{{url('user-profile/'.$review['customer_username'])}}" target="_blank"> -->
                              <i class="bx bx-edit user-total-reviews">
                              <span>{{ functionController::reviewNumber($review['customer_id']) }}</span>
                              </i>
                              <i class="bx bx-like likes_of_user_{{$review['customer_id']}}">
                              <span>{{ functionController::likeNumber($review['customer_id']) }}</span>
                              </i>
                           </p>
                     </div>
                  </div>
                  <div class="col-md-7 col-sm-10 col-xs-9">
                  <!-- User Review box -->
                  <div class="comment-box">
                  <div class="comment-head">
                  <!-- social media buttons -->
                  <?php
                     if ($review['heading'] != null && $review['heading'] != 'n/a') {
                         $heading = str_replace("'", "", $review['heading']);
                         $heading = str_replace('"', "", $heading);
                         $heading = trim(preg_replace('/\s+/', ' ', $heading));
                     } else {
                         $heading = '';
                     }
                     if ($review['body'] != null && $review['body'] != 'n/a') {
                         $body = str_replace("'", "", $review['body']);
                         $body = str_replace('"', "", $body);
                         $body = trim(preg_replace('/\s+/', ' ', $body));
                     } else {
                         $body = '';
                     }
                     $newline = '\n';
                     $pretext = 'Review about';
                     $partner_name = " " . str_replace("'", "\'", $review['partnerInfo']['partner_name']);
                     $posttext = 'on royaltybd.com';
                     $review_body = $body;
                     $review_head = $heading;
                     $enc_review_id = (new functionController)->socialShareEncryption('encrypt', $review['id']);
                     $review_url = url('/review/' . $enc_review_id);
                     ?>
                  <div class="social-buttons">
                  <!-- Twitter share button code -->
                  <span onclick="window.open('https://twitter.com/intent/tweet?text=' +
                     encodeURIComponent('<?php echo $pretext . $partner_name . $newline . $newline .
                        substr($review_head,0, 30).'...' . $newline . substr($review_body,0, 130).'...' .
                        $newline . $newline . $review_url;?>')); return false;">
                  <a href="#"><i class="bx bxl-twitter"></i></a>
                  </span>
                  <!-- Facebook share button code -->
                  <span>
                  <?php $review_url = 'https://www.facebook.com/sharer.php?href=https%3A%2F%2F'.url('/').'%2Freview-share%2F' . $enc_review_id; ?>
                  <a href="<?php echo $review_url;?>" target="_blank"><i class="bx bxl-facebook-circle"></i></a>
                  </span>
                  @if(Session::get('customer_id') == $review['customer_id'])
                  <p class="review-delete-warning">
                  <a class="btn btn-danger btn-xs" href="{{url('/reviewDelete/'.$review['id'])}}"
                     onclick="return confirm('Are you sure you want to delete this review?')">
                  <i class="delete-icon"></i>
                  </a>
                  </p>
                  @endif
                  </div>
                  <!-- social media buttons END -->
                  </div>
                  <div class="comment-content">
                  <div class="review-star">
                  @if($review['rating'] == 1)
                  <div class="reviewer-star-rating-div">
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  </div>
                  @elseif($review['rating'] == 2)
                  <div class="reviewer-star-rating-div">
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  </div>
                  @elseif($review['rating'] == 3)
                  <div class="reviewer-star-rating-div">
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  </div>
                  @elseif($review['rating'] == 4)
                  <div class="reviewer-star-rating-div">
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bx-star yellow"></i>
                  </div>
                  @else
                  <div class="reviewer-star-rating-div">
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  <i class="bx bxs-star yellow"></i>
                  </div>
                  @endif
                  </div>
                  <?php
                     $posted_on = date("Y-M-d H:i:s", strtotime($review['posted_on']));
                     $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                     ?>
                  <span class="review-post-date">
                  {{$created->diffForHumans()}}
                  </span>
                  <p class="review-head bold">{{$review_head}}</p>
                  <p class="review-description">{{$review_body}}</p>
                  <div class="like-button">
                  {{--onclick event for liker list--}}
                  <?php
                     $onclick = count($review['likes']) > 0 ? 'onclick="getReviewLikerList('.$review['id'].')"' : '';
                     ?>
                  {{--Like option--}}
                  @if(Session::has('customer_id') && $review['previous_like'] == 1 && Session::get('customer_id') != $review['customer_id'])
                  <!-- if liked and want to unlike -->
                  <div class="like-content" title="Like">
                  <button class="btn-like unlike-review" id="principalSelect-{{$review['id']}}"
                     value="{{$review['id']}}" data-source="{{$review['previous_like_id']}}">
                  <i class="love-f-icon"></i>
                  </button>
                  </div>
                  <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$review['id']}}">
                  {{ count($review['likes']) }}
                  {{ count($review['likes']) > 1 ? ' likes' : ' like'}}
                  </p>
                  <!-- if wants to like -->
                  @elseif(Session::has('customer_id') && $review['previous_like'] == 0 && Session::get('customer_id') != $review['customer_id'])
                  <div class="like-content">
                  <button class="btn-like like-review" id="principalSelect-{{$review['id']}}"
                     value="{{$review['id']}}" data-source="{{$review['previous_like_id']}}">
                  <i class="love-e-icon"></i>
                  </button>
                  </div>
                  <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$review['id']}}">
                  {{ count($review['likes']) }}
                  {{ count($review['likes']) > 1 ? ' likes' : ' like'}}
                  </p>
                  <!-- if own review and cant like -->
                  @elseif(Session::has('customer_id') && Session::get('customer_id') == $review['customer_id'])
                  {{--
                  <div class="like-content" title="You can not like your own review">--}}
                  {{--<button class="btn-like" data-source="{{$reviews[$i]['source_id']}}">--}}
                  {{--<i class="love-e-icon"></i>--}}
                  {{--</button>--}}
                  {{--
                  </div>
                  --}}
                  <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$review['id']}}">
                  {{ count($review['likes']) }}
                  {{ count($review['likes']) > 1 ? ' likes' : ' like'}}
                  </p>
                  <!-- if partner wants to like -->
                  @elseif(Session::has('partner_id') && $review['liked'] == 0 && Session::get('partner_id') == $partnerInfo->info->partner_account_id)
                  <div class="like-content">
                  <button class="btn-like like-review" id="principalSelect-{{$review['id']}}"
                     value="{{$review['id']}}" data-source="{{$review['previous_like_id']}}">
                  <i class="love-e-icon"></i>
                  </button>
                  </div>
                  <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$review['id']}}">
                  {{ count($review['likes']) }}{{ count($review['likes']) > 1 ? ' likes' : ' like'}}
                  </p>
                  @else
                  <!-- when no one is logged in and cannot like  -->
                  <div class="like-content">
                  <button class="btn-like" data-toggle="modal" data-target="#nonClickableLike">
                  <i class="love-e-icon"></i>
                  </button>
                  </div>
                  <p class="likes-on-review" {!! $onclick !!} id="likes_of_review_{{$review['id']}}">
                  {{ count($review['likes']) }}
                  {{ count($review['likes']) > 1 ? ' likes' : ' like'}}
                  </p>
                  @endif{{--Like option ends--}}
                  </div>
                  <p class="review-liability">This review is the subjective opinion of a Royalty member and not of Royalty.</p>
                  </div>
                  </div>
                  </div>
                  <div class="col-md-3 col-sm-12 col-xs-12">
                     <i class="fas fa-check-circle fa-2x" style=" color: #2fab5d;"></i>
                     <p>100% Verified Reviews
                     <p>
                     <p>All reviews are from people who have availed offers at this partner.
                     <p>
                  </div>
               </div>
               <!-- Partner reply in written -->
               @if(isset($review['comments'][0]) && $review['comments'][0]['moderation_status'] == 1)
               <div class="row m-0">
                  <div class="col-md-10 col-md-offset-2 col-sm-11 col-sm-offset-1 col-xs-11 col-xs-offset-1">
                     <div class="whitebox comment-box-partner">
                        <div class="comment-content comment-content-partner">
                           <p class="comment-name partner-response"><b>{{$review['partnerInfo']['partner_name']}}</b>
                              <span>responded to this review</span>
                           </p>
                           <?php
                              $posted_on = date("Y-M-d H:i:s", strtotime($review['comments'][0]['posted_on']));
                              $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                              ?>
                           <p class="partner-reply-date">
                              {{$created->diffForHumans()}}
                           </p>
                           <p class="partner-reply">{{$review['comments'][0]['comment']}}</p>
                        </div>
                     </div>
                  </div>
               </div>
               @endif
               <!-- when partner will reply and wil see empty box -->
               @if(Session::has('partner_id') && $review['partner_account_id'] == Session::get('partner_id') && empty($review['comments'][0]['comment']))
               <div class="row">
                  <div class="">
                     <!-- Partner reply box -->
                     <div class="whitebox partner-color">
                        <form action="{{url('replyReview/'.$review['id'])}}" method="post" style="padding: 15px 0;">
                           {{csrf_field()}}
                           <div class="form-group">
                              <textarea name="reply" id="review{{$i}}" cols="78" rows="4" placeholder="Your reply goes here..."
                                 required class="form-control" maxlength="500" onkeyup="replyChars({{$i}});"></textarea>
                           </div>
                           <p align="right" style="font-size: small; margin-top: -10px">
                              <span id="charNum{{$i}}">0/500</span>
                           </p>
                           <input type="hidden" name="customerID" value="{{$review['customer_id']}}">
                           <input type="hidden" name="review_id" value="{{  $review['id']}}">
                           <button type="submit" class="btn btn-primary partner-reply-btn">Reply</button>
                        </form>
                     </div>
                  </div>
               </div>
               @endif
            </div>
            @endif
            <form>
               <input type="hidden" id="position_lm" name="position_lm" value="0"/>
            </form>
            @endforeach
            @endif
         </div>
         <?php if ($row > $review_loadmore){?>
         <div id="review_load_btn">
            <input type="button" id="btnloadmore" value="Load more" onclick="loadmore('{{count($reviews)}}')" class="btn btn-primary"/>
         </div>
         <?php } ?>
      </div>
      <div id="nearbyPartners" class="whitebox">
         <div class="whitebox-header-text">NEARBY</div>
         @if(count($nearbyPartners) == 0)
         <div class="no-info">
            <p class="no-nearby-partner">There are no nearby Royalty Partners.</p>
         </div>
         @else
         <!-- Nearby Carousel -->
         <div class="large-12 columns">
            <div class="owl-carousel owl-theme">
               @foreach($nearbyPartners as $nearbyPartner)
               @if($nearbyPartner->partner_name != $partnerInfo->info->partner_name)
               <div class="item">
                  <?php $pname = str_replace("'", "", $nearbyPartner->partner_name); ?>
                  <div class="card card-inverse card-info">
                     <a href="{{ url('partner-profile/'. $pname .'/'.$nearbyPartner->id)}}">
                     <img src="{{ $nearbyPartner->partner_profile_image }}" class="card-img-top lazyload" alt="Royalty Partner"/>
                     </a>
                     <div class="card-block">
                        <a href="{{ url('partner-profile/'. $pname .'/'.$nearbyPartner->id)}}">
                           <h4 class="card-title card-partner-name">{{$nearbyPartner->partner_name}}</h4>
                        </a>
                        <div class="card-text">
                           <a href="{{ url('partner-profile/'. $pname .'/'.$nearbyPartner->id)}}">
                              <p>
                                 {{$nearbyPartner->partner_address}} - <?php $ratings = [1,2,3,4,5]; ?>
                        @if($nearbyPartner->avg_rating == 0)
                        <span class="partner-box-info-rating">new</span>
                        @elseif(in_array($nearbyPartner->avg_rating, $ratings))
                        <i class="bx bxs-star yellow"></i>
                        <span class="partner-box-info-rating">{{round($nearbyPartner->avg_rating).'.0'}}</span>
                        @else
                        <i class="bx bxs-star yellow"></i>
                        <span class="partner-box-info-rating">{{round($nearbyPartner->avg_rating, 1)}}</span>
                        @endif
                              </p>
                           </a>
                           <p class="card-text nearby-distance">
                              {{$nearbyPartner->distance}} km nearby
                           </p>
                        </div>
                     </div>
                     <div class="card-footer">
                        <label class="label-tag-small">OFFER</label>
                        <a href="{{ url('partner-profile/'. $pname .'/'.$nearbyPartner->id)}}">
                        <small class="bold black"> {{$nearbyPartner->offer_heading}}</small>
                        </a>
                     </div>
                  </div>
               </div>
               @endif
               @endforeach
            </div>
         </div>
         <!-- /Nearby Carousal -->
         @endif
      </div>
      <div class="center">
         <p>Royalty is not responsible for the accuracy of the general information above provided by the respective partner.</p>
      </div>
   </div>
</section>
{{--Partner/non user Cannot write a review--}}
<div id="partnerReviewModal" class="modal" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Sorry!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div>
               @if(Session::has('partner_id') || Session::has('customer_id'))
               <p>Only Royalty Members can rate or write reviews about our partners.</p>
               @else
               <p>Only Royalty Members can rate or write reviews about our partners. Please
                  <a href="{{url('login')}}">Login</a> to write reviews about your favourite partners!
               </p>
               @endif
            </div>
         </div>
      </div>
   </div>
</div>
{{-- ====================FOLLOWERS Modal====================== --}}
<div id="followersModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Followers of {{ $partnerInfo->info->partner_name }}</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            @if(isset($followers_info) && count($followers_info) > 0)
            @foreach($followers_info as $info)
            <div class="row">
               <div class="col-md-8 col-sm-8 col-xs-8">
                  {{--<a href="{{url('user-profile/'.$info['customer_username'])}}" target="_blank">--}}
                  <a>
                     <img class="lazyload image-left"
                        data-src="{{asset($info['customer_profile_image'] != '' ?
                        $info['customer_profile_image'] : 'images/user.png')}}" Royalty customer">
                     <p class="heading-right">
                        {{$info['customer_first_name'].' '.$info['customer_last_name']}}
                     </p>
                     <br>
                     <p class="sub-heading-right">
                        <i class="bx bxs-star yellow"></i>
                        @if($info['customer_type']==1)Gold Member
                        @elseif($info['customer_type']==2) Royalty Member
                        @else Member
                        @endif
                     </p>
                  </a>
               </div>
               <div class="col-md-4 col-sm-4 col-xs-4 user-review-like-modal">
                  <i class="review-icon"></i>&nbsp;{{ (new functionController)->reviewNumber($info['customer_id']) }}
                  <i class="like-icon"></i>&nbsp;{{ (new functionController)->likeNumber($info['customer_id']) }}
               </div>
            </div>
            @endforeach
            @else
            <div class="no-info">
               <p>No followers</p>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>
{{--Branch Modal--}}
<div id="branchModal" class="modal fade" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Branches</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body">
            <div class="partner-branches">
               <?php $allBranches = (new functionController)->branchesOfPartner($partnerInfo->info->partner_account_id); ?>
               <ul>
                  @for($i=0; $i<count($allBranches->branches); $i++)
                  <a href="{{ url('partner-profile/'. $partnerInfo->info->partner_name.'/'.$allBranches->branches[$i]->id) }}">
                     <li>
                        <span>
                        {{   $partnerInfo->info->partner_name.' - '.$allBranches->branches[$i]['partner_address'] }}
                        </span>
                     </li>
                  </a>
                  @endfor
               </ul>
            </div>
         </div>
      </div>
   </div>
</div>
{{-- Credits modal --}}
<!-- The Modal -->
<!-- <div class="modal" id="pointdetailModal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">            <h4 class="modal-title">What does 1x, 2x, 3x or N/A mean?</h4>
            <button type="button" class="close" data-dismiss="modal">
               <i class="cross-icon"></i></button>
   
         </div>
         <div class="modal-body">
            <div class="no-info" style="text-align: unset;">
               <p>
                  &#x2022;1x Credits at any of our partners means you get the Credits one time while availing the discount/offer.<br>
                  &#x2022;2x Credits at any of our partners means you the Credits double times while availing the discount/offer up to 10 Credits a day and so on.
                  <br>
                  &#x2022;N/A means that no Credits available at that partner.
               </p>
            </div>
         </div>
      </div>
   </div>
   </div> -->
<!-- Javascript For review Loadmore-->
<script>
   $(document).ready(function () {
       $("#review_load_gif").hide();
       $("#review_load_btn").show();
   });
   
   function loadmore(total_reviews) {
       var partner_id = document.getElementById('partner_id_lm').value;
       var pos = document.getElementById('position_lm');
       var position = parseInt(pos.value);
       position++;
       var url = "{{ url('/reviewLoad')}}";
       pos.value = position;
       $.ajax({
           type: "POST",
           url: url,
           data: {'_token': '<?php echo csrf_token(); ?>',
               'position': position,
               'partner_id': partner_id,
               'total_reviews': total_reviews
           },
           success: function (data) {
               //nothing
               if (data['status'] == 0) {
                   $('#btnloadmore').hide();
               }
               if (data['output'] == "error") {
                   $('#containerloadmore').append("");
                   $('#btnloadmore').attr('disabled', true);
               }
               else {
                   $('#containerloadmore').append(data['output']);
               }
           }
       });
   }
</script>
@include('footer')
<script>
   function replyChars(i) {
       var no_of_chars = $("#review"+i).val();
       $("#charNum"+i).text(no_of_chars.length+'/500');
   }
</script>
<script type="text/javascript">
   $('#offers').children('.column').last().css("border-bottom", "none");
</script>
<script type="text/javascript" async>
   function buyMembershipBeforeDealPurchase() {
      var cur_url = '{{url()->current()}}';
      localStorage.setItem('take_to_deal_page_after_buy_membership', cur_url);
      window.location.href = '{{url("select-card")}}';
   }
   localStorage.removeItem("take_to_deal_page_after_buy_membership");
</script>
@include('footer-js.partner-profile-js')
<script type="text/javascript">
   $(".whitebox-inner-box-deals:not(:first)").css( "border-top", "1px solid #dee2e6" );
   $(".whitebox-inner-box-offers:not(:first)").css( "border-top", "1px solid #dee2e6" );
</script>