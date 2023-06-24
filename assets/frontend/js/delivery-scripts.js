jQuery(document).ready(function ($) {
  // Check if first load happens
  // if (!getCookieValue("wp_liefer_first_load")) {
  //   document.cookie =
  //     "wp_liefer_first_load=1;path=/";
  // }

  /**
   * ------------------------------------------
   *  Delivery Option & Branch Selection Popup
   * ------------------------------------------
   */
  var delivery_type = WPLiefermanagerData.settings.delivery_type;
  var branch_option = WPLiefermanagerData.settings.branch_option;

  var delivery_option = "delivery";

  if (delivery_type == "pickup_only") {
    delivery_option = "pickup";
  } else if (delivery_type == "delivery_only") {
    delivery_option = "delivery";
  } else if (delivery_type == "disable") {
    delivery_option = "";
  } else {
    delivery_option = getCookieValue("wp_liefer_delivery_option")
      ? getCookieValue("wp_liefer_delivery_option")
      : "delivery";
  }

  function generateBranchSelect() {
    var branchesData = WPLiefermanagerData.branches;

    var branch_select =
      '<label for="wp_liefer_branch_select">Filiale</label><select id="wp_liefer_branch_select" name="wp_liefer_branch_select">';

    branchesData.forEach(function (branch, i) {
      // '{"id": 32, "name": "Uttara Branch"}'

      var branchData =
        "{'id':" + branch.term_id + ",'name':'" + branch.name + "'}";

      branch_select +=
        '<option value="' + branchData + '">' + branch.name + "</option>";
    });

    branch_select += "</select>";

    return branch_select;
  }

  function zipcodeInputHtml() {
    return '<label for="wp_liefer_user_zipcode">Bitte geben sie ihre PLZ ein</label><input type="text" id="wp_liefer_user_zipcode" name="wp_liefer_user_zipcode" placeholder="Bitte geben sie ihre PLZ ein" required> <p class="zipcode-error error-text"></p>';
  }

  // Show / Hide Branch Modal
  function showHideBranchModal(
    branchOption = "multi",
    deliveryType = "delivery"
  ) {
    var deliveryOptionSelected = getCookieValue("wp_liefer_delivery_option");
    var branchSelected = getCookieValue("wp_liefer_selected_branch");
    var zipcodeSelected = getCookieValue("wp_liefer_user_zipcode");

    var forDelivery = deliveryType != "disable" && deliveryOptionSelected == "";

    var forBranch = branchOption == "multi" && !branchSelected;

    var forZipcode = deliveryOptionSelected == "delivery" && !zipcodeSelected;

    if (forDelivery || forBranch || forZipcode) {
      $("#branchModal").css({ display: "flex" });
    }
  }

  function getDeliveryInputValue(deliveryType) {
    var inputValue = "delivery";

    switch (deliveryType) {
      case "disable":
        inputValue = "";
        break;

      case "pickup_only":
        inputValue = "pickup";
        break;

      default:
        inputValue = "delivery";
        break;
    }

    return inputValue;
  }

  /**
   * ---------------------------------
   * Generate Delivery Info Popup
   * ---------------------------------
   */

  var branch_select = branch_option == "single" ? "" : generateBranchSelect();

  var zipcodeInput =
    delivery_type == "disable" || delivery_type == "pickup_only"
      ? ""
      : zipcodeInputHtml();

  var deliveryPickupSwitcher = `<p class="delivery-option" id="wp_liefer_delivery_option_field">
      <span class="woocommerce-input-wrapper">
          <label for="wp_liefer_delivery_option_delivery" class="radio" data-option="delivery">Lieferung</label>
          
          <label for="wp_liefer_delivery_option_pickup" class="radio" data-option="pickup">Abholung</label>
      </span>
  </p>`;

  var deliveryPickupHtml = `<div class="wp-liefer-delivery-info">
      ${deliveryPickupSwitcher}

      <form id="wp_liefer_delivery_popup_form" class="delivery-datetime-picker">
          <input type="hidden" class="input-radio" value="delivery" name="wp_liefer_delivery_option" id="wp_liefer_delivery_option_delivery" checked="checked" />
              ${branch_select} ${zipcodeInput}
              <button class="wp_liefer_save_cookies"> Starten sie meine bestellung </button>
      </form>
    
      <form id="wp_liefer_pickup_popup_form" class="pickup-datetime-picker">
        <input type="hidden" class="input-radio" value="pickup" name="wp_liefer_delivery_option" id="wp_liefer_delivery_option_pickup" checked="checked" />
          ${branch_select}
          <button class="wp_liefer_save_cookies"> Starten sie meine bestellung </button>
      </form>
    </div>`;

  var deliveryInputValue = getDeliveryInputValue(delivery_type);

  var deliveryDisabledHtml = `<form id="wp_liefer_delivery_popup_form">
        <input type="hidden" class="input-radio" value="${deliveryInputValue}" name="wp_liefer_delivery_option" id="wp_liefer_delivery_option_delivery" checked="checked" />
        ${branch_select} ${zipcodeInput}
        <button class="wp_liefer_save_cookies"> Starten sie meine bestellung </button>
    </form>`;

  var branchModalContent =
    delivery_type == "delivery_pickup"
      ? deliveryPickupHtml
      : deliveryDisabledHtml;

  var delivery_branch_modal =
    '<div id="branchModal" class="modal"><div class="modal-content"><span class="close">&times;</span><div class="modal-body">' +
    branchModalContent +
    "</div></div></div>";

  if (
    (delivery_type == "disable" && branch_option == "single") ||
    window.location.href.includes("checkout") ||
    window.location.href.includes("kasse")
  ) {
  } else {
    $("body").prepend(delivery_branch_modal);
  }

  showHideBranchModal(branch_option, delivery_type);

  setDeliveryOption(delivery_option);

  $(
    '#branchModal .delivery-option label[data-option="' + delivery_option + '"]'
  ).click();

  if (getCookieValue("wp_liefer_user_zipcode")) {
    $("form.woocommerce-checkout #billing_postcode").val(
      getCookieValue("wp_liefer_user_zipcode")
    );
  }

  /**
   * --------------------------------------------------
   * Toggle date picker based on delivery/pickup option
   * --------------------------------------------------
   */
  function switchDeliveryOption(deliveryOption) {
    if (deliveryOption == "delivery") {
      $('[for="wp_liefer_delivery_option_pickup"]').removeClass("active");
      $('[for="wp_liefer_delivery_option_delivery"]').addClass("active");

      $(".pickup-datetime-picker").hide();
      $(".delivery-datetime-picker").show();
    } else if (deliveryOption == "pickup") {
      $('[for="wp_liefer_delivery_option_delivery"]').removeClass("active");
      $('[for="wp_liefer_delivery_option_pickup"]').addClass("active");

      $(".delivery-datetime-picker").hide();
      $(".pickup-datetime-picker").show();
    }
  }

  function setDeliveryOption(deliveryOption) {
    switchDeliveryOption(deliveryOption);

    if (delivery_type != "delivery_pickup") {
      localStorage.setItem("wp_liefer_delivery_option", deliveryOption);

      document.cookie =
        "wp_liefer_delivery_option=" + deliveryOption + ";path=/";
    }

    $.ajax({
      method: "POST",
      url: WPLiefermanagerData.ajaxurl,
      data: {
        action: "wp_liefer_set_delivery_option",
        delivery_option: deliveryOption,
      },
      success: function (res) {
        $(document.body).trigger("update_checkout");
      },
      error: function (err) {
        console.log(err);
      },
    });
  }

  $("body").on(
    "change",
    'input[name="wp_liefer_delivery_option"]',
    function () {
      var deliveryOption = $(this).val();

      localStorage.setItem("wp_liefer_delivery_option", deliveryOption);
      document.cookie =
        "wp_liefer_delivery_option=" + deliveryOption + ";path=/";

      setDeliveryOption(deliveryOption);
    }
  );

  $("body").on("click", "#branchModal .delivery-option label", function () {
    var deliveryOption = $(this).data("option");

    switchDeliveryOption(deliveryOption);
  });

  /**
   * ------------------------------------------------
   * Save delivery option, branch, zipcode as cookies
   * ------------------------------------------------
   */
  function wpLieferSaveCookies(form) {
    // Save Delivery Option
    delivery_option = $(form).serializeArray()[0].value;

    if (localStorage.getItem("wp_liefer_delivery_option") != delivery_option) {
      localStorage.setItem("wp_liefer_delivery_option", delivery_option);
    }

    if (getCookieValue("wp_liefer_delivery_option") != delivery_option) {
      document.cookie =
        "wp_liefer_delivery_option=" + delivery_option + ";path=/";
    }

    // Save branch
    var branchExists = $(form).find("#wp_liefer_branch_select").length;

    var selected_branch = branchExists
      ? $(form).find("#wp_liefer_branch_select").val().replaceAll("'", '"')
      : "";

    localStorage.setItem("wp_liefer_selected_branch", selected_branch);

    document.cookie =
      "wp_liefer_selected_branch=" + selected_branch + ";path=/";

    // Save zipcode
    var user_zipcode = $(form).find("#wp_liefer_user_zipcode").val() || "";

    localStorage.setItem("wp_liefer_user_zipcode", user_zipcode);

    document.cookie = "wp_liefer_user_zipcode=" + user_zipcode + ";path=/";

    $("#branchModal").hide();

    window.location.reload();
  }

  $(document).on("submit", "#wp_liefer_delivery_popup_form", function (e) {
    e.preventDefault();

    var branchExists = $(this).find("#wp_liefer_branch_select").length;

    var selected_branch = branchExists
      ? JSON.parse(
          $(this).find("#wp_liefer_branch_select").val().replaceAll("'", '"')
        )
      : "";

    var branchId = selected_branch ? parseInt(selected_branch.id) : 0;

    var choosen_zipcode = $(this).find("#wp_liefer_user_zipcode").val().trim();

    jQuery.ajax({
      method: "POST",
      url: WPLiefermanagerData.ajaxurl,
      data: {
        action: "wp_liefer_get_zipcodes",
        branch_id: branchId,
      },
      success: function (res) {
        if (res.includes(choosen_zipcode)) {
          wpLieferSaveCookies("#wp_liefer_delivery_popup_form");
        } else {
          $(".zipcode-error").text(
            "Derzeit bieten wir keine Lieferung an diese Adresse an."
          );
        }
      },
      error: function (err) {
        console.log(err);
      },
    });
  });

  $(document).on("submit", "#wp_liefer_pickup_popup_form", function (e) {
    e.preventDefault();
    wpLieferSaveCookies($(this));
  });

  /**
   * ------------------------------------
   * Helper Functions
   * ------------------------------------
   */
  function millisecondsToTime(ms) {
    const date = new Date(ms);
    const hours = date.getHours().toString().padStart(2, "0");
    const minutes = date.getMinutes().toString().padStart(2, "0");
    return `${hours}:${minutes}`;
  }

  function getNextHalfOrFullHour(time) {
    var [hour, minute] = time.split(":");
    hour = parseInt(hour);
    minute = parseInt(minute);

    if (minute >= 30) {
      hour++;
      minute = 0;
    }

    if (minute < 30) {
      minute = 30;
    }

    if (hour >= 24) {
      hour = 0;
    }

    return `${hour
      .toString()
      .padStart(2, "0")}:${minute.toString().padStart(2, "0")}`;
  }

  function addExtraMinutes(time, minutesToAdd) {
    var parts = time.split(":");
    var hours = parseInt(parts[0]);
    var minutes = parseInt(parts[1]);

    // Add the minutes
    minutes += minutesToAdd;

    // Check if minutes are greater than 60
    if (minutes >= 60) {
      // Add the excess minutes to the next hour
      hours += Math.floor(minutes / 60);
      minutes = minutes % 60;
    }

    // Format the time string
    var formattedTime =
      hours.toString().padStart(2, "0") +
      ":" +
      minutes.toString().padStart(2, "0");

    return formattedTime;
  }

  /**
   *---------------------------------------------
   * Delivery date time picker
   *---------------------------------------------
   */
  var deliveryOffdays = function (date) {
    // Disable weekends
    var day = date.getDay();

    var offDays = [3];

    if (offDays.includes(day)) {
      return [false, ""];
    }

    // Disable specific dates
    var disabled_dates = []; // "2023-05-05", "2023-05-08"
    var disabled_string = jQuery.datepicker.formatDate("yy-mm-dd", date);

    if (jQuery.inArray(disabled_string, disabled_dates) != -1) {
      return [false, ""];
    }
    return [true, ""];
  };

  // Delivery date picker
  // $("#wp_liefer_delivery_datepicker").datepicker({
  //   dateFormat: "yy-mm-dd",
  //   beforeShowDay: deliveryOffdays,
  //   minDate: 0,
  //   maxDate: "+1m",
  // });

  function generateDeliveryTimes(weekDay, date, branchId = 0) {
    const year = date.getFullYear();
    const month = ("0" + (date.getMonth() + 1)).slice(-2);
    const day = ("0" + date.getDate()).slice(-2);
    const formattedDate = `${year}-${month}-${day}`;

    jQuery.ajax({
      method: "GET",
      url: WPLiefermanagerData.ajaxurl,
      data: {
        action: "wp_liefer_generate_delivery_times",
        weekday: weekDay,
        branchId: branchId,
      },
      success: function (res) {
        var deliveryTimes = res?.deliveryTimes;

        var slotInterval = 30; // in minutes

        var preparationTime = 0; // in minutes

        if (deliveryTimes.length) {
          $("#wp_liefer_delivery_timepicker").val("");
          $("#wp_liefer_delivery_timepicker").attr("disabled", false);

          var choosenDate = new Date(formattedDate + "T" + res?.openingTime);

          var todayDate = new Date();

          var startTimeInMs = Math.max(
            todayDate.getTime(),
            choosenDate.getTime()
          );

          var deliveryStartTime = millisecondsToTime(parseInt(startTimeInMs));

          deliveryStartTime = getNextHalfOrFullHour(deliveryStartTime);

          deliveryStartTime = addExtraMinutes(
            deliveryStartTime,
            preparationTime
          );

          $("#wp_liefer_delivery_timepicker").timepicker({
            timeFormat: "H:i",
            interval: slotInterval,
            minTime: deliveryStartTime,
            maxTime: res?.closingTime,
            startTime: deliveryStartTime,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            wrapHours: false,
          });
        } else {
          $("#wp_liefer_delivery_timepicker").val("Kein Zeitfenster gefunden.");
          $("#wp_liefer_delivery_timepicker").attr("disabled", true);
        }

        $("#wp_liefer_delivery_timepicker_field").unblock();
      },
      error: function (err) {
        console.log(err);
      },
    });
  }

  function generatePickupTimes(weekDay, date, branchId = 0) {
    const year = date.getFullYear();
    const month = ("0" + (date.getMonth() + 1)).slice(-2);
    const day = ("0" + date.getDate()).slice(-2);
    const formattedDate = `${year}-${month}-${day}`;

    jQuery.ajax({
      method: "GET",
      url: WPLiefermanagerData.ajaxurl,
      data: {
        action: "wp_liefer_generate_pickup_times",
        weekday: weekDay,
        branchId: branchId,
      },
      success: function (res) {
        // console.log(res);

        var deliveryTimes = res?.deliveryTimes;

        var slotInterval = 30; // in minutes

        var preparationTime = 0; // in minutes

        if (deliveryTimes.length) {
          $("#wp_liefer_delivery_timepicker").val("");
          $("#wp_liefer_delivery_timepicker").attr("disabled", false);

          var choosenDate = new Date(formattedDate + "T" + res?.openingTime);

          var todayDate = new Date();

          var startTimeInMs = Math.max(
            todayDate.getTime(),
            choosenDate.getTime()
          );

          var deliveryStartTime = millisecondsToTime(parseInt(startTimeInMs));

          deliveryStartTime = getNextHalfOrFullHour(deliveryStartTime);

          deliveryStartTime = addExtraMinutes(
            deliveryStartTime,
            preparationTime
          );

          $("#wp_liefer_pickup_timepicker").timepicker({
            timeFormat: "H:i",
            interval: slotInterval,
            minTime: deliveryStartTime,
            maxTime: res?.closingTime,
            startTime: deliveryStartTime,
            dynamic: false,
            dropdown: true,
            scrollbar: true,
            wrapHours: false,
          });
        } else {
          $("#wp_liefer_pickup_timepicker").val("Kein Zeitfenster gefunden.");
          $("#wp_liefer_pickup_timepicker").attr("disabled", true);
        }

        $("#wp_liefer_pickup_timepicker_field").unblock();
      },
      error: function (err) {
        console.log(err);
      },
    });
  }

  var selectedBranch = getCookieValue("wp_liefer_selected_branch")
    ? JSON.parse(getCookieValue("wp_liefer_selected_branch"))
    : null;

  var branchId = selectedBranch ? selectedBranch.id : 0;

  var dateToday = new Date();
  var todayWeekDay = dateToday.getDay();

  // Generate Delivery Times for today
  $("#wp_liefer_delivery_timepicker_field").block({
    message: wp_liefer_loader,
  });

  generateDeliveryTimes(todayWeekDay, dateToday, branchId);

  // Generate Pickup Times for  today
  $("#wp_liefer_pickup_timepicker_field").block({
    message: wp_liefer_loader,
  });

  generatePickupTimes(todayWeekDay, dateToday, branchId);

  // Delivery times on date select
  // $("#wp_liefer_delivery_datepicker").on("change", function () {
  //   $("#wp_liefer_delivery_timepicker_field").show();

  //   $("#wp_liefer_delivery_timepicker_field").block({
  //     message: wp_liefer_loader,
  //   });

  //   var choosenDate = new Date($(this).val());
  //   var weekDay = choosenDate.getDay();

  //   generateDeliveryTimes(weekDay, choosenDate);
  // });

  /**
   *---------------------------------------------
   * Pickup date time picker
   *---------------------------------------------
   */
  // $("#wp_liefer_pickup_datepicker").datepicker({
  //   dateFormat: "yy-mm-dd",
  //   beforeShowDay: deliveryOffdays,
  //   minDate: 0,
  //   maxDate: "+1m",
  // });

  // Pickup times on date select
  // $("#wp_liefer_pickup_datepicker").on("change", function () {
  //   $("#wp_liefer_pickup_timepicker_field").show();

  //   $("#wp_liefer_pickup_timepicker_field").block({
  //     message: wp_liefer_loader,
  //   });

  //   var choosenDate = new Date($(this).val());
  //   var weekDay = choosenDate.getDay();

  //   generatePickupTimes(weekDay, choosenDate);
  // });
});
