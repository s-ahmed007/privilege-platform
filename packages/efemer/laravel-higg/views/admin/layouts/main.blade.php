<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="lyk.by">
    <meta name="author" content="John Efemer">
    <link rel="icon" type="image/png" sizes="16x16" href="lyk/images/favicon.png">

    <title>lyk-ing is loving</title>

    <link href="lyk/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="lyk/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <link href="lyk/css/animate.css" rel="stylesheet">
    <link href="lyk/css/style.css" rel="stylesheet">
    <link href="lyk/css/colors/default.css" id="theme"  rel="stylesheet">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="fix-sidebar">

<div class="preloader"><div class="cssload-speeding-wheel"></div></div>

<div id="wrapper">

@include('lyk::elements.blocks.nav-top')

@include('lyk::elements.blocks.nav-main')

<!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">

            @yield('pageContent')

            @include('lyk::elements.blocks.sidebar-right')

        </div>
        <!-- /.container-fluid -->
        <footer class="footer text-center"> 2016 &copy; JE </footer>
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

<script src="lyk/bower_components/jquery/dist/jquery.min.js"></script>
<script src="lyk/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="lyk/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
<script src="lyk/js/jquery.slimscroll.js"></script>
<script src="lyk/js/waves.js"></script>
<script src="lyk/js/custom.js"></script>>

</body>
</html>
