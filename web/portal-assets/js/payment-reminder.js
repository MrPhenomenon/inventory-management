$(document).ready(function () {
  var payment_reminder_btn = $(".payment-reminder-btn");

  payment_reminder_btn.click(function (event) {
    event.preventDefault();
    var button = $(this);
    button
      .html('<div class="spinner-border spinner-border-sm mr-2"></div> Sending')
      .addClass("disabled");
    status_section.addClass(loading_class);

    $.ajax({
      type: "GET",
      url: "/portal/application/send-payment-reminder",
      data: {
        application_id: button.closest(".data-holder-parent").attr("data-id"),
      },

      success: function (response) {
        if (response.status == "SUCCESS") {
          showMessage(response.message, "success");
          button.html("Payment reminder sent");
          logs_content.removeClass("d-none").html(response.application["logs"]);
          no_logs_found.remove();
        }
      },
      error: function (response) {
        showMessage("Something went wrong", "error");
      },
      complete: function () {
        status_section.removeClass(loading_class);
      },
    });
  });
});
