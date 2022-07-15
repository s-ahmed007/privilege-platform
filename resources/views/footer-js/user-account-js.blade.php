{{-- show customer edit error message in modal --}}
<script>
    @if ($errors->has('cus_email') || $errors->has('cus_mobile') || $errors->has('cus_address') || $errors->has('edit_cus_pass') || $errors->has('cus_confirm_pass'))
    $('#editUser').modal('show');
    @endif
</script>

{{-- show customer's bonus list if exist any --}}
<script>
    @if(isset($requests) || isset($customerInfo))
    $('#requestsBeforeTransaction').modal('show');
    @endif
</script>

<script>
    @if ($errors->getBag('default')->first('partner_id') || $errors->getBag('default')->first('partner_branch_id'))
        $('#bonusRequestModal').modal('toggle');
    @endif
</script>

{{-- script after choosing the partner to spend the bonus in customer account --}}
<script defer>
    $(document).on('click', '#bonusRequest', function () {
        var partner_name = document.getElementById("options").value;
        // var partner_name = e.options[e.selectedIndex].text;
        // alert(e);
        return confirm('Do you wish to redeem 250 BDT cash coupon at ' + partner_name + '?');
    });
</script>

{{-- script after choosing the partner to enjoy birthday gift in customer account --}}
<script defer>
    $(document).on('click', '#birthdayRequest', function () {
        var partner_name = document.getElementById("birthdayOption").value;
        // var partner_name = e.options[e.selectedIndex].text;
        // alert(e);
        return confirm('Do you wish to redeem your birthday gift at ' + partner_name + '?');
    });
</script>

{{-- script for share in social sites --}}
<script defer>
    var popupSize = {
        width: 780,
        height: 550
    };

    $(document).on('click', '.social-buttons > a', function (e) {
        var
            verticalPos = Math.floor(($(window).width() - popupSize.width) / 2),
            horisontalPos = Math.floor(($(window).height() - popupSize.height) / 2);

        var popup = window.open($(this).prop('href'), 'social',
            'width=' + popupSize.width + ',height=' + popupSize.height +
            ',left=' + verticalPos + ',top=' + horisontalPos +
            ',location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1');

        if (popup) {
            popup.focus();
            e.preventDefault();
        }
    });
</script>

{{-- ============================================================================================================
      ========================unsubscribe and SubscribeAgain functionality with JavaScript & Ajax====================
============================================================================================================ --}}
<script defer>
    $(document).on('click', '#subscribeAgain', function () {
        var email = this.value;
        var url = "{{ url('/subscribeAgain/') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'email': email},
            success: function (data) {
                $('#unsubscribed').hide();
                document.getElementById('subscribed').style='visible';
            }
        });
    });
    $(document).on('click', '#unsubscribe', function () {
        var email = this.value;
        var url = "{{ url('/unsubscribe/') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'email': email},
            success: function (data) {
                $('#subscribed').hide();
                document.getElementById('unsubscribed').style='visible';
            }
        });
    });
</script>
{{--===========================subscribe, unsubscribe ends========================--}}

<script defer>
    $("#partner_name").change(function () {
        var partner_id = $(this).val();
        var url = "{{ url('/branches_from_partner_id') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'partner_id': partner_id},
            success: function (data) {
                if(data.length == 0){
                    $("#select_branch").hide(100);//show calculate form
                }else{
                    $("#select_branch").empty();//empty previous list
                    $("#select_branch").show(100);//show calculate form
                    //create a select option
                    var sel = $('<select onchange="bonusChangeValidate()" name="partner_branch_id" id="partnerBranches" class="form-control">' +
                        '<option value="" disabled selected>Select Branch</option>').appendTo($('#select_branch'));
                    $(data).each(function (i) {
                        if(this.partner_address.length > 70){
                            sel.append($("<option>").attr('value', this.id).text(this.partner_address.substr(0,70)+'...'));
                        }else{
                            sel.append($("<option>").attr('value', this.id).text(this.partner_address));
                        }

                    });
                }
            }
        });
    });
</script>

{{--show partner selection modal when user comes from 250tk coupon notification--}}
@if(session()->has('show-select-partner-modal')))
<script async>
    $('a[href="#news-feed"]').closest("li").removeClass("active");
    $('a[href="#rewards-requests"]').closest("li").addClass("active");
    $("#news-feed").removeClass("active");
    $("#rewards-requests").addClass("active");
    $("#bonusRequestModal").modal('toggle');
</script>
@endif

{{--show zero modal when user comes from 250tk coupon notification--}}
@if(session()->has('noCouponModal') || session()->has('zeroRefer'))
<script>
    $("#zeroRefer").modal('toggle');
</script>
@endif

{{--On Submit Bonus Request Form Validation--}}
<script async>
    function bonusSubmitValidate() {
         var partner_id = $('#partner_name').val();
         var branch_id = $('#partnerBranches').val();
         if(partner_id == null) {
             $("#branch_error").css("display", "none");
             $("#partner_error").css("display", "block");
             return false;
         } else if(branch_id == null) {
             $("#partner_error").css("display", "none");
             $("#branch_error").css("display", "block");
             return false;
         } else {
            $("#partner_error").css("display", "none");
            $("#branch_error").css("display", "none");
            return true;
         }
    }

    function bonusChangeValidate() {
        $("#partner_error").css("display", "none");
        $("#branch_error").css("display", "none");
    }
</script>

<script async>
function setPin(){
    var pin = $('#userPin').val();
    if(isNaN(pin)){
        $(".set_pin_error").text('Only number is allowed');
        return false;
    }else if(pin.length > 4){
        $(".set_pin_error").text('Please insert a 4 DIGIT PIN');
        return false;
    }else if(pin==null || pin==""){
        $(".set_pin_error").text('Please insert your PIN');
        return false;
    }
    var url = "{{ url('/set-pin/') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'pin': pin},
            success: function (data) {
                if(data['status'] === 1){
                    $('#hide-pin').hide();
                    $("#pinSetSuccessful").modal('show');
                }else{
                    $(".set_pin_error").text(data['text']);
                }
            }
        });
}
</script>
{{--function to update gender--}}
<script async>
    function updateGender(gender) {
        var url = "{{ url('/update-gender/') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'gender': gender},
            success: function (data) {
                if(data.result === 1){
                    $('#hide-gender').hide();
                    $(".progress-bar-success").css('width', data.percent+'%');
                    $(".completed_percentage").text(data.percent+'%');
                    if(data.percent >= 100){
                        $("#profile_completion").hide();
                        $("#profileCompletedModal").modal('show');
                    }
                }
            }
        });
    }
    function isValidDate(year, month, day) {
        if (year != null && month != null && day != null) {
            var dateString = year + '-' + month + '-' + day;
            var birthDate = new Date(dateString);
            var today = new Date();
            if(birthDate > today){
                return false;
            }

            var diff = Math.floor(today.getTime() - birthDate.getTime());
            var day = 1000 * 60 * 60 * 24;

            var days = Math.floor(diff/day);
            var months = Math.floor(days/31);
            var years = Math.floor(months/12);
            if(years <14){
                return false;
            }

            var regEx = /^\d{4}-\d{2}-\d{2}$/;
            if (!dateString.match(regEx)) return false;  // Invalid format
            var d = new Date(dateString);
            if (Number.isNaN(d.getTime())) return false; // Invalid date
            return d.toISOString().slice(0, 10) === dateString;

        } else {
            return false;
        }
    }

    function updateDOB() {
        var year = $('#birth_year').val();
        var month = $('#birth_month').val();
        var day = $('#birth_day').val();
        if(isValidDate(year, month, day)){
            //do nothing
        }else{
            $(".invalid_dob").text('Please select a valid date').css({'display': 'inline-block', 'color': 'red', 'position': 'absolute'});
            return false;
        }
        var url = "{{ url('/update-dob/') }}";
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'year': year, 'month': month, 'day': day},
            success: function (data) {
                if(data.result === 1){
                    $('#hide-dob').hide();
                    $(".progress-bar-success").css('width', data.percent+'%');
                    $(".completed_percentage").text(data.percent+'%');
                    if(data.percent >= 100){
                        $("#profile_completion").hide();
                        $("#profileCompletedModal").modal('show');
                    }
                }
            }
        });
    }
</script>

{{-- review section starts --}}
<script async>
    function createReview(review_submit_url, type){
        $("#reviewForm").attr("action", review_submit_url);
        $("#review_type").val(type);
        $("#reviewModal").modal('show');
    }

    $(document).ready(function () {
        $('.emoji-wysiwyg-editor').keyup(function () {
            var text = $('.emoji-wysiwyg-editor').text();
            $("#revChars").text(text.length+'/500');
        });
        $('.review_title').keyup(function () {
            var text = $( "input[name$='heading']" ).val();
            $("#titleChars").text(text.length+'/50');
        });
    });

//      ============================================================================================================
//       ========================script of star rating in make review section====================
// ============================================================================================================ 
    $( document ).ready( function () {
        $(".rate_star").click(function(){
            $(this).css("color", "#ffc107");
            $(this).prevAll().css("color", "#ffc107");
            $(this).nextAll().css("color", "#000");
            var star = $(this).attr('data-value');
            $("input.get_star").val(star);
        });
    });
    function validate() {
        var star_rating = document.getElementById('get_star').value;
        var heading = document.forms["reviewForm"]["heading"].value;
        var comment = document.forms["reviewForm"]["content"].value;
        
        if (star_rating == "") {
            document.getElementById('star_error').innerHTML = 'Please provide a rating';
            return false;
        }else if (heading == "" && comment == ""){
            $("#review_submit").hide();
            $(".review_submit_succeed").css('display', 'inline-block');
            return true;
        }else if (heading != "" && comment != ""){
            if (!heading.replace(/\s/g, '').length) {//if contains only space
                document.getElementById('heading_error').innerHTML = 'Please provide a title for your review';
                return false;
            }
            if (!comment.replace(/\s/g, '').length) {//if contains only space
                document.getElementById('comment_error').innerHTML = 'Please share your experience in detail';
                return false;
            }
            $("#review_submit").hide();
            $(".review_submit_succeed").css('display', 'inline-block');
            return true;
        }else if (heading != "" || comment != "") {
            if (heading == "" && comment != "") {
                document.getElementById('star_error').innerHTML = '';
                document.getElementById('comment_error').innerHTML = '';
                document.getElementById('heading_error').innerHTML = 'Please provide a title for your review';
                return false;
            } else {
                document.getElementById('star_error').innerHTML = '';
                document.getElementById('comment_error').innerHTML = 'Please share your experience in detail';
                return false;
            }
        } else {
            $("#review_submit").hide();
            $(".review_submit_succeed").css('display', 'inline-block');
            return true;
        }
    }
</script>
@if(session('reviewSubmitted'))
<script>
   $( document ).ready( function () {
      $('#reviewSubmitted').modal('show');
   });
</script>
@endif
{{-- review section ends --}}