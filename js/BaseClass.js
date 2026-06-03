function fncExecute(url, data, success, type) {
    $.ajax({
        type: type == undefined ? "POST" : type,
        url: url,
        data: data,
        processData: false,   // prevent jQuery from processing FormData
        contentType: false,   // let browser set correct multipart headers
        success: success
    });
}
function fncLoadDropDownWithFilter({drpName, value, url}) {
    $.ajax({
        url: url,
        type: 'POST',
        // If the value passed is an empty string, defaults to sending 0
        data: { someValue: value === "" ? 0 : value }, 
        async: false, // Note: Synchronous AJAX is deprecated in modern browsers, but kept here for your workflow logic
        dataType: 'json', // 🌟 Explicitly tells jQuery to parse the JSON response automatically
        beforeSend: function () {
            $("#loader").removeClass("hidden");
        },
        success: function (response) {
            // Target the specific dropdown element passed into the function argument
            var selectedDeviceModel = $(`#${drpName}`);
            
            // Empty out all previous <option> tags inside the dropdown element
            selectedDeviceModel.empty(); 
            var drps = response.list;
            var pCount = response.count;
            if (pCount > 0 && drps) {
                // 🌟 FIX: Added 'let' to safely scope the loop index variable locally
                for (let i = 0; i < pCount; i++) { 
                    selectedDeviceModel.append(
                        $("<option/>").val(drps[i].Value).text(drps[i].Text)
                    );
                }
            }
        },
        complete: function () {
            $("#loader").addClass("hidden");
        },
        error: function (response) {
            alert(response.responseText || "An error occurred loading options.");
        }
    });
}

function ClsUnidentified(val){
// Checking if null, undefined, or empty string and returning True if any of those conditions are met
return val === null || val === undefined || val === "";

}