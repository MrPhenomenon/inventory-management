$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

    var $search_by = $(`input[name="search_by"]:checked`);
    var $term = $(`input[type="search"]`);
    var $button = $(`button[type="submit"]`);
    var default_button_text = $button.html();
    var $results = $(`.results`);

    function updateSearchResults(data) {
        if (data.length > 0) {
            var list = `<p class="text-right mb-0">Companies found: ${data.length}</p>
                    <div class="list-group">`;
            data.forEach((item) => {
                list += `<a href="${item.url}" class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                                <p class="mb-1">#${item.id}</p>
                                <small>${item.date}</small>
                            </div>
                            <h5 class="mb-1">${item.business_name}</h5>
                            <p class="mb-1">${item.email}</p>
                        </a>`;
            });
            list + -`</div>`;

            $results.html(list);
        } else {
            $results.html(`<div class="callout callout-warning mb-0">No companies found</div>`);
        }
    }

    function setProcessing(state) {
        if (state) {
            $button.prop("disabled", true).html('<div class="spinner-border spinner-border-sm mr-2"></div> Searching');
            $(`input[name="search_by"]`).prop("disabled", true);
            $results.css("opacity", "0.5");
        } else {
            $button.prop("disabled", false).html(default_button_text);
            $(`input[name="search_by"]`).prop("disabled", false);
            $results.css("opacity", "1");
        }
    }

    $(`input[name="search_by"]`).change(function () {
        $search_by = $(`input[name="search_by"]:checked`);
        var placeholder = $search_by.attr("data-placeholder");
        $term.attr("placeholder", placeholder);
        $term.valid();
    });

    $(`form`).submit(function (e) {
        e.preventDefault();
        setProcessing(true);

        var is_form_valid = $term.valid() ? true : false;
        if (!is_form_valid) {
            setProcessing(false);
            return;
        }

        $.ajax({
            url: "/portal/customer-service/search",
            type: "GET",
            dataType: "json",
            data: { term: $term.val(), search_by: $search_by.val() },
            success: function (data) {
                updateSearchResults(data);
            },
            error: function () {
                showMessage("Something went wrong", "error");
            },
            complete: function () {
                setProcessing(false);
            },
        });
    });

    $.validator.addMethod(
        "isValidTerm",
        function (value, element) {
            var search_by = $search_by.val();

            switch (search_by) {
                case "id":
                    return /^\d+$/.test(value);
                case "phone":
                    return /^(?:\+1|[0-9\s\-()])+$/ .test(value);
                default:
                    return true;
            }
        },
        "Search term is invalid for this option"
    );

    $("form").validate({
        errorClass: "invalid-feedback",
        errorElement: "div",
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
        rules: {
            term: {
                required: true,
                isValidTerm: true,
                minlength: 4
            },
        },
        messages: {
            term: {
                required: "Search term can't be empty",
                minlength: "Search term should be atleast 4 characters long",
            },
        },
    });

    $(`.results-btn`).click(function (e) { 
        e.preventDefault();
        var data = JSON.parse($(this).attr("data-results"));
        updateSearchResults(data);
        $("#log-details-modal").modal("toggle");
    });

    $("#terms123").autocomplete({
        minLength: 3,
        delay: 500,

        source: function (request, response) {
            var state = $(".state");
            state.removeClass("d-none");
            var search_by = $(`input[name="search_by"]:checked`).val();
            console.log(search_by);

            $.ajax({
                url: "/portal/customer-service/search",
                type: "GET",
                dataType: "json",
                data: { term: request.term, search_by: search_by },
                success: function (data) {
                    updateNewUi(data);
                    if (data.length === 0) {
                        response(data);
                        showMessage("No applications found", "error");
                    } else {
                        response(data);
                    }
                },
                error: function () {
                    showMessage("Something went wrong", "error");
                },
                complete: function () {
                    state.addClass("d-none");
                },
            });
        },

        select: function (event, ui) {
            if (ui.item && ui.item.url) {
                window.location.href = ui.item.url;
            }
            return false;
        },
    });

    $("#terms123").on("focus", function () {});
});
