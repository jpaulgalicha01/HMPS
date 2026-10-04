

$(document).ready(function() {
    initializePropertyTable();
});


function fncAddLevelBtn() {
    const tr = document.createElement('tr');
    tr.classList.add('text-center');
    tr.dataset.categoryLevelId = ""; // Empty ID for new levels
    // Level Name column
    const tdYear = document.createElement('td');
    const inputLevelName = document.createElement('input');
    inputLevelName.type = "text";
    inputLevelName.className = "form-control";
    inputLevelName.required = true;
    inputLevelName.name = "LevelName";
    inputLevelName.placeholder = "Enter Level Name";

    tdYear.appendChild(inputLevelName);
    // Color column
    const tdQtr = document.createElement('td');
    const inputColor = document.createElement('input');
    inputColor.type = "color";
    inputColor.className = "form-control form-control-color";
    inputColor.value = "#563d7c";
    inputColor.title = "Choose your color";
    inputColor.name = "Color";
    tdQtr.appendChild(inputColor);

    // Action column
    const tdAction = document.createElement('td');
    const btn = document.createElement('button');
    btn.className = 'btn btn-danger btn-sm';
    btn.style.borderRadius = "100%";
    btn.innerHTML = '<i class="fa fa-trash"></i>';
    btn.onclick = () => tr.remove();
    tdAction.appendChild(btn);

    // Append all columns to row
    tr.appendChild(tdYear);
    tr.appendChild(tdQtr);
    tr.appendChild(tdAction);
    // Append row to table body
    document.getElementById('catergoryLevelList').appendChild(tr);
}
$(document).on(`reset`, `#frmSubmitCatergoryList`, function() {
    document.getElementById('catergoryLevelList').innerHTML = '';
    $("#frmSubmitCatergoryList #btnReset").html(`<i class="fas fa-redo"></i> Reset`);
    $("#frmSubmitCatergoryList #btnSubmit").html(`<i class="fas fa-plus"></i> Add Category`);
    $("#CategoryID").val('');
});


$(document).on(`submit`, `#frmSubmitCatergoryList`, function(e) {
    e.preventDefault();
    const formData = new FormData();
    const levels = [];
    document.querySelectorAll('#catergoryLevelList tr').forEach(tr => {
        const categoryLevelId = tr.dataset.categoryLevelId;
        const LevelName = tr.querySelector('td:nth-child(1) input').value;
        const Color = tr.querySelector('td:nth-child(2) input').value;
        levels.push({ categoryLevelId, LevelName, Color });
    });
    formData.append('CategoryID', document.getElementById('CategoryID').value);
    formData.append('CategoryName', document.getElementById('CategoryName').value);
    formData.append('CategoryLevelList', JSON.stringify(levels));
    formData.append('add_category', true);
    fncExecute('inputConfig.php', formData, function (response, textStatus, jqXHR) {
        const res = JSON.parse(response);
        if (res.status === 200) {
            ClsAlert({ icon: "success", title: res.message });
            document.getElementById('frmSubmitCatergoryList').reset();
            initializePropertyTable();
        } else {
            ClsAlert({ icon: "error", title: res.message });
        }
    }, 'POST');
})

const initializePropertyTable = () => {
    propertyTable = $("#categoryTable").DataTable({
        destroy: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        searching: true,
        search: true,
         ajax: {
            "url": "inputConfig.php",
            "type":"GET",
            "data": {"getCategoryList": true},
        },
        columns: [
            { title: "Category Name", data: "CategoryName", className: "dt-body-center dt-head-center  px-2", width: "40%" },
            {
                title: "Category Level", className: "dt-body-center dt-head-center  px-2", width: "40%",orderable: false,
                data: "CategoryListLevel",
                render: function (data, type, row) {
                    if (!data) return '';
                    // Build HTML list of levels
                    return data.map(l => 
                        `<span class="badge bg-primary" style="background-color: ${l.Color} !important;">${l.CatLevelName}</span>`
                    ).join(" ");
                }
            },
            {
                title: "Action", data: "CategoryID", className: "dt-body-center dt-head-center", width: "10%",
                render: function (data, type, row) {
                    if (data == null) return '';
                    return `
                     <div class="dropdown dropstart">
                      <button class="btn btn-success btn-sm" data-bs-toggle="dropdown">
                        <i class="fa fa-bars"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item">
                          <button class="form-control btn btn-success btn-sm" onclick="updateCategory('${data}')"><i class="fas fa-edit"></i> Edit</button>
                        </li>
                        <li class="dropdown-item">
                          <button class="form-control btn btn-danger btn-sm" onclick="deleteCategory('${data}', '${row.CategoryName}')"><i class="fas fa-trash"></i> Delete</button>
                        </li>
                      </ul>
                    </div>
                    `;
                }
            },
        ]
    });
};

function updateCategory(id) {
    $("#frmSubmitCatergoryList #btnReset").html(`<i class="fas fa-times"></i> Cancel`);
    $("#frmSubmitCatergoryList #btnSubmit").html(`<i class="fas fa-edit"></i> Update Category`);

    const formData = new FormData();  
    formData.append("getCategoryDetailsWithID", true);
    formData.append("CategoryID", id);

    fncExecute('inputConfig.php', formData, function (response, textStatus, jqXHR) {
        const res = JSON.parse(response);  
        if(res.status === 200) {
            const data = res.data;
            $("#frmSubmitCatergoryList #CategoryName").val(data.CategoryName);
            $("#frmSubmitCatergoryList #CategoryID").val(data.CategoryID);
            document.getElementById('catergoryLevelList').innerHTML = '';
            data.CategoryListLevel.forEach(level => {
                const tr = document.createElement('tr');
                tr.classList.add('text-center');
                // Store CategoryLevelID in row dataset for later use (e.g., update/delete)
                tr.dataset.categoryLevelId = level.CategoryLevelID;

                // Level Name column
                const tdYear = document.createElement('td');
                const inputLevelName = document.createElement('input');
                inputLevelName.type = "text";
                inputLevelName.className = "form-control";
                inputLevelName.required = true;
                inputLevelName.name = "LevelName";
                inputLevelName.placeholder = "Enter Level Name";
                inputLevelName.value = level.CatLevelName;
                tdYear.appendChild(inputLevelName);
                // Color column
                const tdQtr = document.createElement('td');
                const inputColor = document.createElement('input');
                inputColor.type = "color";
                inputColor.className = "form-control form-control-color";
                inputColor.value = level.Color;
                inputColor.title = "Choose your color";
                inputColor.name = "Color";
                tdQtr.appendChild(inputColor);
                // Action column
                const tdAction = document.createElement('td');
                const btn = document.createElement('button');
                btn.className = 'btn btn-danger btn-sm';
                btn.style.borderRadius = "100%";
                btn.innerHTML = '<i class="fa fa-trash"></i>';
                btn.onclick = () => tr.remove();
                tdAction.appendChild(btn);
                // Append all columns to row
                tr.appendChild(tdYear);
                tr.appendChild(tdQtr);
                tr.appendChild(tdAction);
                // Append row to table body
                document.getElementById('catergoryLevelList').appendChild(tr);
            })
        }
            else {
                ClsAlert({ icon: "error", title: res.message });
            }
    }, 'POST');
}

function deleteCategory(id, name) {
    const formData = new FormData();
    formData.append("deleteCategory", true);
    formData.append("CategoryID", id); 
    ClsConfirmAlert({
        icon: "error",
        title: `Are you sure you want to delete the category "${name}"?`,
        confirmText: "Delete",
        cancelText: "Cancel",
        onConfirm: function() {
            fncExecute('inputConfig.php', formData, function (response, textStatus, jqXHR) {
                const res = JSON.parse(response);
                if(res.status === 200) {
                    ClsAlert({ icon: "success", title: res.message });
                    // ✅ safer: reload instead of reinit
                    $("#categoryTable").DataTable().ajax.reload();
                } else {
                    ClsAlert({ icon: "error", title: res.message });
                }
            }, 'POST');
        }
    });

}