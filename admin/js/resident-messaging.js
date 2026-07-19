

$(document).ready(function (){
initializeIndividualRecTable();
loadingSmsHistory();
})


function  loadingSmsHistory (){

 $("#smsNotificationHistory").DataTable({
        destroy: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        searching: true,
        search: true,
         ajax: {
            "url": "inputConfig.php",
            "type":"GET",
            "data": {"getAllSmsHistory": true},
        },
        columns: [
            { title: "Date", data: "date", className: "dt-body-center dt-head-center px-2",width: "20%"  },
            { title: "Target Group", data: "categories", className: "dt-body-center dt-head-center px-2",width: "15%",},
            { title: "Failed Sent", data: "failed_count", className: "dt-body-center dt-head-center px-2 text-danger",width: "20%"  },
            { title: "Success Sent", data: "success_count", className: "dt-body-center dt-head-center px-2 text-success",width: "20%"   },
           
        ]
    });
};




const initializeIndividualRecTable = () => {
   $("#listTemplateTable").DataTable({
        destroy: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        searching: true,
        search: true,
         ajax: {
            "url": "inputConfig.php",
            "type":"GET",
            "data": {"getAllTemplateMessage": true},
        },
        columns: [
            { title: "Date Added", data: "DateAdded", className: "dt-body-center dt-head-center px-2",width: "20%"  },
            { title: "Target Group", data: "category_name", className: "dt-body-center dt-head-center px-2",width: "15%",
                render: function (data,type,row){
                    if (data == null) return '';
                    const textColor = getContrastColor(row.category_level_color);
                    return`
                        <div style="background-color:${row.category_level_color}">
                            <span class="fw-bold" style="color: ${textColor}">${row.category_name} (${row.category_level_name})</span>
                        </div>
                    `;
                }
              },
            { title: "Template Name", data: "TemplateName", className: "dt-body-center dt-head-center px-2",width: "20%"  },
            { title: "Message Snippet", data: "TemplateMessage", className: "dt-body-center dt-head-center px-2"  },
            {
                title: "Action", data: "template_id", className: "dt-body-center dt-head-center", width: "10%",
                render: function (data, type, row) {
                    if (data == null) return '';
                    return `
                     <div class="dropdown dropstart">
                      <button class="btn btn-success btn-sm " data-bs-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item">
                          <button class="form-control btn btn-success btn-sm" onclick="updateTemplate('${row.template_id}')"><i class="fas fa-edit"></i> Edit</button>
                        </li>
                        <li class="dropdown-item">
                          <button class="form-control btn btn-danger btn-sm" onclick="deleteIndividualRecords('${row.template_id}','${row.reference_number}')"><i class="fas fa-trash"></i> Delete</button>
                        </li>
                      </ul>
                    </div>
                    `;
                }
            },
        ]
    });
};



function updateTemplate(templateID){
    var formData = new FormData();
    formData.append("getTempleInfo",true);
    formData.append("templateID",templateID);


    fncExecute("inputConfig.php",formData,function(response, textStatus, jqXHR){
        const res = JSON.parse(response);  
            if(res.status === 200) {
            // console.log(res.data)
                FillDataFormFromDB({ form:"#submitTemplateAlert", data:res.data[0] })
                 setTimeout(function() {
                    $("#CategoryLevel").val(res.data[0].CategoryLevel);
                }, 200); // Adjust the timeout duration as needed based on your data loading time
            }
    },"POST")
    $("#btnData").html(`<i class="fas fa-edit"></i> Update Data`);
    $("#btdnResetForm").html(`<i class="fas fa-times"></i> Cancel`);
}


$("#categoryID").change(function(){
    var value = $(this).val();
    fncExecute(`inputConfig.php?getCategoryLevel=true&categoryID=${value}`,'',function (response) {
        const res = JSON.parse(response);
        if(res.status == 200){
            const drpDown = document.getElementById("CategoryLevel");
            drpDown.innerHTML = '<option disabled selected>Select Category Level</option>';
            res.data.forEach(level => {
                const option = document.createElement("option");
                option.value = level.category_level_id;
                option.textContent = level.category_level_name;
                option.style.backgroundColor = level.category_level_color;
                option.style.color = getContrastColor(level.category_level_color);
                drpDown.appendChild(option);
            });
        }

    },'GET')
})


$(document).on("submit","#submitTemplateAlert", function(e){
    e.preventDefault();
    var formData = new FormData(this);
    formData.append("submitTemplateAlert",true);
    fncExecute("inputConfig.php",formData,function(response, textStatus, jqXHR){
        var res = JSON.parse(response);
        if (res.status == 200) {
            ClsAlert({icon: "success", title: res.message});
            $("#submitTemplateAlert")[0].reset();
            initializeIndividualRecTable();
        }else{
            ClsAlert({icon: "error", title: res.message});
        }
    },"POST")

})

$(document).on("reset", "#submitTemplateAlert", function (e) {
   $("#type_of_disability").val("N/A").trigger("change");   
   $("#template_id").val("")
    $("#btnData").html(`<i class="fas fa-plus"></i> Add Template`);
   $("#btdnResetForm").html(`<i class="fas fa-redo"></i> Reset`);
});


$("#sendingModal #drpCategories").change(function (){
    var value = $(this).val();
    $("#contentCategoriesLevel").removeClass("d-none")

    fncExecute(`inputConfig.php?getCategoryLevel=true&categoryID=${value}`,'',function (response) {
            const res = JSON.parse(response);
            if(res.status == 200){
                const list = document.getElementById("listCategoriesLevel");
                list.innerHTML = ""; // clear old checkboxes
                res.data.forEach((level, i) => {
                    const col = document.createElement("div");
                    col.className = "col-md-4";

                    const wrapper = document.createElement("div");
                    wrapper.className = "mb-3 form-check";

                    const cb = document.createElement("input");
                    cb.type = "checkbox";
                    cb.className = "form-check-input";
                    cb.id = `levelCheck${i}`;
                    cb.name = "categoriesLevel[]";
                    cb.value = level.category_level_id;

                    const label = document.createElement("label");
                    label.className = "form-check-label";
                    label.setAttribute("for", cb.id);
                    label.textContent = level.category_level_name;

                    wrapper.appendChild(cb);
                    wrapper.appendChild(label);
                    col.appendChild(wrapper);
                    list.appendChild(col);
                });
            }

        },'GET')
})


$(document).on("submit","#sendingModal",function(e){
    e.preventDefault();
    var formData = new FormData();
    let categoriesLevelID = [];

    $("input[name='categoriesLevel[]']:checked").each(function () {
        formData.append("CategoriesLevelId[]", $(this).val());
    });
    
    formData.append("Categories",$("#drpCategories").val())
    formData.append("SendingNotif",true);
    fncExecute("inputconfig.php",formData,function(response, textStatus, jqXHR){
        var res = JSON.parse(response);
        if(res.status == 200){
                $("#sendingModal").modal("hide");
                ClsAlert({icon: "success", title: res.message});
                loadingSmsHistory();

        }else{
            ClsAlert({icon: "error", title: res.message});
        }
    },"POST")

})
