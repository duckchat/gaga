var upgradeVersion = 0;
var isCheckUpgradeTokenKey = "is_check_upgrade_token";
var isSureSiteBackup = "is_sure_site_backup";
var versionsKey = "versions";
var currentUpgradeVersionKey = "current_upgrade_version";
var endUpgradeVersionKey = "end_upgrade_version";
var upgradeId = undefined;
var needUpgrade =$(".needUpgrade").val();
//-------------------------------------page upgrade int------------------------------------

function displayInitUpgrade() {
    var passwordFileName = $(".passwordFileName").val();
    var html = template("tpl-upgrade-token", {
        passwordFileName:passwordFileName
    });
    $(".zaly_upgrade").html(html);
}
displayInitUpgrade();

if(needUpgrade != 1) {
    var html = "已是最新版本";
    $(".upgrade_next_btn").html(html);
    $(".upgrade_next_btn").attr("disabled", "disabled");
    $(".upgrade_token").attr("disabled", "disabled");
}


//-------------------------------------site backup-------------------------------------s

var isCheckUpgradeToken = localStorage.getItem(isCheckUpgradeTokenKey);
var isSureSiteBackup = localStorage.getItem(isSureSiteBackup, "yes");

$(document).on("click",".zaly_site_backup_sure", function () {
    localStorage.setItem(isSureSiteBackup, "yes");
    //TODO 拉取需要升级的版本数据
    $(".zaly_window")[0].style.display = "none";
    displayUpgradeVersion();
});

function displayUpgradeVersion()
{
    try{
        var siteVersionStr = localStorage.getItem(versionsKey);
        var siteVersions = JSON.parse(siteVersionStr);
        var html = template("tpl-upgrade-init", {
            versions:siteVersions,
            length:Object.keys(siteVersions).length,
            nowLength:0
        });
        $(".zaly_upgrade").html(html);
    }catch (error) {
        displayInitUpgrade();
    }
}
//-------------------------------------page.password.version-------------------------------------

function checkUpgradeToken() {
    var upgradeToken = $(".upgrade_token").val();
    var data = {
        password:upgradeToken
    }
    $.ajax({
        method: "POST",
        url: "./index.php?action=page.version.password",
        data: data,
        success: function (resp) {
            var data = JSON.parse(resp);
            hideLoading();
            if (data.errCode == "error") {
                alert("校验口令失败");
            } else {
                var versionData = JSON.parse(resp);
                var versions = versionData.versions;
                localStorage.setItem(versionsKey, JSON.stringify(versions));
                upgradeVersion = Object.keys(versions)[0];
                localStorage.setItem(currentUpgradeVersionKey, upgradeVersion);
                var length = Object.keys(versions).length-1;
                localStorage.setItem(endUpgradeVersionKey, Object.keys(versions)[length]);
                var html = template("tpl-backup-tip", {});
                $(".zaly_window").html(html);
                $(".zaly_window")[0].style.display = "flex";
                localStorage.setItem(isCheckUpgradeTokenKey, "yes");
            }
        },
        fail:function () {
            hideLoading();
        }
    });
}

$(".upgrade_next_btn").on("click", function () {
    showLoading($(".container"));
    checkUpgradeToken();
});


//-------------------------------------page.version.upgrade function-------------------------------------

function sendUpgrade() {
    var upgradeVersionNum = localStorage.getItem(currentUpgradeVersionKey);
    var upgradeSiteVersion = $("#v_"+upgradeVersionNum).attr("version");
    var data = {
        versionCode:upgradeSiteVersion
    }
    clearInterval(upgradeId);

    upgradeId = setInterval(function () {
        checkUpgradeResult();
    }, 1000);

    $.ajax({
        method: "POST",
        url: "./index.php?action=page.version.upgrade",
        data: data,
        success: function (resp) {
        },
        fail:function (resp) {
            console.log(resp);
            alert("请求失败");
        }
    });
}

function isPhone(){
    if((/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent))) {
        return true;
    }
    return false;
}

function upgradeSiteVersion(currentVersionCode) {
    updateUpgradeProgress("done", currentVersionCode, "");

    var endUpgradeNum = localStorage.getItem(endUpgradeVersionKey);
    if(currentVersionCode >= Number(endUpgradeNum)) {
        if(isPhone()) {
            var html = "升级完成，关闭当前页面";
            $(".upgrade_staring_btn").attr("goto", "close_page");
        }else {
            var html = "升级完成，前往站点";
            $(".upgrade_staring_btn").attr("goto", "site");
        }
        $(".upgrade_staring_btn").html(html);
        $(".upgrade_staring_btn").attr("disabled", false);
        return;
    };
    // var nextUpgradeVersionNum = Number(Number(currentVersionCode)+1);
    localStorage.setItem(currentUpgradeVersionKey, currentVersionCode);

    updateUpgradeProgress("start",  currentVersionCode, "");
    sendUpgrade();
}

function updateSiteVersionFailed(resp)
{
    if(resp == "") {
        var info = "请求失败";
    } else {
        var info = resp;
    }
    updateUpgradeProgress("fail", resp.versionCode, resp);
    $(".upgrade_staring_btn").html("升级失败");
}

//-------------------------------------page.version.upgrade-------------------------------------
var nextUpgradeVersionNumKey = 1;
function updateUpgradeProgress(type,  upgradeVersionCode, info)
{
    var upgradeVersionNum = localStorage.getItem(currentUpgradeVersionKey);

    try{
        if(type == "start"){
            var versionStr = localStorage.getItem(versionsKey);
            var versions  = JSON.parse(versionStr);
            var nextUpgradeVersionNum = Object.keys(versions)[nextUpgradeVersionNumKey];
            $("#v_line_"+upgradeVersionNum).attr("src", "../../public/img/upgrade/current_line.png");
            $("#v_"+nextUpgradeVersionNum).attr("src", "../../public/img/upgrade/current.png");

            $(".text_"+nextUpgradeVersionNum)[0].style.color = "RGBA(73, 205, 186, 1)";
            var versionCode = $("#v_"+nextUpgradeVersionNum).attr("version");
            var versionName = $("#v_"+nextUpgradeVersionNum).attr("versionName");
            var info = template("tpl-upgrade-upgradeInfo", {
                versionCode:versionCode,
                color:"rgba(73,205,186,1)",
                errorInfo:versionName+"版本正在升级中...."
            });
            $(".upgrade_info_msg").append(info);
            nextUpgradeVersionNumKey = Number(nextUpgradeVersionNumKey+1);
        } else if(type=="done") {
            $("#v_line_"+upgradeVersionNum).attr("src", "../../public/img/upgrade/success_line.png");
            $("#v_"+upgradeVersionCode).attr("src", "../../public/img/upgrade/success.png");
            $(".text_"+upgradeVersionCode)[0].style.color="rgba(76,59,177,1)";
            var versionCode = $("#v_"+upgradeVersionCode).attr("version");
            var versionName = $("#v_"+upgradeVersionCode).attr("versionName");
            $(".version_"+versionCode)[0].style.color="RGBA(20, 16, 48, 1)";
            var html = versionName+"版本升级完成";
            $(".version_"+versionCode).html(html);

        } else if (type== "fail") {
            $("#v_"+upgradeVersionCode).attr("src", "../../public/img/upgrade/fail.png");
            $(".text_"+upgradeVersionCode)[0].style.color = "RGBA(244, 67, 54, 1)";

            var versionCode = $("#v_"+upgradeVersionCode).attr("version");
            var versionName = $("#v_"+upgradeVersionCode).attr("versionName");
            $(".version_"+versionCode)[0].style.color="rgba(244,67,54,1)";
            var html = versionName+"版本升级失败，失败原因如下";
            $(".version_"+versionCode).html(html);

            var info = template("tpl-upgrade-upgradeInfo", {
                versionCode:$("#v_"+upgradeVersionNum).attr("versionName"),
                color:"rgba(244,67,54,1); padding-left:3rem;",
                errorInfo:info
            });
            $(".upgrade_info_msg").append(info);

        }
    }catch(error){
        console.log(error.message);
    }

}

$(document).on("click", ".upgrade_staring_btn", function () {
    var goto = $(this).attr("goto");
    if(goto == "site") {
        window.location.href="./index.php";
        return;
    } else if(goto == "close_page") {
        zalyjsClosePage();
        return;
    }
    var html = "正在升级...";
    $(this).html(html);
    $(this).attr("disabled", "disabled");
    updateUpgradeProgress("start", "", "");
    sendUpgrade();
});


//-------------------------------------page.version.check-------------------------------------


function checkUpgradeResult() {
    if(upgradeId == undefined) {
        return ;
    }
    $.ajax({
        method: "POST",
        url: "./index.php?action=page.version.check",
        success: function (resp) {
            var data = JSON.parse(resp);
            if(data.upgradeErrCode == "") {
                return;
            }
            clearInterval(upgradeId);
            upgradeId = undefined;
            if (data.upgradeErrCode == "success") {
                upgradeSiteVersion(data.versionCode);
            } else {
                updateSiteVersionFailed(resp);
            }
        }
    });
}
