@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
      <?php
            $date=date("F d, Y", strtotime($blog->posted_on));
            ?>
         <h2>Posted on {{$date}}</h2>
         <p>{{$blog->heading}}.</p>
      </div>
   </div>
</section>
<section>
<div class="container">
   <div class="row">
      <div class="col-md-8 col-sm-8 col-xs-12 single-blog">
         <div class="shadow">
            <img class="card-img-top" src="{{$blog->image_url}}" alt="Royalty blog">
         </div>
         <hr>
         <div class="blog-body">
            <p class="lead">{!! html_entity_decode($blog->details) !!}</p>
         </div>
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
         </div><br>
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
                  <br><br> Refer your friends & family to earn Royalty Credit which you can redeem for greater rewards!
               </p>
               <a href="{{url('login')}}">
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