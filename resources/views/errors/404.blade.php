<!DOCTYPE html>
<html>
   <head>
      <title>Page not found</title>
      <link href="{{asset('css/bootstrap.3.3.4.min.css')}}" rel="stylesheet">
      <link href="{{asset('css/stylenew.css')}}" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
      <meta name="viewport" content="width=device-width, initial-scale = 1.0, user-scalable = no">
      <meta name="description"
         content="The first dedicated discount platform in the Bangladesh. Discover offers from hotels, restaurants, spas, salons and many more. Pay less, enjoy more!">
      <meta name="keywords"
         content="health fitness, beauty spa, entertainment beauty, fitness getaways, spa health, entertainment beauty spa, health fitness getaways, spa health fitness, beauty spa health, drinks lifestyle entertainment, royalty, discount, offersbd, offersdhaka, privilege, loyaltycard, royaltycard, royaltycard, discountdhaka">
      <meta name="theme-color" content="#007bff">
      <meta charset="UTF-8">
      <link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo-user.png">
      <meta name="title" content ="Royalty - Your Lifestyle Partner">
      <style type="text/css">
         body{
        color: #007bff;;
         }
         .notfound-cont{
         text-align: center; margin: 90px auto;
         }
         @media only screen and (max-width:599px){
         .notfound-cont{
         margin: 60px auto!important;
         }
         }
      </style>
   </head>
   <body>
      <div class="container notfound-cont">
         <div class="row">
            <div class="col-md-offset-3 col-md-6"> 
               <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/not-found.png" style="width: 100%">
            </div>
            <div class="col-md-3">
            </div>
         </div>
         <div>
            <h3>Dhaka, we have a problem.</h3>
            <h4>In the meantime, head <a href="{{url('/')}}">home</a> or checkout the latest <a href="{{ url('offers/all') }}">offers</a> in Royalty</h4>
            <a href="{{url('/')}}">
            <button class="btn btn-primary">
            Go home
            </button>
            </a>
         </div>
      </div>
   </body>
</html>