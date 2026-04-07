function applyAllFilters(extraParams = []) {
    const params = new URLSearchParams(window.location.search);

    const searchTerm = $("#search_term_filter_2").val().trim();
    const status = $(".status_filter_btn.active").data("status") || "";
    const payment = $(".payment_filter").val();
    const dateStart = $("#date_filter_start").val();
    const dateEnd = $("#date_filter_end").val();
    const site = $("#site").val();

    if (searchTerm) {
        params.set("q", searchTerm);
        params.set("date_start", "2023-12-31");
        params.set("date_end", moment().format("YYYY-MM-DD"));
    } else {
        params.delete("q");

        if (dateStart && dateEnd) {
            params.set("date_start", dateStart);
            params.set("date_end", dateEnd);
        } else {
            params.delete("date_start");
            params.delete("date_end");
        }
    }

    if (site) params.set("site", site);
    else params.delete("site");

    if (status) params.set("status", status);
    else params.delete("status");

    if (payment && payment !== "all") params.set("payment", payment);
    else params.delete("payment");

    extraParams.forEach(([name, value]) => {
        if (value && value.trim() !== "") params.set(name, value);
        else params.delete(name);
    });

    const url = new URL(window.location.href);
    url.search = params.toString();
    window.location.href = url.toString();
}

$(".status_filter_btn").click(function () {
    const btn = $(this);
    $(".status_filter_btn").not(btn).removeClass("active");

    let value = btn.attr("data-status");

    if (btn.hasClass("active")) {
        btn.removeClass("active").html('<div class="spinner-border spinner-border-sm mr-2"></div> Removing');
        value = "";
    } else {
        btn.addClass("active").html('<div class="spinner-border spinner-border-sm mr-2"></div> Applying');
    }

    applyAllFilters([["status", value]]);
});

$(".payment_filter").change(function () {
    applyAllFilters();
});

let last_value = $("#search_term_filter_2").val().trim();

$("#search_term_filter_2").on("keypress", function (e) {
    var enter_key = 13;
    const current_value = $(this).val().trim();
    if (e.which === enter_key) {
        if (current_value !== last_value) {
            last_value = current_value;
            applyAllFilters();
        }
    }
});

$("#search_term_filter_2").on("blur", function () {
    const current_value = $(this).val().trim();
    if (current_value !== last_value) {
        last_value = current_value;
        applyAllFilters();
    }
});

$("#search_term_filter_2").on("search", function () {
    const current_value = $(this).val().trim();
    if (current_value == "") {
        applyAllFilters();
    }
});

$('input[id="df"]').daterangepicker(
    {
        opens: "left",
        timePicker: false,
        minYear: 2022,
        maxYear: 2030,
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "Last 7 Days": [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
            "Remove Filter": [moment("2023-12-31"), moment()],
        },
        linkedCalendars: false,
        alwaysShowCalendars: true,
        autoApply: true,
        locale: {
            format: "YYYY-MM-DD",
        },
        startDate: moment($("#date_filter_start").val() || moment().subtract(90, "days")),
        endDate: moment($("#date_filter_end").val() || moment()),
    },
    function (start, end) {
        $("#date_filter_start").val(start.format("YYYY-MM-DD"));
        $("#date_filter_end").val(end.format("YYYY-MM-DD"));
        applyAllFilters();
    }
);

$("#site").change(function () {
    applyAllFilters();
});
