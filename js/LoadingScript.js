let activeAjaxRequests = 0;
let loaderTimeout = null;
const MAX_LOADER_TIMEOUT = 300000; // 30 mins safety net

function hideLoaderIfNoRequests() {
    if (activeAjaxRequests === 0) {
        $("#pageLoader").hide();
        if (loaderTimeout) {
            clearTimeout(loaderTimeout);
            loaderTimeout = null;
        }
    }
}

$(document)
    .ajaxSend(function () {
        activeAjaxRequests++;
        showLoader();
    })
    .ajaxComplete(function () {
        activeAjaxRequests = Math.max(0, activeAjaxRequests - 1);
        hideLoaderIfNoRequests();
    })
    .ajaxError(function () {
        activeAjaxRequests = Math.max(0, activeAjaxRequests - 1);
        hideLoaderIfNoRequests();
    });

function showLoader() {
    // If loader doesn’t exist yet, append it once
    if (!document.getElementById("pageLoader")) {
        const overlay = document.createElement("div");
        overlay.className = "loader-overlay";
        overlay.id = "pageLoader";

        const loader = document.createElement("div");
        loader.className = "loader";

        overlay.appendChild(loader);
        document.body.appendChild(overlay);
    }
    // Show it
    $("#pageLoader").show();

    // Safety net timeout
    if (!loaderTimeout) {
        loaderTimeout = setTimeout(hideLoader, MAX_LOADER_TIMEOUT);
    }
}
function hideLoader() {
    $("#pageLoader").hide();
}
