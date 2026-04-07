var status_section = $(".application-management__status");
var loading_class = "application-management__section--loading";
var logs_section = $(".application-management__logs");
var no_logs_found = logs_section.find(".text-center");
var logs_content = $(".logs-content");

// Status update logic

var update_status_btn = $(".update-status-btn");

update_status_btn.click(function () {
  var button = $(this);
  var application_ein = $("#ein-number").val();
  var application_status = $("#application-status").val();
  var application_id = button.closest(".data-holder-parent").attr("data-id");

  button
    .prop("disabled", true)
    .html('<div class="spinner-border spinner-border-sm mr-2"></div> Saving');
  status_section.addClass(loading_class);

  $.ajax({
    type: "GET",
    url: "/portal/application/update-status",
    data: {
      application_id: application_id,
      application_status: application_status,
      application_ein: application_ein,
    },
    success: function (response) {
      if (response.status == "FAIL") {
        showMessage(response.message, "error");
      } else if (response.status == "SUCCESS") {
        showMessage(response.message, "success");
        logs_content.removeClass("d-none").html(response.application["logs"]);
        no_logs_found.remove();
      }
    },
    complete: function () {
      button.prop("disabled", false).html("Save");
      status_section.removeClass(loading_class);
    },
  });
});

$(document).ready(function () {
  let status_input;
  let confirmed;

  $(".application-status").on("change", function () {
    status_input = $(this);
    const selectedText = status_input.find("option:selected").text();

    $("#status-change-confirm-modal .target-status").text(selectedText);
    confirmed = false;

    $("#status-change-confirm-modal").modal("show");
  });

  $("#status-change-confirm-modal").on("hidden.bs.modal", function () {
    if (!confirmed) {
      status_input.val(status_input.data("previous-state"));
    }
  });

  $("#status-change-confirm-modal .btn-primary").on("click", function () {
    var button = $(this);
    button
      .html(
        '<div class="spinner-border spinner-border-sm mr-2"></div> Processing'
      )
      .addClass("disabled");

    var status_bar = status_input.closest(".status-bar");
    var password = $("#status-change-confirm-modal .password").val();
    var url = status_input.closest(".data-holder-parent").attr("data-status-url");

    $.ajax({
      url: url + '/update-status',
      type: "GET",
      data: {
        application_status: status_input.val(),
        password: password,
        application_id: status_input
          .closest(".data-holder-parent")
          .attr("data-id"),
      },
      success: function (response) {
        if (response) {

          confirmed = true;
          $("#status-change-confirm-modal").modal("hide");
          showMessage(response.message, response.status.toLowerCase());
          setTimeout(() => {
            location.reload();
          }, 500);
        }
      },
      error: function () {
        showMessage("Failed to update status.", "error");
        status_input.val(status_input.data("previous-state"));
      },
      complete: function () {
        button.html("Change status").removeClass("disabled");
      },
    });
  });
});
$(function () {
  $('[data-toggle="tooltip"]').tooltip();
});
