@include('header')
<style type="text/css">
    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #007bff;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
    }
    .step.active {opacity: 1;}
    .step.finish {background-color: #007bff;}
</style>
<section id="hero">
   <div class="container">
      <div class="section-title-hero" data-aos="fade-up">
         <h2>Royalty Partners</h2>
         <p>Get loyal customers and get exposed to thousands of new customers</p>
      </div>
   </div>
</section>
   <section id="about" class="about">
      <div class="container-fluid">
         <div class="row">
            <div class="col-xl-5 col-lg-6 video-box1 d-flex justify-content-center align-items-stretch" data-aos="fade-right">
               <a href="https://www.youtube.com/embed/T9fyXWtpk6o" class="venobox play-btn mb-4" data-vbtype="video" data-autoplay="true"></a>
            </div>
            <div class="col-xl-7 col-lg-6 icon-boxes d-flex flex-column align-items-stretch justify-content-center py-5 px-lg-5" data-aos="fade-left">
               <h3>Why join Royalty as a Partner?</h3>
               <p>STAY AHEAD OF THE CURVE
                BY JOINING THE FIRST PRIVILEGE & LOYALTY PROGRAM OF THE COUNTRY</p>
               <div class="icon-box" data-aos="zoom-in" data-aos-delay="100">
                  <div class="icon"><i class="bx bx-door-open"></i></div>
                  <h4 class="title"><a href="">BE SEEN</a></h4>
                  <p class="description">Get exposed to tens of thousands of new customers on our platfrom. Create loyal customers and boost your sales</p>
               </div>
               <div class="icon-box" data-aos="zoom-in" data-aos-delay="200">
                  <div class="icon"><i class="bx bx-magnet"></i></div>
                  <h4 class="title"><a href="">ATTRACT</a></h4>
                  <p class="description">Get our loyal customers attention when you post about new additions. Get authentic review about your business</p>
               </div>
               <div class="icon-box" data-aos="zoom-in" data-aos-delay="300">
                  <div class="icon"><i class='bx bx-stats'></i></div>
                  <h4 class="title"><a href="">GROW</a></h4>
                  <p class="description">Encourage repeat business and boost your sales like never before. Get more and new customers every day</p>
               </div>
            </div>
         </div>
      </div>
   </section>
<section>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form id="regForm" class="whitebox" action="{{url('/partnerReg')}}" method="post">
                {{csrf_field()}}
                <div class="partner-join-head center">
                    <h3>Get Enlisted For Free</h3>
                    <p>You’re just a minute away from joining the Royalty platform as a partner to grow your business.</p>
                </div>
                <div class="partner-join-step center">
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                </div>
                <div class="tab partner-form-tab">
                    <label class="label-mandatory">Enter business name</label>
                    @if ($errors->getBag('default')->first('businessName'))
                    <span class="error_name">
                            {{ $errors->getBag('default')->first('businessName') }}
                        </span>
                    <p class="partner-form-field">
                        <input type="text" name="businessName" id="bname" placeholder="Eg. 138 East, Chuti Resort" class="form-control invalid">
                    </p>
                    @else
                    <p class="partner-form-field">
                        <input type="text" name="businessName" id="bname" placeholder="Eg. 138 East, Chuti Resort" class="form-control">
                    </p>
                    @endif
                    <label class="label-mandatory">Enter business owner(s) name</label>
                    <p class="partner-form-field">
                        <input type="text" name="ownerName" id="oname" placeholder="Business owner's full name" class="form-control">
                    </p>
                </div>
                <div class="tab partner-form-tab">
                    <label class="label-mandatory">Enter your mobile number</label>
                    <p class="partner-form-field">
                        <input type="text" name="businessNumber" id="bnumber" placeholder="Enter mobile number" class="form-control" maxlength="15">
                    </p>
                    <label class="label-mandatory">Enter business E-mail address</label>
                    <p class="partner-form-field">
                        <input type="email" name="businessEmail" id="bemail" placeholder="Eg. partner@royaltybd.com" class="form-control">
                    </p>
                    <label class="label-mandatory">Enter business address</label>
                    <p class="partner-form-field">
                        <input type="text" name="businessAddress" id="baddress" placeholder="Enter full business address" class="form-control">
                    </p>
                    <label class="label-mandatory">Enter business FB link</label>
                    <p class="partner-form-field">
                        <input type="text" name="fb_link" id="bfblink" placeholder="Enter business facebook link" class="form-control">
                    </p>
                    <label>Enter business website link</label>
                    <p class="partner-form-field">
                        <input type="text" name="web_link" id="bweblink" placeholder="Enter business website link" class="form-control">
                    </p>
                </div>
                <div class="tab partner-form-tab">
                    <label class="label-mandatory">Select Division</label>
                    <p>
                        <select class="form-control division-select" id="bdivision" name="partnerDiv">
                            <option value="">Select Division</option>
                            <option value="Dhaka">Dhaka</option>
                            <option value="Chittagong">Chittagong</option>
                            <option value="Rajshahi">Rajshahi</option>
                            <option value="Khulna">Khulna</option>
                            <option value="Barishal">Barishal</option>
                            <option value="Sylhet">Sylhet</option>
                            <option value="Mymensingh">Mymensingh</option>
                            <option value="Rangpur">Rangpur</option>
                        </select>
                    </p>
                    <label class="label-mandatory">Type Your Area</label>
                    <p class="partner-form-field">
                        <input type="text" class="form-control area-select" name="partnerArea" id="barea" placeholder="Enter business area">
                    </p>
                    <label class="label-mandatory">Select business category</label>
                    <p>
                        <select class="form-control category-select" id="bcategory" name="businessCategory">
                            <option value="">Select business category</option>
                            @foreach($categories as $category)
                                <option value="{{$category->name}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </p>
                </div>
                <div class="partner-join-btn-box">
                    <div>
                        <button type="button" id="prevBtn" onclick="nextPrev(-1)" class="btn btn-default">Previous
                        </button>
                        <button type="button" id="nextBtn" onclick="nextPrev(1)" class="btn btn-primary btn-next">Next</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</section>
{{--modal of join status of new partner--}}
<div id="joinStatus" class="modal" role="dialog" style="top: 20%;">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">  <h4 class="modal-title">Thank you!</h4>
            <button type="button" class="close" data-dismiss="modal">
            <i class="cross-icon"></i>
            </button>
          
         </div>
         <div class="modal-body">
            <p>{{ session('join_status') }}</p>
         </div>
      </div>
   </div>
</div>
@include('footer')
@if(session('join_status'))
<script>
   $( document ).ready( function () {
     $('#joinStatus').modal('show');
   });
</script>
@endif
<script>
    var currentTab = 0; // Current tab is set to be the first tab (0)
    showTab(currentTab); // Display the current tab

    function showTab(n) {
        // This function will display the specified tab of the form...
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
        //... and fix the Previous/Next buttons:
        if (n == 0) {
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "inline";
        }
        if (n == (x.length - 1)) {
            document.getElementById("nextBtn").innerHTML = "Submit";
        } else {
            document.getElementById("nextBtn").innerHTML = "Next";
        }
        //... and run a function that will display the correct step indicator:
        fixStepIndicator(n)
    }

    function nextPrev(n) {
        // This function will figure out which tab to display
        var x = document.getElementsByClassName("tab");
        // Exit the function if any field in the current tab is invalid:
        if (n == 1 && !validateForm(currentTab)) return false;
        // Hide the current tab:
        x[currentTab].style.display = "none";
        // Increase or decrease the current tab by 1:
        currentTab = currentTab + n;
        // if you have reached the end of the form...
        if (currentTab >= x.length) {
            // ... the form gets submitted:
            document.getElementById("regForm").submit();
            return false;
        }
        // Otherwise, display the correct tab:
        showTab(currentTab);
    }

    function validateForm(currentTab) {
        var bname = document.getElementById('bname').value;
        var oname = document.getElementById('oname').value;
        var bnumber = document.getElementById('bnumber').value;
        var bemail = document.getElementById('bemail').value;
        var baddress = document.getElementById('baddress').value;
        var bfblink = document.getElementById('bfblink').value;

        atpos = bemail.indexOf("@");
        dotpos = bemail.lastIndexOf(".");

        //check if fields are set or not
        if (currentTab == 0) {
            if (bname == '') {
                document.getElementById('bname').className += " invalid";
                return false;
            } else if (oname == '') {
                document.getElementById('oname').className += " invalid";
                return false;
            } else {
                document.getElementsByClassName("step")[currentTab].className += " finish";
                return true;
            }
        } else if (currentTab == 1) {
            if (bnumber == '' || bnumber.length > 15 || isNaN(bnumber)) {
                document.getElementById('bnumber').className += " invalid";
                return false;
            } else if (atpos < 1 || (dotpos - atpos < 2) || bemail == '') {
                document.getElementById('bemail').className += " invalid";
                return false;
            } else if (baddress == '') {
                document.getElementById('baddress').className += " invalid";
                return false;
            } else if (bfblink == '') {
                document.getElementById('bfblink').className += " invalid";
                return false;
            } else {
                document.getElementsByClassName("step")[currentTab].className += " finish";
                return true;
            }
        } else {
            var bdivision = $("#bdivision").val();
            var barea = $("#barea").val();
            var bcategory = $("#bcategory").val();

            if (bdivision == '') {
                document.getElementById("bdivision").className += " invalid";
                return false;
            } else if (barea == '') {
                document.getElementById("barea").className += " invalid";
                return false;
            } else if (bcategory == '') {
                document.getElementById("bcategory").className += " invalid";
                return false;
            } else {
                document.getElementsByClassName("step")[currentTab].className += " finish";
                return true;
            }
        }
    }

    function fixStepIndicator(n) {
        // This function removes the "active" class of all steps...
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        //... and adds the "active" class on the current step:
        x[n].className += " active";
    }
</script>