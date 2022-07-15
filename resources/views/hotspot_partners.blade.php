@include('header')
<div class="container hotspot-container">
   @if(isset($hotspotName))
   <div class="row" title="Hotspot">
      <div class="banner_top_image_caption mtb-10">
         <h1>{{$hotspotName}}</h1>
         <span>{{$hotspot_description->description}}</span>
      </div>
   </div>
   @endif
   <br><br>
   {{--List of offers start--}}
   <div class="row">
      {{--Sort Row--}}
      <div class="col-md-3 col-sm-12 col-xs-12" style="line-height: 1;padding-left: 0">
         <div class="refer_promotion shadow">
            <div class="offers-refer-block">
               <h3>Refer your FNF!</h3>
               <p class="refer-desc">
                  Refer your friends & family to earn Royalty Credit which you can avail in our partner's outlets!
               </p>
               <div class="refer-code-copy">
                  <div class="contact">
                     <div class="btn-group" style="float: right;">
                        @if(Session::has('customer_id'))
                        <button type="button" class="btn btn-default btn-copy js-tooltip js-copy"
                           data-toggle="tooltip" data-placement="bottom"
                           data-copy="{{session('referral_number')}}"
                           title="Copy">
                        {{session('referral_number')}}
                        <i class="bx bxs-copy"></i>
                        </button>
                        @endif
                     </div>
                  </div>
               </div>
            </div>
            <hr>
            <div class="offers-category-block">
               <p>
               Read more about referring on our <a href="{{ url('faq') }}" target="_blank">FAQs
               page</a>
</p>.
            </div>
         </div>
      </div>
      {{--Sorting Row ends--}}
      {{--offers--}}
      <div class="col-md-9 col-lg-9 col-sm-6 col-xs-12 offers" style="padding-right: 0">
         <div class="row" id="offers">
            @if (isset($partnerInfo) &&  count($partnerInfo) > 0 )
            @foreach($partnerInfo as $info)
            <?php $pname = str_replace("'", "", $info->info['partner_name']); ?>
            <a href="{{ url('partner-profile/'. $pname .'/'.$info->id)}}">
               <div class="col-md-6 partner-offer-box">
                  <div class="row offer-box">
                     <div class="col-md-6 image-box">
                        <div class="box">
                           <?php
                              $keys = array_keys($info->info['galleryImages']->toArray());
                              $random = $keys[array_rand($keys, 1)];
                              $image = $info->info['galleryImages'][$random]['partner_gallery_image'];
                              ?>
                           <img src="{{ $image }}" class="lazyload" Royalty Hotspot">
                        </div>
                     </div>
                     <div class="col-md-6 discount-info-box">
                        <img src="{{asset($info->info['profileImage']['partner_thumb_image'])}}"
                           class="lazyload" alt="Royalty hotspot">
                        <div class="partner-info">
                           <p class="dots partner-name">{{$info->info['partner_name']}}</p>
                           <p class="partner-area">{{stripslashes($info->info['partner_area'])}}</p>
                           @foreach($categories as $category)
                           @if($category->id == $info->info['partner_category'])
                           <p class="partner-category">{{$category->name}}</p>
                           @endif
                           @endforeach
                        </div>
                        <div>
                           <span>
                           @if($info->info['rating']['average_rating'] == 1)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] == 2)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] == 3)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] == 4)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] == 5)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] >1.0 && $info->info['rating']['average_rating'] <=1.5)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star-half yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] >1.5 && $info->info['rating']['average_rating'] < 2.0)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] > 2.0 && $info->info['rating']['average_rating'] <= 2.5)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star-half yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] > 2.5 && $info->info['rating']['average_rating'] < 3.0)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] > 3 && $info->info['rating']['average_rating'] <= 3.5)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star-half yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] > 3.5 && $info->info['rating']['average_rating'] < 4.0)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bx-star yellow"></i>
                           @elseif($info->info['rating']['average_rating'] > 4.0 && $info->info['rating']['average_rating'] <= 4.5)
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star yellow"></i>
                           <i class="bx bxs-star-half yellow"></i>
                           @elseif($info->info['rating']['average_rating'] > 4.5 && $info->info['rating']['average_rating'] <= 5.0)
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
                           </span>
                           <span>({{count($info->info['reviews'])}}{{count($info->info['reviews']) > 1 ? ' reviews' : ' review'}})</span>
                        </div>
                     </div>
                  </div>
               </div>
            </a>
            @endforeach
            @else
            <div class="no-info">
               <h4 style="color: #000">No hotspot offers found</h4>
            </div>
            @endif
         </div>
         {{--offers end--}}
      </div>
      {{--list of offers ends--}}
   </div>
</div>
@include('footer')
<script>
   $(window).scroll(function () {
       $("#offer_banner_image").css("background-position", "50% 0");
   });
</script>