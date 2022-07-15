@include('partner-dashboard.header')
<div class="container-fluid">
<div class="row bg-title">
        <div class="col-lg-5 col-md-4 col-sm-4 col-xs-12">
            <h3 class="d-inline-block">{{__('partner/common.my_offers')}}</h3>
                <h5 class="d-inline-block float-right">{{__('partner/offers.see_your_offers_here')}}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @if(count($sorted_offers) > 0)
                @foreach($sorted_offers as $offer)
                    <div class="column">
                        <div class="row m-z-a">
                            <div class="col-md-10">
                                <div class="partner-offer-box-l">
                                    <h4>{{$offer->offer_description}}</h4>
                                    <?php
                                    if($offer->actual_price != 0){
                                        $deducted_price = $offer->actual_price - $offer->price;
                                        $percentage = floor(($deducted_price * 100)/$offer->actual_price);
                                    }else{
                                        $percentage = 0;
                                    }
                                    ?>
                                    <div class="partner-offer-timings">
                                        <p>Valid till -
                                            <span>
                                        {{date("F d, Y", strtotime($offer->date_duration[0]['to']))}}
                                        </span>
                                        </p>
                                        <p>Valid for - <span>{{$offer->valid_for}}</span></p>
                                        <p>Valid on -
                                            <span>
                                            <?php
                                                $weekdays = $offer->weekdays[0];
                                                ?>
                                                @if($weekdays['sat'] == '1' && $weekdays['sun'] == '1' && $weekdays['mon'] == '1' && $weekdays['tue'] == '1' &&
                                                $weekdays['wed'] == '1' && $weekdays['thu'] == '1' && $weekdays['fri'] == '1')
                                                    All days
                                                @else
                                                    @if($weekdays['sat'] == '1')
                                                        Sat
                                                    @endif
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
                                                @endif
                                        </span>
                                        </p>
                                        @if($offer->time_duration != null)
                                            <p>Timing -
                                                <span>
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
                                    </div>
                                    <div>
                                        @php
                                            $date = date("d-m-Y");
                                            //check expiry
                                            $offer_date = $offer->date_duration[0];
                                            if (
                                            new DateTime($offer_date["from"]) <= new DateTime($date) && new DateTime($offer_date["to"]) >= new DateTime($date)
                                            && $offer->active == 1
                                            ) {
                                            echo '<span class="badge badge-success">Available</span>';
                                            } else {
                                            echo '<span class="badge badge-danger">Expired</span>';
                                            }
                                        @endphp
                                        <span class="offer-used-partner">
                                            Used: {{$offer->offer_use_count}} {{$offer->offer_use_count > 1 ? ' times':' time'}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="pp-offer-btn mtb-10">
                                    @if($offer->offer_full_description != null)
                                        <button class="btn btn-primary offerDetails" data-offer-id="{{$offer->id}}"
                                                data-offer-tab="details">Details</button>
                                    @endif
                                    <button class="btn btn-primary offerDetails" data-offer-id="{{$offer->id}}"
                                            data-offer-tab="tnc">T&C</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="offerDetails_{{$offer->id}}" tabindex="-1"
                         role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close"><i class="fa fa-times"></i>
                                    </button>
                                    <h4 class="modal-title">{{ $offer->offer_description }}</h4>
                                </div>
                                <div class="modal-body">
                                    <div role="tabpanel">
                                        <!-- Nav tabs -->
                                        <ul class="nav nav-tabs" role="tablist">
                                            <li role="presentation" class="active">
                                                <a href="#detailsTab{{$offer->id}}"
                                                   aria-controls="detailsTab{{$offer->id}}"
                                                   role="tab" data-toggle="tab">Details</a>
                                            </li>
                                            <li role="presentation">
                                                <a href="#tncTab{{$offer->id}}"
                                                   aria-controls="tncTab{{$offer->id}}" role="tab"
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
            @else
                <p style="padding: 10px 20px;">Sorry, no offers available at the moment, please check back later for current offers.</p>
            @endif
        </div>
    </div>

</div>

@include('partner-dashboard.footer')
<script>
    // script to show individual offer details modal & open specific tab
    $(document).ready(function() {
        $(".offerDetails").click(function() {
            var offer_id = $(this).data("offer-id");
            var offer_tab = $(this).data("offer-tab");
            if (offer_tab === "details") {
                $('a[href^="#tncTab' + offer_id + '"]').parent().removeClass("active");
                $('a[href^="#detailsTab' + offer_id + '"]').parent().addClass("active");
                $("#tncTab" + offer_id).removeClass("active");
                $("#detailsTab" + offer_id).addClass("active");
            } else if (offer_tab === "tnc") {
                $('a[href^="#detailsTab' + offer_id + '"]').parent().removeClass("active");
                $('a[href^="#tncTab' + offer_id + '"]').parent().addClass("active");
                $("#detailsTab" + offer_id).removeClass("active");
                $("#tncTab" + offer_id).addClass("active");
            }
            $("#offerDetails_" + offer_id).modal("show");
        });
    });
</script>