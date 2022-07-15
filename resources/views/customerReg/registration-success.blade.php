@include('header')
<div class="container">
    <div class="row text-center">
        <div class="col-sm-6 col-sm-offset-3">
            <h2>Registration Successful!</h2>
            <h3>Thank You!</h3>
            <p style="font-size:17px;color:#5C5C5C;">
                Your registration process has been completed successfully.
                Please login to continue.</p>
            <br>
            <a href="{{url('login')}}" class="btn btn-primary" style="margin-bottom: 20px;">Log in</a>
        </div>
    </div>
</div>
@include('footer')
<script>
    //remove local storage value after use
    localStorage.removeItem("signup_phone");
</script>