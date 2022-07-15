@include('header')

<link href="{{asset('css/local-services.css')}}" rel="stylesheet">
<div class="container">
    <div class="row banner_top_image_offer_localservices">
        <div class="banner_top_image_caption mtb-10">
            <h3>Local Services</h3>
            <span>Get the best service for you and your family at your doorstep.
                Simply choose from the wide range of local services Royalty is offering and enjoy the awesome discounts.
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tab" role="tabpanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#Section1" aria-controls="home" role="tab" data-toggle="tab">
                            <i class="fa fa-globe"></i>All Offers
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#Section2" aria-controls="profile" role="tab" data-toggle="tab">
                            <i class="fa fa-car"></i>Automotive
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#Section3" aria-controls="messages" role="tab" data-toggle="tab">
                            <i class="fa fa-home"></i>Home Services
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#Section4" aria-controls="messages" role="tab" data-toggle="tab">
                            <i class="fa fa-briefcase"></i>Personal Services
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#Section5" aria-controls="messages" role="tab" data-toggle="tab">
                            <i class="fa fa-book"></i>Education
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#Section6" aria-controls="messages" role="tab" data-toggle="tab">
                            <i class="fa fa-binoculars"></i>Others
                        </a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content tabs">
                    <div role="tabpanel" class="tab-pane fade in active" id="Section1">
                        <div class="row">
                            @foreach($allLocalServices as $local)
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="deal">
                                        <a href="{{url('local-service/'.$local['name'])}}">
                                            <img src="{{asset($local['profile_image'])}}" class="local_image"
                                                 class="lazyload" alt="local image">
                                            <p class="local_name">{{$local['name']}}</p>
                                        </a>
                                        <div class="local_type">
                                            <span>{{$local['offer_header']}}</span>
                                        </div>
                                        <span class="local_percentage">{{$local['offer_percentage']}}% off </span>
                                        <span class="local_amount"> {{$local['offered_amount']}} tk</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="Section2">
                        <div class="row">
                            @foreach($allLocalServices as $local)
                                @if($local['category'] == 'automotive')
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="deal">
                                            <a href="{{url('local-service/'.$local['name'])}}">
                                                <img src="{{asset($local['profile_image'])}}" class="local_image"
                                                     class="lazyload" alt="local image">
                                                <p class="local_name">{{$local['name']}}</p>
                                            </a>
                                            <div class="local_type">
                                                <span>{{$local['offer_header']}}</span>
                                            </div>
                                            <span class="local_percentage">{{$local['offer_percentage']}}% off </span>
                                            <span class="local_amount"> {{$local['offered_amount']}} tk</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="Section3">
                        <div class="row">
                            @foreach($allLocalServices as $local)
                                @if($local['category'] == 'home_service')
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="deal">
                                            <a href="{{url('local-service/'.$local['name'])}}">
                                                <img src="{{asset($local['profile_image'])}}" class="local_image"
                                                     class="lazyload" alt="local image">
                                                <p class="local_name">{{$local['name']}}</p>
                                            </a>
                                            <div class="local_type">
                                                <span>{{$local['offer_header']}}</span>
                                            </div>
                                            <span class="local_percentage">{{$local['offer_percentage']}}% off </span>
                                            <span class="local_amount"> {{$local['offered_amount']}} tk</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="Section4">
                        <div class="row">
                            @foreach($allLocalServices as $local)
                                @if($local['category'] == 'personal_service')
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="deal">
                                            <a href="{{url('local-service/'.$local['name'])}}">
                                                <img src="{{asset($local['profile_image'])}}" class="local_image"
                                                     class="lazyload" alt="local image">
                                                <p class="local_name">{{$local['name']}}</p>
                                            </a>
                                            <div class="local_type">
                                                <span>{{$local['offer_header']}}</span>
                                            </div>
                                            <span class="local_percentage">{{$local['offer_percentage']}}% off </span>
                                            <span class="local_amount"> {{$local['offered_amount']}} tk</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="Section5">
                        <div class="row">
                            @foreach($allLocalServices as $local)
                                @if($local['category'] == 'education')
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="deal">
                                            <a href="{{url('local-service/'.$local['name'])}}">
                                                <img src="{{asset($local['profile_image'])}}" class="local_image"
                                                     class="lazyload" alt="local image">
                                                <p class="local_name">{{$local['name']}}</p>
                                            </a>
                                            <div class="local_type">
                                                <span>{{$local['offer_header']}}</span>
                                            </div>
                                            <span class="local_percentage">{{$local['offer_percentage']}}% off </span>
                                            <span class="local_amount"> {{$local['offered_amount']}} tk</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="Section6">
                        <div class="row">
                            @foreach($allLocalServices as $local)
                                @if($local['category'] == 'others')
                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="deal">
                                            <a href="{{url('local-service/'.$local['name'])}}">
                                                <img src="{{asset($local['profile_image'])}}" class="local_image"
                                                     class="lazyload" alt="local image">
                                                <p class="local_name">{{$local['name']}}</p>
                                            </a>
                                            <div class="local_type">
                                                <span>{{$local['offer_header']}}</span>
                                            </div>
                                            <span class="local_percentage">{{$local['offer_percentage']}}% off </span>
                                            <span class="local_amount"> {{$local['offered_amount']}} tk</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('footer')

<script>
    $(window).scroll(function () {
        $(".banner_top_image_offer_localservices").css("background-position", "50% 0");
    });
</script>