
<script id="tpl-protocol-init" type="text/html">

<div class="zaly_protocol_init">
    <div class="zaly_protocol">
        <div class="zaly_protocol_title">
            DuckChat用户协议及免责声明
        </div>
        <div class="zaly_protocol_content">
                        <textarea disabled>
北京阿卡信信息技术有限公司（以下简称”我公司”）提醒您：在使用DuckChat软件前，请您务必仔细阅读并透彻理解本声明。

1. 如果您使用本软件，您的使用行为将被视为对本声明全部内容的认可。除非您已充分阅读、完全理解并接受本协议所有条款，否则您无权使用服务。您点击“同意”或“下一步”，或您使用服务，或者以其他任何明示或者默示方式表示接受本协议的，均视为您已阅读并同意签署本协议。本协议即在您与我公司之间产生法律效力，成为对双方均具有约束力的法律文件。
2. 本软件的著作权归我公司所有，您可以免费使用此软件，可以用在营利性、非营利性活动中，但不允许您以营利性目的再次分发此软件。
3. 本协议是此软件著作权的一部分，不允许修改此软件的所有权及本安装协议，不允许修改代码删除安装过程中的本协议确认过程，如违反则视为对我著作权的侵犯。
4. 您在运营站点的过程中，请相关法律法规，对在站点上存储、传播的内容做好管控并对相关后果负全部责任，相关行为包括但不限于：
    - 反对宪法所确定的基本原则的。
    - 危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的。
    - 损害国家荣誉和利益的。
    - 煽动民族仇恨、民族歧视，破坏民族团结的。
    - 破坏国家宗教政策，宣扬邪教和封建迷信的。
    - 散布谣言，扰乱社会秩序，破坏社会稳定的。
    - 散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的。
    - 侮辱或者诽谤他人，侵害他人合法权益的。
    - 其他相关法律法规约定。
4. 若本协议有中文、英文等多个语言版本，相应内容不一致的，均以中文版的内容为准。
5. 本协议的签署地为北京市朝阳区。
6. 对此条款的解释、修改及更新权均属于我公司所有。


北京阿卡信信息技术有限公司

2018-09-26
                        </textarea>
        </div>
    </div>
    <div class="zaly_protocol_operation">
        <button class="zaly_protocol_sure " data-local-value="agreeTip">同意并继续</button>
    </div>
    <div class="zaly_protocol_cancel" data-local-value="unagreeProtocolTip">不同意可直接关掉该浏览器</div>
</div>

</script>

<script id="tpl-upgrade-tip" type="text/html">
    <div class="zaly_site_upgrade">
        <div class="zaly_site_upgrade_title">发现新版本</div>
        <div class="zaly_site_upgrade_tip">当前版本为{{siteVersion}}，服务器发现有新版本可升级，去体验一下吧！</div>
        <div class="zaly_site_upgrade_operation">
            <button class="zaly_site_upgrade_sure" data-local-value="upgradeNowTip">立即升级</button>
        </div>
        <div class="zaly_site_upgrade_cancel">
            <span onclick="newStepForCheckEnv('next_step')" style="font-size:1.13rem;font-family:PingFangSC-Regular;font-weight:400;color:#4C3BB1;cursor: pointer; " data-local-value="jumpUpgradeTip">跳过>></span>
        </div>
    </div>
</script>


<script id="tpl-check-site-environment" type="text/html">
    <div class="initDiv" >
        <div class="initHeader" style="">
            检测站点信息
        </div>
        <div class="initHeader-title">
            环境基础检测
        </div>
        <div class="init_check_info margin-top5 " isLoad="{{isPhpVersionValid}}">
            <div class="init_check">
                1.PHP版本大于5.6
            </div>
            <div class="init_check_result isPhpVersionValid">
                {{if isPhpVersionValid}}
                    <img src='../../public/img/init/check_success.png' />
                {{else}}
                    <img src='../../public/img//init/check_failed.png'  />
                {{/if}}
            </div>
        </div>

        <div class="init_check_info  ext_open_ssl" isLoad="isLoadOpenssl">
            <div class="init_check isLoadOpenssl">
                2.是否支持OpenSSL
            </div>
            <div class="init_check_result">
                {{if isLoadOpenssl}}
                <img src='../../public/img/init/check_success.png' />
                {{else}}
                <img src='../../public/img//init/check_failed.png'  />
                {{/if}}
            </div>
        </div>

        <div class="init_check_info justify-content-left ext_curl" isLoad="{{isLoadCurl}}">
            <div class="init_check isLoadCurl">
                3.是否安装Curl
            </div>
            <div class="init_check_result">
                {{if isLoadCurl}}
                <img src='../../public/img/init/check_success.png' />
                {{else}}
                <img src='../../public/img//init/check_failed.png'  />
                {{/if}}
            </div>
        </div>

        <div class="init_check_info justify-content-left  ext_is_write" isLoad="{{isWritePermission}}">
            <div class="init_check isWritePermission">
                4.当前目录写权限
            </div>
            <div class="init_check_result">
                {{if isWritePermission}}
                <img src='../../public/img/init/check_success.png' />
                {{else}}
                <img src='../../public/img//init/check_failed.png'  />
                {{/if}}
            </div>
        </div>

        <div class="init_check_info justify-content-left  ext_is_write" isLoad="{{isLoadProperties}}">
            <div class="init_check isLoadProperties">
                5.是否可以加载语言包
            </div>
            <div class="init_check_result">
                {{if isLoadProperties}}
                <img src='../../public/img/init/check_success.png' />
                {{else}}
                <img src='../../public/img//init/check_failed.png'  />
                {{/if}}
            </div>
        </div>

        <div style="margin-top:4rem;  text-align: center;">
            <button class="previte_init_protocol" data-local-value="prevStepTip">上一步</button>
            <button class="next_init_data" style="background:rgba(201,201,201,1);"  disabled data-local-value="nextStepTip">下一步</button>
        </div>
        <div style="text-align: center; margin-top:4rem;margin-bottom: 3rem;">
            <a class="phpinfo" href="./{{phpinfo}}" target="_blank"  data-local-value="phpinfoTip">查看当前PHP环境</a>
        </div>
</script>


<script id="tpl-init-data" type="text/html">
    <div class="initDiv ">
        <div class="initHeader" style="margin-top: 0rem;">
            数据初始化
        </div>
<!--        <div class="initHeader-setting">-->
<!--            请选择登录方式：-->
<!--            <select id="verifyPluginId">-->
<!--                <option class="selectOption" pluginId="102">本地账户密码校验</option>-->
<!--            </select>-->
<!--        </div>-->

        <div class="initHeader-setting">
            安装程序支持的配置
        </div>

        <div class="initHeader-sql">
            <div class="radioDiv" onclick="clickRadio('sqlite')">sqlite <span><img
                            src="../../public/img/init/select.png" class="dbType radioImg sqliteRadio" data="sqlite"
                            isSelected="1"> </span></div>
            <div class="radioDiv" onclick="clickRadio('mysql')">mysql <span><img
                            src="../../public/img/init/un_select.png" class="dbType radioImg mysqlRadio"
                            data="mysql" isSelected="0"></span></div>
        </div>


        <div class="init_check_info justify-content-left  ext_pdo_sqlite" isLoad="{{isLoadPDOSqlite}}" style="display: none;">
            <div class="init_check isLoadPDOSqlite">
                是否安装PDO_Sqlite
            </div>
            <div class="init_check_result">
                {{if isLoadPDOSqlite}}
                <img src='../../public/img/init/check_success.png' />
                {{else}}
                <img src='../../public/img//init/check_failed.png'  />
                {{/if}}
            </div>
        </div>


        <div class="init_check_info justify-content-left ext_pdo_mysql"  isLoad="{{isLoadPDOMysql}}" >
            <div class="init_check isLoadPDOMysql">
                是否安装PDO_Mysql
            </div>
            <div class="init_check_result">
                {{if isLoadPDOMysql}}
                <img src='../../public/img/init/check_success.png' />
                {{else}}
                <img src='../../public/img//init/check_failed.png'  />
                {{/if}}
            </div>
        </div>

        <div class="mysql-div">
            <!--       sql address         -->
            <div class="sql-setting mysql-setting">
                <span>数据库地址：</span>
                <input type="text" class="sql-input sql-dbHost" placeholder="数据库地址">
                <img src="../../public/img/init/failed.png" class="failed_img dbHostFailed">
            </div>
            <!--       sql port         -->
            <div class="sql-setting mysql-setting">
                <span>端口号：</span>
                <input type="text" class="sql-input sql-dbPort" placeholder="数据库端口号,默认：3306">
                <img src="../../public/img/init/failed.png" class="failed_img dbPortFailed">
            </div>
            <!--      sql name          -->
            <div class="sql-setting mysql-setting">
                <span>数据库名称：</span>
                <input type="text" class="sql-input sql-dbName" placeholder="数据库名称">
                <img src="../../public/img/init/failed.png" class="failed_img dbNameFailed">
            </div>
            <!--      sql user          -->
            <div class="sql-setting mysql-setting">
                <span>用户名：</span>
                <input type="text" class="sql-input sql-dbUserName" placeholder="数据库用户名">
                <img src="../../public/img/init/failed.png" class="failed_img dbUserNameFailed">
            </div>
            <!--      sql password          -->
            <div class="sql-setting mysql-setting">
                <span>数据库密码：</span><input type="password" class="sql-input sql-dbPassword" placeholder="数据库密码">
                <img src="../../public/img/init/failed.png" class="failed_img dbPasswordFailed">
            </div>
        </div>
        <div class="sqlite-div">
            <span style="width:6.57rem;height:1.31rem;font-size:1.31rem;font-family:PingFangSC-Regular;font-weight:400;color:rgba(102,102,102,1);line-height:1.31rem;margin-right: 1rem;">本地文件:</span>
            <select id="sqlite-file">
                <option class="selectOption" fileName="">创建新Sqlite数据库</option>
                    {{each dbFiles file }}
                        <option class="selectOption {{file}}" fileName="{{file}}">{{file}}</option>
                    {{/each}}
            </select>
        </div>



        <div class="initHeader-setting">
            管理员账号
        </div>
       <div>
           <div class="initHeader-admin">
               <span>用户名：</span><input type="text" class="admin-input admin_name">
               <img src="../../public/img/init/failed.png" class="admin_failed_img admin_name_failed">
           </div>
           <div class="initHeader-admin-tip">包含字母，长度5-24</div>

           <div class="initHeader-admin">
               <span>密码：</span><input type="password" class="admin-input admin_pwd">
               <img src="../../public/img/init/failed.png" class="admin_failed_img admin_pwd_failed">
           </div>
           <div class="initHeader-admin-tip">包含字母，长度8-32</div>

           <div class="initHeader-admin">
               <span>确认密码：</span><input type="password" class="admin-input admin_repwd">
               <img src="../../public/img/init/failed.png" class="admin_failed_img admin_repwd_failed">
           </div>


       </div>

        <div class="errorInfo">
        </div>

        <div class="d-flex flex-row justify-content-center init_data_btn" >
            <button class="previte_init_env" onclick="newStepForCheckEnv('data_init')" data-local-value="prevStepTip">Previous Step</button>
            <button type="button" class="btn login_button"><span class="span_btn_tip" data-local-value="initSiteTip">初始化站点</span></button>
        </div>
    </div>
</script>

<script id="tpl-error-info" type="text/html">
        {{errorInfo}}
        <a style='color:rgba(76,59,177,1);' target="_blank" href='https://duckchat.akaxin.com/wiki/server/faq-mysql.md'>数据库常见问题汇总</a>
    </script>