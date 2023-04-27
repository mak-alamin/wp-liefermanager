(function ($) {
  $(document).ready(function () {
    // Quantity Change
    $(".wp_liefer_extra_option .change_quantity").on("change", function () {
      let extra_option = JSON.parse(
        $(this).closest(".wp_liefer_extra_option").find(".extra_option").val()
      );

      extra_option.quantity = $(this).val();

      $(this)
        .closest(".wp_liefer_extra_option")
        .find(".extra_option")
        .val(JSON.stringify(extra_option));
    });

    // Variation Extras
    jQuery(document.body).on("change", "#pa_pizza-groessen", function () {
      $(".food_extras").html("Wird geladen...");

      let productId = $(this).closest("form").data("product_id");

      jQuery.ajax({
        method: "GET",
        url: WPLiefermanagerData.ajaxurl,
        data: {
          action: "wp_liefer_show_variation_extras",
          productId: productId,
          sizeAttr: $(this).val(),
        },
        success: function (res) {
          $(".food_extras").html(res);
        },
        error: function (err) {
          console.log(err);
        },
      });
    });
  });
})(jQuery);
