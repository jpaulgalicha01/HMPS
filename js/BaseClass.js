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



function phoneNumberFormat(input) {
    // 1. Force the +639 prefix
    if (!input.value.startsWith('+639')) {
        // Strip any accidental +639 variations from the beginning, keep the rest
        let cleanText = input.value.replace(/^\+?6?3?9?/, '');
        input.value = '+639' + cleanText;
    }

    // 2. Extract only numbers after the '+639' prefix
    // Limit to 9 digits maximum (since +639 is followed by 9 digits)
    let digits = input.value.substring(4).replace(/\D/g, '').substring(0, 9);
    
    // 3. Format the numbers dynamically with hyphens
    let formatted = '';
    
    if (digits.length > 0) {
        // First group: Next 2 digits after 9 (e.g., +639[17])
        formatted += '-'+digits.substring(0, 3);
    }
    if (digits.length > 3) {
        // Second group: Next 3 digits (e.g., -123)
        formatted += '-' + digits.substring(3, 6);
    }
    if (digits.length > 6) {
        // Third group: Last 4 digits (e.g., -4567)
        formatted += '-' + digits.substring(6, 10);
    }

    // 4. Update the input value
    input.value = '+639' + formatted;
}


function houseHoldNumberFormat(input) {
    let formatted = '';
    let digits = input.value.replace(/\D/g, '').substring(0,20);
    if (digits.length > 0) {
        formatted += +digits.substring(0, 5);
    }
    if (digits.length > 5) {
        formatted += '-'+digits.substring(5, 9);
    }
    if (digits.length > 9) {
        formatted += '-' + digits.substring(9, 13);
    }

    if (digits.length > 13) {
        formatted += '-' + digits.substring(13, 17);
    }
    input.value = formatted;
}



function FillDataFormFromDB({ form, data, dataKey = null }) {
    // Fills form inputs based on provided backend JSON (NO AJAX).
    // - `form`: selector string or DOM/jQuery element
    // - `data`: full backend JSON response object OR the payload object
    // - `dataKey`: if backend response is {status:200,data:{...}}, set dataKey='data'

    const $form = (typeof form === 'string') ? $(form) : $(form);

    if (!data || typeof data !== 'object') return;

    // Resolve payload
    let payload = data;
    if (dataKey) {
        payload = data?.[dataKey];
    } else {
        // auto-detect common shape
        if ('data' in data && typeof data.data === 'object') {
            payload = data.data;
        }
    }

    if (!payload || typeof payload !== 'object') return;

    // For each [name] field in the form, set value if it exists in payload
    $form.find('input[name], select[name], textarea[name]').each(function () {
        const el = this;
        const name = el.name;
        if (!(name in payload)) return;

        const value = payload[name];

        if (el.tagName === 'SELECT') {
            $(el).val(value);
        } else if (el.type === 'checkbox') {
            el.checked = Boolean(value);
        } else if (el.type === 'radio') {
            $(el).prop('checked', el.value == value);
        } else {
            $(el).val(value);
        }

        $(el).trigger('change');
    });
}


function fncLoadDropDownWithFilter({drpName, value, url}) {
    // and send it as AJAX request to the newly created action
    $.ajax({
        url: url,
        type: 'POST',
        data: { someValue: value == "" ? 0 : value },
        async: false,
        success: function (response) {
            var drps = (response.list);
            var selectedDeviceModel = $(`#${drpName}`);
            var pCount = (response.count);
            $(`#${drpName} > option`).remove();
            if (pCount > 0) {
                for (i = 0; i < pCount; i++) {
                    selectedDeviceModel.append($("<option/>").val(drps[i].Value).text(drps[i].Text));
                }
            }
        },
        complete: function () {
            $("#loader").addClass("hidden");

        },
        error: function (response) {
            alert(response.responseText);
        }
    });
}
