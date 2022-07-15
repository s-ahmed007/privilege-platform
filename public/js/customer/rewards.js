var base_url = window.location.origin;

// script to show individual offer details modal & open specific tab
$(document).ready(function () {
  $(".offerDetails").click(function () {
    var offer_id = $(this).data("offer-id");
    var offer_tab = $(this).data("offer-tab");
    if (offer_tab === "details") {
      $('a[href^="#tncTab' + offer_id + '"]')
        .parent()
        .removeClass("active");
      $('a[href^="#detailsTab' + offer_id + '"]')
        .parent()
        .addClass("active");
      $("#tncTab" + offer_id).removeClass("active");
      $("#detailsTab" + offer_id).addClass("active");
    } else if (offer_tab === "tnc") {
      $('a[href^="#detailsTab' + offer_id + '"]')
        .parent()
        .removeClass("active");
      $('a[href^="#tncTab' + offer_id + '"]')
        .parent()
        .addClass("active");
      $("#detailsTab" + offer_id).removeClass("active");
      $("#tncTab" + offer_id).addClass("active");
    }
    $("#offerDetails_" + offer_id).modal("show");
  });
});

function updateRedeemSection(cur_reward) {
  "use strict";
  var my_rewards = JSON.parse(localStorage.getItem("my_rewards"));
  var my_branch_point = Number(JSON.parse(localStorage.getItem("my_branch_point")));
  var total_points = 0;

  if (my_rewards.length !== 0) {
    var output = "";
    my_rewards.forEach(function (reward) {
      var counter_btn = $(".reward_counter_" + reward.id);
      var points = reward.selling_point * Number(counter_btn.val());
      total_points += points;

      if (total_points > my_branch_point) {
        rewardCounter(cur_reward, false);
        alert("Sorry, you do not have sufficient credits.");
        return false;
      }
      $(".pls_add").css("display", "none");

      output += '<div class="row"> <div class="col-md-6 col-sm-6 col-xs-6">\n';
      output += "<p>" + reward.offer_description + "</p>\n";
      output += "</div>\n";
      output += '<div class="col-md-3 col-sm-3 col-xs-3">x' + counter_btn.val() + "</div>\n";
      output +=
        '<div class="col-md-3 col-sm-3 col-xs-3"><span class="points">' +
        points +
        "</span></div></div>\n";
    });
    if (my_branch_point >= total_points) {
      $(".remaining_point").text(my_branch_point - total_points);
      output +=
        '<div class="row" style="border-top: 1px solid;"><div class="col-md-6 col-sm-6 col-xs-6"><p>Total</p></div>\n';
      output += '<div class="col-md-3 col-sm-3 col-xs-3"></div>';
      output +=
        '<div class="col-md-3 col-sm-3 col-xs-3"><p class="points">' +
        total_points +
        "</p></div></div>\n";
      $("#reward_redeem_summery")
        .hide()
        .html(output).css('padding', '2rem')
        .fadeIn("slow");
      //check if req fields set or not
      var req_phone = $("#req_phone").val();
      var req_email = $("#req_email").val();
      var req_del_add = $("#req_del_add").val();
      var req_others = $("#req_others").val();
      var _function = '';
      if (typeof req_phone === 'undefined' && typeof req_email === 'undefined' && typeof req_del_add === 'undefined'
        && typeof req_others === 'undefined') {
        _function = 'redeemRewardConfirm()';
      } else {
        _function = 'redeemRewardNext()';
      }
      $("#redeem_confirm_button").prop('disabled', false).attr("onclick", _function).css('background-color', 'limegreen');

      return true;
    } else {
      return false;
    }
  } else {
    $(".remaining_point").text(my_branch_point - total_points);
    $("#reward_redeem_summery").empty().css('padding', 'unset');
    $(".pls_add").css("display", "block");
    return false;
  }
}

var idarray = [];
var rewardarray = [];
function addRewardId(id, entry) {
  //not using right now
  if (entry) {
    if (!idarray.includes(id)) {
      //add reward id to array
      idarray.push(id);
      $(".reward_ids").val(idarray);
    }
  } else {
    if (idarray.includes(id)) {
      //remove reward id from array
      var index = idarray.indexOf(id);
      if (index > -1) {
        idarray.splice(index, 1);
      }
      $(".reward_ids").val(idarray);
    }
  }
}

function updateRewardAvailability(reward, action) {
  'use strict';
  var available_reward = $(".available_reward");
  var counter = available_reward.text();
  var redeemed_counter = Number($(".redeemed_counter").val());
  if (action) {
    var available = Number(counter) - 1;
    available_reward.text(available);
    if (available <= 0) {
      $(".counter_increment").prop('disabled', true);
    }
    redeemed_counter += 1;
    $(".redeemed_counter").val(redeemed_counter);
  } else {
    var available = Number(counter) + 1;
    available_reward.text(available);
    if (available > 0) {
      $(".counter_increment").prop('disabled', false);
    }
    redeemed_counter -= 1;
    $(".redeemed_counter").val(redeemed_counter);
  }
  //update redeemed reward counter
  if (getHighestQuantity(reward) === Number($(".redeemed_counter").val())) {
    $(".counter_increment").prop('disabled', true);
  }
}
function addReward(reward, entry) {
  "use strict";
  if (entry) {
    if (!rewardarray.includes(reward)) {
      //add reward to local storage
      rewardarray.push(reward);
      localStorage.setItem("my_rewards", JSON.stringify(rewardarray));
    }
  } else {
    rewardarray.forEach(function (row, index) {
      if (row.id === reward.id) {
        //remove reward from local storage
        if (index > -1) {
          rewardarray.splice(index, 1);
          localStorage.setItem("my_rewards", JSON.stringify(rewardarray));
        }
      }
    });
  }
}

function updateOnclickOnRedeem() {
  "use strict";
  var reward_array = JSON.parse(localStorage.getItem("my_rewards"));
  if (reward_array.length === 0) {
    $("#redeem_confirm_button").prop('disabled', true).removeAttr('onclick');
  }
}

function rewardCounter(reward, range) {
  updateRewardAvailability(reward, range);
  var counter_btn = $(".reward_counter_" + reward["id"]);
  var reward_counter = 0;
  if (range) {
    reward_counter = Number(counter_btn.val()) + 1;
    counter_btn.val(reward_counter).text(reward_counter);
    // addRewardId(reward['id'], true);
    updateRedeemSection(reward);
  } else {
    reward_counter = Number(counter_btn.val()) - 1;
    counter_btn.val(reward_counter).text(reward_counter);
    if (reward_counter < 1) {
      counter_btn.val(0);
      $(".inc_dec_" + reward["id"]).css("display", "none");
      $(".add_reward_" + reward["id"]).css("display", "inline-block");
      // addRewardId(reward['id'], false);
      addReward(reward, false);
      updateOnclickOnRedeem();
    }
    updateRedeemSection(reward);
  }
}
function getHighestQuantity(reward) {
  var available_reward = $(".available_reward").text();
  var selling_point = reward.selling_point;
  var myPoint = Number(JSON.parse(localStorage.getItem("my_branch_point")));
  var myHighestGetting = (myPoint / selling_point);
  if (reward.scan_limit != null && myHighestGetting >= reward.scan_limit) {
    return reward.scan_limit;
  } else if (myHighestGetting >= available_reward) {
    return available_reward;
  } else {
    return myHighestGetting;
  }
}

function addRewardToCart(reward) {
  updateRewardAvailability(reward, true);
  var counter_btn = $(".reward_counter_" + reward.id);
  reward_counter = Number(counter_btn.val()) + 1;
  counter_btn.val(reward_counter).text(reward_counter);

  addReward(reward, true);
  if (updateRedeemSection(reward)) {
    $(".add_reward_" + reward.id).css("display", "none");
    $(".inc_dec_" + reward.id).css("display", "block");
  }
}

function redeemRewardNext() {
  'use strict';
  if ($("#canRedeemReward").val() == 'false') {
    $("#canNotRedeemRewardModal").modal('toggle');
  } else {
    $("#redeemAddressModal").modal('toggle');
  }
}

function checkReqFields() {
  'use strict';
  var req_phone = $("#req_phone").val();
  var req_email = $("#req_email").val();
  var req_del_add = $("#req_del_add").val();
  var req_others = $("#req_others").val();
  var ret_val = true;
  var all_fields = [];

  if (typeof req_phone !== 'undefined') {
    var filter_phone = /^(?:\+88|01)?(?:\d{11}|\d{13})$/;
    if (!filter_phone.test(req_phone)) {
      ret_val = false;
      $(".req_phone_error").text('Please provide a valid phone.');
    } else {
      ret_val = true;
      var phone = {};
      phone.type = 0;
      phone.value = req_phone;
      all_fields.push(phone);
      $(".req_phone_error").empty();
    }
  }
  if (typeof req_email !== 'undefined') {
    var filter_email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!filter_email.test(req_email)) {
      $(".req_email_error").text('Please provide a valid email.');
      ret_val = false;
    } else {
      ret_val = true
      var email = {};
      email.type = 1;
      email.value = req_email;
      all_fields.push(email);
      $(".req_email_error").empty();
    }
  }
  if (typeof req_del_add !== 'undefined') {
    if (req_del_add === '') {
      $(".req_del_add_error").text('Please provide a delivery address.');
      ret_val = false;
    } else {
      ret_val = true;
      var del_add = {};
      del_add.type = 2;
      del_add.value = req_del_add;
      all_fields.push(del_add);
      $(".req_del_add_error").empty();
    }
  }
  if (typeof req_others !== 'undefined') {
    if (req_others === '') {
      $(".req_others_error").text('Please write something.');
      ret_val = false;
    } else {
      ret_val = true;
      var others = {};
      others.type = 3;
      others.value = req_others;
      all_fields.push(others);
      $(".req_others_error").empty();
    }
  }
  return { 'fields': all_fields, 'ret_val': ret_val };
}

function redeemRewardConfirm() {
  "use strict";
  if ($("#canRedeemReward").val() == 'false') {
    $("#canNotRedeemRewardModal").modal('toggle');
    return false;
  }
  var result = checkReqFields();

  if (result.ret_val === false) {
    return false;
  }

  if (confirm("Are you sure?")) {
    var data = [];
    var my_rewards = JSON.parse(localStorage.getItem("my_rewards"));
    var partner_type = $(".rewards_of").val();

    my_rewards.forEach(function (reward) {
      var counter_btn = $(".reward_counter_" + reward.id);
      var reward_details = {
        offer_id: reward.id,
        quantity: counter_btn.val(),
        type: partner_type,
        required_fields: JSON.stringify(result.fields)
      };
      data.push(reward_details);
    });

    var url = base_url + "/" + "reward_redeem_confirm/";
    axios
      .post(url, {
        data: data
      })
      .then(function (response) {
        if (response.status === 200) {
          if (my_rewards[0].branch_id === 999999997) {
            localStorage.setItem("reward_redeemed_success_msg", "Your reward request is successful. Our team will contact " +
              "you soon.");
            // $(".redeem_success_msg").text('');
          } else {
            localStorage.setItem("reward_redeemed_success_msg", "Your reward request is successful. On your next visit to " +
              "this merchant, scan the QR at their outlet to redeem your reward.");
            // $(".redeem_success_msg").text('');
          }
          window.location.href = base_url + '/users/' + response.data + '/rewards';
          // $("#redeemAddressModal").modal('hide');
          // $("#redeemSuccessModal").modal("show");
          // setTimeout(function() {
          //   window.location = window.location.href;
          // }, 3000);
        }
      })
      .catch(function (error) {
        alert(error.response.data.error);
      });
  } else {
    $("#redeemAddressModal").modal('hide');
  }
}

window.onbeforeunload = function () {
  localStorage.removeItem("my_rewards");
};
