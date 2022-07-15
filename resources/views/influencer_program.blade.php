@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>ROYALTY INFLUENCER PROGRAM</h2>
         <p>Become an Influencer for Royalty today!</p>
      </div>
   </div>
</section>
<section>
<div class="container">
   <div class="mb-2">
         <h2>What is Royalty?</h2>
         <p>Royalty is the first dedicated privilege platform in Bangladesh that flourishes
            you with amazing offers on your day to day relaxation expenses. Starting from food & drinks, lifestyle, getaways,
            entertainment and much more. Conquering over {{$total_branch_count}}+ exclusive partnerships, we're working to make your life easy
            and would love to move forward and grow bigger with all your love and support.</p>
      </div>
      <div class="mb-2">
         <h2>Who is an Influencer?</h2>
         <p>An Influencer is an individual who has the power to affect the purchase decisions of
            others because of his/her authority, knowledge, position or relationship with his/her audience. An individual
            who has a following in a particular niche, which they actively engage with. Do you have it in you?</p>
      </div>
      <div class="mb-2">
         <h2>Am I eligible to be an Influencer?</h2>
         <p>Are you a social Influencer? Love spending quality time eating outside? The urge
            of chasing the latest fashion? Thinking about visiting the best all-in-one app where you can see updates and
            reviews alongside that is going to take your passion to a whole new level? Also, where you can have your own
            followers and make your own blog to share your day to day discoveries? Look no further, Royalty now brings
            you the opportunity to become an Influencer!</p>
      </div>
      <div class="mb-2">
         <h2>Do I get paid?</h2>
         <p>Without a doubt! Earn a substantial amount and all our influencers get
            monetary gratifications. Apart from all these, a lot of rewards and goodies are a must!</p>
      </div>
      <div class="mb-2">
         <h2>How do I start?</h2>
         <p>Yes, it's that simple, you can become an Influencer in no time. All you need is to
            fill some required data and follow the flow with the simple mentioned steps. There you go, just a click away!
            Good luck! </p>
      </div>
   <hr>
   <div class="sign-up-content">
      <form action="{{url('influencer-request')}}" method="post" class="signup-form">
      <div class="mb-2">
         <h4 class="card-title">Category of your blog ?</h4>
         <div class="form-radio">
            <input type="radio" name="influencer-type" value="fashion" id="fashion" checked="checked" />
            <label for="fashion">Fashion</label>
            <input type="radio" name="influencer-type" value="beauty" id="beauty" />
            <label for="beauty">Beauty</label>
            <input type="radio" name="influencer-type" value="lifestyle" id="lifestyle" />
            <label for="lifestyle">Lifestyle</label>
            <input type="radio" name="influencer-type" value="fitness" id="fitness" />
            <label for="fitness">Fitness</label>
            <input type="radio" name="influencer-type" value="travel" id="travel" />
            <label for="travel">Travel</label>
            <input type="radio" name="influencer-type" value="entertainment" id="entertainment" />
            <label for="entertainment">Entertainment</label>
         </div>
         <div class="form-textbox">
            <label for="name">Full name</label>
            <span style="color: red;">
               @if ($errors->getBag('default')->first('name'))
                  {{ $errors->getBag('default')->first('name') }}
               @endif
             </span>
            <input type="text" name="name" id="name" />
         </div>
         <div class="form-textbox">
            <label for="blogname">Blog name</label>
            <span style="color: red;">
               @if ($errors->getBag('default')->first('blogname'))
                  {{ $errors->getBag('default')->first('blogname') }}
               @endif
             </span>
            <input type="text" name="blogname" id="blogname" />
         </div>
         <div class="form-textbox">
            <label for="email">Email</label>
            <span style="color: red;">
               @if ($errors->getBag('default')->first('email'))
                  {{ $errors->getBag('default')->first('email') }}
               @endif
             </span>
            <input type="email" name="email" id="email" />
         </div>
</div>
         <div class="mb-2">
            <h4 class="card-title">Social Links (fill up whichever platform has your influncer account)</h4>
            <div class="form-textbox">
               <label for="fb-link">Facebook Page</label>
               <input type="text" name="fb-link" id="fb-link" />
            </div>
            <div class="form-textbox">
               <label for="ig-link">Instagram Page</label>
               <input type="text" name="ig-link" id="ig-link" />
            </div>
            <div class="form-textbox">
               <label for="yt-link">Youtube Page</label>
               <input type="text" name="yt-link" id="yt-link" />
            </div>
            <div class="form-textbox">
               <label for="web-link">Website/Blog</label>
               <input type="text" name="web-link" id="web-link" />
            </div>
         </div>
         <div class="form-group">
            <label for="agree-term" class="label-agree-term agreement">
               <p>By submitting the form, you agree to our
                  <a href="{{ url('terms&conditions') }}" target="_blank">
                  Terms & Conditions</a>
                  and
                  <a href="{{ url('privacypolicy') }}" target="_blank">
                  Privacy Policy</a>.
               </p>
            </label>
         </div>
         <input type="hidden" name="_token" value="{{ csrf_token() }}">
         <div class="form-textbox">
            <input type="submit" name="submit" id="submit" class="submit"  onclick="return social_link_validate();"  value="Create account" />
         </div>
      </form>
   </div>
</div>
</section>
{{-- Influencer request successful --}}
<div id="influencer_request" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Thank You</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
          
         </div>
         <div class="modal-body">
            <div>
               <p>{{session('inflencer_request_successful')}}</p>
            </div>
         </div>
      </div>
   </div>
</div>
@include('footer')

@if(session('inflencer_request_successful'))
<script>
   $(document).ready(function(){
      $('#influencer_request').modal('show');
   });
</script>
@endif
<script>
   function social_link_validate() {
      var fb_link = $("#fb-link").val();
      var ig_link = $("#ig-link").val();
      var yt_link = $("#yt-link").val();
      var web_link = $("#web-link").val();
      if(fb_link == '' && ig_link == '' && yt_link == '' && web_link == ''){
         alert("Please provide at least one social link");
         return false;
      } else {
         return true;
      }
   }
</script>