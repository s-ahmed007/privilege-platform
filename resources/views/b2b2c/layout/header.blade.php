<?php
//To Disable Cache Load if Browser Back Button Pressed
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>

        <!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo.png">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Royalty | Admin</title>
    <!-- Bootstrap -->
    <link href="{{ asset('admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('font/fontawesome5.6.3/css/all.css')}}">
    <!-- NProgress -->
    <link href="{{ asset('admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ asset('admin/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="{{ asset('admin/build/css/custom.min.css') }}" rel="stylesheet">
    <!-- Custom  Style -->
    <link href="{{ asset('admin/build/css/admin-panel.css') }}" rel="stylesheet">

    <style>
        .form-group .main_branch {
            display: none;
        }

        .form-group .main_branch + .btn-group > label span {
            width: 20px;
        }

        .form-group .main_branch + .btn-group > label span:first-child {
            display: none;
        }
        .form-group .main_branch + .btn-group > label span:last-child {
            display: inline-block;
        }

        .form-group .main_branch:checked + .btn-group > label span:first-child {
            display: inline-block;
        }
        .form-group .main_branch:checked + .btn-group > label span:last-child {
            display: none;
        }
    </style>
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="{{ url('client/dashboard') }}" class="site_title"><span>Home</span></a>
                </div>

                <div class="clearfix"></div>

                <!-- menu profile quick info -->
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <img src="{{Session::get('client-admin-image')}}"
                             alt="pp" class="img-circle profile_img">
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2>{{Session::get('client-admin-name')}}</h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br/>
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <h3>Menu</h3>
                        <ul class="nav side-menu">
                            <li><a href="{{ url('client/dashboard') }}"><i class="home-icon"></i>Dashboard</a></li>
                            <li><a><i class="user-icon"></i>Members Hub <span class="arrow-icon"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ url('client/customers') }}">All Members</a></li>
                                    <li><a href="{{ url('client/add-customer') }}">Add Members</a></li>
                                </ul>
                            </li>
                            <li><a href="{{ url('client/card-delivery') }}"><i class="truck-icon"></i>Card Delivery</a></li>
                            <li><a href="{{ url('client/all-transactions') }}"><i class="money-icon"></i>All Transactions</a></li>
                            <li><a href="{{ url('client/all-post') }}"><i class="review-icon"></i>All Posts</a></li>
                        </ul>
                    </div>
                </div>
                <!-- /sidebar menu -->
            </div>
        </div>
        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav>
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="bar-icon"></i></a>
                    </div>
                    <ul class="nav navbar-nav navbar-right">
                    @if(Session()->has('leaderboard_alert'))
                        @if(Session()->get('leaderboard_alert') == 0)
                            <br><span class="alert alert-info">Please update the leaderboard</span>
                        @endif
                    @endif
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                               aria-expanded="false">
                                Admin
                                <span class="arrow-icon"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li><a href="{{ url('client/adminLogout') }}"><i class="logout-icon pull-right"></i> Log Out</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div><!-- /top navigation -->
