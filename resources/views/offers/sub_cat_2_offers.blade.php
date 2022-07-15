@include('header')
<style type="text/css">
    .active_category{background-color: #ffc82c}
   .ribbon{
      position: absolute;
      right: 14px;
      top: 9px;
      z-index: 1;
      overflow: hidden;
      width: 70px;
      height: 70px;
      text-align: right;
   }
   .ribbon-text{
      font-size: 10px;
      font-weight: bold;
      color: #FFF;
      text-transform: uppercase;
      text-align: center;
      line-height: 20px;
      transform: rotate(45deg);
      -webkit-transform: rotate(45deg);
      width: 100px;
      display: block;
      background: #79A70A;
      background: linear-gradient(#007bff 0%, #1e5799 100%);
      /* box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1); */
      position: absolute;
      top: 19px;
      right: -23px;
   }
    .partner-box-info-container-l img{width: 100px; height: 100px; border-radius: 50%; float: right;}
</style>
<?php use Illuminate\Support\Facades\Request; ?>
<div class="page_loader" style="display: none;">
    <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/loading.gif" alt="Royalty Loading GIF" class="lazyload" Royalty loading icon">
</div>
<div class="container offers-container">
    @if(isset($selected_category))
        <div class="row banner_top_image_category" style="background: url({{$selected_category->banner_img}}) no-repeat fixed 50% 0;"
             title="{{$selected_category->name}}" id="offer_banner_image">
            <div class="banner_top_image_caption mtb-10">
                <h3>{{$selected_category->name}}</h3>
                <span>{{$selected_category->caption}}</span>
            </div>
        </div>
    @endif
    {{--End of header image--}}
    <div class="row">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{url('/')}}">Home</a>
                </li>
                @foreach($categories as $value)
                    @if(!isset($selected_category))
                        <li class="breadcrumb-item active" aria-current="page">All Offers</li>
                        @break
                    @elseif($value->type == $selected_category->type)
                        @if(isset($sub_cat_1) && $sub_cat_1 != 'show')
                            @if(isset($sub_cat_2) && $sub_cat_2 != 'show')
                                <li class="breadcrumb-item">
                                    <a href="{{url('offers_copy')}}" style="">All Offers</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">
                                    <a href="{{url('offers_copy/'.$value->type)}}" style="">{{$value->name}}</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">
                                    <a href="{{url('offers_copy/'.$value->type.'/'.$sub_cat_1)}}" style="">{{$sub_cat_1}}</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{$sub_cat_2}}
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{url('offers_copy')}}" style="">All Offers</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">
                                    <a href="{{url('offers_copy/'.$value->type)}}" style="">{{$value->name}}</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{$sub_cat_1}}
                                </li>
                            @endif
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{url('offers_copy')}}" style="">All Offers</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{$value->name}}
                            </li>
                        @endif
                    @endif
                @endforeach
            </ol>
        </nav>
    </div>

    {{--List of offers start--}}
    <div class="row">
        <div class="col-md-3 col-sm-12 col-xs-12" style="line-height: 1;padding-left: 0">
            <div class="refer_promotion shadow">
                <div class="offers-refer-block">
                    <p class="f-m offerspage-leftbox-head">New Offers Everyday!</p>
                    <p class="offerspage-leftbox-para">
                        We are the first dedicated privilege platform to offer you the best offers in town. Our subscribed members can enjoy up
                        to 75% discount in our partner outlets which includes hotels, restaurants, spas, salons and many more.
                        <br><br>
                        Refer your friends & family to earn Royalty credit which you can redeem for greater rewards!
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
                            <select data-placeholder="Select" name="division" class="chosen-select" title="sorting"
                                    style="margin-bottom: 10px;width: 100%;" id="filter-division">
                                <option selected disabled>&nbsp;&nbsp;&nbsp;Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{$division->name}}">&nbsp;&nbsp;&nbsp;{{$division->name}}</option>
                                @endforeach
                            </select>
                            <select style="width: 100%" data-placeholder="Select" name="area" class="chosen-select"
                                    title="sorting" id="filter-area">
                                <option disabled selected>&nbsp;&nbsp;&nbsp;Area</option>
                                @foreach($divisions[0]->areas as $area)
                                    <option value="{{$area->area_name}}">&nbsp;&nbsp;&nbsp;{{$area->area_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="reset" class="reset-btn btn-primary-r" onclick="ResetFilter()">Reset</button>
                    </form>
                @endif
            </div>
        </div>
        {{--Sorting ends--}}
        {{--partners--}}
        <div class="col-md-9 col-sm-12 col-xs-12 offers">
                <div class="row" id="offers">
                @if (isset($partners) && count($partners) != 0)
                    @foreach($partners as $partner)
                    <?php $pname = str_replace("'", "", $partner['partner_name']); ?>
                  <div class="col-md-6 col-sm-6 col-xs-12 partner-offer-box">
                     <div class="offer-box shadow">
                        @if(count($partner['branches']) == 1)
                            <a href="{{ url('partner-profile/'. $pname .'/'.$partner['branches'][0]['id'])}}">
                        @else
                            <div onclick="showLocationModal( '{{$partner['partner_account_id']}}' )" style="cursor: pointer">
                        @endif
                            <div class="partner-box-holder">
                               <div class="partner-box-img-container">
                                  @if($partner['featured'])
                                     <p class="ribbon">
                                     <span class="ribbon-text">featured</span>
                                     </p>
                                  @endif
                                  @if($partner['partner_cover_photo'] != null)
                                     <img src="{{ $partner['partner_cover_photo'] }}" class="lazyload offer-gallery-img"
                                          title="Royalty Partner Cover Image">
                                  @else
                                     <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/offers/nobanner.png"
                                          alt="Royalty Partner Cover Image" width="100%" height="200px">
                                  @endif
                               </div>
                               <div class="offers-sub-container">
                                  <div class="partner-box-info-container-l">
                                     <img src="{{ $partner['partner_profile_image'] }}"
                                          class="lazyload offer-img-s primary-border">
                                     <p class="card-partner-name dots" style="width: 260px;">{{$partner['partner_name']}}</p>
                                     <p class="partner-area">
                                        {{$partner['locations']}}
                                     </p>
                                     <p class="partner-category">{{$partner['partner_type']}}</p>
                                  </div>
                                  <div class="partner-box-info-offer dots">
                                     <div class="partner-box-info-rating">
                                        <?php $ratings = [1,2,3,4,5]; ?>
                                        @if($partner['average_rating'] == 0)
                                           <p class="partner-box-info-rating">new</p>
                                        @elseif(in_array($partner['average_rating'], $ratings))
                                           <i class="bx bxs-star yellow"></i>
                                           <p class="partner-box-info-rating">{{round($partner['average_rating']).'.0'}}</p>
                                        @else
                                           <i class="bx bxs-star yellow"></i>
                                           <p class="partner-box-info-rating">{{round($partner['average_rating'], 1)}}</p>
                                        @endif
                                     </div>
                                     <span class="partner-box-info-span">
                                    <div class="alloffer-offerdetail dots">
                                       {{$partner['offer_heading']}}
                                    </div>
                                     </span>
                                  </div>
                               </div>
                            </div>
                        @if(count($partner['branches']) == 1)
                            </a><!-- end of profile link -->
                        @else
                            </div><!-- end of profile location modal -->
                        @endif
                  </div>
                </div>
                    @endforeach
            {{--pagination starts here--}}
            <div class="col-md-12 col-sm-12 col-xs-12 pagination">
                {!! $partners->appends(Request::except('page'))->render() !!}
            </div>
            {{--pagination ends here--}}
            @else
                <div><h4 style="text-align: center; color: #007bff;">No offers available.</h4></div>
            @endif
        </div>
    </div>{{--offers end--}}
</div>
</div>
{{--list of offers ends--}}
{{--modal to show branch list of a partner--}}
<div id="profile-modal" class="modal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header"> <h4 class="modal-title partner-name-in-modal"></h4>
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
            jQuery('#facility_tree').toggle();
        });
    });

    $('.sort_heading').click(function () {    // But isn't it an arrow right??
        // And should be a class.
        $(this).children('i').toggleClass("arrow-down");
    });
</script>