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
  });
})(jQuery);
