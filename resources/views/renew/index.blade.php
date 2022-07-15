@include('header')
<!-- HTTPS required. HTTP will give a 403 forbidden response -->
<script src="https://sdk.accountkit.com/en_US/sdk.js"></script>
<?php $validation_page = 0;?> @if ($errors->getBag('default')->first('first') || $errors->getBag('default')->first('last'))
    <?php $validation_page = 1;?> @endif
    <section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2> @if($card_delivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::virtual_card && $exp_status == 'active')
            Upgrade Membership
            @else
            Renew Membership
            @endif
         </h2>
         <!-- <p>Edit Profile</p> -->
      </div>
   </div>
</section>
<section>
   <div class="container buy-card-background">
         <div class="whitebox">
            <div class="whitebox-inner-box" style="display: unset;">
               <div class="row">
                  <div class="col-md-12 col-sm-12 col-xs-12">
                     @if(session('error'))
                     <div style="color:red;">{{ session('error') }}</div>
                     @endif
                  </div>
               </div>
                  <form class="form-vertical cus_reg_form" action="{{ url('/confirm_renew') }}" method="post" onsubmit="return checkFields();">
                  <div class="row m-0">
                     <div class="col-md-6 col-sm-12 col-xs-12">
                           <div>
                              <p><b>Choose your membership period:</b></p>
                              @if (session('own_refer_code'))
                              <span style="visibility: hidden;">{{ session('own_refer_code') }}</span>
                              @endif
                              <span class="plan_error"></span>
                              <span style="color: #E74430;">
                              @if ($errors->getBag('default')->first('card_duration'))
                              {{ "Please choose your plan" }}
                              @endif
                              </span>
                              <?php $i=0; ?>
                              @foreach($cards as $card)
                              <input type="radio" id="registration_soflow" name="card_duration" {{$i == 0 ? "checked":''}}
                              value="{{$card->month.'-'.$card->price}}" onchange="changeSelectedValue()">
                              {{$card->month}}{{$card->month > 1 ? ' Months':' Month'}}(৳{{$card->price}})<br>
                              <?php $i++; ?>
                              @endforeach
                           </div>
                           <br>
                        <!-- Promo Code -->
                              <div class="promo-code-box">
                                 <p class="promo-head"><b>Promo Code (Optional)</b></p>
                                 <div>
                                    @if ($errors->has('card_promo'))
                                    <div style="color: red;">
                                       <ul id="card_promo_error">
                                          <li>{{ $errors->first('card_promo') }}</li>
                                       </ul>
                                    </div>
                                    @elseif (session('card_promo'))
                                    <span style="color: red">{{ session('card_promo') }}</span> @endif
                                    <div class="form-group">
                                       <input type="text" name="card_promo" placeholder="Enter Code" class="form-control" id="card_promo">
                                    </div>
                                 </div>
                                 <div>
                                    <span class="correct_card_promo" id="correct_card_promo"></span>
                                 </div>
                                 <div>
                                    <span class="error_card_promo" id="error_card_promo"></span>
                                 </div>
                              </div>
                        @if($card_delivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::virtual_card && $exp_status != 'expired')
                        <p style="color: grey;">You have selected one of our premium membership subscriptions. If you are a trial member,
                           your trial will end immediately.
                        </p>
                        <br>
                        @endif
                        @if($exp_status != 'active')
                        <input type="hidden" value="{{\App\Http\Controllers\Enum\PromoType::RENEW}}" id="mem_type">
                        @else
                        @if($card_delivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::virtual_card)
                        <input type="hidden" value="{{\App\Http\Controllers\Enum\PromoType::UPGRADE}}" id="mem_type">
                        @else
                        <input type="hidden" value="{{\App\Http\Controllers\Enum\PromoType::RENEW}}" id="mem_type">
                        @endif
                        @endif
                        <!-- <p class="payment-warning">*Only available for online payment.</p> -->
                     </div>
                     <div class="col-md-offset-2 col-md-4 col-sm-12 col-xs-12">
                        <p class="promo-head">
                           <b>Bill Summary:</b>
                        </p>                    
                        <div class="row bill-box m-0 border">
                           <div class="card-bill-price-l col-xs-8">Premium Membership Price</div>
                           <div class="card-bill-price col-xs-4"></div>
                           <div class="card-bill-promo-l col-xs-8">Promo Code Discount</div>
                           <div class="card-bill-promo col-xs-4"></div>
                        </div>
                        <div class="card-full-final-bill row m-0">
                           <div class="card-bill-final-l col-xs-8" style="border: unset">Final Bill</div>
                           <div class="card-bill-final col-xs-4"></div>
                        </div>
                     </div>
                  </div>
                  <input type="hidden" name="_token" value="{{csrf_token()}}">
                     <div class="row m-0">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                           <button type="submit" class="btn btn-primary pull-right mr-0" name="submit">Continue to payment
                           <i class="fas fa-arrow-circle-right"></i>
                           </button>
                        </div>
                     </div>
                <div>
                     @if($exp_status != 'active')
                     <input type="hidden" class="delivery_type" name="delivery_type" value="{{\App\Http\Controllers\Enum\DeliveryType::renew}}">
                     @else
                     @if($card_delivery->delivery_type == \App\Http\Controllers\Enum\DeliveryType::virtual_card)
                     <input type="hidden" class="delivery_type" name="delivery_type" value="{{\App\Http\Controllers\Enum\DeliveryType::home_delivery}}">
                     @else
                     <input type="hidden" class="delivery_type" name="delivery_type" value="{{\App\Http\Controllers\Enum\DeliveryType::renew}}">
                     @endif
                     @endif
                     <input type="hidden" class="promotion-discount" name="promotion-discount" value="not_set">
                     <input type="hidden" class="customized_price" name="customized_price" value="not_set">
                     </div>
                  </form>
         
            </div>
         </div>
   </div>
</section>
@include('footer')
<script>
    // changeSelectedValue();
    billSummery();


    $('#card_promo').keyup(function () {
        checkPromo();
    });

    function billSummery() {
        var card = $('input[name=card_duration]:checked').val();
        var delivery_type = $('.delivery_type').val();

        if(card === undefined){
            card_price = 0;
            var final_price = 0;
        }else{
            var card_price = card.split('-');
            card_price = card_price[1];
            if(delivery_type == 11){//check if virtual card holder or not
                {{--//delivery charge--}}
                var final_price = Number(card_price);
            }else{
                final_price = Number(card_price);
            }
        }
        {{--//add promo discount--}}
        var promo_discount = $(".promotion-discount").val();
        if (promo_discount !== 'not_set') {
            $(".card-bill-promo-l").show();
            $(".card-bill-promo").show().html('-৳' + promo_discount);
            final_price -= Number(promo_discount);
        } else {
            $(".card-bill-promo-l").hide();
            $(".card-bill-promo").hide();
        }
        $(".card-bill-price").html("৳" + card_price);
        if(card === null) {
            $(".card-bill-shipping").html("৳0");
        }else{
            $(".card-bill-shipping").html("৳{{$amounts[3]['price']}}");
        }
        $(".card-bill-final").html("৳" + final_price);
    }

    function checkPromo() {
        $(".promotion-discount").val('not_set');
        billSummery();
        var promo = document.getElementById("card_promo").value;
        var card = $('input[name=card_duration]:checked').val();
        var mem_type = $("#mem_type").val();
        if(card === undefined){
            $(".plan_error").html('Please choose your plan');
            card_price = 0;
        }else{
            var card_month_price = card.split('-');
            card_price = Number(card_month_price[1]);

            if (promo !== '') {
                var url = "{{ url('/checkCardPromoCode') }}";
                return $.ajax({
                    type: "POST",
                    url: url,
                    data: {'_token': '<?php echo csrf_token(); ?>',
                        'card_promo': promo,
                        'card_price': card_price,
                        'month': Number(card_month_price[0]),
                        'mem_type': mem_type},
                    statusCode: {
                        200: function(data) {
                            $(".promotion-discount").val(data.promo_price);
                            $(".correct_card_promo").html('&#10004;');
                            $(".error_card_promo").empty();
                            $("#card_promo_error").empty();
                            billSummery();
                        },
                        201: function(data) {
                            $(".error_card_promo").html(data.result);
                            $(".correct_card_promo").empty();
                            $("#card_promo_error").empty();
                            return false;
                        }
                    }
                });
            } else {
                $(".error_card_promo").empty();
                $(".correct_card_promo").empty();
                $("#card_promo_error").empty();
            }
        }
    }

    function checkFields(){
        //check plan
        var card = $('input[name=card_duration]:checked').val();
        if(card === undefined){
            $(".plan_error").html('Please choose your plan');
            return false;
        }
        //check card promo code
        var error_card_promo = document.getElementById("error_card_promo").innerText;
        if (error_card_promo !== ''){
            $(".error_card_promo").html('Please enter a valid promo code or delete invalid code.');
            return false;
        }

        return true;
    }

    window.onbeforeunload = function () {
        $('#card_promo').val('');
        billSummery();
    };

    {{--js to change type & duration on select--}}
    function changeSelectedValue() {
        $(".plan_error").empty();
        checkPromo();
        billSummery();
    }
</script>