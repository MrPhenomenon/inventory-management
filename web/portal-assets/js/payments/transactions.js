$("#date_submitted").daterangepicker(
	{
		opens: "center",
		timePicker: true,
		showDropdowns: true,
		minYear: 2024,
		maxYear: 2030,
		ranges: {
			"Last 24 Hours": [moment().subtract(24, "hours"), moment()],
			Today: [moment(), moment()],
			Yesterday: [moment().subtract(1, "days"), moment().subtract(1, "days")],
			"Last 7 Days": [moment().subtract(6, "days"), moment()],
			"Last 30 Days": [moment().subtract(29, "days"), moment()],
			"This Month": [moment().startOf("month"), moment().endOf("month")],
			"Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
		},
		linkedCalendars: false,
		alwaysShowCalendars: true,
		minDate: "01/01/2024",
		maxDate: "12/31/2050",
		drops: "auto",
		autoApply: true,
		locale: {
			format: "YYYY-MM-DD HH:mm:ss",
		},
	},
	function (start, end, label) {
		$("#date_submitted_start").val(start.format("YYYY-MM-DD HH:mm:ss"));
		$("#date_submitted_end").val(end.format("YYYY-MM-DD HH:mm:ss"));
	},
);

$(".transaction_details").click(function () {
	var id = $(this).attr("data-id");
	var record = $("#record-" + id);

	if (record.length) {
		var data = JSON.parse(record.text());
		var actions = data.actions;
		actions.reverse();

		var html = "<div class='table-responsive'><table class='table table-striped table-bordered mb-0'>";
		html += "<thead><tr><th>Action Type</th><th>Amount</th><th>Status</th><th>Text</th><th>Date</th></tr></thead>";
		html += "<tbody>";
		for (var i = 0; i < actions.length; i++) {
			html += "<tr>";
			html += "<td><span class='badge badge-pill badge-" + actions[i].type.color + "'>" + actions[i].type.label + "</span></td>";
			html += "<td>$" + actions[i].amount + "</td>";

			if (actions[i].success == 1) {
				html += "<td><span class='badge badge-pill badge-success'>Success</span></td>";
			} else {
				html += "<td><span class='badge badge-pill badge-danger'>Failed</span></td>";
			}

			html += "<td>" + actions[i].response_text + "</td>";
			html += "<td>" + actions[i].dateEst + "</td>";
			html += "</tr>";
		}
		html += "</tbody></table></div>";

		$("#details-modal .modal-title").html(`Transaction #` + data.transaction_id);
		$("#details-modal .modal-body").html(html);
		$("#details-modal").modal("toggle");
	}
});
