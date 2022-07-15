@include("header")
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{asset('css/owl.carousel.min.css')}}" type="text/css">
<link href="{{asset('css/deals/main.css')}}" rel="stylesheet">
<style>
   .partner-box-img-container img{
   height: unset;
   }
   .card {
   font-size: 1em;
   overflow: hidden;
   padding: 0;
   border: none;
   border-radius: .28571429rem;
   box-shadow: 0 1px 3px 0 #d4d4d5, 0 0 0 1px #d4d4d5;
   }
   .card-block {
   font-size: 1em;
   position: relative;
   margin: 0;
   padding: 1em;
   border: none;
   border-top: 1px solid rgba(34, 36, 38, .1);
   box-shadow: none;
   }
   .card-img-top {
   display: block;
   width: 100%;
   height: auto;
   }
   .card-title {
   line-height: 1.2857em;
   }
   .card-text {
   clear: both;
   margin-top: .5em;
   color: rgba(0, 0, 0, .68);
   }
   .card-footer {
   font-size: 1em;
   position: static;
   top: 0;
   left: 0;
   max-width: 100%;
   padding: .75em 1em;
   border-top: 1px solid rgba(0, 0, 0, .05) !important;
   background: #fff;
   }
   .card-inverse .btn {
   border: 1px solid rgba(0, 0, 0, .05);
   }
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
<div class="page_loader" style="display: none;">
   <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif" alt="Royalty Loading GIF" class="lazyload" alt="Royalty loading icon">
</div>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Deals</h2>
         <p>Buy paid and free deals to avail excitings offers</p>
      </div>
   </div>
</section>
<section id="counts" class="counts">
   <div class="container">
      <div class="row" data-aos="fade-up">
         <div class="col-md-12">
            @foreach($banner_images as $row)
            @if($row['category'] == 'all')
            <img src="{{$row['image']}}" alt="Royalty Deals Banner" width="100%" />
            @endif
            @endforeach
         </div>
      </div>
   </div>
</section>
<div class="container-fluid" style="background-color:#f5f5ff; position: sticky; top: 0; z-index:100; padding: 10px 0 0 0">
   <div class="container">
      <div class="section-title" data-aos="fade-up">
         <h2>Filter Deals</h2>
      </div>
      <div class="row" data-aos="fade-up">
         <div class="col-md-12">
            @if(isset($divisions))
            <form class="form-inline" id="filterForm">
               {{--<input type="hidden" name="" value="{{$cur_category}}" id="cat_type">--}}
               <div class="form-group">
                  <select data-placeholder="Select" name="category" class="form-control chosen-selectv mb-2" title="sorting"
                     style="width: 100%;" id="filter-category">
                     <option disabled selected>&nbsp;&nbsp;&nbsp;Category</option>
                     @foreach($categories as $category)
                     <option value="{{$category->id}}">&nbsp;&nbsp;&nbsp;{{$category->name}}</option>
                     @endforeach
                  </select>
               </div>
               <div class="form-group">
                  <select style="width: 100%" data-placeholder="Select" name="area" class="form-control chosen-select mb-2" title="sorting" id="filter-area">
                     <option disabled selected>&nbsp;&nbsp;&nbsp;Area</option>
                     @foreach($divisions->areas as $area)
                     <option value="{{$area->area_name}}">&nbsp;&nbsp;&nbsp;{{$area->area_name}}</option>
                     @endforeach
                  </select>
               </div>
               <div class="form-group">
                  <select style="width: 100%" data-placeholder="Select" name="price" class="form-control chosen-select mb-2" title="sorting" id="filter-price">
                     <option disabled selected>&nbsp;&nbsp;&nbsp;Price</option>
                     <option value="htl">&nbsp;&nbsp;&nbsp;High to Low</option>
                     <option value="lth">&nbsp;&nbsp;&nbsp;Low to High</option>
                  </select>
               </div>
               <div class="btn-group">
                  <button type="button" class="btn btn-deal-rating-sort dropdown-toggle mb-2" data-toggle="dropdown">Rating</button>
                  <div class="dropdown-menu" style="width: 200px">
                     <div class="dropdown-box">
                        <input type="radio" id="rating" name="rating" value="all">
                        <label for="rating">All
                        </label><br>
                        <input type="radio" id="rating" name="rating" value="4">
                        <label for="rating">
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        & up ({{$rat_deals_count['four']}})
                        </label><br>
                        <input type="radio" id="rating" name="rating" value="3">
                        <label for="rating">
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        & up ({{$rat_deals_count['three']}})
                        </label><br>
                        <input type="radio" id="rating" name="rating" value="2">
                        <label for="rating">
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        & up ({{$rat_deals_count['two']}})
                        </label><br>
                        <input type="radio" id="rating" name="rating" value="1">
                        <label for="rating">
                        <i class="bx bxs-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        <i class="bx bx-star yellow"></i>
                        & up ({{$rat_deals_count['one']}})
                     </div>
                  </div>
               </div>
               <div class="btn btn-group" style="float: right;">
                  <button type="reset" class="btn btn-primary mb-2" onclick="ResetFilter()">Reset</button>
               </div>
            </form>
            @endif
         </div>
      </div>
   </div>
</div>
<section id="counts" class="counts">
   <div class="container">
      <div class="section-title" data-aos="fade-up">
         <h2>Local Deals</h2>
         <p><span class="deal_count">{{$total_vochuers}}{{$total_vochuers > 1 ? ' deals':' deal'}}</span> for you</p>
      </div>
      <div class="row" data-aos="fade-left">
         <div class="col-md-12">
            <div class="container">
               <div class="row" id="deals_section">
                  @if(count($vouchers) > 0)
                  @foreach($vouchers as $voucher)
                  <?php $pname = str_replace("'", "", $voucher['branch']['info']['partner_name']); ?>
                  <div class="col-sm-6 col-md-3 col-lg-3 mt-3">
                     <div class="card card-inverse card-info">
                        <a href="{{url('partner-profile/'. $pname .'/'.$voucher['branch']['id'])}}">
                           <img class="card-img-top" src="{{$voucher['branch']['info']['profile_image']['partner_cover_photo']}}" alt="royalty-partner-cover">
                           <div class="card-block">
                              {{-- <figure class="profile profile-inline">
                                 <img src="{{ $exclusiveOffers['partner_profile_image'] }}" class="profile-avatar border" alt="royalty-partner-profile-logo">
                                 </figure> --}}
                              <h4 class="card-title card-partner-name">{{$voucher['branch']['info']['partner_name']}}</h4>
                              <div class="card-text">
                                 <p>
                                    {{$voucher['branch']['partner_area'].', '.$voucher['branch']['partner_division']}}
                                 </p>
                                 <p class="card-partner-type">{{$voucher['branch']['info']['partner_type']}}</p>
                              </div>
                           </div>
                           <div class="card-footer">
                              <small>{{$voucher['heading']}}</small>
                              <button class="btn float-right btn-sm">
                                 <?php $ratings = [1,2,3,4,5]; ?>
                                 @if($voucher['branch']['info']['rating']['average_rating'] == 0)
                                    <p class="partner-box-info-rating">new</p>
                                 @elseif(in_array($voucher['branch']['info']['rating']['average_rating'], $ratings))
                                    <i class="bx bxs-star yellow"></i>
                                    <p class="partner-box-info-rating">
                                    {{round($voucher['branch']['info']['rating']['average_rating']).'.0'}}</p>
                                 @else
                                    <i class="bx bxs-star yellow"></i>
                                    <p class="partner-box-info-rating">{{round($voucher['branch']['info']['rating']['average_rating'], 1)}}</p>
                                 @endif
                              </button>
                           </div>
                        </a>
                        <a href="{{url('deals/'.$voucher['branch_id'])}}">
                           <div class="deal-buy-btn btn-primary-thin">Buy Now</div>
                        </a>
                     </div>
                  </div>
                  @endforeach
                  <div class="col-md-12 col-sm-12 col-xs-12 pagination">
                     {{$vouchers->links()}}
                  </div>
                  @else
                  <h3>No Deal found.</h3>
                  @endif
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<section id="faq" class="faq section-bg">
   <div class="container">
      <div class="section-title" data-aos="fade-up">
         <h2>F.A.Q</h2>
         <p>Frequently Asked Questions</p>
      </div>
      <div class="faq-list">
         <ul>
            <li data-aos="fade-up">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" class="collapsed" href="#faq-list-1">What are Royalty deals?<i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-1" class="collapse" data-parent=".faq-list">
                  <p>
                     Royalty deals are pre-purchased offers that customers can redeem at partners’ physical stores in a given time period.
                  </p>
               </div>
            </li>
            <li data-aos="fade-up" data-aos-delay="100">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-2" class="collapsed">How do I redeem the deals? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-2" class="collapse" data-parent=".faq-list">
                  <p>
                     After you buy a deal of your choice, the deal gets added to the “My Purchases” tab in the “More” section of the app/ user account on the web. After visiting the particular partner outlet, show the deal in your app from “My Purchases” “Available” tap on the deal and ask the manager to enter their PIN to redeem the deal. Remember to see the details page of the particular deal to know more about the Partner and their deals.
                  </p>
               </div>
            </li>
            <li data-aos="fade-up" data-aos-delay="200">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-3" class="collapsed">Till when can I use the Deals? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-3" class="collapse" data-parent=".faq-list">
                  <p>
                     Each deal has a different redemption period, so your deal can only be redeemed at the specified redemption time given on the details of the deal page.
                     For purchased deals: To check your redemption period, click the 'My Purchases' tab > Select the deal and view details to know about the redemption time period.
                  </p>
               </div>
            </li>
            <li data-aos="fade-up" data-aos-delay="300">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-4" class="collapsed"> Are the Deals valid on Royalty Offers?<i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-4" class="collapse" data-parent=".faq-list">
                  <p>
                     The deal may not be applicable with the existing offers at the participating partner outlet(s).
                  </p>
               </div>
            </li>
            <li data-aos="fade-up" data-aos-delay="400">
               <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#faq-list-5" class="collapsed">Can I cancel Deal after purchase? <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
               <div id="faq-list-5" class="collapse" data-parent=".faq-list">
                  <p>
                     Yes, you can get a refund as long as your cancelable deal is within the cancellation deadline. You will find an option to cancel a deal on the deals details page.<br>
                     Please Note: The refund will be added to your account as Royalty Credits which can be used later for purchasing more deals. <br>
                     For any issues with cancellation, kindly contact our team via support@royaltybd.com within the cancellation deadline.
                  </p>
               </div>
            </li>
         </ul>
      </div>
   </div>
</section>
@include("footer")
<script src="{{asset('js/deals/all_deals.js')}}"></script>