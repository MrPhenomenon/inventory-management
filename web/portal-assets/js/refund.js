$(document).ready(function () {
  var refund_btn = $(".refund-btn");
  var confirm_refund_btn = $(".confirm-refund-btn");
  var refund_confirmation_modal = $("#refund-confirmation");

  refund_btn.on("click", function (event) {
    event.preventDefault();
    var button = $(this);
    refund_url = button.attr("data-refund-url");
    confirm_refund_btn.attr("href", refund_url);
    refund_confirmation_modal.modal("show");
  });

  confirm_refund_btn.on("click", function (event) {
    var password = $("#refund-password").val().trim();
    if(password !== "EIN@24"){
      event.preventDefault();
      showMessage('Invalid password', "error");
      refund_confirmation_modal.modal("hide");
      return;
    }
    var button = $(this);
    status_section.addClass(loading_class);
    button
      .html(
        '<div class="spinner-border spinner-border-sm mr-2"></div> Refunding'
      )
      .addClass("disabled");
  });

});
