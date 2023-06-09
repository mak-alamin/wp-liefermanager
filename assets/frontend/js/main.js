(function ($) {
  var wp_liefer_loader =
    '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';

  $(document).ready(function () {
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
    //   var extra_option = JSON.parse(
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

    var checkboxExtraPrice = 0;
    var radioExtraPrice = 0;
    var selectExtraPrice = 0;
    var productPrice = parseFloat($(".wp-liefer-total-price").text());

    // Variation Extras
    $(document.body).on("change", "#pa_pizza-groessen", function () {
      $(".food_extras").html(wp_liefer_loader);

      var productId = $(this).closest("form").data("product_id");

      var quantity = parseInt($(".quantity input.qty").val());

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

          var totalPriceHtml = (parseFloat(res?.price) * quantity).toFixed(2);

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

        var options = $(
          '.wp_liefer_extra_option input[type="checkbox"].active'
        );

        var option = {};

        for (var index = 0; index < options.length; index++) {
          option = JSON.parse(jQuery(options[index]).val());

          var itemPrice = parseFloat(option?.option_price.replace(",", "."));

          checkboxExtraPrice += itemPrice;
        }

        var totalPrice =
          productPrice +
          checkboxExtraPrice +
          radioExtraPrice +
          selectExtraPrice;

        var quantity = parseInt($(".quantity input.qty").val());

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

        var options = $(".wp_liefer_extra_option input[type='radio']:checked");

        var option = {};

        for (var index = 0; index < options.length; index++) {
          option = JSON.parse(jQuery(options[index]).val());

          var itemPrice = parseFloat(option?.option_price.replace(",", "."));

          radioExtraPrice += itemPrice;
        }

        var totalPrice =
          productPrice +
          checkboxExtraPrice +
          radioExtraPrice +
          selectExtraPrice;

        var quantity = parseInt($(".quantity input.qty").val());

        totalPrice *= quantity;

        $(".wp-liefer-total-price").text(totalPrice.toFixed(2));
      }
    );

    // Calculate Select box extras
    $(document).on("change", ".wp_liefer_extra_option select", function () {
      selectExtraPrice = 0;

      var options = $(".wp_liefer_extra_option select");

      var option = {};

      for (var index = 0; index < options.length; index++) {
        var option_val = jQuery(options[index]).val();

        if (!isJsonString(option_val)) {
          continue;
        }

        option = JSON.parse(option_val);

        var itemPrice = parseFloat(option?.option_price.replace(",", "."));

        selectExtraPrice += itemPrice;
      }

      var totalPrice =
        productPrice + checkboxExtraPrice + radioExtraPrice + selectExtraPrice;

      var quantity = parseInt($(".quantity input.qty").val());

      totalPrice *= quantity;

      $(".wp-liefer-total-price").text(totalPrice.toFixed(2));
    });

    // Calculate price for quantity change
    $(document).on("change", ".quantity input.qty", function () {
      var totalPrice =
        productPrice + checkboxExtraPrice + radioExtraPrice + selectExtraPrice;

      var quantity = parseInt($(this).val());

      totalPrice *= quantity;

      $(".wp-liefer-total-price").text(totalPrice.toFixed(2));
    });
  });

  /**
   * ------------------------------
   * Products Layout
   * ------------------------------
   */
  $(function () {
    $(".cat-title-tabs .tab-menu").on("click", "a", function (event) {
      event.preventDefault();
      $(this).closest(".tab-menu").find("li").removeClass("active");
      $(this).parent().addClass("active");
      var target = $(this).attr("href");
      $(target).siblings(".tab-pane").removeClass("active");
      $(target).addClass("active");
    });
  });

  var tableId = new URLSearchParams(window.location.search).get("table_id");

  var tableInfo = tableId ? 'data-table_id="' + tableId + '"' : "";

  // Product Popup
  var product_modal =
    '<div id="productModal" class="modal"><div class="modal-content"><span class="close">&times;</span><div class="modal-body"><iframe src="" id="productEmbed" ' +
    tableInfo +
    "></iframe></div></div></div>";

  $("body").prepend(product_modal);

  $(document).on(
    "click",
    ".wpliefer-product-layout a.add_to_cart",
    function (e) {
      e.preventDefault();

      var productDiv = $(this).closest(".product");

      productDiv.block({
        message: wp_liefer_loader,
      });

      var productUrl = $(this).attr("href");

      $("#productModal .modal-body iframe").attr("src", productUrl);
    }
  );

  $("iframe#productEmbed").on("load", function () {
    console.log("iframe loaded");

    hideUnwantedElementsfromIframe();

    $("#productModal").show();

    var addedToCart = $("iframe#productEmbed").contents().find(".wc-forward");

    if (addedToCart.length) {
      $("body").unblock();
      $("#productModal").hide();
    }

    if ($("iframe#productEmbed").data("table_id")) {
      var tableId = $("iframe#productEmbed").data("table_id");

      var tableInput =
        '<input type="hidden" name="table_id" value="' + tableId + '" />';

      $($("iframe#productEmbed").contents().find("form.cart")[0]).append(
        tableInput
      );
    }

    $(".wpliefer-product-layout .product").unblock();
  });

  // Hide Unwanted Elements
  function hideUnwantedElementsfromIframe() {
    // Find and hide the element within the iframe
    $("iframe#productEmbed").contents().find("#wpadminbar").remove();
    $("iframe#productEmbed").contents().find("header").remove();
    $("iframe#productEmbed").contents().find(".header").remove();
    $("iframe#productEmbed").contents().find("footer").remove();
    $("iframe#productEmbed").contents().find(".footer").remove();

    $("iframe#productEmbed")
      .contents()
      .find(".elementor-location-header")
      .remove();

    $("iframe#productEmbed")
      .contents()
      .find(".elementor-location-footer")
      .remove();

    $("iframe#productEmbed").contents().find(".related.products").remove();

    $("iframe#productEmbed")
      .contents()
      .find(".woocommerce-store-notice")
      .remove();

    $("iframe#productEmbed").contents().find(".demo_store").remove();

    $("iframe#productEmbed")
      .contents()
      .find(".woocommerce-breadcrumb")
      .closest(".elementor-section")
      .remove();

    $("iframe#productEmbed")
      .contents()
      .find(".elementor-heading-title:not(.product_title)")
      .closest(".elementor-section")
      .remove();

    $("iframe#productEmbed").contents().find(".product_meta").remove();

    $("iframe#productEmbed")
      .contents()
      .find(".product_meta")
      .closest(".elementor-element")
      .remove();

    $("iframe#productEmbed")
      .contents()
      .find(".elementor-button-wrapper")
      .remove();
    $("iframe#productEmbed")
      .contents()
      .find(".elementor-button-wrapper")
      .closest(".elementor-element")
      .remove();
  }

  $(document).on("click", ".modal .close", function () {
    $(this).closest(".modal").hide();
  });

  $(document).on("click", ".single_add_to_cart_button", function () {
    $(this).text("Hinzufügen...");

    $("body").block({ message: wp_liefer_loader });
  });

  /**
   * ------------------------------------------
   * Branch Selection Popup
   * ------------------------------------------
   */
  $(document).ready(function () {
    var branchesData = WPLiefermanagerData.branches;

    var branch_select = '<select id="wp_liefer_branch_select">';

    branchesData.forEach(function (branch, i) {
      // '{"id": 32, "name": "Uttara Branch"}'

      var branchData = "{'id':" + branch.term_id + ",'name':'" + branch.name + "'}";

      branch_select +=
        '<option value="' + branchData + '">' + branch.name + "</option>";
    });

    branch_select += "</select>";

    var branch_save_button = '<button id="wp_liefer_save_branch"> OK </button>';

    var branch_modal =
      '<div id="branchModal" class="modal"><div class="modal-content"><span class="close">&times;</span><div class="modal-body"><h2>Wählen Sie Filiale</h2>' +
      branch_select +
      branch_save_button +
      "</div></div></div>";

    $("body").prepend(branch_modal);

    var branchNotSelected = !localStorage.getItem("wp_liefer_selected_branch") || localStorage.getItem("wp_liefer_selected_branch") == '';

    if(branchNotSelected){ $("#branchModal").css({display: 'flex'}) }

    $("#wp_liefer_save_branch").on("click", function () {
      var selected_branch = $("#wp_liefer_branch_select").val();

      localStorage.setItem("wp_liefer_selected_branch", selected_branch);

      document.cookie = "wp_liefer_selected_branch=" + selected_branch + ";path=/";

      $("#branchModal").hide();
    });
  });
})(jQuery);
