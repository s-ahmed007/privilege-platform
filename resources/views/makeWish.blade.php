@if(!session()->has('customer_id'))
<script>
    window.location = "{{ url('/') }}";
</script>
@endif 
@include('header')
<section id="hero">
    <div class="container">
        <div class="section-title-hero" data-aos="fade-up">
            <h2>Make a wish</h2>
            <p>Any suggestions or improvements regarding our service?</p>
        </div>
    </div>
</section>
<section id="contact" class="contact">
    <div class="container">
        <div class="row">
        <div class="col-lg-4" data-aos="fade-right" data-aos-delay="100">
            <div class="info">
               <div class="address">
                  <i class="bx bx-bulb"></i>
                  <h4>Your opinion matters</h4>
                  <p>We really appreciate the time you are going to take to think about our service.<br />
                        We hope to find it to be very helpful.<br />
                        We strive for consumers like you to help us keep improving.</p>
               </div>
               <div class="email">
                  <i class="bx bx-edit"></i>
                  <h4>You can give:</h4>
                  <p>
                        -Service suggestions<br />
                        -Improvement suggestions<br />
                        -Any bug found on the site<br />
                        -Functionality suggestions
                        
                  </p>
               </div>
            </div>
         </div>
            <div class="col-lg-8 mt-5 mt-lg-0" data-aos="fade-left" data-aos-delay="200">
                <p>
                    @if ($errors->has('comment'))
                </p>
                <div id="remove-msg">
                    <ul>
                        <li>{{ $errors->first('comment') }}</li>
                    </ul>
                </div>
                @endif
                <form class="form-vertical input-form-box" action="{{url('makeWish')}}" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                            <textarea class="form-control contact-us-comment" name="comment" rows="5" data-rule="required" data-msg="Please write something for us" placeholder="Please enter your message (at least 10 characters)" required></textarea>
                    </div>
                    <div class="form-group">
                    <div class="text-right">
                    <button type="submit" name="submit" class="btn btn-primary">Send My Wish</button>
               </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
{{--wish modal--}}
<div id="wishModal" class="modal" role="dialog" style="top: 10%">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header"> <h4 class="modal-title">Thank you!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
           
         </div>
         <div class="modal-body" id="profile_modal" class="profile_modal">
            <div>
               <p>{{session('wish')}}</p>
            </div>
         </div>
      </div>
   </div>
</div>
@include('footer')
@if(session('wish'))
<script>
    $(document).ready(function(){
       $('#wishModal').modal('show');
    });
</script>
@endif