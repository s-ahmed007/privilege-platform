// check customer
var base_url = window.location.origin;
function checkCustomer() {
  var url = base_url + "/" + "checkUser";
  var customer_id = $("#customer_id").val();
  if (customer_id === "" || isNaN(customer_id) || customer_id.length !== 16) {
    return false;
  }
  $.ajax({
    type: "POST",
    url: url,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    },
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
      customer_id: customer_id
    },
    success: function(data) {
      if (data.error) {
        $(".user_not_found")
            .text(data.error)
            .css("color", "red");
      } else {
        $(".user_not_found").empty();
        // set user info
        $(".user_img").attr("src", data.customer.customer_profile_image);
        $(".customer_name").text(data.customer.customer_full_name);
        $(".customer_id").text(data.customer.customer_id);
        var output = "";
        data.offers.forEach(function(offer) {

          output += '<li class="list-group-item">\n';
          if(typeof offer.redeem !== 'undefined'){
            output += '<span style="padding: 5px; background-color: #ffc107; border-radius: 3px">Reward</span>\n';
          }
          output += '<div class="row">\n';
          output += '<div class="col-md-8">\n';
          output +=
              '<p class="offer_description">' +
              offer.offer_description +
              "</p>\n";
          output +=
              '<p>Valid till <span class="valid_till">' +
              offer.offer_to +
              "</span></p>\n";
          if(typeof offer.redeem === 'undefined'){
            output +=
                '<p>Valid for <span class="valid_for">' +
                offer.valid_for +
                "</span></p>\n";
          }else{
            output +=
                '<p>Quantity <span class="valid_for">' +
                offer.redeem.quantity +
                "</span></p>\n";
          }
          output += "</div>\n";
          output += '<div class="col-md-4">\n';
          var btn_text = typeof offer.redeem === 'undefined' ? 'CONFIRM' : 'REDEEM';
          var btn_color = typeof offer.redeem === 'undefined' ? 'success' : 'warning';
          var redeem_id = typeof offer.redeem === 'undefined' ? null : offer.redeem.id;
          if (!offer.expired) {
            output +=
                '<button class="btn btn-' + btn_color + '" id="offer_' +
                offer.id +
                '" onclick="confirmCheckout(' +
                data.customer.customer_id +
                "," +
                offer.id +
                "," +
                redeem_id +
                ')">' + btn_text + '</button>\n';
          }else{
            output += '<button class="btn btn-danger">Expired </button>\n';
          }
          output += "</div>\n";
          output += "</div>\n";
          output += "</li>";

        });
        $("#offersList")
            .hide()
            .html(output)
            .fadeIn("slow");
        $("#offersModal").modal("show");
      }
    }
  });
}

// confirm checkout
function confirmCheckout(customerId, offerId, redeem_id) {
  var conf_text = redeem_id === null ? 'Are you sure to avail this offer?' : 'Are you sure to redeem this reward?';
  if (confirm(conf_text)) {
    //make button inactive
    var btn = document.getElementById("offer_" + offerId);
    btn.disabled = true;
    btn.innerText = "Submitting...";

    var url = base_url + "/" + "confirm_offer_transaction/";
    axios
        .post(url, {
          user_id: customerId,
          offer_id: offerId,
          redeem_id: redeem_id
        })
        .then(function(response) {
          if (response.status === 200) {
            window.location = base_url + "/" + "branch/all-transactions";
          }
        })
        .catch(function(error) {
          $("#offersModal").modal("hide");
          alert(error.response.data.error);
        });
  } else {
    //ntng
  }
}

// update request status
function updateStatus(notificationID, sourceID, status, posted_on) {
  $(".notification_"+notificationID).prop('disabled', true);
  // var prev_time = moment(Date.now()).subtract(10, "minutes");
  // prev_time = prev_time.toString();
  // var created = moment(posted_on).toString();
  // $(".notification_" + notificationID).attr("disabled", true);
  // if (created < prev_time) {
  //   alert("Already expired");
  // } else {
  var url =
      base_url +
      "/" +
      "update_transaction_request/" +
      notificationID +
      "/" +
      sourceID +
      "/" +
      status;
  window.location = url;
  // }
}

//on refresh empty the customer id input field
window.onbeforeunload = function() {
  $('#customer_id').val('');
};
