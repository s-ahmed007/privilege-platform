<head>
<meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale = 1.0, user-scalable = no">
   <link href="{{asset('css/bootstrap.3.3.4.min.css')}}" rel="stylesheet">
   <link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
   <link href="{{asset('css/stylenew.css')}}" rel="stylesheet">
   <link href="css/boxicons/css/boxicons.min.css" rel="stylesheet">
   <title>How it works | royaltybd.com</title>
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
<section id="faq" class="faq section-bg">
   <div class="container">
      <div class="section-title-hero-blue" data-aos="fade-up">
         <h2>Royalty Rewards</h2>
         <p>Earn credits to redeem greater rewards</p>
      </div>
      </section>
<section id="pricing" class="pricing">
   <div class="container">
      <div class="row" data-aos="fade-left">
         <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
            <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
               <h3>
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/rewards/offers.png" class="lazyload"  alt="Royalty Rewards">
               </h3>
               <h4>Availing Offers</h4>
               <p>Earn credits every time you scan QR at our partner outlets. Different categories of partners will bring you different amounts of credits. Make sure to claim credits by availing offers successfully!
               <h5 class="center">How to avail an offer?</h5>
               <p>i. Scan QR Code stand of the partner: Find QR stand at the partner outlets to scan and select your desired offer.
               </p>
               <p>ii. Tap to avail from the app: Go to partner profile and tap on the desired offer to avail. 
               </p>
               <div class="btn-wrap">
                  <a href=""></a>
               </div>
            </div>
         </div>
         <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
            <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
               <h3>
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/rewards/refer.png" class="lazyload"  alt="Royalty Rewards">
               </h3>
               <h4>Referring F&F</h4>
               <p>
               Earn credits everytime your F&F sign up using your Refer code and scans at least twice at our partner outlets,
                  both of you will earn {{$prices->where('type', 'refer_bonus')->first()->price}} Royalty Credits each. Invite more to earn more!
               </p>
               <u>
                     <p>For Mobile App:</p>
                  </u>
                  <p>Left drawer(on android)/More(on iOS)->Refer & Earn. Tap on it to see your referral code.
                  </p>
               <div class="btn-wrap">
                  <a href=""></a>
               </div>
            </div>
         </div>
         <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
            <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
               <h3>
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/rewards/rating.png" class="lazyload"  alt="Royalty Rewards">
               </h3>
               <h4>Rating & Reviewing Partners</h4>
               <p>
                  Rate & Review partners after every successful scan on a scale of 1 to 5 to earn {{$prices->where('type', 'rating')->first()->price}} Credit. Sharing your experience will help others
                  make a decision. Earn {{$prices->where('type', 'review')->first()->price}} Credits per review.
               </p>
               <div class="btn-wrap">
                  <a href=""></a>
               </div>
            </div>
         </div>
         <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
            <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
               <h3>
                  <img src="https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/rewards/activities.png" class="lazyload"  alt="Royalty Rewards">
               </h3>
               <h4>Activities On The Platform</h4>
               <p>
                  Participate in ongoing activities and campaigns on the platform (Web & App, both!) to earn credits all round the year! For example, occassional games, quizes, surveys etc.
               </p>
               <div class="btn-wrap">
                  <a href=""></a>
               </div>
            </div>
         </div>
      </div>
      <br>
      <div>
         <p> Please note that credits can take up to 24 hours to add, credits do not expire unless redeemed. They may decrese if the content is removed for violating our <a href="{{ url('terms&conditions') }}">Terms</a>.
         </p>
      </div>
   </div>
   </div>
</section>
</body>