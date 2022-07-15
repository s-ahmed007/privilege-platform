var base_url = window.location.origin;

function ResetFilter() {
  if (localStorage.getItem("sorted_deals_object") != null) {
    //sort with prev sorted list if exists
    $(".page_loader").fadeIn(); //show loading gif
    var category = null;
    var area = null;
    var price = null;
    var rating = null;

    var sorted_object = localStorage.getItem("sorted_deals_object");
    var parsedObject = JSON.parse(sorted_object);
    sortedVoucherList(category, area, price, rating, parsedObject);
  }
}

function getFullObject(category, area, price, rating) {
  var url = base_url + "/" + "sort-deals";
  $.ajax({
    type: "POST",
    url: url,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      _token: $('meta[name="csrf-token"]').attr("content"),
      category: 'all'
    },
    success: function (data) {
      //append filtered vouchers in view page
      localStorage.setItem("sorted_deals_object", JSON.stringify(data));
      sortedVoucherList(category, area, price, rating, data);
    },
  });
}

$("#filter-category").change(function () {
  $(".page_loader").fadeIn(); //show loading gif
  var category = $("#filter-category").val();
  var area = $("#filter-area").val();
  var price = $("#filter-price").val();
  var rating = $('input[name=rating]').val();

  if (localStorage.getItem("sorted_deals_object") != null) {
    //sort with prev sorted list if exists
    var sorted_object = localStorage.getItem("sorted_deals_object");
    var parsedObject = JSON.parse(sorted_object);
    sortedVoucherList(category, area, price, rating, parsedObject);
    return false;
  }
  getFullObject(category, area, price, rating);
});

$("#filter-area").change(function () {
  $(".page_loader").fadeIn(); //show loading gif
  var category = $("#filter-category").val();
  var area = $(this).val();
  var price = $("#filter-price").val();
  var rating = $('input[name=rating]').val();

  if (localStorage.getItem("sorted_deals_object") != null) {
    //sort with prev sorted list if exists
    var sorted_object = localStorage.getItem("sorted_deals_object");
    var parsedObject = JSON.parse(sorted_object);
    sortedVoucherList(category, area, price, rating, parsedObject);
    return false;
  }
  getFullObject(category, area, price, rating);
});

$("#filter-price").change(function () {
  $(".page_loader").fadeIn(); //show loading gif
  var category = $("#filter-category").val();
  var area = $("#filter-area").val();
  var price = $(this).val();
  var rating = $('input[name=rating]').val();

  if (localStorage.getItem("sorted_deals_object") != null) {
    //sort with prev sorted list if exists
    var sorted_object = localStorage.getItem("sorted_deals_object");
    var parsedObject = JSON.parse(sorted_object);
    sortedVoucherList(category, area, price, rating, parsedObject);
    return false;
  }
  getFullObject(category, area, price, rating);
});

$('input[name=rating]').change(function () {
  $(".page_loader").fadeIn(); //show loading gif
  var category = $("#filter-category").val();
  var area = $("#filter-area").val();
  var price = $("#filter-price").val();
  var rating = $(this).val();

  if (localStorage.getItem("sorted_deals_object") != null) {
    //sort with prev sorted list if exists
    var sorted_object = localStorage.getItem("sorted_deals_object");
    var parsedObject = JSON.parse(sorted_object);
    sortedVoucherList(category, area, price, rating, parsedObject);
    return false;
  }
  getFullObject(category, area, price, rating);
});

function sortedVoucherList(category, area, price, rating, object) {
  //sort category wise
  if (category != null) {
    var category_result = [];
    var k = 0;
    for (var i = 0; i < object.length; i++) {
      if (object[i].branch.info.partner_category == category) {
        category_result[k] = object[i];
        k++;
      }
    }
    object = category_result;
  }

  //sort area wise
  if (area != null) {
    var area_result = [];
    var k = 0;
    for (var i = 0; i < object.length; i++) {
      if (object[i].branch.partner_area === area) {
        area_result[k] = object[i];
        k++;
      }
    }
    object = area_result;
  }

  //sort price wise
  if (price != null) {
    if (price == "htl") {
      object.sort(function (a, b) {
        return b.selling_price - a.selling_price;
      });
    } else {
      object.sort(function (a, b) {
        return a.selling_price - b.selling_price;
      });
    }
  }

  //sort rating wise
  if (rating != null && rating != 'all') {
    var rating_result = [];
    var k = 0;
    for (var i = 0; i < object.length; i++) {
      if (Number(object[i].branch.info.rating.average_rating) > Number(rating)) {
        rating_result[k] = object[i];
        k++;
      }
    }
    object = rating_result;
  }

  var total_deals = object.length;
  total_deals += object.length > 1 ? ' deals' : ' deal';
  $(".deal_count").text(total_deals);
  //remove duplicate voucher
  // object = checkDuplicateInObject(object);
  var deals_div = $("#deals_section");
  var output = "";
  if (object.length === 0) {
    output += '<div class="col-md-12 col-sm-12 col-xs-12">';
    output += '<div>';
    output += '<p>No Deals Found!</p>';
    output += '</div>';
    output += '</div>';

    deals_div.empty().html(output);
    $(".page_loader").fadeOut(); //hide loading gif
  } else {
    for (i = 0; i < object.length; i++) {
      var pname = object[i].branch.info.partner_name.replace("'", "");
      var pro_url =
        base_url + "/partner-profile/" + pname + "/" + object[i].branch.id;
      var deal_url = base_url + "/deals/" + object[i].branch_id;
      var image = "";
      if (object[i].branch.info.profile_image.partner_cover_photo != null) {
        image = object[i].branch.info.profile_image.partner_cover_photo;
      } else {
        image =
          "https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/offers/nobanner.png";
      }
      output += '<div class="col-sm-6 col-md-3 col-lg-3 mt-3">';
      output += '<div class="card card-inverse card-info">';
      output +=
        '<a href="' +
        pro_url +
        '">' +
        '<img class="card-img-top"' +
        '" alt="royalty-partner-cover" src="' +
        image +
        '">';
      output += '<div class="card-block">';
      output += '<h4 class="card-title card-partner-name">' + object[i].branch.info.partner_name + '</h4>';
      output += '<div class="card-text">';
      output += '<p>';
      output += object[i].branch.partner_area + ", " + object[i].branch.partner_division;
      output += '</p>';
      output += '<p>' + object[i].branch.info.partner_type + '</p>';
      output += '</div>';
      output += '</div>';
      output += '<div class="card-footer">';
      output += '<small>' + object[i].heading + '</small>';
      output += '<button class="btn float-right btn-sm">';
      var ratings = ["1.00", "2.00", "3.00", "4.00", "5.00"];
      if (object[i].branch.info.rating.average_rating == 0) {
        output += '<p class="partner-box-info-rating">New</p>';
      } else if (
        ratings.includes(object[i].branch.info.rating.average_rating)
      ) {
        output += '<i class="bx bxs-star yellow"></i>';
        output += '<p class="partner-box-info-rating">';
        output += Math.round(object[i].branch.info.rating.average_rating * 10) / 10 + ".0";
        output += '</p>';
      } else {
        output += '<i class="bx bxs-star yellow"></i>';
        output += '<p class="partner-box-info-rating">';
        output += Math.round(object[i].branch.info.rating.average_rating * 10) / 10;
        output += '</p>';
      }
      output += '</button>';
      output += '</div>';
      output += '</a>';
      output += '<a href="' + deal_url + '">';
      output += '<div class="deal-buy-btn btn-primary-thin">Buy Now</div>';
      output += '</a>';
      output += '</div>';
      output += '</div>';
    }

    deals_div.empty().html(output).fadeIn("slow");
    $(".page_loader").fadeOut(); //hide loading gif
  }
}

function checkDuplicateInObject(inputArray) {
  var object = inputArray
    .map(JSON.stringify)
    .reverse()
    .filter(function (item, index, inputArray) {
      return inputArray.indexOf(item, index + 1) === -1;
    })
    .reverse()
    .map(JSON.parse);
  return object;
}

//remove specific local storage item if page is refreshed
window.onbeforeunload = function () {
  localStorage.removeItem("sorted_deals_object");
};
//unset localstorage value ends
console.clear();
if ($("#filterForm")) {
  document.getElementById("filterForm").reset(); //reset all fields of filter on page refresh specially from back button
}
