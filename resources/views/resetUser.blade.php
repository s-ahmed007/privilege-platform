<?php
//get last part of url to not show login button in login page
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/', $link);
$dont_redirect_mobile_page = end($link_array);
?>
@include('header')
<section id="hero">
    <div class="container">
        <div class="section-title-hero" data-aos="fade-up">
            <h2>Royalty Account PIN Reset</h2>
        </div>
    </div>
</section>
<div class="container">
    <div class="login-wrap-1 row">
        <div class="reset-box">
            <div class="red">
                <p>** Please note that the following page cannot be retrieved once you leave the page. You will need to click on forgot PIN again from the login page.</p>
            </div>
            <div class="pass_reset mtb-10">
            <p>Reset Your PIN</p>
            <hr>
                <form action="{{url('reset-user/'.$token)}}" onsubmit="return checkFields();" autocomplete="off">
                    <div class="row mtb-10">
                        <div class="col-sm-9">
                            @if ($errors->has('password'))
                                <div class="red">
                                    <ul>
                                        <li>{{ $errors->first('password') }}</li>
                                    </ul>
                                </div>
                            @endif
                            <label class="control-label" for="reset_password">
                            New PIN:
                            </label>
                            <span class="correct_pass"></span><span class="error_pass"></span>
                            <input type="password" id="reset_password" name="password" required
                                   placeholder="Enter new PIN" class="form-control"
                                   pattern="[0-9]{4}" maxlength="4"
                                   title="Must contain only 4 digit number">
                            <span toggle="#reset_password"
                                  class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                        </div>
                        <div class="col-sm-3">
                        </div>
                    </div>                    
{{--                    <div class="row mtb-10">--}}
{{--                        <div class="col-sm-9">--}}
{{--                            <label class="control-label" for="reset_conf_pass">--}}
{{--                        Confirm Password:--}}
{{--                            </label>--}}
{{--                            <span class="error_conf_pass" style="color: red"></span><span style="color: green" class="correct_conf_pass"></span>--}}
{{--                            <input type="password" id="reset_conf_pass" name="confirm_pass" required--}}
{{--                                   placeholder="Enter new password again" class="form-control">--}}
{{--                            <span toggle="#reset_conf_pass"--}}
{{--                                  class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-3">                            --}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <input type="hidden" name="token" id="token" value="{{ $token }}">
                    <input type="submit" class="btn btn-success" value="Submit" style="margin: unset;">
                </form>
            </div>
        </div>
    </div>
</div>
@include('footer')

{{--reset form validation--}}
<script>
    //validate pin
    function validatePassword() {
        var pin = $("#reset_password").val();
        if(isNaN(pin)){
            $(".error_pass").html('Only number is allowed');
            $(".correct_pass").empty();
        }else if (pin.length > 4 || pin.length < 4) {
            $(".error_pass").html('Please insert a 4 DIGIT PIN');
            $(".correct_pass").empty();
            return false;
        }else if(pin==null || pin==""){
            $(".error_pass").html('Please insert your PIN');
            $(".correct_pass").empty();
            return false;
        }else {
            $(".correct_pass").html('&#10004;');
            $(".error_pass").empty();
        }
        return true;
    }

    $(document).ready(function () {
        // validate password
        $('#reset_password').keyup(function () {
            validatePassword();
        });
        //key up ends
        //==========================mouse up================================================
        // validate password
        $('#reset_password').mouseup(function () {
            validatePassword();
        });
    });

    //check some fields onsubmit
    function checkFields() {
        if (validatePassword() == true) {
        } else {
            return false;
        }
        return true;
    }
</script>