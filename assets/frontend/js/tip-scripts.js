jQuery(document).ready(function ($) {
  function calculateTip() {
    var tipsType = $("#tip_option").val();
    var tipsAmount = parseFloat($("#tip_amount").val());

    if (!tipsAmount) {
      return;
    }

    var total = parseFloat(
      $(".cart-subtotal .woocommerce-Price-amount.amount")
        .text()
        .replace(/[^0-9.-]+/g, "")
    );

    if (tipsType === "percent") {
      tipsAmount = total * (tipsAmount / 100);
    }

    document.getElementById("order_review").scrollIntoView();

    $.ajax({
      method: "POST",
      url: WPLiefermanagerData.ajaxurl,
      data: {
        action: "wp_liefer_add_tip",
        tip_option: tipsType,
        tip_amount: tipsAmount,
      },
      success: function (res) {
        if (res.new_total) {
          $(".order-total td strong").html(res.new_total);
        }

        $(document.body).trigger("update_checkout");
      },
      error: function (err) {
        console.log(err);
      },
    });
  }

  $("body").on("click", ".calculate-tip", function (e) {
    e.preventDefault();
    calculateTip();
  });

  $("body").on("change", "#tip_option", function (e) {
    e.preventDefault();

    if ($(this).val()) {
      $("#tip_amount_field").show();
      $(".calculate-tip").show();
      $(".remove-tip").show();
    }
  });

  $("body").on("click", ".remove-tip", function (e) {
    e.preventDefault();

    $.ajax({
      method: "POST",
      url: WPLiefermanagerData.ajaxurl,
      data: {
        action: "wp_liefer_remove_tip",
      },
      success: function (res) {
        $(document.body).trigger("update_checkout");

        $("#tip_option").val("");
        $("#tip_amount_field").hide();
        $(".calculate-tip").hide();
        $(".remove-tip").hide();
      },
      error: function (err) {
        console.log(err);
      },
    });
  });
});
