function isAndroid() {

    var userAgent = window.navigator.userAgent.toLowerCase();
    if (userAgent.indexOf("android") != -1) {
        return true;
    }

    return false;
}

function isMobile() {
    if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
        return true;
    }
    return false;
}

function getLanguage() {
    var nl = navigator.language;
    if ("zh-cn" == nl || "zh-CN" == nl) {
        return 1;
    }
    return 0;
}


function zalyjsAjaxPostJSON(url, body, callback) {
    zalyjsAjaxPost(url, jsonToQueryString(body), function (data) {
        var json = JSON.parse(data)
        callback(json)
    })
}


function zalyjsNavOpenPage(url) {
    var messageBody = {}
    messageBody["url"] = url
    messageBody = JSON.stringify(messageBody)

    if (isAndroid()) {
        window.Android.zalyjsNavOpenPage(messageBody)
    } else {
        window.webkit.messageHandlers.zalyjsNavOpenPage.postMessage(messageBody)
    }
}

function zalyjsCommonAjaxGet(url, callBack) {
    $.ajax({
        url: url,
        method: "GET",
        success: function (result) {

            callBack(url, result);

        },
        error: function (err) {
            alert("error");
        }
    });

}


function zalyjsCommonAjaxPost(url, value, callBack) {
    $.ajax({
        url: url,
        method: "POST",
        data: value,
        success: function (result) {
            callBack(url, value, result);
        },
        error: function (err) {
            alert("error");
        }
    });

}

function zalyjsCommonAjaxPostJson(url, jsonBody, callBack) {
    $.ajax({
        url: url,
        method: "POST",
        data: jsonBody,
        success: function (result) {

            callBack(url, jsonBody, result);

        },
        error: function (err) {
            alert("error");
        }
    });

}

/**
 * _blank    在新窗口中打开被链接文档。
 * _self    默认。在相同的框架中打开被链接文档。
 * _parent    在父框架集中打开被链接文档。
 * _top    在整个窗口中打开被链接文档。
 * framename    在指定的框架中打开被链接文档。
 *
 * @param url
 * @param target
 */
function zalyjsCommonOpenPage(url) {
    location.href = url;
}

function zalyjsCommonOpenNewPage(url) {
    if (isMobile()) {
        zalyjsNavOpenPage(url);
    } else {
        // window.open(url, target);
        location.href = url;
    }
}