@include('header')

{{--for gallery & menu image--}}
<script src="//code.jquery.com/jquery.min.js"></script>
<script src="{{asset('js/partner-profile/images-grid.js')}}"></script>

<link href="{{asset('css/local_service_profile.css')}}" rel="stylesheet">

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div id="localGallery"></div>
            <script>
                (function ($) {
                    $(document).ready(function () {
                        $('#localGallery').imagesGrid({
                            images: [
                                    <?php
                                    foreach($localService['gallery'] as $local){?>
                                {
                                    src: '<?php echo "https://royaltybd.com/" . $local['image_link'] ?>', // url
                                    alt: 'Menu Image',// alternative text
                                    title: 'Menu',// title
                                    caption: 'Menu Image',// modal caption
                                    thumbnail: '<?php echo "https://royaltybd.com/" . $local['image_link'] ?>'
                                },
                                <?php } ?>
                            ],
                            align: true,
                            cells: 3,
                            getViewAllText: function (imgsCount) {
                                return 'View all in Menu Gallery'
                            }
                        });
                    });
                })(jQuery);
            </script>
        </div>
        <div class="col-md-4 ls_profile_details" >
            <h3 class="ls_name">
                {{$localService['name']}}
            </h3>
            <p class="ls_location">
            {{count($localService['info'])}}{{count($localService['info']) > 1 ? ' locations':' location'}}
            </p>
            <p>{{mb_substr($localService['offer_description'], 0, 150)}}
                <a href="" data-toggle="modal" data-target="#description">...more</a>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 ls_details">
            <div class="m-0">
            <div class="col-md-12">
                <div class="ls_block">
                    <p>{{$localService['offer_header']}}</p>
                </div>
                <div class="ls_block">
                    <p>How to use this offer</p>
                    <span>{{$localService['offer_description']}}</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="ls_block">
                    <p>Cancellation policy</p>
                    <span>Non-Cancellable</span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="ls_block">
                    <p>Location</p>
                    <div id="map"></div>
                </div>
                <script>
                    function initMap() {
                        var uluru = {lat: -23.24568, lng: 90.21548};
                        var map = new google.maps.Map(document.getElementById('map'), {
                            zoom: 4,
                            center: uluru
                        });
                        var marker = new google.maps.Marker({
                            position: uluru,
                            map: map
                        });
                    }
                </script>
                <script
                        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiUAI3PhLfsR4XlKiPRG2P8-Aq1LGEvxQ&callback=initMap">
                </script>
            </div>
        </div>
        </div>
        {{--right side bar--}}
        <div class="col-md-4" style="padding: 10px 0 10px 0">
            <div class="ls_right_bar">
                <span>Discount details</span>
                <h2>{{$localService['offer_percentage']}}% OFF</h2>
                <h3>Only at</h3>
                <h4>{{$localService['offered_amount']}}tk</h4>
            </div>
        </div>
    </div>
</div>

<div id="description" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">           <h4 class="modal-title">{{$localService['name']}}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="cross-icon"></i>
                </button>
     
            </div>
            <div class="modal-body">
                <p>{{$localService['offer_description']}}</p>
            </div>
        </div>
    </div>
</div>

@include('footer')