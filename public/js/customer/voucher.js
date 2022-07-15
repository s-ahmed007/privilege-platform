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
    var my_rewards = JSON.parse(localStorage.getItem("my_vouchers"));
    var total_amount = 0;

    if (my_rewards.length !== 0) {
        var output = "";
        my_rewards.forEach(function (reward) {
            var counter_btn = $(".reward_counter_" + reward.id);
            var amount = reward.selling_price * Number(counter_btn.val());
            total_amount += amount;

            $(".pls_add").css("display", "none");

            output += '<div class="row"> <div class="col-md-6 col-sm-6 col-xs-6">\n';
            output += "<p class=\"dots\">" + reward.heading + "</p>\n";
            output += "</div>\n";
            output += '<div class="col-md-3 col-sm-3 col-xs-3">x' + counter_btn.val() + "</div>\n";
            output +=
                '<div class="col-md-3 col-sm-3 col-xs-3"><span class="points">' +
                amount +
                "</span></div></div>\n";
        });

        output += "<div class=\"row\" id=\"credit_used_div\" style=\"color: red; display: none\"> <div class=\"col-md-9 col-sm-9 col-xs-6\">\n";
        output += "<p class=\"dots\">Credit Used</p>\n";
        output += "</div>\n";
        output += "<div class=\"col-md-3 col-sm-3 col-xs-3\"><span class=\"points\" id=\"user_credit_used\">" +
            $("#user_credits_checkbox").val() +
            "</span></div></div>\n";

        output +=
            '<div class="row" id="total_deal_amount_div" style="border-top: 1px solid;"><div class="col-md-6 col-sm-6 col-xs-6"><p>Total</p></div>\n';
        output += '<div class="col-md-3 col-sm-3 col-xs-3"></div>';
        output += '<div class="col-md-3 col-sm-3 col-xs-3"><p class="points" id="total_deal_amount">' +
            total_amount +
            '</p></div></div>\n';

        $("#total_deal_price").val(total_amount);
        $("#reward_redeem_summery")
            .hide()
            .html(output).css('padding', '2rem')
            .fadeIn("slow");
        var _function = '';
        _function = 'redeemRewardConfirm()';
        // _function = 'redeemRewardNext()';

        $("#redeem_confirm_button").prop('disabled', false).attr("onclick", _function).css('background-color', 'limegreen');
        if ($(".user_credits_checkbox").length != 0) {
            $(".user_credits_checkbox").css('display', 'inline-block');
            creditUsed();
        }
        //checkbox for guest or expired member
        if ($("#guest_credits_checkbox").length != 0) {
            $("#guest_credits_checkbox").css('display', 'inline-block');
        }

        return true;
    } else {
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
    var available_reward = $(".available_reward_" + reward.id);
    var counter = available_reward.text();
    var redeemed_counter = Number($(".redeemed_counter_" + reward.id).val());
    if (action) {
        var available = Number(counter) - 1;
        available_reward.text(available);
        if (available <= 0) {
            $(".counter_increment_" + reward.id).prop('disabled', true);
        }
        redeemed_counter += 1;
        $(".redeemed_counter_" + reward.id).val(redeemed_counter);
    } else {
        var available = Number(counter) + 1;
        available_reward.text(available);
        if (available > 0) {
            $(".counter_increment_" + reward.id).prop('disabled', false);
        }
        redeemed_counter -= 1;
        $(".redeemed_counter_" + reward.id).val(redeemed_counter);
    }
    //update redeemed reward counter
    if (reward.scan_limit != null && Number($(".redeemed_counter_" + reward.id).val()) >= reward.scan_limit) {
        $(".counter_increment_" + reward.id).css('background-color', 'gray');
        // toastr.error('You can not purchase more than '+reward.scan_limit+' deals');
        // return false;
    }
    // return true;
}

function addReward(reward, entry) {
    "use strict";
    if (entry) {
        if (!rewardarray.includes(reward)) {
            //add reward to local storage
            rewardarray.push(reward);
            localStorage.setItem("my_vouchers", JSON.stringify(rewardarray));
        }
    } else {
        rewardarray.forEach(function (row, index) {
            if (row.id === reward.id) {
                //remove reward from local storage
                if (index > -1) {
                    rewardarray.splice(index, 1);
                    localStorage.setItem("my_vouchers", JSON.stringify(rewardarray));
                }
            }
        });
    }
}

function updateOnclickOnRedeem() {
    "use strict";
    var reward_array = JSON.parse(localStorage.getItem("my_vouchers"));
    if (reward_array.length === 0) {
        $("#redeem_confirm_button").prop('disabled', true).removeAttr('onclick');
        $(".user_credits_checkbox").css('display', 'none');
    }
}

function rewardCounter(reward, action) {
    //check if user is purchasing more than scan limit
    var redeemed_counter = Number($(".redeemed_counter_" + reward.id).val());
    if (action) {
        if (reward.scan_limit != null && redeemed_counter >= reward.scan_limit) {
            toastr.error('You can not purchase more than ' + reward.scan_limit + ' deals');
            return false;
        }
    } else {
        $(".counter_increment_" + reward.id).css('background-color', '#49df56');
        if (reward.scan_limit != null && (redeemed_counter - 1) >= reward.scan_limit) {
            toastr.error('You can not purchase more than ' + reward.scan_limit + ' deals');
            return false;
        }
    }
    //update voucher availibility
    updateRewardAvailability(reward, action);
    var counter_btn = $(".reward_counter_" + reward["id"]);
    var reward_counter = 0;
    if (action) {
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
    var myPoint = JSON.parse(localStorage.getItem("my_branch_point"));
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
    if ($("#customer_email_verified").val() == 'false') {
        //take user to profile to verify email
        $("#dealVerifyEmailModal").modal('show');
        return false;
    } else if (reward.scan_limit == 0) {
        //if user already purchased all
        toastr.error('You have already crossed the limit of purchasing this deal.');
        return false;
    }

    updateRewardAvailability(reward, true);
    var counter_btn = $(".reward_counter_" + reward.id);
    reward_counter = Number(counter_btn.val()) + 1;
    counter_btn.val(reward_counter).text(reward_counter);

    addReward(reward, true);
    if (updateRedeemSection(reward)) {
        $(".add_reward_" + reward.id).css("display", "none");
        $(".inc_dec_" + reward.id).css("display", "block");
    }
    localStorage.setItem("branch_id", reward.branch_id);
}

function redeemRewardNext() {
    'use strict';
    if ($("#canRedeemReward").val() == 'false') {
        $("#canNotRedeemRewardModal").modal('toggle');
    } else {
        $("#redeemAddressModal").modal('toggle');
    }
}

function redeemRewardConfirm() {
    "use strict";

    if (confirm("Are you sure?")) {
        var data = [];
        var my_rewards = JSON.parse(localStorage.getItem("my_vouchers"));
        var credit_used = false;
        if (document.getElementById("user_credits_checkbox") != null) {
            credit_used = document.getElementById("user_credits_checkbox").checked;
        }
        my_rewards.forEach(function (reward) {
            var counter_btn = $(".reward_counter_" + reward.id);
            var reward_details = {
                voucher_id: reward.id,
                quantity: counter_btn.val(),
            };
            data.push(reward_details);
        });

        var url = base_url + "/" + "confirm_voucher_purchase/";
        axios
            .post(url, {
                data: data,
                creditUsed: credit_used
            })
            .then(function (response) {
                if (response.status === 200) {
                    var result = response.data;
                    var url = base_url + '/submit_voucher_to_ssl';

                    $('<form action="' + url + '" method="POST">' +
                        '<input type="hidden" name="_token" value="' + result.csrf + '"/>' +
                        '<input type="hidden" name="customer_id" value="' + result.customer + '"/>' +
                        '<input type="hidden" name="amount" value="' + result.amount + '"/>' +
                        '<input type="hidden" name="used_credits" value="' + result.used_credits + '"/>' +
                        '<input type="hidden" name="tran_id" value="' + result.tran_id + '"/>' +
                        '</form>').appendTo($(document.body)).submit();
                }
            })
            .catch(function (error) {
                alert(error.response.data);
            });
    } else {
        $("#redeemAddressModal").modal('hide');
    }
}

function creditUsed() {
    var user_credit = Number($("#user_credits_checkbox").val());
    var deal_amount = Number($("#total_deal_price").val());
    if (document.getElementById("user_credits_checkbox").checked) {
        $("#credit_used_div").css('display', 'block');
        if (user_credit >= deal_amount) {
            $("#user_credit_used").text('-'+deal_amount);
            $("#total_deal_amount").text(0);
        } else if ((deal_amount - user_credit) > 0 && (deal_amount - user_credit) < 10) {
            user_credit -= deal_amount - user_credit;
            $("#user_credit_used").text('-'+user_credit);
            $("#total_deal_amount").text(deal_amount - user_credit);
        } else {
            $("#user_credit_used").text('-'+user_credit);
            $("#total_deal_amount").text(deal_amount - user_credit);
        }
    } else {
        $("#credit_used_div").css('display', 'none');
        $("#total_deal_amount").text(deal_amount);
    }
}

$("#user_credits_checkbox").change(function (e) {
    creditUsed();
})
function premiumMembershipModal() {
    $("#premiumMembersipModal").modal('show');
    $('input[name$="user_credits"]').prop('checked', false);
    return false;
}

window.onbeforeunload = function () {
    localStorage.removeItem("my_vouchers");
};
