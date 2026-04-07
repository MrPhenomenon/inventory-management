$(document).ready(function () {
  $("#delete-confirmation").on("show.bs.modal", function (event) {
    var trigger = $(event.relatedTarget);
    var href = trigger.attr("href");

    $(this).find(".confirm-delete-btn").attr("href", href);
  });

  $("#delete-confirmation").on("hide.bs.modal", function () {
    $(this).find(".confirm-delete-btn").attr("href", "");
  });
});

$("#app-delete-confirmation").on("show.bs.modal", function (event) {
    var trigger = $(event.relatedTarget);
    var href = trigger.attr("href");

    $("#delete-password").val("");
    $("#delete-error").addClass("d-none");

    $(this).find(".confirm-delete-btn").data("href", href);
});

$(document).on("click", "#app-delete-confirmation .confirm-delete-btn", function (e) {
    e.preventDefault();
    const password = $("#delete-password").val().trim();
    const href = $(this).data("href");
    const sendConfirmation = $("#send_confirmation").is(":checked") ? 1 : 0;

    if (!password) return showMessage("Password required", "error");
    if (password !== "EIN@24") return showMessage("Invalid password", "error");

    const redirectUrl = href + "&sendConfirmation=" + sendConfirmation;
    window.location.href = redirectUrl;
});

