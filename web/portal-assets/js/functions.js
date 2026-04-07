function showMessage(message, style) {
  if (style == "success") {
    var icon = "success";
  } else if (style == "error") {
    var icon = "error";
  }

  const Toast = Swal.mixin({
    toast: true,
    position: "bottom",
    showConfirmButton: false,
    timer: 10000,
  });

  Toast.fire({
    icon: icon,
    title: message,
  });
}
