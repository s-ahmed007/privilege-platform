@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <!-- <h2>Find your profile details, usages, rewards all together here</h2> -->
         <p>Apply for job</p>
      </div>
   </div>
</section>
<section>
   <div class="container">
      <div>
         <div class="j-d-top-head">
            <h2 class="job-position"> Position Name:
               {{ $openingInfo['position'] }}
            </h2>
            <span class="job-location">
            <i class="address-icon"></i>
            <b>Dhaka</b>
            </span>
            <span class="job-type">
            <i class="briefcase-icon"></i>
            {{ $openingInfo['duration'] }}
            </span>
            <span class="job-salary">
            <i class="refer-icon"></i>
            {{ $openingInfo['salary'] }}
            </span>
            <p class="Date">
               <?php $job_deadline = date("F j, Y", strtotime($openingInfo['deadline'])); ?>
               Last Date to Apply: {{ $job_deadline }}
            </p>
         </div>
         <div class="j-d-body">
            <div class="j-description">
               {!! html_entity_decode($openingInfo['requirements']) !!}
            </div>
         </div>
         <div class="j-d-footer">
            See more job offerings <a href="{{url('careers')}}">here</a>.
         </div>
      </div>
   </div>
</section>
@include('footer')