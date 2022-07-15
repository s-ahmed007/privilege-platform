@if (Session::has('post_notification_seen'))
    <script>
        {{-- show post section if post notification is clicked --}}
        $('#post_in_account')[0].click();
        
        //var session_post_id = <!-- ?php echo  Session::get('post_notification_seen');?-->;
        //var myElement = document.getElementById("post-id-<!-- ?php echo  Session::get('post_notification_seen');?-->");
        //alert(myElement.value());
        //console.log(data);
        //var topPos = myElement.offsetTop;
        //document.getElementById("newsfeed_dashboard").scrollTop = topPos;
		//location.hash = "#post-id-" + session_post_id;
    </script>
@endif

{{-- show "expired" message on process discount button in partner account --}}
<script>
    // $(function () {
    //     $("#show-expired").tooltip({
    //         show: {
    //             effect: "slideDown",
    //             delay: 300
    //         }
    //     });
    // });
</script>


{{-- ============================================================================================================
  ========================check user 'validation & request' with JavaScript & Ajax====================
============================================================================================================= --}}
<script>
    $('#calculate').hide(100);//keep calculate form hidden firstly
    $(document).ready(function () {
        $('#checkUser').on('click', function () {
            var url = "{{ url('/checkUser') }}";

            $.ajax({
                type: "POST",
                url: url,
                data: {'_token': '<?php echo csrf_token(); ?>', 'id': $('input[name=customer_ID]').val()},
                success: function (data) {
                    console.log(data);
//                    alert(data[0]['expiry_date']);
                    //if customer has request
                    if (typeof (data['requests']) != 'undefined' && data['requests'] !== null) {
                        $("#calculate").show(100);//show calculate form
                        $('[name=customerID]').val(data['customerInfo']['customer_id']);
                        $('#cal_name').append(data['customerInfo']['customer_first_name'] + ' ' + data['customerInfo']['customer_last_name']);
                        $('#cal_type').append(data['customerInfo']['type']+' member');
                        //create image in a div
                        var img = $('<img />', {
                            class: 'img-circle',
                            src: data['customerInfo']['customer_profile_image'],
                            alt: 'MyAlt',
                            width: 100,
                            height: 100
                        });
                        img.appendTo($('#cal_img'));
                        $('.img-circle').css('object-fit', 'cover');
                        //create a select option
                        var sel = $('<select name="customerRequest" id="customerRequest" class="form-control">' +
                            '<option value="0" disabled selected>Select Discount Type</option>').appendTo($('#select'));
                        $(data['requests']).each(function (i) {
                            sel.append($("<option>").attr('value', this.coupon_type + '_' + this.request_code).text(this.reward_text));
                        });
                        //make something hidden and disabled
                        $("#invalidUser").hide(100);
                        $("#checkUser").attr("disabled", "disabled");
                        // if customer has no request
                    } else if (typeof (data['customerInfo']) != 'undefined' && data['customerInfo'] !== null) {
                        $("#calculate").show(100);//show calculate form
                        $('[name=customerID]').val(data['customerInfo']['customer_id']);
                        $('#cal_name').append(data['customerInfo']['customer_first_name'] + ' ' + data['customerInfo']['customer_last_name']);
                        $('#cal_type').append(data['customerInfo']['type']+' member');
                        //create image in a div
                        var img = $('<img />', {
                            class: 'img-circle',
                            src: data['customerInfo']['customer_profile_image'],
                            alt: 'MyAlt',
                            width: 100,
                            height: 100
                        });
                        img.appendTo($('#cal_img'));
                        $('.img-circle').css('object-fit', 'cover');
                        //make something hidden and disabled
                        $("#invalidUser").hide(100);
                        $("#checkUser").attr("disabled", "disabled");
                    } else {
                        //invalid user
                        $("#calculate").hide(100);
                        $("#invalidUser").show(100);
                        document.getElementById("invalid_user").innerHTML = data['invalid_user'];
                    }
                }

            })
        });
    });
</script>
{{-- ENDS check user 'validation & request' with JavaScript & Ajax --}}

{{-- ============================================================================================================
  ========================calculate bill with JavaScript & Ajax====================
============================================================================================================= --}}
<script>
    $(document).ready(function () {
        $('#calculate_bill').on('click', function () {
            var url = "{{ url('/calculateBill') }}";
            var bill = $("#customerBill").val();
            var customer_ID = $("#customerID").val();
            var partner_ID = $("#partnerID").val();
            //check if bonus request option exist or not
            if (document.getElementById('customerRequest')) {
                var request = $("#customerRequest").val();
            } else {
                var request = 0;
            }
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    '_token': '<?php echo csrf_token(); ?>',
                    'bill': bill,
                    'customer_id': customer_ID,
                    'partner_id': partner_ID,
                    'customerRequest': request
                },
                success: function (data) {
                    // console.log(data.bill);
                    //alert(data);
                    if (data !== null) {
                        $('#discountModal').modal('show');
                        //data instered in modal
                        document.getElementById('cus_id_in_confirm_bill').innerHTML = data.customerID;
                        document.getElementById('final_bill_in_confirm_bill').innerHTML = data.bill;
                        //elements by class always returns an array. For this I had to run a loop
                        var className = document.getElementsByClassName('discount_in_confirm_bill');
                        for (var idx = 0; idx < className.length; idx++) {
                            className[idx].innerHTML= data.discount;
                        }
                        document.getElementById('final_amount_in_confirm_bill').innerHTML = data.bill_amount;
                        //data inserted in form in modal
                        document.getElementById('submit_customer_id').value = data.encrypted_customerID;
                        document.getElementById('submit_total_bill').value = data.encrypted_bill;
                        document.getElementById('submit_discount').value = data.encrypted_discount;
                        document.getElementById('submit_final_bill').value = data.encrypted_bill_amount;
                        document.getElementById('customer_request').value = data.requestCode;
                    }
                }
            })
        })
    });
</script>

{{-- ============================================================================================================
  ========================sort transaction history with JavaScript & Ajax====================
============================================================================================================= --}}
<script>
    function SortPartTranHis(){
        var url = "{{ url('/sort-partner-transaction-history') }}";
        var year = $("#sortPartTranHisYear").val();
        var month = $("#sortPartTranHisMonth").val();
        var branch_id = '<?php echo $partner_data->id ?>';
        if(year === 'all' && month !== 'all'){
            alert('Please select a year to see result');
            return false;
        }
        $('.page_loader').fadeIn();//show loading gif
        $.ajax({
            type: "POST",
            url: url,
            data: {'_token': '<?php echo csrf_token(); ?>', 'year': year, 'month': month, 'branch_id': branch_id},
            success: function (data) {
                $("#partner_tran_his").hide().html(data).fadeIn('slow');
                $('.page_loader').fadeOut();//hide loading gif
            }
        })
    }
</script>

<script src="{{asset('js/accounts_js/jquery.scrollTo.min.js')}}"></script>
<script src="{{asset('js/accounts_js/jquery.sparkline.js')}}"></script>
<script class="include" type="text/javascript" src="{{asset('js/accounts_js/jquery.dcjqaccordion.2.7.js')}}"></script>
<!--common script for all pages-->
<script src="{{asset('js/accounts_js/common-scripts.js')}}"></script>

