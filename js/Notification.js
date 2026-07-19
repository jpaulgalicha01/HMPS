function ClsAlert(options) {
    var icon = options.icon;
    var title = options.title;

    var Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 1500
    });

    return Toast.fire({
      icon: icon,
      title: title
    });
  }
  // Modal-style alert
  function ClsModalAlert(options) {
    var icon = options.icon;
    var title = options.title;

    Swal.fire({
      text: title,
      icon: icon,
      showConfirmButton: true,
    });
  }

function ClsConfirmAlert(options) {
  var icon = options.icon || "question";
  var title = options.title || "Are you sure?";
  var confirmText = options.confirmText || "Yes";
  var cancelText = options.cancelText || "No";
  Swal.fire({
    text: title,
    icon: icon,
    showCancelButton: true,
    confirmButtonText: confirmText,
    cancelButtonText: cancelText,
  }).then((result) => {
    if (result.isConfirmed) {
      if (typeof options.onConfirm === "function") {
        options.onConfirm(); // ✅ run your transaction or action
      }
    } else if (result.isDismissed) {
      if (typeof options.onCancel === "function") {
        options.onCancel(); // optional cancel handler
      }
    }
  });
}

