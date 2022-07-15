<head>
<meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale = 1.0, user-scalable = no">
   <link href="{{asset('css/bootstrap.3.3.4.min.css')}}" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
   <link href="{{asset('css/stylenew.css')}}" rel="stylesheet">
   <link href="{{asset('css/mq.css')}}" rel="stylesheet">
   <link rel="stylesheet" href="{{asset('font/fontawesome5.6.3/css/all.css')}}">
   <link href="css/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="css/venobox/venobox.css" rel="stylesheet">
<link href="css/remixicon/remixicon.css" rel="stylesheet">
   <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111366305-1"></script>
   <script>
      window.dataLayer = window.dataLayer || [];
      function gtag() {
          dataLayer.push(arguments);
      }
      gtag('js', new Date());
      gtag('config', 'UA-111366305-1');
   </script>
</head>
<body>
<section>
<div class="container">
<div class="center" style="margin: 0 0 20px;">
      <button class="btn btn-primary">
      <a href="{{url('appblog')}}" style="color: white">Go back to blog home</a>
      </button>
   </div>
<div class="section-title-hero-blue" data-aos="fade-up">
   <h2>Royalty Blog</h2>
         <p>Stay updated with the latest events, how to's etc.</p>
      </div>
   <div class="row">
      <div class="col-md-4 col-sm-4 col-xs-12">
          <div style="margin-bottom:15px;box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)">
            <p class="card-header">Categories</p>
            <div class="card-body">
               <ul class="category_list">
                  @foreach($categories as $category)
                  <?php $string = str_replace('?', '', $category->category); ?>
                  <a href="{{url($string.'/appblog')}}">
                     <li>{{$category->category}}</li>
                  </a>
                  @endforeach
               </ul>
            </div>
         </div>
      </div>
      <div class="col-md-8 col-sm-8 col-xs-12 single-blog">
         <h1 class="mt-4">{{$blog->heading}}</h1>
         <?php
            $date=date("F d, Y", strtotime($blog->posted_on));
            ?>
         <p>Posted on {{$date}}</p>
         <hr>
         <div>
            <img class="card-img-top" src="{{$blog->image_url}}" alt="Royalty Blog">
         </div>
         <hr>
         <div class="blog-body">
            <p class="lead">{!! html_entity_decode($blog->details) !!}</p>
         </div>
         <hr>
      </div>
   </div>
</div>
</section>
   </body>