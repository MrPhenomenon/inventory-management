$(".resend-confirmation").on("click", function (e) {
  e.preventDefault();
  const application_id = $("#resend-confirmation").data("id");
  var button = $(this);
  button
    .html('<div class="spinner-border spinner-border-sm mr-2"></div> Resending')
    .addClass("disabled");
  $.ajax({
    type: "GET",
    url: "/portal/application/resend-confirmation",
    data: { id: application_id },
    success: function (response) {
      if (response.status == "SUCCESS") {
        $("#refund-confirmation").modal("hide");
        showMessage(response.message, "success");
      } else {
        showMessage(response.message, "error");
      }
      setTimeout(() => {
        location.reload();
      }, 1000);
    },
  });
});
