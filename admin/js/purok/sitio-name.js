$(document).ready(function(){
initialize_Purok_Sitio_TableList();

});
const initialize_Purok_Sitio_TableList = () => {
    propertyTable = $("#PurokTable").DataTable({
        destroy: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        searching: true,
        search: true,
         ajax: {
            "url": "inputConfig.php",
            "type":"GET",
            "data": {"getAllPurokSitioList": true},
        },
        columns: [
            { title: "Purok/Sitio ID", data: "purok_sitio_id",visible:false  },
            { title: "Purok/Sitio Name", data: "purok_sitio_name", className: "dt-body-center dt-head-center  px-2"},
            {
                title: "Action", data: "purok_sitio_id", className: "dt-body-center dt-head-center", width: "10%",
                render: function (data, type, row) {
                    if (data == null) return '';
                    return `
                     <div class="dropdown dropstart">
                      <button class="btn btn-success btn-sm " data-bs-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item">
                          <button class="form-control btn btn-success btn-sm" onclick="updatePurokSitio('${row.purok_sitio_id}', '${row.purok_sitio_name}')"><i class="fas fa-edit"></i> Edit</button>
                        </li>
                        <li class="dropdown-item">
                          <button class="form-control btn btn-danger btn-sm" onclick="deletePurokSitio('${row.purok_sitio_id}', '${row.purok_sitio_name}')"><i class="fas fa-trash"></i> Delete</button>
                        </li>
                      </ul>
                    </div>
                    `;
                }
            },
        ]
    });
};


function updatePurokSitio(purokSitioId, purokSitioName) {
    // Implementation for updating family
    $("#PurokUniqueId").val(purokSitioId);
    $("#PurokName").val(purokSitioName);

    $("#btnAddPurok").html(`<i class="fas fa-edit"></i> Update Purok/Sitio`);
    $("#btdnResetPurokForm").html(`<i class="fas fa-times"></i> Cancel`);
}

function deletePurokSitio(purokSitioId, purokSitioName) {
    var formdata = new FormData();
    formdata.append("PurokUniqueId", purokSitioId); 
    formdata.append("deletePurokSitio", true);
   ClsConfirmAlert({
    icon: "error",
    title: `Delete purok/sitio "${purokSitioName}"?`,
    confirmText: "Delete",
    cancelText: "Cancel",
    onConfirm: function() {
      fncExecute("inputConfig.php", formdata, function (response, textStatus, jqXHR) {
        var response = JSON.parse(response);
        if (response.status == 200) {
          ClsAlert({ icon: "success", title: response.message });
          // ✅ safer: reload instead of reinit
          $("#PurokTable").DataTable().ajax.reload();
          $("#frmSubmitPurok")[0].reset();

        } else {
          ClsAlert({ icon: "error", title: response.message });
        }
      }, "POST");
    }
  });
}



$(document).on("submit","#frmSubmitPurok",function(e){
    e.preventDefault();
    var formData = new FormData(this);
    formData.append("submitPurokSitio", true);  

    fncExecute("inputConfig.php", formData, function(response){
        var res = JSON.parse(response);
        if(res.status == 200){
            ClsAlert({ icon: "success", title: res.message });
            $("#frmSubmitPurok")[0].reset();
            $("#PurokUniqueId").val("");
            initialize_Purok_Sitio_TableList();

        }else{
            ClsAlert({ icon: "error", title: res.message });
        }
    
    }, "POST");

});

$(document).on("reset", "#frmSubmitPurok", function (e) {
  $("#frmSubmitPurok #PurokUniqueId").val("")
    $("#btnAddPurok").html(`<i class="fas fa-plus"></i> Add Purok/Sitio`);
   $("#btdnResetPurokForm").html(`<i class="fas fa-redo"></i> Reset`);
   
});

