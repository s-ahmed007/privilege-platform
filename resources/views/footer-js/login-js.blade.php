<script>
    function verifyOTP() {
        $(".loading-gif").css('display', 'inline-block');
        var phone = $("#phone_number").val();
        var code = $("#phone_verifying_code").val();
        if (code === ''){
            toastr.error('Please provide your verification code.');
            $(".loading-gif").css('display', 'none');
        }
        var user_type = $("#user_type").val();
        var verification_type = '{{\App\Http\Controllers\Enum\VerificationType::phone_verification}}';
        var url = "{{ url('/check_code_phone') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'code': code, 'phone': phone, 'type': verification_type},
            success: function (response) {
                var verify_msg = $(".phone_verify_status");
                var stat_id = localStorage.getItem('otp_stat_phone_id');
                if(response.status){
                    // updateStatNumber(stat_id, 1, '+880'+phone);
                    if(user_type == 'new_user'){
                        toastr.success(response.message);
                        // verify_msg.text(response.message).css({'color': 'green', 'text-align': 'center'});
                        localStorage.setItem('signup_phone', phone);
                        window.location.href = '{{url("/signup")}}';
                    }else if(user_type == 'existing_user'){
                        directLogin(phone);
                    }
                }else{
                    toastr.error(response.message);
                    // verify_msg.text(response.message).css({'color': 'red', 'text-align': 'center'});
                    // updateStatNumber(stat_id, 2);
                }
                $(".loading-gif").css('display', 'none');
            }
        });
    }

    //login pass user after validate phone
    function directLogin(phone) {
        var url = "{{ url('/direct-login') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'phone':phone},
            success: function (data) {
                window.location = data;
            }
        });
    }

    function insertAccKitStat(phone) {
        var url = "{{ url('/setStatNumber') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'phone': phone},
            success: function (response) {
                localStorage.setItem('otp_stat_phone_id', response['id']);
            }
        });
    }

    function checkPhone() {
        var phone = $("#phone_number").val();
        if(phone === ''){
            toastr.error('Please provide a valid phone number.');
            return false;
        }
        var url = "{{ url('/checkPhoneNumber') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'phone': phone},
            error: function(xhr) {
                $("#user_deactive_text").children('p').text(xhr.responseJSON.msg);
                $("#deactive_user_modal").modal('show');
            },
            success: function (data) {
                if(data['customer'] === 'invalid'){
                    //insert into stats table
                    // insertAccKitStat(phone);
                    //proceed to sign-up
                    $("#user_type").val('new_user');
                    $(".verify_phone").text(data.phone);
                    toastr.success(data['message']);
                    // $(".phone_verify_msg").text(data['message']).css('text-align', 'center');
                    $("#verification_code_modal").modal('show');
                }else{
                    //user can login
                    //verify with otp & direct login
                    $("#user_type").val('existing_user');
                    toastr.success(data['message']);
                    // $(".phone_verify_msg").text(data['message']).css('text-align', 'center');
                    $(".verify_phone").text(data.customer);
                    $("#verification_code_modal").modal('show');
                    // if(data.pin === 0){
                    //     // insertAccKitStat(phone);
                    //     //verify with otp & direct login
                    //     $("#user_type").val('pass_user');
                    //     $(".phone_verify_msg").text(data['message']).css('text-align', 'center');
                    //     $("#verification_code_modal").modal('show');
                    // }else{
                    //     var user_pin = $(".user-pin");
                    //     $("#phone_number").attr('disabled', true);
                    //     user_pin.css('display', 'block');
                    //     var loginFunction = "doLogin('"+data.customer+"')";
                    //     $(".signinbtn").attr("onclick",loginFunction).val('Login');
                    //
                    //     user_pin.children('label').html('Enter PIN');
                    //     $("#type").val(2);
                    //     $(".forgot-pass").attr("onclick","resetPin()").text('Forgot PIN?');
                    //     $("#pinPass").focus();
                    // }
                }
            }
        });
    }

    function doLogin(phone) {
        var pinPass = $("#pinPass").val();
        var type = $("#type").val();
        var url = "{{ url('/checkPinPass') }}";
        var physical_address = localStorage.getItem('physical_address_for_login_session');
        $.ajax({
            type: "POST",
            url: url,
            async: false,
            data: {'_token': '<?php echo csrf_token(); ?>',
                'customer_phone': phone,
                'pinPass': pinPass,
                'type': type,
                'physical_address': physical_address
            },
            success: function (data) {
                if(data['status'] === 1){//redirect ot user profile
                    localStorage.setItem('physical_address_for_login_session', data['physical_address']);
                    window.location = data['text'];

                    //MIX PANEL
                    mixpanel.identify(data['phone_number']);
                    mixpanel.track(
                        "Logged In",
                        {
                            "Platform": "Website"
                        }
                    );
                }else{//wrong credential
                    $(".wrong_pin").html(data['text']);
                }
            }
        });
    }

    function updateStatNumber(id, status, phone=null) {
        var url = "{{ url('/updateStatNumber') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'id': id, 'status': status, 'phone': phone},
            success: function (data) {
                //acc_kit_stat updated
            }
        });
    }
    //set phone to local storage to use on reset pin page
    function resetPin(){
        var phone = $("#phone_number").val();
        localStorage.setItem('reset_phone', phone);
        var base_url = window.location.origin;
        window.location = base_url+'/reset_pin/send-sms';
    }

    //trigger checkPhone() function
    var phone = document.getElementById("phone_number");
    var pin = document.getElementById("pinPass");
    phone.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("signinbtn").click();
        }
    });
    pin.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("signinbtn").click();
        }
    });
    //trigger checkPhone() function ends
</script>
