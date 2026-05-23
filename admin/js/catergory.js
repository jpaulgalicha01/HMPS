

$(document).ready(function() {
    initializePropertyTable();
});


function fncAddLevelBtn() {
    const tr = document.createElement('tr');
    tr.classList.add('text-center');
    // Year column
    const tdYear = document.createElement('td');
    const inputLevelName = document.createElement('input');
    inputLevelName.type = "text";
    inputLevelName.className = "form-control";
    inputLevelName.required = true;
    inputLevelName.name = "LevelName";
    inputLevelName.placeholder = "Enter Level Name";

    tdYear.appendChild(inputLevelName);
    // QTR column
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
});


$(document).on(`submit`, `#frmSubmitCatergoryList`, function(e) {
    e.preventDefault();
    const formData = new FormData();
    const levels = [];
    document.querySelectorAll('#catergoryLevelList tr').forEach(tr => {
        const LevelName = tr.querySelector('td:nth-child(1) input').value;
        const Color = tr.querySelector('td:nth-child(2) input').value;
        levels.push({ LevelName, Color });
    });
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
                          <button class="form-control btn btn-danger btn-sm" onclick="deleteCategory('${data}')"><i class="fas fa-trash"></i> Delete</button>
                        </li>
                      </ul>
                    </div>
                    `;
                }
            },
        ]
    });
};