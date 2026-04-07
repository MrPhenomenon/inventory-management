$(document).ready(function () {
    $(".update-payment-status").click(function (event) {
        event.preventDefault();
        var button = $(this);
        button.html('<div class="spinner-border spinner-border-sm mr-2"></div> Checking').addClass("disabled");

        $.ajax({
            type: "GET",
            url: "/portal/application/update-payment-status",
            dataType: "json",
            data: {
                application_id: button.closest(".data-holder-parent").attr("data-id"),
            },
            success: function (response) {
                if (response.status == "SUCCESS") {
                    showMessage(response.message, "success");
                } else {
                    showMessage(response.message, "error");
                }
            },
            error: function (response) {
                showMessage("Something went wrong", "error");
            },
            complete: function () {
                button.html("Update Payment Status").removeClass("disabled");
            },
        });
    });
});
