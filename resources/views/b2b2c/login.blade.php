<!DOCTYPE html>
<html lang="en">
  <head>
  <link rel="icon" href="{{ asset('images/Royalty Logo-02.png') }}">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Dashboard</title>

    <!-- Bootstrap -->
    <link href="{{ asset('admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ asset('admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- Animate.css') }} -->
    <link href="{{ asset('admin/vendors/animate.css/animate.min.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ asset('admin/build/css/custom.min.css') }}" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            @if (session('wrong info'))
              <div style="text-shadow: none; color: red; font-size: 1.3em">
                  {{ session('wrong info') }}
              </div>
            @endif
            <form action="{{ url('client/b2b2c-admin-login') }}" method="post">
              
              <h1 style="letter-spacing: 0.05em;">Login Form</h1>  
              <div>
                <input type="text" class="form-control" placeholder="Username" name="username">
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" name="password" />
              </div>
              <div>
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" name="submit" class="btn btn-default submit" style="float: inherit; margin-left: 0; padding: 6px 12px; font-size: 1.2em">Log in</button>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>
