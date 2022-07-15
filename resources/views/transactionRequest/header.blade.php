<!DOCTYPE html>
<html lang="en">
   <head>
      <link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo-merchant.png">
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title>Royalty | Partner</title>
      <meta charset="utf-8">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
      <!-- Font Awesome -->
      <link rel="stylesheet" href="{{asset('font/fontawesome5.6.3/css/all.css')}}">
      <script src="https://js.pusher.com/5.0/pusher.min.js"></script>
      <title>Royalty | Admin</title>
      <link href="{{asset('css/merchant.css')}}" rel="stylesheet">
      <style>
         .merchant_request_count{
            padding: 0 5px;
    background-color: red;
    border-radius: 50%;
    top: 0;
    display: inline;
    margin-top: 12px;
    position: absolute;
    font-size: 0.6em;
         }
      </style>
   </head>
   <body>
      <div class="container">
      <div class="row">
         <div class="col-md-12">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
               <a class="navbar-brand" href="{{url('branch/requests')}}">
                  <img src="{{session('partner_pro_img')}}" alt="Profile Image" width="50" height="50" style="border-radius: 50%">
                  {{session('partner_name').', '}}
                  <small>{{session('branch_area')}}</small>
                  @if($notification_count > 0)
                     <span class="merchant_request_count">{{$notification_count}}</span>
                  @else
                     <span class="merchant_request_count" style="display: none;"></span>
                  @endif
               </a>
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" 
                  aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse w-100 order-3 dual-collapse2" id="navbarSupportedContent">
                  <ul class="navbar-nav ml-auto">
                     @if(isset($point))
                     <li class="nav-item" style="border-bottom: unset;">
                        <a class="nav-link" href="{{url('branch/point_prizes')}}">
                        <button class="btn btn-primary">
                        Point Balance {{$point}}
                        </button>
                        </a>
                     </li>
                     @endif
                     <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <button class="btn" style="background-color: #343a40; color: white;">
                           {{ session('branch_user_full_name') }}
                           </button>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                           <a class="dropdown-item" href="{{ url('branch/requests') }}">Home</a>
                           <a class="dropdown-item" href="{{ url('branch/all-transactions') }}">All Transactions</a>
                           <a class="dropdown-item" href="{{ url('branch/leaderboard') }}">Leader Board</a>
                           <a class="dropdown-item" href="{{ url('branch_user_logout') }}">Logout</a>
                        </div>
                     </li>
                  </ul>
               </div>
            </nav>
         </div>
      </div>
      <br>