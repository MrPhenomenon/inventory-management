var loading_class = "application-management__section--loading";
var note_section = $(".application-management__note");
var note_form = $("#add-note-modal form");
var save_note_btn = $("#add-note-modal .save-note-btn");
var no_notes_found = note_section.find(".text-center");
var note_content = $(".note-content");

$(document).on("show.bs.modal", "#add-note-modal", function (event) {
  note_form[0].reset();
  var id = $(event.relatedTarget).closest(".data-holder-parent").data("id");
  $(this).find("#application-id").val(id);
});

save_note_btn.click(function (event) {
  event.preventDefault();
  $(this)
    .html('<div class="spinner-border spinner-border-sm mr-2"></div> Saving')
    .prop("disabled", true);
  note_section.addClass(loading_class);
  note_form.submit();
});

note_form.submit(function (event) {
  event.preventDefault();
  var form_data = new FormData($(this)[0]);
    var url = note_section.closest(".data-holder-parent").attr("data-status-url");


  $.ajax({
    url: url + "/add-note",
    type: "POST",
    data: form_data,
    processData: false,
    contentType: false,
    success: function (response) {
      if (response.status == "FAIL") {
        showMessage(response.message, "error");
      } else if (response.status == "SUCCESS") {
        showMessage(response.message, "success");

        setTimeout(function () {
          window.location.reload();
        }, 500);

        note_content.removeClass("d-none").html(response.note);
        no_notes_found.remove();
      }
    },
    complete: function () {
      note_section.removeClass(loading_class);
      save_note_btn.html("Save note").prop("disabled", false);
      $('#add-note-modal').modal('toggle');
    },
  });
});
