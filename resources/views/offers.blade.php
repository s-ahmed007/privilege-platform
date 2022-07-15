@include('header')
<style>.active_category{background-color: #ffc82c}
   .partner-box-info-container-l img{width: 100px; height: 100px; border-radius: 50%; float: right;}
   
   .profile {
   position: absolute;
   top: -12px;
   display: inline-block;
   overflow: hidden;
   box-sizing: border-box;
   width: 25px;
   height: 25px;
   margin: 0;
   border: 1px solid #fff;
   border-radius: 50%;
   }
   .profile-avatar border {
   display: block;
   width: 100%;
   height: 100%;
   border-radius: 50%;
   }
   .profile-inline {
   position: relative;
   top: 0;
   display: inline-block;
   }
   .profile-inline ~ .card-title {
   display: inline-block;
   margin-left: 4px;
   vertical-align: top;
   }
</style>
<?php use Illuminate\Support\Facades\Request; ?>
<div class="page_loader" style="display: none;">
   <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif" alt="Royalty Loading GIF" class="lazyload" alt="Royalty loading icon">
</div>
<!-- <section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>All Offers</h2>
         <p>Get exclusive offers, discounts & rewards</p>
         <p>
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
               {{--               
               <li class="breadcrumb-item">--}}
                  {{--                  <a href="{{url('/')}}">Home</a>--}}
                  {{--               
               </li>
               --}}
               {{--               @foreach($categories as $value)--}}
               {{--               @if(!isset($selected_category))--}}
               {{--               
               <li class="breadcrumb-item active" aria-current="page">All Offers</li>
               --}}
               {{--               @break--}}
               {{--               @elseif($value->type == $selected_category)--}}
               {{--               
               <li class="breadcrumb-item">--}}
                  {{--                  <a href="{{url('offers/all')}}" style="">All Offers</a>--}}
                  {{--               
               </li>
               --}}
               {{--               
               <li class="breadcrumb-item active" aria-current="page">--}}
                  {{--                  {{$value->name}}--}}
                  {{--               
               </li>
               --}}
               {{--               @endif--}}
               {{--               @endforeach--}}
               @if(isset($selected_category))
               <li class="breadcrumb-item">
                  <a href="{{url('offers/all')}}" style="">All Offers</a>
               </li>
               @foreach($categories as $value)
               @if($value->type == $selected_category)
               <li class="breadcrumb-item active" aria-current="page">
                  {{$value->name}}
               </li>
               @endif
               @endforeach
               @endif
            </ol>
         </nav>
         </p>
      </div>
   </div>
</section> -->
<section style="padding: 100px 0 60px;">
   <div class="container offers-container">
      @if(isset($selected_category))
      @if($selected_category == 'food_and_drinks')
      <div class="banner_top_image_offer_foodndrinks" title="Food and drinks" id="offer_banner_image">
         <div class="banner_top_image_caption mtb-10">
            <h1>Food & Drinks</h1>
            <span>Dine and drink in style with our exclusive savings.</span>
         </div>
      </div>
      @elseif($selected_category == 'health_and_fitness')
      <div class="banner_top_image_offer_healthandfitness" title="Health and Fitness" id="offer_banner_image">
         <div class="banner_top_image_caption mtb-10">
            <h1>Health & Fitness</h1>
            <span>Go for a run, lose weight, lift some weights while we cover you.</span>
         </div>
      </div>
      @elseif($selected_category == 'lifestyle')
      <div class="banner_top_image_offer_lifestyle" title="Lifestyle" id="offer_banner_image">
         <div class="banner_top_image_caption mtb-10">
            <h1>Lifestyle</h1>
            <span>Stay trendy, look good and feel good with these exclusive offers.</span>
         </div>
      </div>
      @elseif($selected_category == 'beauty_and_spa')
      <div class="banner_top_image_offer_beautyandspa" title="Beauty and spa" id="offer_banner_image">
         <div class="banner_top_image_caption mtb-10">
            <h1>Beauty & Spa</h1>
            <span>Give yourself a well-deserved break and relax with these great discounts.</span>
         </div>
      </div>
      @elseif($selected_category == 'entertainment')
      <div class="banner_top_image_offer_entertainment" title="Entertainment" id="offer_banner_image">
         <div class="banner_top_image_caption mtb-10">
            <h1>Entertainment</h1>
            <span>Catch a movie, play pool or just chill with family & friends!</span>
         </div>
      </div>
      @else
      <div class="banner_top_image_offer_getaway" title="Getaways" id="offer_banner_image">
         <div class="banner_top_image_caption mtb-10">
            <h1>Getaways</h1>
            <span>Get epic offers at some of your favorite getaway destinations!</span>
         </div>
      </div>
      @endif
      @else
      <div title="All Offers">
         <div class="banner_top_image_caption mtb-10">
            <h1>Exclusive Offers</h1>
            <span>All our exclusive offers for the Royal Customers.</span>
         </div>
      </div>
      @endif
      {{--End of header image--}}
      {{--List of offers start--}}
      <div class="row">
         <div class="col-md-3 col-sm-12 col-xs-12">
            <div class="refer_promotion shadow">
               <div class="offers-refer-block">
                  <p class="f-m offerspage-leftbox-head">New Offers Everyday!</p>
                  <p class="offerspage-leftbox-para">
                     We are the first dedicated privilege platform to offer you the best offers in town. Our subscribed members can enjoy up
                     to 75% discount in our partner outlets which includes hotels, restaurants, spas, salons and many more.
                     <br><br>
                     Refer your friends & family to earn Royalty Credit which you can redeem for greater rewards!
                  </p>
               </div>
               <hr>
               <div class="offers-category-block"><p>
                  Read our <a href="{{ url('faq') }}" target="_blank">FAQs </a> to know more about us!</p>
               </div>
            </div>
            <br>
            <div class="offer_filter_sidemenu_container shadow">
               @if(isset($selected_category))
               <form id="filterForm">
                  <p class="sort_heading bolder">Sort by</p>
                  <div class="sort-select">
                     {{--<select data-placeholder="Select" name="priceLevel" class="chosen-select" title="sorting"--}}
                     {{--style="margin-bottom: 10px; width: 100%" id="filter-discount">--}}
                     {{--
                     <option disabled selected>&nbsp;&nbsp;&nbsp;Discount</option>
                     --}}
                     {{--
                     <option value="lh">&nbsp;&nbsp;&nbsp;Low to High</option>
                     --}}
                     {{--
                     <option value="hl">&nbsp;&nbsp;&nbsp;High to Low</option>
                     --}}
                     {{--</select>--}}
                     <select data-placeholder="Select" name="division" class="chosen-select" title="sorting"
                        style="margin-bottom: 10px;width: 100%;" id="filter-division">
                        <option disabled>&nbsp;&nbsp;&nbsp;Division</option>
                        <option selected value="Dhaka">&nbsp;&nbsp;&nbsp;Dhaka</option>
                        {{--                     @foreach($divisions as $division)--}}
                        {{--
                        <option value="{{$division->name}}">&nbsp;&nbsp;&nbsp;{{$division->name}}</option>
                        --}}
                        {{--                     @endforeach--}}
                     </select>
                     <select style="width: 100%" data-placeholder="Select" name="area" class="chosen-select"
                        title="sorting" id="filter-area">
                        <option disabled selected>&nbsp;&nbsp;&nbsp;Area</option>
                        @foreach($divisions[0]->areas as $area)
                        <option value="{{$area->area_name}}">&nbsp;&nbsp;&nbsp;{{$area->area_name}}</option>
                        @endforeach
                     </select>
                  </div>
                  <p id="facility_toggle" style="cursor:pointer;" class="sort_heading bolder">Facilities
                     <i class="fas fa-chevron-circle-down"></i>
                  </p>
                  <ul id="facility_tree" style="display: none;">
                     @foreach($facilities as $facility)
                     <li>
                        <label class="filter">{{$facility->name}}
                        <input type="checkbox" onclick="filterAttribute({{$facility->id}})">
                        <span class="checkmark"></span>
                        </label>
                     </li>
                     @endforeach
                  </ul>
                  @if(isset($sub_cats->first()->sub_cat_2))
                  @foreach($sub_cats as $key => $category)
                  <p id="subcat_toggle{{$key+1}}" class="sort_heading bolder">{{$category->cat_name}}
                     <i class="fas fa-chevron-circle-down"></i>
                  </p>
                  <ul id="tree{{$key+1}}" style="display: none;">
                     @foreach($category->sub_cat_2 as $key1 => $sub_cat)
                     <li>
                        <label class="filter">{{$sub_cat->cat_name}}
                        <input type="checkbox" onclick="filterSubcategory('{{$sub_cat->id}}')">
                        <span class="checkmark"></span>
                        </label>
                     </li>
                     @endforeach
                  </ul>
                  @endforeach
                  @else
                  <p id="subcat_toggle1" class="sort_heading bolder" style="cursor: pointer">Subcategory
                     <i class="fas fa-chevron-circle-down"></i>
                  </p>
                  <ul id="tree1" style="display: none;">
                     @foreach($sub_cats as $key => $category)
                     <li>
                        <label class="filter">{{$category->cat_name}}
                        <input type="checkbox" onclick="filterSubcategory('{{$category->id}}')">
                        <span class="checkmark"></span>
                        </label>
                     </li>
                     @endforeach
                  </ul>
                  @endif
                  <button type="reset" class="reset-btn btn-primary-r" onclick="ResetFilter()">Reset</button>
               </form>
               @endif
            </div>
         </div>
         {{--Sorting ends--}}
         {{--offers--}}
         <div class="col-md-9 col-sm-12 col-xs-12 offers">
            <?php
               $cat_div_1 = $categories->take(6);
               $cat_div_2 = $categories->slice(6);
               ?>
            <ul class="list-inline mb">
               <li>
                  <a href="{{ url('offers/all') }}">
                     <div class="offer-category{{(! isset($selected_category) ? ' active_category' : '')}}">
                        <p class="offer-category-name bolder">All Offers</p>
                     </div>
                  </a>
               </li>
               @foreach($cat_div_1 as $category)
               <li style="padding: 5px">
                  <a href="{{ url('offers/'.$category->type) }}">
                     <div class="offer-category{{(isset($selected_category) && $selected_category == $category->type ? ' active_category' : '')}}">
                        <p class="offer-category-name bolder">{{$category->name}}</p>
                     </div>
                  </a>
               </li>
               @endforeach
               @foreach($cat_div_2 as $category)
               <li style="display: none; padding: 5px" class="hidden_categories">
                  <a href="{{ url('offers/'.$category->type) }}">
                     <div class="offer-category{{(isset($selected_category) && $selected_category == $category->type ? ' active_category' : '')}}">
                        <p class="offer-category-name bolder">{{$category->name}}</p>
                     </div>
                  </a>
               </li>
               @endforeach
            </ul>
            @if(count($categories) > 6)
            <div class="row">
               <div class="col-md-12 col-sm-11 col-xs-12 offers">
                  <button class="btn btn-primary" style="float: right;" onclick="hideCategory(this)"><i class="fa fa-angle-down"></i></button>
               </div>
            </div>
            @endif
            <div class="row" id="offers">
               @if (isset($profileImages) && count($profileImages) != 0)
               @foreach($profileImages as $exclusiveOffers)
               <?php $pname = str_replace("'", "", $exclusiveOffers['partner_name']); ?>
               <div class="col-sm-6 col-md-4 col-lg-4 mt-4">
                  @if(count($exclusiveOffers['branches']) == 1)
                  <a href="{{ url('partner-profile/'. $pname .'/'.$exclusiveOffers['branches'][0]['id'])}}">
                     @else
                     <div onclick="showLocationModal( '{{$exclusiveOffers['partner_account_id']}}' )" style="cursor: pointer">
                        @endif
                        <div class="card card-inverse card-info">
                           @if($exclusiveOffers['partner_cover_photo'] != null)
                           <img class="card-img-top" src="{{ $exclusiveOffers['partner_cover_photo'] }}" alt="royalty-partner-cover">
                           @else
                           <img class="card-img-top" src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/offers/nobanner.png" alt="royalty-partner-cover">
                           @endif
                           <div class="card-block">
                              <h4 class="card-title card-partner-name">{{$exclusiveOffers['partner_name']}}</h4>
                              <div class="card-text">
                                 <p>
                                    {{$exclusiveOffers['locations']}} -
                                    <?php $ratings = [1,2,3,4,5]; ?>
                                    @if($exclusiveOffers['average_rating'] == 0)
                                    <span class="partner-box-info-rating">new</span>
                                    @elseif(in_array($exclusiveOffers['average_rating'], $ratings))
                                    <i class="bx bxs-star yellow"></i>
                                    <span class="partner-box-info-rating">{{round($exclusiveOffers['average_rating']).'.0'}}</span>
                                    @else
                                    <i class="bx bxs-star yellow"></i>
                                    <span class="partner-box-info-rating">{{round($exclusiveOffers['average_rating'], 1)}}</span>
                                    @endif
                                 </p>
                              </div>
                           </div>
                           <div class="card-footer">
                              <label class="label-tag-small">OFFER</label>
                              <small class="bold black"> {{$exclusiveOffers['offer_heading']}}</small>
                           </div>
                        </div>
                        @if(count($exclusiveOffers['branches']) == 1)
                  </a>
                  @else
                  </div>
                  @endif
               </div>
               @endforeach
               {{--pagination starts here--}}
               <div class="col-md-12 col-sm-12 col-xs-12 pagination">
                  {!! $profileImages->appends(Request::except('page'))->render() !!}
               </div>
               {{--pagination ends here--}}
               @else
               <div>
                  <h4 style="text-align: center; color: #007bff;">No offers available.</h4>
               </div>
               @endif
            </div>
         </div>
      </div>
   </div>
</section>
{{--list of offers ends--}}
{{--modal to show branch list of a partner--}}
<div id="profile-modal" class="modal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title partner-name-in-modal"></h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
         </div>
         <div class="modal-body">
            <div class="partner-branches">
               <ul id="branch_list"></ul>
            </div>
         </div>
      </div>
   </div>
</div>
{{--branch list modal ends--}}
@include('footer')
@include('footer-js.offersPage-js')
<script>
   $(window).scroll(function () {
      $("#offer_banner_image").css("background-position", "50% 0");
   });
</script>
<script>
   $.fn.extend({
      treed: function (o) {
         var openedClass = 'glyphicon-minus-sign';
         var closedClass = 'glyphicon-plus-sign';
         if (typeof o != 'undefined') {
            if (typeof o.openedClass != 'undefined') {
               openedClass = o.openedClass;
            }
            if (typeof o.closedClass != 'undefined') {
               closedClass = o.closedClass;
            }
         }
         //initialize each of the top levels
         var tree = $(this);
         tree.addClass("tree");
         tree.find('li').has("ul").each(function () {
            var branch = $(this); //li with children ul
            branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
            branch.addClass('branch');
            branch.on('click', function (e) {
               if (this == e.target) {
                  var icon = $(this).children('i:first');
                  icon.toggleClass(openedClass + " " + closedClass);
                  $(this).children().children().toggle();
               }
            });
            branch.children().children().toggle();
         });
         //fire event from the dynamically added icon
         tree.find('.branch .indicator').each(function () {
            $(this).on('click', function () {
               $(this).closest('li').click();
            });
         });
         //fire event to open branch if the li contains an anchor instead of text
         tree.find('.branch>a').each(function () {
            $(this).on('click', function (e) {
               $(this).closest('li').click();
               e.preventDefault();
            });
         });
         //fire event to open branch if the li contains a button instead of text
         tree.find('.branch>button').each(function () {
            $(this).on('click', function (e) {
               $(this).closest('li').click();
               e.preventDefault();
            });
         });
      }
   });
   //Initialization of treeviews
   $('#tree1').treed();
   $('#tree2').treed();
   $('#tree3').treed();
   $('#tree4').treed();
   $('#facility_tree').treed();
</script>
<script>
   jQuery(document).ready(function () {
      jQuery('#subcat_toggle1').on('click', function (event) {
         jQuery('#tree1').toggle();
      });
   });
   jQuery(document).ready(function () {
      jQuery('#subcat_toggle2').on('click', function (event) {
         jQuery('#tree2').toggle();
      });
   });
   jQuery(document).ready(function () {
      jQuery('#subcat_toggle3').on('click', function (event) {
         jQuery('#tree3').toggle();
      });
   });
   jQuery(document).ready(function () {
      jQuery('#subcat_toggle4').on('click', function (event) {
         jQuery('#tree4').toggle();
      });
   });
   jQuery(document).ready(function () {
      jQuery('#facility_toggle').on('click', function (event) {
         jQuery('#facility_tree').toggle();
      });
   });
   
   $('.sort_heading').click(function () {    // But isn't it an arrow right??
      // And should be a class.
      $(this).children('i').toggleClass("arrow-down");
   });
   function hideCategory(elem) {
      var cur_elem = $(elem).children('i');
      if (cur_elem.hasClass('fa-angle-down')) {
         cur_elem.removeClass('fa-angle-down').addClass('fa-angle-up');
      } else {
         cur_elem.removeClass('fa-angle-up').addClass('fa-angle-down');
      }
      $(".hidden_categories").toggle();
   }
</script>