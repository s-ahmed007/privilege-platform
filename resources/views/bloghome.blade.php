@include('header')
<?php use \App\Http\Controllers\functionController; ?>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Royalty Blog</h2>
         <p>Stay updated with the latest events, how to's etc.</p>
      </div>
   </div>
</section>
<section>
<div class="container">
   <div class="row">
      <div class="col-md-8 col-sm-8 col-xs-12 all-blogs">
         @if(isset($blogs))
         @foreach($blogs as $blog)
         <div class="shadow blog-box">
            <img class="card-img-top" src="{{$blog->image_url}}" width="750px" height="300px" alt="Royalty blog">
            <div class="card-body">
               <h3 class="card-title">{{$blog->heading}}</h3>
               <p class="card-text dots3 mtb-10">{{ strip_tags($blog->details).'....' }}</p>
               <a href="{{url('blog/'.$blog->heading)}}" class="btn btn-primary ml-0">Read More &rarr;</a>
               <?php
                  $heading = str_replace("'", "", $blog->heading);
                  $heading = str_replace('"', "", $heading);
                  $heading = trim(preg_replace('/\s+/', ' ', $heading));

                  $details = strip_tags($blog->details);
                  $details = str_replace("'", "",$details);
                  $details = str_replace('"', "", $details);
                  $details = trim(preg_replace('/\s+/', ' ', $details));

                  $newline = '\n';
                  $pretext = 'Posted on royaltybd.com\n';
                  $blog_body = '\n'.$details.'\n';
                  $blog_head = '\n'.$heading;

                  $enc_blog_id = (new functionController)->postShareEncryption('encrypt', $blog->id);
                  $blog_url = url('/blog-share/' . $enc_blog_id);
               ?>
               <div class="social-buttons">
                  <!-- Twitter share button code -->
                  <?php $twit_blog_text = $pretext . substr($blog_head,0, 30).'...' . substr($blog_body,0, 130).'...' . $newline . $blog_url; ?>
                  <span onclick="window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent('{{$twit_blog_text}}'))" class="shareCursor">
                     <i class="bx bxl-twitter"></i>
                  </span>
                  <!-- Facebook share button code -->
                  <?php $fb_blog_url = 'https://www.facebook.com/sharer.php?href=https%3A%2F%2F'.url('/').'%2Fblog-share%2F' . $enc_blog_id; ?>
                  <span onclick="window.open('{{$fb_blog_url}}')" class="shareCursor">
                     <i class="bx bxl-facebook-circle"></i>
                  </span>
               </div>
            </div>
            <div class="card-footer text-muted">
               Posted on {{date("F d, Y", strtotime($blog->posted_on))}}
            </div>
         </div>
         @endforeach
         {{ $blogs->links() }}
         @endif
      </div>
      <div class="col-md-4 col-sm-4 col-xs-12">
          <div class="shadow">
            <p class="card-header">Categories</p>
            <div class="card-body">
               <ul class="category_list">
                  @foreach($categories as $category)
                  <?php $string = str_replace('?', '', $category->category); ?>
                  <a href="{{url($string.'/blog')}}">
                     <li>{{$category->category}}</li>
                  </a>
                  @endforeach
               </ul>
            </div>
         </div>
         <br>
          <div class="shadow">
            <p class="card-header">New Offers Everyday!</p>
            {{--If partner is logged in--}}
             @if(Session::has('partner_id'))
            <div class="card-body">
               <p>
                  Royalty signs up the best partners in town!
                  <br><br> Want to know more about Partner Benefits? Visit <a href="{{ url('partner-join') }}" target="_blank">here</a>.
               </p>
            </div>
            {{--If cardholder/guest is logged in--}} @elseif(Session::has('customer_id'))
            <div class="card-body">
               <p>
                  Royalty signs up the best partners in town!
                  <br><br> See all our latest partners of six different categories
                  <a href="{{url('offers/all')}}">
                  here
                  </a>.
               </p>
            </div>
            @else {{--If no one is logged in--}}
            <div class="card-body">
               <p>
                  We offer you the best offers in town. Our subscribed members can enjoy up to 75% discount in our partner outlets.
                  <br><br> Refer your friends & family to earn Royalty Reward which you can redeem for greater rewards!
               </p>
               <a href="{{url('login')}}" style="display: flow-root">
               <button class="btn btn-success sidegreenbtn">SIGN UP</button>
               </a>
            </div>
            @endif
         </div>
      </div>
   </div>
</div>
</section>
@include('footer')