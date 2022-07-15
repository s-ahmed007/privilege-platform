@if(!session()->has('partner_admin'))
    <script type="text/javascript">
        window.location = "{{ url('/') }}";
    </script>
@endif
@include('partner-admin.production.header')
<?php
$attr_count = count($subcatInfo->categoryRelation);

if ($subcatInfo->info->partner_category == '3') {
    $american = $bakery = $bbq = $bengali = $cafe = $chinese = $continental = $drinks_and_juice = $english = $fast_food = $fine_dining = $french = $fusion =
    $indian = $japanese = $korean = $mediterranean = $mexican = $pizza = $portuguese = $shisha_lounge = $steak_house = $sushi = $thai = $turkish = $italian =
    $seafood = $icecream_parlor = 0;
    foreach ($subcatInfo->categoryRelation as $value) {
        switch ($value->cat_rel_id) {
            case 1:
                $american = 1;
                break;
            case 3:
                $bakery = 1;
                break;
            case 4:
                $bbq = 1;
                break;
            case 5:
                $bengali = 1;
                break;
            case 8:
                $cafe = 1;
                break;
            case 9:
                $chinese = 1;
                break;
            case 10:
                $continental = 1;
                break;
            case 11:
                $drinks_and_juice = 1;
                break;
            case 12:
                $english = 1;
                break;
            case 13:
                $fast_food = 1;
                break;
            case 14:
                $fine_dining = 1;
                break;
            case 15:
                $french = 1;
                break;
            case 16:
                $fusion = 1;
                break;
            case 17:
                $indian = 1;
                break;
            case 18:
                $japanese = 1;
                break;
            case 19:
                $korean = 1;
                break;
            case 20:
                $mediterranean = 1;
                break;
            case 21:
                $mexican = 1;
                break;
            case 22:
                $pizza = 1;
                break;
            case 23:
                $portuguese = 1;
                break;
            case 24:
                $shisha_lounge = 1;
                break;
            case 25:
                $steak_house = 1;
                break;
            case 26:
                $sushi = 1;
                break;
            case 27:
                $thai = 1;
                break;
            case 28:
                $turkish = 1;
                break;
            case 87:
                $italian = 1;
                break;
            case 88:
                $seafood = 1;
                break;
            case 89:
                $icecream_parlor = 1;
                break;
            default:
        }
    }

} else if ($subcatInfo->info->partner_category == '5') {
    $shower_facilities_men = $swimming_men = $jacuzzi_men = $steam_men = $sauna_men = $gym_men = $yoga_men = $shower_facilities_women = $swimming_women =
    $jacuzzi_women = $steam_women = $sauna_women = $gym_women = $yoga_women = 0;

    foreach ($subcatInfo->categoryRelation as $value) {
        switch ($value->cat_rel_id) {
            case 58:
                $shower_facilities_men = 1;
                break;
            case 59:
                $swimming_men = 1;
                break;
            case 60:
                $jacuzzi_men = 1;
                break;
            case 61:
                $steam_men = 1;
                break;
            case 62:
                $sauna_men = 1;
                break;
            case 63:
                $gym_men = 1;
                break;
            case 64:
                $yoga_men = 1;
                break;
            case 65:
                $shower_facilities_women = 1;
                break;
            case 66:
                $swimming_women = 1;
                break;
            case 67:
                $jacuzzi_women = 1;
                break;
            case 68:
                $steam_women = 1;
                break;
            case 69:
                $sauna_women = 1;
                break;
            case 70:
                $gym_women = 1;
                break;
            case 71:
                $yoga_women = 1;
                break;
            default:
        }
    }
} else if ($subcatInfo->info->partner_category == '2') {
    $fun_activity = $billiards_and_pool = $bowling = $theme_parks = $movie_theatres = $arcade_gaming = $kids_activity = 0;

    foreach ($subcatInfo->categoryRelation as $value) {
        switch ($value->cat_rel_id) {
            case 40:
                $fun_activity = 1;
                break;
            case 41:
                $billiards_and_pool = 1;
                break;
            case 42:
                $bowling = 1;
                break;
            case 43:
                $theme_parks = 1;
                break;
            case 44:
                $movie_theatres = 1;
                break;
            case 45:
                $arcade_gaming = 1;
                break;
            case 91:
                $kids_activity = 1;
                break;
            default:
        }
    }
} else if ($subcatInfo->info->partner_category == '6') {
    $clothing_men = $footwear_men = $jewelry_men = $clothing_women = $footwear_women = $jewelry_women = $beauty_cosmetics_women = $clothing_kids = $footwear_kids = 0;

    foreach ($subcatInfo->categoryRelation as $value) {
        switch ($value->cat_rel_id) {
            case 32:
                $clothing_men = 1;
                break;
            case 33:
                $footwear_men = 1;
                break;
            case 34:
                $jewelry_men = 1;
                break;
            case 35:
                $clothing_women = 1;
                break;
            case 36:
                $footwear_women = 1;
                break;
            case 37:
                $jewelry_women = 1;
                break;
            case 38:
                $clothing_kids = 1;
                break;
            case 39:
                $footwear_kids = 1;
                break;
            case 90:
                $beauty_cosmetics_women = 1;
                break;
            default:
        }
    }
} else if ($subcatInfo->info->partner_category == '1') {
    $salons_men = $face_and_skin_men = $hair_men = $massages_men = $nails_men = $cosmetic_men = $salons_women = $face_and_skin_women = $hair_women =
    $massages_women = $nails_women = $makeup_women = $brows_women = $cosmetic_women = 0;

    foreach ($subcatInfo->categoryRelation as $value) {
        switch ($value->cat_rel_id) {
            case 46:
                $salons_men = 1;
                break;
            case 47:
                $face_and_skin_men = 1;
                break;
            case 48:
                $hair_men = 1;
                break;
            case 49:
                $massages_men = 1;
                break;
            case 50:
                $nails_men = 1;
                break;
            case 51:
                $cosmetic_men = 1;
                break;
            case 52:
                $salons_women = 1;
                break;
            case 53:
                $face_and_skin_women = 1;
                break;
            case 54:
                $hair_women = 1;
                break;
            case 55:
                $massages_women = 1;
                break;
            case 56:
                $nails_women = 1;
                break;
            case 57:
                $cosmetic_women = 1;
                break;
            case 82:
                $makeup_women = 1;
                break;
            case 83:
                $brows_women = 1;
                break;
            default:
        }
    }
} else {
    $restaurants_hotels = $bars_hotels = $swimming_hotels = $fitness_hotels = $leisure_hotels = $restaurants_resorts = $bars_resorts = $swimming_resorts =
    $fitness_resorts = $leisure_resorts = $kids_play_zone = 0;
    foreach ($subcatInfo->categoryRelation as $value) {
        switch ($value->cat_rel_id) {
            case 72:
                $restaurants_hotels = 1;
                break;
            case 73:
                $bars_hotels = 1;
                break;
            case 74:
                $swimming_hotels = 1;
                break;
            case 75:
                $fitness_hotels = 1;
                break;
            case 76:
                $leisure_hotels = 1;
                break;
            case 77:
                $restaurants_resorts = 1;
                break;
            case 78:
                $bars_resorts = 1;
                break;
            case 79:
                $swimming_resorts = 1;
                break;
            case 80:
                $fitness_resorts = 1;
                break;
            case 81:
                $leisure_resorts = 1;
                break;
            case 92:
                $kids_play_zone = 1;
                break;
            default:
        }
    }
}
?>
<!-- page content -->
<div class="right_col" role="main">
    <div class="page-title">
        @if (Session::has('updated'))
            <div class="title_right alert alert-success" style="text-align: center;">{{ Session::get('updated') }}</div>
        @endif
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <form class="form-horizontal form-label-left" method="post" action="{{ url('editAttributes') }}">
            <div class="form-group">
                <div class="heading">
                    <h3>Edit Subcategories</h3>
                </div>
                <div class="bar"></div>
                <div class="row">
                    @if($subcatInfo->info->partner_category == '3')
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="10" <?php if ($continental == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Continental</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="9" <?php if ($chinese == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Chinese</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="26" <?php if ($sushi == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Sushi</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="17" <?php if ($indian == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Indian</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="20" <?php if ($mediterranean == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Mediterranean</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="21" <?php if ($mexican == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Mexican</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="27" <?php if ($thai == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Thai</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="5" <?php if ($bengali == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Bengali</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="14" <?php if ($fine_dining == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Fine Dining</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="11" <?php if ($drinks_and_juice == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Drinks and Juice</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="8" <?php if ($cafe == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Cafe</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="3" <?php if ($bakery == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Bakery</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="13" <?php if ($fast_food == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Fast Food</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="1" <?php if ($american == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    American</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="4" <?php if ($bbq == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    BBQ</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="15" <?php if ($french == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    French</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="16" <?php if ($fusion == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Fusion</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="18" <?php if ($japanese == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Japanese</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="19" <?php if ($korean == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Korean</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="23" <?php if ($portuguese == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Portuguese</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="24" <?php if ($shisha_lounge == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Shisha Lounge</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="25" <?php if ($steak_house == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Steak House</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="22" <?php if ($pizza == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Pizza</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="28" <?php if ($turkish == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Turkish</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="12" <?php if ($english == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    English</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="87" <?php if ($italian == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Italian</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="88" <?php if ($seafood == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Seafood</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" class="sub_cat"
                                              name="89" <?php if ($icecream_parlor == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                    Ice Cream Parlor</label>
                            </div>
                        </div>

                    @elseif($subcatInfo->info->partner_category == '5')
                        <span><br><b>Men</b></span>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="58" <?php if ($shower_facilities_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Shower Facilities</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="59" <?php if ($swimming_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Swimming</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="60" <?php if ($jacuzzi_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Jacuzzi</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="61" <?php if ($steam_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Steam</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="62" <?php if ($sauna_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Sauna</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="63" <?php if ($gym_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Gym</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="64" <?php if ($yoga_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Yoga & Therapy</label>
                        </div>
                        <span><br><b>Women</b></span>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="65" <?php if ($shower_facilities_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Shower Facilities</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="66" <?php if ($swimming_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Swimming</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="67" <?php if ($jacuzzi_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Jacuzzi</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="68" <?php if ($steam_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Steam</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="69" <?php if ($sauna_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Sauna</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="70" <?php if ($gym_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Gym</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="71" <?php if ($yoga_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Yoga & Therapy</label>
                        </div>
                    @elseif($subcatInfo->info->partner_category == '2')
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="40" <?php if ($fun_activity == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Fun Activity</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="41" <?php if ($billiards_and_pool == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Billiards & Pool</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="42" <?php if ($bowling == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Bowling</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="43" <?php if ($theme_parks == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Theme Parks</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="44" <?php if ($movie_theatres == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Movie Theatres</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="45" <?php if ($arcade_gaming == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Arcade Gaming</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="91" <?php if ($kids_activity == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Kids Activity</label>
                        </div>
                    @elseif($subcatInfo->info->partner_category == '1')
                        <span><br><b>Men</b></span>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="46" <?php if ($salons_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Salons</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="47" <?php if ($face_and_skin_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Face & Skin</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="48" <?php if ($hair_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Hair</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="49" <?php if ($massages_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Massages</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="50" <?php if ($nails_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Nails</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="51" <?php if ($cosmetic_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Cosmetic Procedures</label>
                        </div>
                        <span><br><b>Women</b></span>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="52" <?php if ($salons_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Salons</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="53" <?php if ($face_and_skin_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Face & Skin</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="54" <?php if ($hair_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Hair</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="55" <?php if ($massages_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Massages</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="56" <?php if ($nails_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Nails</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="57" <?php if ($cosmetic_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Cosmetic Procedures</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="82" <?php if ($makeup_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Makeup</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="83" <?php if ($brows_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Brows & Lashes</label>
                        </div>
                    @elseif($subcatInfo->info->partner_category == '6')
                        <span><br><b>Men</b></span>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="32" <?php if ($clothing_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Clothing</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="33" <?php if ($footwear_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Footwear</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="34" <?php if ($jewelry_men == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Jewelry, Gifts & Accessories</label>
                        </div>
                        <span><br><b>Women</b></span>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="35" <?php if ($clothing_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Clothing</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="36" <?php if ($footwear_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Footwear</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="37" <?php if ($jewelry_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Jewelry, Gifts & Accessories</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="90" <?php if ($beauty_cosmetics_women == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Beauty & Cosmetics</label>
                        </div>
                        <span><br><b>Kids</b></span>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="38" <?php if ($clothing_kids == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Clothing</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="39" <?php if ($footwear_kids == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Footwear</label>
                        </div>
                    @else
                        <span><br><b>Hotels</b></span>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="72" <?php if ($restaurants_hotels == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Restaurants</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="73" <?php if ($bars_hotels == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Bars</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="74" <?php if ($swimming_hotels == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Swimming Pool</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="75" <?php if ($fitness_hotels == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Fitness Centre</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="76" <?php if ($leisure_hotels == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Leisure Activities</label>
                        </div>
                        <span><br><b>Resorts</b></span>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="77" <?php if ($restaurants_resorts == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Restaurants</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="78" <?php if ($bars_resorts == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Bars</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="79" <?php if ($swimming_resorts == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Swimming Pool</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="80" <?php if ($fitness_resorts == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Fitness Centre</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="81" <?php if ($leisure_resorts == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Leisure Activities</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" class="sub_cat"
                                          name="92" <?php if ($kids_play_zone == 1) echo 'checked value="1"'; else echo 'value="0"';?>>
                                Kids Play Zone</label>
                        </div>
                    @endif
                </div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </div>
        </form>
    </div>
</div>
@include('partner-admin.production.footer')
<script>
    //FUNCTION TO UPDATE SUB CAT or TERTIARY CAT / ATTRIBUTES
    $(document).on('click', '.sub_cat', function () {
        var url = "{{ url('/editSubcategory') }}";
        var is_checked = $(this).val();
        var rel_id = $(this).attr("name");
        var partner_id = "<?php echo Session::get('partner_id')?>";

        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'is_checked': is_checked, 'rel_id': rel_id, 'partner_id': partner_id},
            success: function (data) {
                // console.log(data);
                $('[name=' + data['rel_id'] + ']').val(data['is_checked']);
            }
        });
    });

</script>