function buildCategoryList(data) {
  const $drpCategories = $('#drpCategories');

  $drpCategories.empty();
  $drpCategories.append($('<option selected value="">All</option>'));

  if (!data || !data.data || data.data.length === 0) {
    return;
  }

  for (const cat of data.data) {
    const catId = cat.CategoryID;
    const catName = cat.CategoryName;
    $drpCategories.append($('<option></option>').attr('value', catId).text(catName));
  }
}


document.addEventListener('DOMContentLoaded', async () => {
  const tbody = document.getElementById('householdTableBody');
  initializePropertyTable();
  fncExecute("inputConfig.php?getCategoryList=true",null,function(response){
    var res = JSON.parse(response);
    if(res.status == 200){
        buildCategoryList(res);
    }
  },"GET")
});





$(document).on("submit","#frmFilterList",function(e){
  e.preventDefault();
  initializePropertyTable();
})

const initializePropertyTable = () => {
    var categoryId = $("#drpCategories").val();
    var filterType = $("#drpFilterType").val();
    var keyword = $("#keyword").val();

    $("#householdTable").DataTable({
        destroy: true,
        pageLength: 5,
        lengthMenu: [[5, 10, 15, -1], [5, 10, 15, "All"]],
        searching: false,
        ajax: {
            "url": "inputConfig.php",
            "type":"GET",
            "data": {
                "getHousholdListWithCategory": true,
                "category_id": categoryId,
                "filter_type": filterType,
                "keyword": keyword
            },
        },
        columns: [
            { title: "Household Number", data: "household_number", className: "dt-body-center dt-head-center px-2", width: "20%" },
            { title: "Household Info", data: "FamilyMember", className: "dt-body-center dt-head-center px-2" },
            {
                title: "Action", data: "houshold_id", className: "dt-body-center dt-head-center", width: "10%",
                render: function (data, type, row) {
                    if (data == null) return '';
                    return `<button class="btn btn-success view-btn" data-id="${row.houshold_id}"><i class="fa fa-eye"></i></button>`;
                }
            },
        ],
       rowCallback: function (row, data) {
            if (data && data.category_level_color) {
                const bgColor = data.category_level_color;
                const textColor = getContrastColor(bgColor);
                $("td", row).each(function () {
                    this.style.setProperty("background-color", bgColor, "important");
                    this.style.setProperty("color", textColor, "important");
                });
            }
        }
    });
};



