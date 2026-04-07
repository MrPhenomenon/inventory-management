$(document).ready(function () {
  var loading_class = "application-management__section--loading";
  var logs_section = $(".application-management__logs");
  var no_logs_found = logs_section.find(".text-center");
  var logs_content = $(".logs-content");

  var data_holder_parent = $(".add-ein").closest(".data-holder-parent");
  var save_ein_btn = $(".save-ein");

  $(document).on("show.bs.modal", "#add-ein-modal", function (event) {
    var ein = data_holder_parent.attr("data-ein");
    $(this).find("input.ein-number").val(ein);
  });

  save_ein_btn.click(function () {
    logs_section.addClass(loading_class);
    var application_ein = $("input.ein-number").val();
    var application_id = data_holder_parent.attr("data-id");

    save_ein_btn
      .prop("disabled", true)
      .html('<div class="spinner-border spinner-border-sm mr-2"></div> Saving');

    $.ajax({
      type: "GET",
      url: "/portal/application/update-ein",
      data: {
        application_id: application_id,
        application_ein: application_ein,
      },
      success: function (response) {
        if (response.status == "FAIL") {
          showMessage(response.message, "error");
        } else if (response.status == "SUCCESS") {
          data_holder_parent.attr("data-ein", response.application["ein_number"]);
          logs_content.removeClass("d-none").html(response.application["logs"]);
          no_logs_found.remove();
          $("#add-ein-modal").modal('hide');
          $("span.ein-number").html(response.application["ein_number"]);
          showMessage(response.message, "success");
        }
      },
      complete: function () {
        save_ein_btn.prop("disabled", false).html("Save EIN");
        logs_section.removeClass(loading_class);
      },
    });
  });
});
