<script>
    $("#spot_continue_customer").val({{session('customer_id')}});
    function openInvitationField(element) {
        $("#trial_promo_code").css('display', 'block');
        $(element).css('display', 'none');
        return false;
    }
	function formatDate(date) {
	  var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];

	  var day = date.getDate();
	  var monthIndex = date.getMonth();
	  var year = date.getFullYear();

	  return monthNames[monthIndex] + ' ' + day + ', ' + year;
	}

	function  upgradeSubscription(month, fee, elem) {
        $("#trial_promo_code").css('display', 'none');
        $(".invite_code_link").css('display', 'block');
		$(".subscription_price").val(fee);
		$(".subscription").val(month);

		if(month !== 'trial'){
            var mon_txt = month > 1 ? ' months':' month';
            $("#selected_duration").html(month+mon_txt);
            $("#spot_continue_month").val(month);
            checkPromo();
        	billSummery();
            $(".pricing .btn-buy").removeClass('active_price_btn');
            $(elem).children().addClass('active_price_btn');
            var target = $('#payment_box');
        	target.css('display', 'block');
            $('html, body').stop().animate({
              scrollTop: target.offset().top - 110
            }, 1000);
		}else{
			$(".buy-card-background").css('display', 'none');
			var CurrentDate = new Date();
			@if($trial)
			CurrentDate.setMonth(CurrentDate.getMonth() + {{$trial->month}});
			@endif
			$(".exp_date").text(formatDate(CurrentDate));
			$("#1MonthTrialModal").modal('show');
		}
	}

   $('#card_promo').keyup(function () {
        checkPromo();
   });

    function billSummery() {
        var card = $('.subscription').val();
        if(card === ''){
            card_price = 0;
            var final_price = 0;
        }else{
	        var card_price = $(".subscription_price").val();
	        {{--//delivery charge--}}
	        var final_price = Number(card_price);
        }

        var promo_discount = $(".promotion-discount").val();
        if (promo_discount !== 'not_set') {
            $(".card-bill-promo-l").show();
            $(".card-bill-promo").show().html('-৳' + promo_discount);
            final_price -= Number(promo_discount);
        } else {
            $(".card-bill-promo-l").hide();
            $(".card-bill-promo").hide();
        }
        var customized = $('#customize').is(":checked");
        if (customized === true) {
            final_price += {{$prices[4]['price']}};
            $(".card-bill-customize-l").show();
            $(".card-bill-customize").show().html("৳{{$prices[4]['price']}}");
        } else {
            $(".card-bill-customize-l").hide();
            $(".card-bill-customize").hide();
        }
        $(".card-bill-price").html("৳" + card_price);
        if(card === null) {
            $(".card-bill-shipping").html("৳0");
        }else{
            $(".card-bill-shipping").html("৳{{$prices[3]['price']}}");
        }
        $(".card-bill-final").html("৳" + final_price);
        $("#spot_continue_price").val(final_price);//for spot sale
    }

    function checkPromo() {
		$(".promotion-discount").val('not_set');
		billSummery();
		var promo = document.getElementById("card_promo").value;
		var month = $('.subscription').val();
        var mem_type = $("#mem_type").val();
		if(month === ''){
		   $(".plan_error").html('Please choose your plan');
		   card_price = 0;
		}else{
			var card_price = $(".subscription_price").val();
		   if (promo !== '') {
		       var url = "{{ url('/checkCardPromoCode') }}";
		       return $.ajax({
		           type: "POST",
		           url: url,
                   async: true,
		           data: {'_token': '<?php echo csrf_token(); ?>',
                       'card_promo': promo,
                       'card_price': card_price,
                       'mem_type': mem_type,
                       'month': month,
                       'renew': null},
                   statusCode: {
                       200: function(data) {
                           $(".promotion-discount").val(data.promo_price);
                           $(".correct_card_promo").html('&#10004;');
                           $(".error_card_promo").empty();
                           $("#card_promo_error").empty();
                           $("#spot_continue_promo").val(data.promo_id);
                           billSummery();
                           if (data.seller){
                               var _function = 'return spotPurchaseSellerOTP("'+data.seller.account.phone+'")';
                               $("#spot_purchase_btn").attr('onclick', _function).css({'cursor': 'pointer', 'background-color': '#007bff',
                               'pointer-events': 'unset'});
                               $("#resendSpotOTP").attr('onclick', _function);
                               $(".verify_button").attr('onclick', "verifyOTP('"+data.seller.account.phone+"')");
                               $("#spot_continue_agent").val(data.seller.account.id);
                           } else{
                               $("#spot_purchase_btn").prop('disabled', true).removeAttr('onclick', 'return spotPurchaseSellerOTP()')
                                   .css({'cursor': 'default', 'background-color': 'gray', 'pointer-events': 'none'});
                           }
                       },
                       201: function(data) {
                           $(".error_card_promo").html(data.result);
                           $(".correct_card_promo").empty();
                           $("#card_promo_error").empty();
                           $("#spot_purchase_btn").prop('disabled', true).removeAttr('onclick', 'return spotPurchaseSellerOTP()')
                               .css({'cursor': 'default', 'background-color': 'gray', 'pointer-events': 'none'});
                           return false;
                       }
                   }
		       });
		   } else {
               $("#spot_purchase_btn").prop('disabled', true).removeAttr('onclick', 'return spotPurchaseSellerOTP()')
                   .css({'cursor': 'default', 'background-color': 'gray', 'pointer-events': 'none'});
               $(".error_card_promo").empty();
               $(".correct_card_promo").empty();
               $("#card_promo_error").empty();
		   }
		}
	}

    function checkFields(){
       //check plan
	   var month = $('.subscription').val();
       if(month === ''){
           $(".plan_error").html('Please choose your plan');
           return false;
       }
       //check card promo code
       var error_card_promo = document.getElementById("error_card_promo").innerText;
       if (error_card_promo !== ''){
           $(".error_card_promo").html('Please enter a valid promo code or skip');
           return false;
       }
       return true;
   }

   window.onbeforeunload = function () {
       $('#card_promo').val('');
       billSummery();
   }

    function checkFieldValue() {
        $(".correct_trial_promo").empty();
        $(".error_trial_promo").empty();
        return false;
    }

   function checkTrialPromo() {
       var promo = $("#trial_promo_code").val();
       var url = '{{url("checkCardPromoCode")}}';
       var trial_url = "{{url('/activate_trial/')}}";

       if(!promo){
           $(".correct_trial_promo").empty();
           $(".error_trial_promo").empty();
           window.location.href = trial_url;
           return false;
       }

       $.ajax({
           type: "POST",
           url: url,
           async: true,
           data: {'_token': '<?php echo csrf_token(); ?>', 'card_promo': promo, 'card_price': null, 'renew': null},
           success: function (data) {
               if (data['error'] === 0) {
                   $(".correct_trial_promo").html('&#10004;');
                   $(".error_trial_promo").empty();
                   trial_url = "{{url('/activate_trial/')}}" + "/" + data['code']['id'];
                   window.location.href = trial_url;
               } else {
                   $(".error_trial_promo").html(data['message']);
                   $(".correct_trial_promo").empty();
                   return false;
               }
           }
       });
   }

   function spotPurchaseSellerOTP(seller_phone) {
       var url = "{{ url('/sendSpotPurchaseSellerOTP') }}";
       return $.ajax({
           type: "POST",
           url: url,
           async: true,
           data: {'_token': '<?php echo csrf_token(); ?>', 'phone': seller_phone},
           statusCode: {
               200: function (data) {
                   toastr.success(data.result.message);
                   $("#OTPSentModal").modal('show');
               },
               201: function (data) {
                   toastr.error(data.result.message);
                   $("#OTPSentModal").modal('show');
                   return false;
               }
           }
       });
   }
   
    function verifyOTP(phone) {
        $(".loading-gif").css('display', 'inline-block');
        var code = $("#phone_verifying_code").val();
        var verification_type = '{{\App\Http\Controllers\Enum\VerificationType::spot_purchase}}';
        var url = "{{ url('/check_code_phone') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'code': code, 'phone': phone, 'type': verification_type},
            success: function (response) {
                if(response.status){
                    toastr.success(response.message);
                    $("#OTPSentModal").modal('hide');
                    $(".spot_final_price").html($(".card-bill-final ").text());
                    $(".spot_card_price").html($(".card-bill-price ").text());
                    $(".spot_promo_discount").html($(".card-bill-promo").text());
                    $("#spotFinalPriceModal").modal('show');
                }else{
                    toastr.error(response.message);
                }
                $(".loading-gif").css('display', 'none');
            }
        });
    }
</script>