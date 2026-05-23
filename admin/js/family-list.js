var propertyTable = "";
// Initialize DataTable on document ready
$(document).ready(function () {
    initializePropertyTable();
});
const initializePropertyTable = () => {
    propertyTable = $("#familyTable").DataTable({
        destroy: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        searching: true,
        search: true,
         ajax: {
            "url": "inputConfig.php",
            "type":"GET",
            "data": {"getFamilyList": true},
        },
        columns: [
            { title: "Family ID", data: "family_id", className: "dt-body-center dt-head-center px-2",width: "20%"  },
            { title: "Family Name", data: "family_name", className: "dt-body-center dt-head-center  px-2"},
            {
                title: "Action", data: "family_unique_id", className: "dt-body-center dt-head-center", width: "10%",
                render: function (data, type, row) {
                    if (data == null) return '';
                    return `
                     <div class="dropdown dropstart">
                      <button class="btn btn-success btn-sm " data-bs-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item">
                          <button class="form-control btn btn-success btn-sm" onclick="updateFamily('${row.family_unique_id}', '${row.family_name}')"><i class="fas fa-edit"></i> Edit</button>
                        </li>
                        <li class="dropdown-item">
                          <button class="form-control btn btn-danger btn-sm" onclick="deleteFamily('${row.family_unique_id}', '${row.family_name}')"><i class="fas fa-trash"></i> Delete</button>
                        </li>
                      </ul>
                    </div>
                    `;
                }
            },
        ]
    });
};
function updateFamily(familyId, familyName) {
    // Implementation for updating family
   $("#FamilyUniqueId").val(familyId);
   $("#FamilyName").val(familyName);

   $("#btnAddFamily").html(`<i class="fas fa-edit"></i> Update Family Name`);
   $("#btdnResetFamilyForm").html(`<i class="fas fa-times"></i> Cancel`);
}
function deleteFamily(familyId, familyName) {
    var formdata = new FormData();
formdata.append("deleteFamilyList", true);
formdata.append("deleteFamilyId", familyId);
  ClsConfirmAlert({
    icon: "error",
    title: `Delete family "${familyName}"?`,
    confirmText: "Delete",
    cancelText: "Cancel",
    onConfirm: function() {
      fncExecute("inputConfig.php", formdata, function (response, textStatus, jqXHR) {
        var response = JSON.parse(response);
        if (response.status == 200) {
          ClsAlert({ icon: "success", title: response.message });
          // ✅ safer: reload instead of reinit
          $("#familyTable").DataTable().ajax.reload();
        } else {
          ClsAlert({ icon: "error", title: response.message });
        }
      }, "POST");
    }
  });
}
$(document).on("submit", "#frmSubmitFamilyList", function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append("addFamilyList", true);
   
  fncExecute("inputConfig.php", formData, function (response, textStatus, jqXHR)  {
        var response = JSON.parse(response);
        if (response.status == 200) {
        ClsAlert({icon: "success", title: response.message});
            initializePropertyTable();
                $("#frmSubmitFamilyList")[0].reset();
                $("#btnAddFamily").html(`<i class="fas fa-plus"></i> Add Family`);
        }else{
            ClsAlert({icon: "error", title: response.message});
        }
  },"POST");
});
$(document).on("reset", "#frmSubmitFamilyList", function (e) {
    $("#btnAddFamily").html(`<i class="fas fa-plus"></i> Add Family`);
   $("#btdnResetFamilyForm").html(`<i class="fas fa-redo"></i> Reset`);
   
});



