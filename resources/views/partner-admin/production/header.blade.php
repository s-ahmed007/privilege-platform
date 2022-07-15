<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo-merchant.png">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Royalty | Partner Admin</title>

    <!-- Bootstrap -->
    <link href="{{ asset('admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('font/fontawesome5.6.3/css/all.css')}}">
    <!-- NProgress -->
    <link href="{{ asset('admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ asset('admin/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="{{ asset('admin/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet">
    <!-- JQVMap -->
    <link href="{{ asset('admin/vendors/jqvmap/dist/jqvmap.min.css') }}" rel="stylesheet"/>
    <!-- bootstrap-daterangepicker -->
    <link href="{{ asset('admin/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ asset('admin/build/css/custom.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/build/css/partner-admin.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/build/css/admin-panel.css') }}" rel="stylesheet">
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="{{ url('/partner/adminDashboard/' . session("partner_username")) }}" class="site_title">
                        <span>Partner Admin Panel</span>
                    </a>
                </div>
                <div class="clearfix"></div>
                <!-- menu profile quick info -->
                <div class="profile clearfix">
                    <div class="profile_pic">
                        <img src="{{ asset(Session::get('partner_profile_image'))}}" alt="..." class="img-circle profile_img">
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2>{{Session::get('partner_name')}}</h2>
                    </div>
                </div>
                <!-- /menu profile quick info -->
                <br/>
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <h3>Menu</h3>
                        <ul class="nav side-menu">
                            <li><a href="{{ url('/partner/adminDashboard/'.Session::get('partner_username')) }}"><i class="home-icon"></i>Dashboard</a></li>
                            <li><a><i class="review-icon"></i>Edit <span class="arrow-icon"></span></a>
                            <ul class="nav child_menu">
                                <li><a href="{{ url('/partner/edit-basic-info') }}">Edit Basic Info</a></li>
                                <li><a href="{{ url('allBranches') }}">Edit Branch Info</a></li>
                                    <li><a href="{{ url('partner/admin-dashboard/edit-subcategory') }}">Edit Subcategories</a></li>
                                    {{--<li><a href="{{ url('partner/admin-dashboard/edit-opening-hours') }}">Edit Opening Hours</a></li>--}}
                                  {{--<li><a href="{{ url('partner/admin-dashboard/edit-discount') }}">Edit Discount and T&C</a></li>--}}
                                </ul>
                            </li>
                            <li><a><i class='bx bx-news' ></i>Post <span class="arrow-icon"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ url('partner/post') }}">Add a new post / All posts</a></li>
                                </ul>
                            </li>
                            <li><a><i class="img-icon"></i>Images <span class="arrow-icon"></span></a>
                                <ul class="nav child_menu">
                                    <li><a href="{{ url('partner/admin-dashboard/profile-image') }}">Profile image</a></li>
                                    {{--<li><a href="{{ url('partner/admin-dashboard/menu-images') }}">Menu image</a></li>--}}
                                    <li><a href="{{ url('partner/admin-dashboard/gallery-images') }}">Gallery image</a></li>
                                    <!-- {{--<li><a href="{{ url('partner/admin-dashboard/cover-photo') }}">Cover Photo</a></li>--}} -->
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="top_nav">
            <div class="nav_menu">
                <nav style="text-align: center">
                    <div class="nav toggle">
                        <a id="menu_toggle">
                            <i class="bar-icon"></i>
                        </a>
                    </div>
                    <ul class="nav navbar-nav navbar-right" style="width: 20%;">
                        <li>
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            Admin
                                <span class="arrow-icon"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li><a href="{{ url('partnerAdminLogout') }}"><i class="logout-icon pull-right"></i> Log Out</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>