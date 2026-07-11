

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
    
        }else{
            ClsAlert({icon: "error", title: res.message});
        }
    },"POST")

})

$(document).on("reset", "#submitTemplateAlert", function (e) {
   $("#type_of_disability").val("N/A").trigger("change");   
   $("#person_unique_id").val("")
    $("#btnData").html(`<i class="fas fa-plus"></i> Add Data`);
   $("#btdnResetForm").html(`<i class="fas fa-redo"></i> Reset`);
});