<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?php if ($lang == "1") { ?>小程序列表<?php } else { ?>Mini Program List<?php } ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="../../public/manage/config.css"/>
    <link rel="stylesheet" href="../../public/manage/search.css"/>

    <style>
        .item-row-title {
            /*width: 100%;*/
            height: 20px;
            font-size: 14px;
            font-family: PingFangSC-Medium;
            font-weight: 500;
            color: rgba(153, 153, 153, 1);
            line-height: 20px;
            margin: 17px 0px 7px 10px;
        }

        #search-group-div {
            text-align: center;
        }

        .item-row {
            cursor: pointer;
        }
    </style>

</head>

<body>

<div class="wrapper" id="wrapper">

    <div class="layout-all-row">

        <div class="list-item-center">

            <div class="item-row-title">
                <div class="">
                    <?php if ($lang == "1") { ?>
                        小程序管理列表
                    <?php } else { ?>
                        Mini Program List
                    <?php } ?>
                </div>
            </div>

            <?php foreach ($miniProgramList as $key => $value) { ?>
                <div class="item-row miniProgram-profile"
                     onclick="showMiniprogramProfile('<?php echo($value["pluginId"]) ?>')">
                    <div class="item-body">
                        <div class="item-body-display">

                            <div class="item-body-desc"><?php echo($value["name"]) ?></div>

                            <div class="item-body-tail">
                                <div class="item-body-value">
                                    <?php echo $value['sort']; ?>
                                </div>
                                <div class="item-body-value">
                                    <img class="more-img" src="../../public/img/manage/more.png"/>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="division-line"></div>
            <?php } ?>

            <div class="item-bottom">

            </div>
        </div>

    </div>
</div>


<script type="text/javascript" src="../../public/jquery/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../public/manage/native.js"></script>

<script type="text/javascript">

    function showMiniprogramProfile(pluginId) {
        var url = "index.php?action=manage.miniProgram.profile&lang=" + getLanguage() + "&pluginId=" + pluginId;

        zalyjsCommonOpenPage(url);
    }


</script>


</body>
</html>




