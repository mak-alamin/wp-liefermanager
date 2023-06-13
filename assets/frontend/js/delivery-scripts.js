jQuery(document).ready(function ($) {
  let wp_liefer_loader =
    '<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';

    function getCookieValue(cookieName) {
      var name = cookieName + "=";
      var decodedCookie = decodeURIComponent(document.cookie);
      var cookieArray = decodedCookie.split(";");
    
      for (var i = 0; i < cookieArray.length; i++) {
        var cookie = cookieArray[i].trim();
        
        if (cookie.indexOf(name) === 0) {
          return cookie.substring(name.length, cookie.length);
        }
      }
    
      return "";
    }
        
  function wpLieferMillisecondsToTime(ms) {
    const date = new Date(ms);
    const hours = date.getHours().toString().padStart(2, "0");
    const minutes = date.getMinutes().toString().padStart(2, "0");
    return `${hours}:${minutes}`;
  }

  function wpLieferGetNextHalfOrFullHour(time) {
    let [hour, minute] = time.split(":");
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

  function wpLieferAddExtraMinutes(time, minutesToAdd) {
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

  // Show/hide date picker based on delivery/pickup option
  let wp_liefer_delivery_option = $(
    "input[name='wp_liefer_delivery_option']"
  ).val();

  wpLieferSetDeliveryOption(wp_liefer_delivery_option);

  $('input[name="wp_liefer_delivery_option"]').change(function () {
    wp_liefer_delivery_option = $(this).val();
    wpLieferSetDeliveryOption(wp_liefer_delivery_option);
  });

  function wpLieferSetDeliveryOption(delivery_option) {
    if (delivery_option == "delivery") {
      $(".pickup-datetime-picker").hide();
      $(".delivery-datetime-picker").show();
    } else if (delivery_option == "pickup") {
      $(".delivery-datetime-picker").hide();
      $(".pickup-datetime-picker").show();
    }
  }

  /**
   *---------------------------------------------
   * Delivery date time picker
   *---------------------------------------------
   */
  var deliveryOffdays = function (date) {
    // Disable weekends
    var day = date.getDay();

    let offDays = [3];

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

  function wpLieferGenerateDeliveryTimes(weekDay, date, branchId = 0) {
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
        branchId: branchId
      },
      success: function (res) {
        let deliveryTimes = res?.deliveryTimes;

        let slotInterval = 30; // in minutes

        let preparationTime = 0; // in minutes

        if (deliveryTimes.length) {
          $("#wp_liefer_delivery_timepicker").val("");
          $("#wp_liefer_delivery_timepicker").attr("disabled", false);

          let choosenDate = new Date(formattedDate + "T" + res?.openingTime);

          let todayDate = new Date();

          let startTimeInMs = Math.max(
            todayDate.getTime(),
            choosenDate.getTime()
          );

          let deliveryStartTime = wpLieferMillisecondsToTime(
            parseInt(startTimeInMs)
          );

          deliveryStartTime = wpLieferGetNextHalfOrFullHour(deliveryStartTime);

          deliveryStartTime = wpLieferAddExtraMinutes(
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

  // Generate Delivery Times for today
  $("#wp_liefer_delivery_timepicker_field").block({
    message: wp_liefer_loader,
  });
  var deliveryDate = new Date();
  var deliveryWeekDay = deliveryDate.getDay();

  var selectedBranch = JSON.parse(getCookieValue('wp_liefer_selected_branch'));

  wpLieferGenerateDeliveryTimes(deliveryWeekDay, deliveryDate, selectedBranch.id);

  // Delivery times on date select
  // $("#wp_liefer_delivery_datepicker").on("change", function () {
  //   $("#wp_liefer_delivery_timepicker_field").show();

  //   $("#wp_liefer_delivery_timepicker_field").block({
  //     message: wp_liefer_loader,
  //   });

  //   let choosenDate = new Date($(this).val());
  //   let weekDay = choosenDate.getDay();

  //   wpLieferGenerateDeliveryTimes(weekDay, choosenDate);
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

  //   let choosenDate = new Date($(this).val());
  //   let weekDay = choosenDate.getDay();

  //   wpLieferGeneratePickupTimes(weekDay, choosenDate);
  // });

  function wpLieferGeneratePickupTimes(weekDay, date, branchId = 0) {
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
        branchId: branchId
      },
      success: function (res) {
        // console.log(res);

        let deliveryTimes = res?.deliveryTimes;

        let slotInterval = 30; // in minutes

        let preparationTime = 0; // in minutes

        if (deliveryTimes.length) {
          $("#wp_liefer_delivery_timepicker").val("");
          $("#wp_liefer_delivery_timepicker").attr("disabled", false);

          let choosenDate = new Date(formattedDate + "T" + res?.openingTime);

          let todayDate = new Date();

          let startTimeInMs = Math.max(
            todayDate.getTime(),
            choosenDate.getTime()
          );

          let deliveryStartTime = wpLieferMillisecondsToTime(
            parseInt(startTimeInMs)
          );

          deliveryStartTime = wpLieferGetNextHalfOrFullHour(deliveryStartTime);
        
          deliveryStartTime = wpLieferAddExtraMinutes(
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

  // Generate Pickup Times for current day
  $("#wp_liefer_pickup_timepicker_field").block({
    message: wp_liefer_loader,
  });
  let currentPickupDate = new Date();
  let pickupWeekDay = currentPickupDate.getDay();

  wpLieferGeneratePickupTimes(pickupWeekDay, currentPickupDate, selectedBranch.id);
});
