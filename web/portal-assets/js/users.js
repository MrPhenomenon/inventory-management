$(document).ready(function () {
  $("input[data-bootstrap-switch]").each(function () {
    $(this).bootstrapSwitch({
      state: $(this).prop("checked"),
      size: "mini",
      onText: "Yes",
      offText: "No",
      offColor: "danger",
      onColor: "success",
      onSwitchChange: function (event, state) {
        var status_switch = $(event.target);
        var id = status_switch.data("id");
        var role = status_switch.data("role");
        var active = state ? 1 : 0;

        $.ajax({
          url: "/portal/user-management/disable",
          type: "POST",
          data: { id: id, active: active, role: role },
          success: function (response) {
            showMessage("Status updated", "success");
          },
          error: function (xhr) {
            showMessage("Status could not be updated", "error");
            status_switch.bootstrapSwitch("state", !state, true);
          },
        });
      },
    });
  });
});
