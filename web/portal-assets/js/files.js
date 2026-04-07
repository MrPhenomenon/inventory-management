var loading_class = "application-management__section--loading";
var files_section = $(".application-management__files");

$(document).on("show.bs.modal", "#upload-files", function (event) {
  $("#upload-form")[0].reset();
  $("#file-list").empty();
  var modal = $(this);
  var id = $(event.relatedTarget).closest(".data-holder-parent").data("id");
  modal.find("#application-id").val(id);
});

$("#upload-files #files").on("change", function () {
  var files = $(this)[0].files;
  $.each(files, function (index, file) {
    $("#file-list").append(
      '<span class="badge badge-primary">' + file.name + "</span> "
    );
  });
});

$("#upload-files #upload-files-btn").click(function (event) {
  $(this)
    .html('<div class="spinner-border spinner-border-sm mr-2"></div> Uploading')
    .prop("disabled", true);
  files_section.addClass(loading_class);
  $("#upload-form").submit();
});

$("#upload-files #upload-form").submit(function (event) {
  event.preventDefault();
  var formData = new FormData($(this)[0]);
  url = $('.data-holder-parent.application-management').attr('data-status-url');

  $.ajax({
    url: url + "/upload-files",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      if (response.status == "FAIL") {
        showMessage(response.message, "error");
        $("#upload-files #upload-files-btn").html("Upload files");
        $("#upload-files #upload-files-btn").prop("disabled", false);
      } else if (response.status == "SUCCESS") {
        showMessage(response.message, "success");
        location.reload();
      }
    },
    complete: function () {
      files_section.removeClass(loading_class);
    },
  });
});

// Generate S4

$(".generate-s4-pdf").click(function (event) {
  event.preventDefault();
  var id = $(this).closest(".data-holder-parent").attr("data-id");

  var button = $(this);
  var button_text = $(this).html();
  button
    .html(
      '<div class="spinner-border spinner-border-sm mr-2"></div> Generating'
    )
    .prop("disabled", true);
  files_section.addClass(loading_class);

  $.ajax({
    type: "POST",
    url: "/portal/application/generate-s4",
    data: {
      application_id: id,
      _csrf: $("#upload-files .csrf").val(),
    },

    success: function (response) {
      if (response.status == "FAIL") {
        showMessage(response.message, "error");
      } else if (response.status == "SUCCESS") {
        showMessage(response.message, "success");
        button.html('<div class="spinner-border spinner-border-sm mr-2"></div> Refreshing')
        setTimeout(() => {
          location.reload();
        }, 2000);
      }
    },
    error: function (xhr, status, error) {
      showMessage(xhr.responseText, "error");
      button.html(button_text).prop("disabled", false);
      files_section.removeClass(loading_class);
    }
  });
});

// Generate EIN PDF

$(".generate-ein-pdf").click(function (event) {
  event.preventDefault();

  var button = $(this);
  var button_text = $(this).html();
  var application_id = button.closest(".data-holder-parent").attr("data-id");

  button
    .html(
      '<div class="spinner-border spinner-border-sm mr-2"></div> Generating'
    )
    .prop("disabled", true);
  files_section.addClass(loading_class);

  $.ajax({
    url: "/portal/application/generate-ein-pdf",
    type: "POST",
    data: {
      application_id: application_id,
      _csrf: $("#upload-files .csrf").val(),
    },
    success: function (response) {
      if (response.status == "FAIL") {
        showMessage(response.message, "error");
        button.html(button_text).prop("disabled", false);
        files_section.removeClass(loading_class);
      } else if (response.status == "SUCCESS") {
        showMessage(response.message, "success");
        
        button.html('<div class="spinner-border spinner-border-sm mr-2"></div> Refreshing')
        setTimeout(() => {
          location.reload();
        }, 2000);
      }
    },
    error: function (xhr, status, error) {
      showMessage(xhr.responseText, "error");
      button.html(button_text).prop("disabled", false);
      files_section.removeClass(loading_class);
    }
  });
});

$(".delete-file").on("click", function (e) {
  e.preventDefault();
  delete_url = $(this).attr("href");
  $("#delete-confirmation a.btn-danger").attr("href", delete_url);
  $("#delete-confirmation").modal("show");
});

$("#delete-confirmation .btn-danger").on("click", function (e) {
  var button = $(this);
  button
    .html(
      '<div class="spinner-border spinner-border-sm mr-2"></div> Deleting'
    )
    .addClass("disabled");
  files_section.addClass(loading_class);
});
