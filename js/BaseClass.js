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


