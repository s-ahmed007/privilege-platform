<!DOCTYPE html>
<html lang="zxx" class="no-js">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/donation/fav.png">
    <meta name="keywords" content="">
    <meta charset="UTF-8">
    <title>FBD Payment Form | royaltybd.com</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,400,300,500,600,700" rel="stylesheet">
    <script src="https://kit.fontawesome.com/9e60b11f48.js" crossorigin="anonymous" defer></script>
    <link href="{{asset('css/donate-css/nice-select.css')}}" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    <link href="{{asset('css/donate-css/bootstrap.css')}}" rel="stylesheet">
    <link href="{{asset('css/donate-css/mainv1.css')}}" rel="stylesheet">
</head>

<body>
   <section>
      <div class="container">
         <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
               <div class="donate-form-h-div">
                  <a href="{{ url('/donate') }}">
                  <span class="go-back">
                  Go back
                  </span>
                  </a>
                  <h2 class="donate-form-h">
                  Donate with Feeding Bangladesh
                  <h2>
                      <p style="font-size:15px">-An initiative of Royalty</p>
               </div>
               <form class="form-control form-box" action="{{url('save_donation')}}" onsubmit="return checkFields();" method="post">
                  <h4>Payment details</h4>
                  <br>
                  <p>All the details you provide are safe with us. You can donate using international cards too.</p>
                  {{csrf_field()}}
                  <h3 id="payable_amount1"></h3>
                  <br>
                  <div class="form-group" id="payable_amount2">
                     <label for="amount">Amount:</label>
                     <label for="amount" style="font-size: 0.7em;">Minimum is 10Tk</label>
                     <span class="correct_amount"></span>
                     <span class="error_amount"></span>
                     <input type="text" class="form-control" name="amount" id="amount">
                  </div>
                  <div>
                     Provide us the following details-
                  </div>
                  <br>
                  <div class="form-group">
                     <label for="name">Your Name:*</label>
                     <span class="correct_name"></span>
                     <span class="error_name"></span>
                     <label for="name" style="font-size: 0.7em;">Write Anonymous if you would like not to submit your name</label>
                     <input type="text" class="form-control" name="name" id="name">
                  </div>
                  <div class="form-group">
                     <label for="phone">Your Phone Number:*</label>
                     <span class="correct_phone"></span>
                     <span class="error_phone"></span>
                     <input type="text" class="form-control" name="phone" id="phone">
                  </div>
                  <div class="form-group">
                     <label for="email">Your Email Address:*</label>
                     <span class="correct_email"></span>
                     <span class="error_email"></span>
                     <input type="email" class="form-control" name="email" id="email">
                  </div>
                  <button type="submit" class="btn btn-success form-control">PAY NOW</button>
               </form>
            </div>
         </div>
      </div>
   </section>

    <script src="{{asset('js/donate-js/vendor/jquery-2.2.4.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="{{asset('js/donate-js/vendor/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/donate-js/jquery.ajaxchimp.min.js')}}"></script>
    <script src="{{asset('js/donate-js/jquery.nice-select.min.js')}}"></script>
    <script src="{{asset('js/donate-js/jquery.sticky.js')}}"></script>
    <script src="{{asset('js/donate-js/parallax.min.js')}}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{asset('js/donate-js/main.js')}}"></script>

    <script type="text/javascript">
      if(localStorage.getItem("donation_amount") !== null){
        var amount = localStorage.getItem('donation_amount');
        if (amount != 0) {
            $("#amount").val(amount);
            $("#payable_amount1").text("৳"+amount+"/-");
            $("#payable_amount2").hide();
        }else{
            $("#payable_amount1").hide();
            $("#payable_amount2").show();
        }
      }else{
        $("#payable_amount1").hide();
        $("#payable_amount2").show();
      }
       localStorage.removeItem('donation_amount');

    function validateAmount() {
        var amount = $("#amount").val();
        if (!amount) {
            $(".error_amount").html('Please enter valid amount');
            $(".correct_amount").empty();
            return false;
        }else if (isNaN(amount)) {
            $(".error_amount").html('Please enter valid amount');
            $(".correct_amount").empty();
            return false;
        }else if (amount < 10) {
            $(".error_amount").html('Minimum payable amount is 10');
            $(".correct_amount").empty();
            return false;
        }else{
            $(".correct_amount").html('&#10004;');
            $(".error_amount").empty();
        }
        return true;
    }

    function validateName() {
        var name = $("#name").val();
        var reg = /^[A-Za-z .]+$/;

        if(name === ''){
            $(".correct_name").empty();
            $(".error_name").html('Please enter your name');
            return false;
        }else if(!reg.test(name)) {
            $(".correct_name").empty();
            $(".error_name").html("It looks like you've entered a mobile number or an email address. Please enter your name.");
            return false;
        }else if(name.length < 3) {
            $(".correct_name").empty();
            $(".error_name").html("Name too small");
            return false;
        } else {
            $(".error_name").empty();
            $(".correct_name").html('&#10004;');
        }
        return true;
    }

    function validatePhone() {
        var phone = $("#phone").val();
        if (phone.length === 0) {
          $(".correct_phone").empty();
            $(".error_phone").text('Please enter a valid phone number');
            return false;
          }else{
            $(".error_phone").empty();
            $(".correct_phone").html('&#10004;');
          }
          return true;
        // var filter = /^(?:\+88|01)?(?:\d{11}|\d{13})$/;
        // if (!filter.test(phone)) {
        //     $(".correct_phone").empty();
        //     $(".error_phone").text('Please enter a valid phone number');
        //     return false;
        // }else{
        //     $(".error_phone").empty();
        //     $(".correct_phone").html('&#10004;');
        // }
        // return true
    }

    function validateEmail() {
        var email = $("#email").val();
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!filter.test(email)) {
            $(".correct_email").empty();
            $(".error_email").text('Please enter a valid E-mail address');
            return false;
        }else{
            $(".error_email").empty();
            $(".correct_email").html('&#10004;');
        }
        return true;
    }

    //call amount validate function
    $('#amount').keyup(function () {
        validateAmount();
    });
    $('#amount').mouseup(function () {
        validateAmount();
    });

    //call name validate function
    $('#name').keyup(function () {
        validateName();
    });
    $('#name').mouseup(function () {
        validateName();
    });

    //call phone validate function
    $('#phone').keyup(function () {
        validatePhone();
    });
    $('#phone').mouseup(function () {
        validatePhone();
    });

    //call email validate function
    $('#email').keyup(function () {
        validateEmail();
    });
    $('#email').mouseup(function () {
        validateEmail();
    });

    function checkFields() {
        if(!validateAmount()){
            return false;
        }else if(! validateName()){
            return false;
        }else if(!validatePhone()){
            return false;
        }else if(!validateEmail()){
            return false;
        }else{
            return true;
        }
    }
    </script>
</body>

</html>