(function ($) {
  $(document).ready(function () {
    let wp_liefer_loader =
      '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';

    function isJsonString(str) {
      try {
        JSON.parse(str);
      } catch (e) {
        return false;
      }
      return true;
    }

    // Quantity Change
    // $(".wp_liefer_extra_option .change_quantity").on("change", function () {
    //   let extra_option = JSON.parse(
    //     $(this).closest(".wp_liefer_extra_option").find(".extra_option").val()
    //   );

    //   extra_option.quantity = $(this).val();

    //   $(this)
    //     .closest(".wp_liefer_extra_option")
    //     .find(".extra_option")
    //     .val(JSON.stringify(extra_option));
    // });

    /**
     * ----------------------------------
     *  Extra Items
     * ----------------------------------
     */

    let checkboxExtraPrice = 0;
    let radioExtraPrice = 0;
    let selectExtraPrice = 0;
    let productPrice = parseFloat($(".wp-liefer-total-price").text());

    // Variation Extras
    $(document.body).on("change", "#pa_pizza-groessen", function () {
      $(".food_extras").html(wp_liefer_loader);

      let productId = $(this).closest("form").data("product_id");

      let quantity = parseInt($(".quantity input.qty").val());

      $.ajax({
        method: "GET",
        url: WPLiefermanagerData.ajaxurl,
        data: {
          action: "wp_liefer_show_variation_extras",
          productId: productId,
          sizeAttr: $(this).val(),
        },
        success: function (res) {
          $(".food_extras").html(res?.extra_html);

          productPrice = parseFloat(res?.price);

          let totalPriceHtml = (parseFloat(res?.price) * quantity).toFixed(2);

          $(".wp-liefer-total-price").html(totalPriceHtml);
        },
        error: function (err) {
          console.log(err);
        },
      });
    });

    /**
     * -------------------------------------
     * Live price calculation
     * -------------------------------------
     */
    // Calculate checkbox extras
    $(document).on(
      "click",
      '.wp_liefer_extra_option input[type="checkbox"]',
      function () {
        checkboxExtraPrice = 0;

        $(this).toggleClass("active");

        let options = $(
          '.wp_liefer_extra_option input[type="checkbox"].active'
        );

        let option = {};

        for (let index = 0; index < options.length; index++) {
          option = JSON.parse(jQuery(options[index]).val());

          var itemPrice = parseFloat(option?.option_price.replace(",", "."));

          checkboxExtraPrice += itemPrice;
        }

        let totalPrice =
          productPrice +
          checkboxExtraPrice +
          radioExtraPrice +
          selectExtraPrice;

        let quantity = parseInt($(".quantity input.qty").val());

        totalPrice *= quantity;

        $(".wp-liefer-total-price").text(totalPrice.toFixed(2));
      }
    );

    // Calculate radio extras
    $(document).on(
      "change",
      ".wp_liefer_extra_option input[type='radio']",
      function () {
        radioExtraPrice = 0;

        let options = $(".wp_liefer_extra_option input[type='radio']:checked");

        let option = {};

        for (let index = 0; index < options.length; index++) {
          option = JSON.parse(jQuery(options[index]).val());

          var itemPrice = parseFloat(option?.option_price.replace(",", "."));

          radioExtraPrice += itemPrice;
        }

        let totalPrice =
          productPrice +
          checkboxExtraPrice +
          radioExtraPrice +
          selectExtraPrice;

        let quantity = parseInt($(".quantity input.qty").val());

        totalPrice *= quantity;

        $(".wp-liefer-total-price").text(totalPrice.toFixed(2));
      }
    );

    // Calculate Select box extras
    $(document).on("change", ".wp_liefer_extra_option select", function () {
      selectExtraPrice = 0;

      let options = $(".wp_liefer_extra_option select");

      let option = {};

      for (let index = 0; index < options.length; index++) {
        let option_val = jQuery(options[index]).val();

        if (!isJsonString(option_val)) {
          continue;
        }

        option = JSON.parse(option_val);

        var itemPrice = parseFloat(option?.option_price.replace(",", "."));

        selectExtraPrice += itemPrice;
      }

      let totalPrice =
        productPrice + checkboxExtraPrice + radioExtraPrice + selectExtraPrice;

      let quantity = parseInt($(".quantity input.qty").val());

      totalPrice *= quantity;

      $(".wp-liefer-total-price").text(totalPrice.toFixed(2));
    });

    // Calculate price for quantity change
    $(document).on("change", ".quantity input.qty", function () {
      let totalPrice =
        productPrice + checkboxExtraPrice + radioExtraPrice + selectExtraPrice;

      let quantity = parseInt($(this).val());

      totalPrice *= quantity;

      $(".wp-liefer-total-price").text(totalPrice.toFixed(2));
    });
  });
})(jQuery);
