@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Careers</h2>
         <p>Envision your career with us. Learn about our loyalty program and apply to explore the boundless possibilities.</p>
      </div>
   </div>
</section>
<section>
   <div class="container">
      <section id="pricing" class="pricing">
         <div class="container">
            <div class="row" data-aos="fade-left">
               <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
                  <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
                     <h3>
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/careers/it-team.png" class="lazyload" alt="Royalty Team">
                        <p></p>
                     </h3>
                     <h4>IT Team</h4>
                     <p>
                        Our developers are very dedicated who bring energetic ideas and pride to their work. We balance challenging requirements, co-creating with our clients, building the capabilities required for them to create repeated results. Security and Privacy is our
                        pillar of success.
                     </p>
                     <div class="btn-wrap">
                        <a href=""></a>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
                  <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
                     <h3>
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/careers/mkt-team.png" class="lazyload" alt="Royalty Team">
                        <p></p>
                     </h3>
                     <h4>Marketing Team</h4>
                     <p>
                        Our marketing team is highly committed to deliver exceptional and effective ideas for both our customers and partners. We believe that a marketing team should always come up with an effective & better plan for a bigger picture of the company in every
                        phase of a business.
                     </p>
                     <div class="btn-wrap">
                        <a href=""></a>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
                  <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
                     <h3>
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/careers/hr-team.png" class="lazyload" alt="Royalty Team">
                        <p></p>
                     </h3>
                     <h4>HR Team</h4>
                     <p>
                        Our HR team handles areas such as personnel management, social welfare and the maintenance of HR records. This team is responsible for guiding a group of employees as they complete a project within the timeline. They are also responsible for developing
                        and implementing a timeline.
                     </p>
                     <div class="btn-wrap">
                        <a href=""></a>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
                  <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
                     <h3>
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/careers/graphics-team.png" class="lazyload" alt="Royalty Team">
                        <p></p>
                     </h3>
                     <h4>Graphics Team</h4>
                     <p>
                        Visual content converts faster than words alone. Content marketing strategies outperform all others, and with a variety of visual assets supporting your marketing, we turn brands into ROI engines. Attract a larger targetted audience and enhance your customer engagement
                        with the design that matters for both the parties.
                     </p>
                     <div class="btn-wrap">
                        <a href=""></a>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
                  <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
                     <h3>
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/careers/video-team.png" class="lazyload" alt="Royalty Team">
                        <p></p>
                     </h3>
                     <h4>Video Team</h4>
                     <p>
                        Video content is engaging, compelling, and memorable. It has a unique ability to quickly grab your audience's attention until you deliver your messages. Video marketing is an interactive experience that leaves a lasting impression. Our video team delivers
                        the perfect video content that soothes your eyes and mind.
                     </p>
                     <div class="btn-wrap">
                        <a href=""></a>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
                  <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
                     <h3>
                        <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/careers/content-team.png" class="lazyload" alt="Royalty Team">
                        <p></p>
                     </h3>
                     <h4>Content Team</h4>
                     <p>
                        Quality content writing is the best way to reach your audience. Our skilled content writers are committed to satisfy your customers’ appetite for content and your thirst for results. Our copywriters are well-versed in SEO and social media strategy, making
                        them well-rounded web content and strategy specialists.
                     </p>
                     <div class="btn-wrap">
                        <a href=""></a>
                     </div>
                  </div>
               </div>
            </div>
            <br>
            <h4 class="center">Openings</h4>
            <br>
            <div class="row" data-aos="fade-left">
               @if(count($openingInfo)>0) @foreach($openingInfo as $info)
               <a href="{{url('job_opening_details'.'/'.$info['position'])}}">
                  <div class="col-lg-4 col-md-4 mt-4 mt-md-0">
                     <div class="box featured" data-aos="zoom-in" data-aos-delay="200">
                        <h3>
                           <img src="https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/careers/it-team.png" class="lazyload" alt="Royalty Team">
                           <p></p>
                        </h3>
                        <h4>{{ $info['position'] }}</h4>
                        <p>
                           Apply NOW!
                        </p>
                        <div class="btn-wrap">
               <a href=""></a>
               </div>
               </div>
               </div>
               </a>
               @endforeach
               @else
               <div class="no-vacancy">
                  <p>No vacancy available!</p>
               </div>
               @endif
               <div id="job1">
               </div>
            </div>
         </div>
      </section>
   </div>
</section>
@include('footer')