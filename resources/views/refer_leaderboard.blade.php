<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<title>Refer Leaderboard | royaltybd.com</title>
<link rel="icon" href="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/icon/top-logo.png">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!DOCTYPE html>
<html>
   <head>
      <style>
         body{
         background: rgb(79,91,105);
         background: linear-gradient(90deg, rgba(79,91,105,1) 0%, rgba(104,120,138,1) 47%);
         }
         .table{
         box-shadow: 0 7px 30px rgba(62, 9, 11, 0.3);
         color: white;
         }
         .t-dark{
         background: #007bff;
         color: white;
         padding: 10px;
         }
         .heading-leaderboard{
         padding: 0 0 0 15px;
         font-size: 1.7rem;
         font-weight: bold;
         }
         .t-light{
         background: rgb(79,91,105);
         color: white;
         }
         .col2{
         font-size: 1.2rem;
         }
         tbody tr {
         background: #353e47;
         }
         .table tbody>tr>th, .table tbody>tr>td{
         border-top: 1px solid rgb(79,91,105);
         }
         .table thead>tr>th{
         border: unset;
         }
         .refer-leaderboard-pt{
         font-weight: bold;
         }
      </style>
   </head>
   <body>
      <div class="container">
         <div class="row" style="margin-top:60px;">
            <div class="col-md-offset-3 col-md-6">
               <table class="table">
                  <div>
                     <div class="t-dark">
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/about-us/logo.png" height="50px"/>
                        <span class="heading-leaderboard">
                        REFER LEADERBOARD
                        </span>
                     </div>
                  </div>
                  @if(count($data) > 0)
                     <thead>
                        <tr class="t-light">
                           <th scope="col" class="col2">Rank</th>
                           <th scope="col" class="col2">Name</th>
{{--                           <th scope="col" class="col2">Pts</th>--}}
                        </tr>
                     </thead>
                     <tbody>
                     <?php $i=1; ?>
                     @foreach($data as $item)
                     <tr>
                        <th scope="row">{{$i}}</th>
                        <td>{{$item->customer_full_name}}</td>
{{--                        <td class="refer-leaderboard-pt">{{$item->reference_used}}</td>--}}
                     </tr>
                     <?php $i++; ?>
                     @endforeach
                  </tbody>
                  @else
                     <td>
                        There is no top referrals for this time. Be the first.
                     </td>
                  @endif
               </table>
            </div>
         </div>
      </div>
   </body>
</html>