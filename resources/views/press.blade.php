@include('header')
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Royalty Press</h2>
         <p>Find out about us on newspapers and online articles.</p>
      </div>
   </div>
</section>
<section>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            @if(isset($news))
                @foreach($news as $value)
                <div class="shadow">
                    <img class="card-img-top" src="{{$value->press_image}}" width="750px" height="300px" alt="Royalty blog">
                    <div class="card-body">
                        <h2 class="card-title">{{$value->sub_title}}</h2>
                        <?php
                        ?>
                        <p class="card-text dots3 mtb-10">{{ strip_tags($value->press_details).'....' }}</p>
                        <a href="{{$value->press_link}}" class="btn btn-primary ml-0">Read More &rarr;</a>
                    </div>
                    <div class="card-footer text-muted">
                        <?php
                        $date=date("F d, Y", strtotime($value->date));
                        ?>
                        Posted on {{$date}}
                    </div>
                </div>
            @endforeach
            {{ $news->links() }}
            @endif
        </div>
        <div class="col-md-4">
             <div class="shadow">
                <p class="card-header">New Offers Everyday!</p>
                {{--If partner is logged in--}} @if(Session::has('partner_id'))
                    <div class="card-body">
                        <p>
                            Royalty signs up the best partners in town!
                            <br><br> Want to know more about Partner Benefits? Visit <a href="{{ url('partner-join') }}">here.</a>
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
                        <a href="{{url('login')}}" style="display: flow-root">
                            <button class="btn btn-success sidegreenbtn">SIGN UP</button>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<section>
@include('footer')