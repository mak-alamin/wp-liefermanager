jQuery(document).ready(function ($) {
  function calculateTip() {
    var tipsType = jQuery("#tip_option").val();
    var tipsAmount = parseFloat(jQuery("#tip_amount").val());

    if (!tipsType || !tipsAmount) {
      return;
    }

    var total = parseFloat(
      jQuery(".cart-subtotal .woocommerce-Price-amount.amount")
        .text()
        .replace(/[^0-9.-]+/g, "")
    );

    if (tipsType === "percent") {
      tipsAmount = total * (tipsAmount / 100);
    }

    document.getElementById("order_review").scrollIntoView();

    jQuery.ajax({
      method: "POST",
      url: WPLiefermanagerData.ajaxurl,
      data: {
        action: "wp_liefer_add_tip",
        tip_option: tipsType,
        tip_amount: tipsAmount,
      },
      success: function (res) {
        if (res.new_total) {
          jQuery(".order-total td strong").html(res.new_total);
        }

        jQuery(document.body).trigger("update_checkout");
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
      jQuery("#tip_amount_field").show();
      jQuery(".calculate-tip").show();
      jQuery(".remove-tip").show();
    }
  });

  $("body").on("click", ".remove-tip", function (e) {
    e.preventDefault();

    jQuery.ajax({
      method: "POST",
      url: WPLiefermanagerData.ajaxurl,
      data: {
        action: "wp_liefer_remove_tip",
      },
      success: function (res) {
        jQuery(document.body).trigger("update_checkout");

        jQuery("#tip_option").val("");
        jQuery("#tip_amount_field").hide();
        jQuery(".calculate-tip").hide();
        $(e.target).hide();
      },
      error: function (err) {
        console.log(err);
      },
    });
  });
});
