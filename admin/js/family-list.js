
$(document).ready(function () {
    initializeIndividualRecTable();
});
const initializeIndividualRecTable = () => {
   $("#familyTable").DataTable({
        destroy: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        searching: true,
        search: true,
         ajax: {
            "url": "inputConfig.php",
            "type":"GET",
            "data": {"getAllPersonalRecords": true},
        },
        columns: [
            { title: "Referrence Number", data: "reference_number", className: "dt-body-center dt-head-center px-2",width: "30%"  },
            { title: "Full Name", data: "full_name", className: "dt-body-center dt-head-center  px-2"},
            {
                title: "Action", data: "person_unique_id", className: "dt-body-center dt-head-center", width: "10%",
                render: function (data, type, row) {
                    if (data == null) return '';
                    return `
                     <div class="dropdown dropstart">
                      <button class="btn btn-success btn-sm " data-bs-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item">
                          <button class="form-control btn btn-success btn-sm" onclick="updateFamily('${row.person_unique_id}')"><i class="fas fa-edit"></i> Edit</button>
                        </li>
                        <li class="dropdown-item">
                          <button class="form-control btn btn-danger btn-sm" onclick="deleteIndividualRecords('${row.person_unique_id}','${row.reference_number}')"><i class="fas fa-trash"></i> Delete</button>
                        </li>
                      </ul>
                    </div>
                    `;
                }
            },
        ]
    });
};
 function updateFamily(person_unique_id) {
  var formData = new FormData();
  formData.append("get_records_person",true);
  formData.append("person_unique_id", person_unique_id);
  console.log(formData.getAll)

  fncExecute("inputConfig.php",formData,function(response, textStatus, jqXHR){
      const res = JSON.parse(response);  
        if(res.status === 200) {
          // console.log(res.data)
          FillDataFormFromDB({ form:"#frmSubmitPersonalInfo", data:res.data[0] })
        }
  },"POST")
  
   $("#btnData").html(`<i class="fas fa-edit"></i> Update Data`);
   $("#btdnResetForm").html(`<i class="fas fa-times"></i> Cancel`);


}
function deleteIndividualRecords(person_unique_id, reference_number) {
    var formdata = new FormData();
formdata.append("deleteIndividualRecord", true);
formdata.append("person_unique_id", person_unique_id);
  ClsConfirmAlert({
    icon: "error",
    title: `Are you sure to delete this referrence number "${reference_number}"?`,
    confirmText: "Delete",
    cancelText: "Cancel",
    onConfirm: function() {
      fncExecute("inputConfig.php", formdata, function (response, textStatus, jqXHR) {
        var response = JSON.parse(response);
        if (response.status == 200) {
          ClsAlert({ icon: "success", title: response.message });
          // ✅ safer: reload instead of reinit
          $("#familyTable").DataTable().ajax.reload();
          $("#frmSubmitPersonalInfo")[0].reset();
        } else {
          ClsAlert({ icon: "error", title: response.message });
        }
      }, "POST");
    }
  });
}


$(document).on("reset", "#frmSubmitPersonalInfo", function (e) {
   $("#type_of_disability").val("N/A").trigger("change");   
   $("#person_unique_id").val("")
    $("#btnData").html(`<i class="fas fa-plus"></i> Add Data`);
   $("#btdnResetForm").html(`<i class="fas fa-redo"></i> Reset`);
});


$("#type_of_disability").change(function() {
    var value = $(this).val();
    if (value == "Others") {
        $("#TypeOfDisabilitySpecificDrpDwn").removeClass("d-none");
        $("#type_of_disability_others").prop("required", true);
        return;
    }
        $("#TypeOfDisabilitySpecificDrpDwn").addClass("d-none");
        $("#type_of_disability_others").prop("required", false);
        $("#type_of_disability_others").val("");

})


$(document).on("submit","#frmSubmitPersonalInfo",function(e){
  e.preventDefault();
  var formData = new FormData(this);
  formData.append("adding_update_individual_info",true);
  fncExecute("inputConfig.php",formData,function(response, textStatus, jqXHR){
   var response = JSON.parse(response);
        if (response.status == 200) {
          ClsAlert({ icon: "success", title: response.message });
          $("#frmSubmitPersonalInfo")[0].reset();
          initializeIndividualRecTable();
        } else {
          ClsAlert({ icon: "error", title: response.message });
        }

  },"POST")

})




$(document).on("click","#btnUploadFile", function(e) {
      e.preventDefault();

      if(filedData.length === 0) {
        ClsAlert({ icon: "info", title: "Please select a file to upload." });
        return;
      }
    const formData = new FormData();
    formData.append("fileInput", filedData[0]); // Assuming only one file is uploaded
    formData.append("uploadCSV", true);
    fncExecute("inputConfig.php", formData, function(response) {
      const res = JSON.parse(response);
      if (res.status === 200) {
        ClsAlert({ icon: "success", title: res.message });
        initializeIndividualRecTable();
        fileList.innerHTML = "";
        filedData.length = 0; // Clear the filedData array
        document.getElementById('fileInput').value = ""; // Reset the file input
        $("#exampleModal").modal("hide");
      } else if (res.status === 409) {
        ClsAlert({ icon: "error", title: res.message });
        console.log(res.data);
      } else {
        ClsAlert({ icon: "error", title: res.message });
      }
    
    }, "POST");
})