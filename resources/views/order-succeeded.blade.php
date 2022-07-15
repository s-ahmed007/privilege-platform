<?php
//To Disable Cache Load if Browser Back Button Pressed
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order-Success</title>
    <link rel="stylesheet" href="{{asset('css/fail-success.css')}}">
    <meta name="theme-color" content="#007bff">
    <link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo-user.png">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <div class="content-body">
        <div class="image-body">
            <div class="row">
                <div class="col-md-3 col-sm-2 col-xs-12">
                </div>
                <div class="col-md-6 col-sm-8 col-xs-12">
                    <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/payment/payment-success.png" class="logo-img lazyload" Royalty Logo">
                </div>
                <div class="col-md-3 col-sm-2 col-xs-12">
                </div>
            </div>
        </div>
        <div class="text-body">
            <h1 class="heading">Congratulations!</h1>
            <p class="para">Your order is successfully placed.<br>We have accepted your request. You will receive a call from our team within 48 hours.
                @if($show_username == 1)
                   Your username is <span style="font-weight: bold">{{$username->customer_username}}</span>. You will receive an e-mail, once your payment is confirmed.<br>
                @endif
            </p><br>
            <p>In case of any support, please contact us at 
                <a href="mailto:support@royaltybd.com">
                     support@royaltybd.com
                     </a> or call us at
                +880-963-862-0202.
            </p>
        </div>
        <br>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align: center">
                @if(isset($username))
                    <a href="{{url('users/'.$username->customer_username)}}">
                        <p class="btn btn-primary">Go to your Account</p>
                    </a>
                @else
                    <a href="{{url('login')}}">
                        <p class="btn btn-primary">Login</p>
                    </a>
                @endif
            </div>
            <div class="col-md-4"></div>
        </div>
        <br>
    </div>
</div>
</body>
</html>