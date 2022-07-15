@include('header')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Contact Us</h2>
         <p>Partners, fill up the form to contact</p>
      </div>
   </div>
</section>
<section id="contact" class="contact">
   <div class="container">
      <div class="row">
         <div class="col-lg-4" data-aos="fade-right" data-aos-delay="100">
            <div class="info">
               <div class="address">
                  <i class='bx bx-map'></i>
                  <h4>Location:</h4>
                  <p>Bashundhara Rd, Bashundhara R/A.</p>
               </div>
               <div class="email">
                  <i class='bx bx-envelope-open'></i>
                  <h4>Email:</h4>
                  <p><a href="mailto:support@royaltybd.com">
                     support@royaltybd.com
                     </a>
                  </p>
               </div>
               <div class="phone">
                  <i class="bx bx-phone"></i>
                  <h4>Call:</h4>
                  <p><a href="tel:+8809638620202">
                     +880-963-862-0202
                     </a> (10am-6pm) 
                  </p>
               </div>
            </div>
         </div>
         <div class="col-lg-8 mt-5 mt-lg-0" data-aos="fade-left" data-aos-delay="200">
            <form method="post" action="{{ url('user-contact') }}" id="contactfrm" class="form-vertical input-form-box" role="form">
               <div class="form-row">
                  <div class="col-md-6 form-group">
                     <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" data-rule="minlen:2" data-msg="Please enter at least 2 characters" value="{{old('name')}}" required />
                     <div class="validate">
                        @if ($errors->getBag('default')->first('name'))
                        {{ $errors->getBag('default')->first('name') }}
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6 form-group">
                     <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" data-rule="email" data-msg="Please enter a valid email address" value="{{old('email')}}" required />
                     <div class="validate">
                        @if ($errors->getBag('default')->first('email'))
                        {{ $errors->getBag('default')->first('email') }}
                        @endif
                     </div>
                  </div>
               </div>
               <div class="form-group">
                  <textarea class="form-control contact-us-comment" name="comment" rows="5" data-rule="required" data-msg="Please write something for us" placeholder="Please enter your message (at least 10 characters)" required>{{old('comment')}}</textarea>
                  <div class="validate">
                     @if ($errors->getBag('default')->first('comment'))
                     {{ $errors->getBag('default')->first('comment') }}
                     @endif
                  </div>
               </div>
               <div class="g-recaptcha" data-sitekey="{{env('CAPTCHA_KEY')}}"></div>
               <br/>
               <span>
                  @if ($errors->getBag('default')->first('g-recaptcha-response'))
                  <p style="color: red; float: left;">Please verify that you are not a robot</p>
                  @endif
               </span>
               <input type="hidden" name="_token" value="{{ csrf_token() }}">
               <div class="text-right">
               <button name="submit" type="submit" id="submit" class="btn btn-primary">Submit</button>
               </div>
               <div class="result"></div>
            </form>
         </div>
      </div>
   </div>
</section>
<section id="contact" class="contact">
   <div class="container">
   <div class="row">
   <div class="col-md-12">
   <div class="icon-box" data-aos="zoom-in" data-aos-delay="300">
      <h4 class="title">LET'S KEEP IN TOUCH</h4>
         <p class="description">If you would like to get in touch with us for business purposes, please fill out the form. If you want to reach our customer care, please feel free to send e-mail to support@royaltybd.com and our customer care department will contact you at your earliest convenience.</p>
   </div>
   </div>
</div>
</div>
</section>
@if(session('contact_status')))
<div id="contact_message_modal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">            <h4 class="modal-title">Thank you!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>

         </div>
         <div class="modal-body">
            <div>
               <p>{{ session('contact_status') }}</p>
            </div>
         </div>
      </div>
   </div>
</div>
@endif
@include('footer') 
@if(session('contact_status')))
<script>
   $(document).ready(function(){
       $('#contact_message_modal').modal('show');
   });
</script>
@endif
<script type="text/javascript">
   var onloadCallback = function() {
     //grecaptcha is ready
   };
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
   async defer></script>