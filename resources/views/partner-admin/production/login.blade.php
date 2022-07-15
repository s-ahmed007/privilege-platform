@if(session()->has('partner-admin'))
    <script>
        window.location = "{{ url('/partner/admin-panel') }}";
    </script>
@endif
@include('../header')
<link href="{{asset('css/login.css')}}" rel="stylesheet">

<div class="container" >
    <div class="background-image lazyload"></div>
    <div class="login-container">
        <div class="cont">
            <div class="form sign-in">
                <div class="login-logo">
                    <img class="img-responsive lazyload" src="{{asset('images/login/logo1.png')}}" alt="logo">
                </div>
                @if(isset($error))
                    <div class="error">{{$error}}</div>
                @elseif (isset($errors) && $errors->has('username') || $errors->has('password'))
                    <div class="error">
                        Username or Password didn't match!
                    </div>
                @endif
                <form method="post" action="{!! url('login') !!}">
                    {{csrf_field()}}
                    <label>
                        <span>Username</span>
                        <input class="loginInput" type="text" name="login_username"/>
                    </label>
                    <label>
                        <span>Password</span>
                        <input class="loginInput" type="password" name="login_password"/>
                    </label>
                    <a href="{{url('resetPassword')}}" class="forgot_password_anchor"><p class="forgot-pass">Forgot password?</p></a>
                    <button class="snip0040">
                        <span><b>Sign In</b></span>
                    </button>
                </form>
            </div>

            <div class="sub-cont">
                <div class="img lazyload">
                    <div class="img__text m--up">
                        <h2>New Here?</h2>
                        <p>Sign Up & Discover A World Of Endless Possibilities!</p>
                    </div>
                    <a href="https://www.royaltybd.com/select-card">
                        <div class="img__btn">
                            <span class="m--up"><b>Sign Up</b></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@include('../footer-js.login-js')