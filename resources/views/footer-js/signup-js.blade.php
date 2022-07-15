<script>
    //check if user directly trying to access signup page without validating phone
    if (localStorage.getItem("signup_phone") === null) {
        window.location = "{{ url('/login') }}";
    }
    /*FORM VALIDATION*/
    function validatePhone() {
        var field_phone = $('#phone_number').val();
        var localstorage_phone = localStorage.getItem('signup_phone');
        if(field_phone === '' || field_phone !== localstorage_phone){
            $(".error_phone").text('Please enter the validated phone');
            $(".correct_phone").empty();
            return false;
        }else{
            $(".error_phone").empty();
            $(".correct_phone").html('&#10004;');
        }
        return true
    }

    function checkEmail(data) {
        if (data === 0) {
            $(".error_email").empty();
            $(".correct_email").html('&#10004;');
        } else if (data === 1) {
            $(".correct_email").empty();
            $(".error_email").text('Email already exists');
            return false;
        } else {
            $(".correct_email").empty();
            $(".error_email").text('Please enter a valid E-mail');
            return false;
        }
        return true;
    }

    function validateEmail() {
        var email = $("#signup_email").val();
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!filter.test(email)) {
            return checkEmail(2);
        } else {
            var url = "{{ url('/checkPreEmail') }}";
            return $.ajax({
                type: "POST",
                url: url,
                data: {'_token': '<?php echo csrf_token(); ?>', 'email': email},
                success: function (data) {
                    return checkEmail(data);
                }
            });
        }
    }

    function validateName() {
        var full_name = $("#full_name").val();
        var reg = /^[A-Za-z .]+$/;

        if(full_name === ''){
            $(".correct_name").empty();
            $(".error_name").html('Please enter your name');
            return false;
        }else if(!reg.test(full_name)) {
            $(".correct_name").empty();
            $(".error_name").html("It looks like you've entered a mobile number or email address. Please enter your name.");
            return false;
        }else if(full_name.length < 3) {
            $(".correct_name").empty();
            $(".error_name").html("Name too small");
            return false;
        } else {
            $(".error_name").empty();
            $(".correct_name").html('&#10004;');
        }
        return true;
    }

    function validatePin(){
        var pin = $('#pin').val();
        if(pin==null || pin==""){
            $(".correct_pin").empty();
            $(".error_pin").text('Please set a 4-DIGIT PIN');
            return false;
        }else if(isNaN(pin)){
            $(".correct_pin").empty();
            $(".error_pin").text('Only number is allowed');
            return false;
        }else if(pin.length > 4 || pin.length < 4){
            $(".correct_pin").empty();
            $(".error_pin").text('Please set a 4-DIGIT PIN');
            return false;
        }else if(pin.length === 4){
            $(".error_pin").empty();
            $(".correct_pin").html('&#10004;');
            return true;
        }
    }

    function validateRefer() {
        var refer_code = $('#refer_code').val();
        var return_val = false;
        if(refer_code == ''){
            $(".error_reg_refer").empty();
            $(".correct_reg_refer").empty();
            return true;
        }else{
            if(refer_code.length !== 5){
                $(".correct_reg_refer").empty();
                $(".error_reg_refer").text('Please enter a valid refer code');
                return false;
            }
        }
        var url = "{{ url('/checkRegReferCode') }}";
        $.ajax({
            type: "POST",
            url: url,
            async: false,
            data: {'_token': '<?php echo csrf_token(); ?>', 'refer_code': refer_code},
            success: function (data) {
                if(data){
                    $(".error_reg_refer").empty();
                    $(".correct_reg_refer").html('&#10004;');
                    return_val = true;
                }else{
                    $(".correct_reg_refer").empty();
                    $(".error_reg_refer").text('Please enter a valid refer code');
                    return_val = false;
                }
            }
        });
        return return_val;
    }

    //call email validate function
    var signup_email = $('#signup_email');
    signup_email.keyup(function () {
        validateEmail();
    });
    signup_email.mouseup(function () {
        validateEmail();
    });
    //call name validate function
    var full_name = $('#full_name');
    full_name.keyup(function () {
        validateName();
    });
    full_name.mouseup(function () {
        validateName();
    });
    //call pin validate function
    var pin = $('#pin');
    pin.keyup(function () {
        validatePin();
    });
    pin.mouseup(function () {
        validatePin();
    });
    //call refer validate function
    var refer_code = $('#refer_code');
    refer_code.keyup(function () {
        validateRefer();
    });
    refer_code.mouseup(function () {
        validateRefer();
    });

    function checkFields() {
        if(!validatePhone()){
            return false;
        }else if(!validateName()){
            return false;
        }else if(!validateEmail()){
            return false;
        }else if(!validateRefer()){
            return false;
        }else{
            return true;
        }
        // else if(!validatePin()){
        //     return false;
        // }
    }

    /*FORM VALIDATION ENDS*/

    //set value of phone number from local storage
    $("#phone_number").val(localStorage.getItem('signup_phone')).prop("readonly", true);
    $("#phone_number2").val(localStorage.getItem('signup_phone'));
    $('.signup-form .mat-div').first().addClass('is-completed');

    //handle prev url
    <?php if (!empty(session('error')['prev_url'])) { ?>
        document.getElementById('prev_url').value = '{{ session('error')['prev_url'] }}';
        <?php
    } else {
        ?>
        document.getElementById('prev_url').value = localStorage.getItem("prev_url_at_login") != null ?
            localStorage.getItem('prev_url_at_login') : '';
    <?php } ?>

    //remove local storage value after use
    localStorage.removeItem("prev_url_at_login");

</script>