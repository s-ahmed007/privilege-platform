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
   <div data-aos="fade-up">
         <h2>Royalty Blog</h2>
         <p>Stay updated with the latest events, how to's etc.</p>
      </div>
      <div class="row">
         <div class="col-md-4 col-sm-4 col-xs-12">
             <div class="shadow mb-3">
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
         <br>
         <div class="col-md-8 col-sm-8 col-xs-12 all-mobile-blogs">
            @if(isset($blogs)) @foreach($blogs as $blog)
            <div class="shadow blog-box">
               <img class="card-img-top" src="{{$blog->image_url}}" width="750px" height="300px" alt="Royalty Blog">
               <div class="card-body">
                  <h3 class="card-title">{{$blog->heading}}</h3>
                  <?php 
                     ?>
                  <p class="card-text dots3 mtb-10">{{ strip_tags($blog->details).'....' }}</p>
                  <a href="{{url('app-blog-single/'.$blog->heading)}}" class="btn btn-primary ml-0">Read More &rarr;</a>
               </div>
               <div class="card-footer text-muted">
                  <?php
                     $date=date("F d, Y", strtotime($blog->posted_on));
                       ?>
                  Posted on {{$date}}
               </div>
            </div>
            @endforeach
            {{ $blogs->links() }} @endif
         </div>
      </div>
</section>
</body>
<script src="{{asset('js/jquery.min.js')}}"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>