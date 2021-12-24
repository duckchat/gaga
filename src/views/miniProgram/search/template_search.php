

<script id="tpl-recommend-group" type="text/html">

    <div class="group_row"  groupId="{{groupId}}>
        <div class="group_info">
            <div class="group_detail_info">
                <div >
                    <img class="avatar" src="../../public/img/msg/default_user.png">
                </div>
                <div>
                    <div class="group_name">
                       {{groupName}}
                    </div>
                    <div class="group_owner">
                        群主：{{groupOwnerName}}
                    </div>
                </div>
            </div>
            <div class="add_group_button">
                <button class="join_group" groupId="{{groupId}}">一键加入</button>
            </div>
        </div>
    </div>
    <div class="line"></div>
</script>





<script id="tpl-search-user" type="text/html">
    <div class="item-row">
        <div class="item-header">
            <img class="user-avatar-image" avatar="{{avatar}}"
                 src="{{avatar}}"
                 onerror="this.src='../../public/img/msg/default_user.png'"/>
        </div>
        <div class="item-body">
            <div class="item-body-display">
                <div class="item-body-desc" style="font-size: 10px;" onclick="showUserChat({{userId}})">
                    {{nickname}}
                </div>

                <div class="item-body-tail">
                    {{if isFllow }}
                    <button class="chatButton" userId="{{userId}}">
                        发起会话
                    </button>
                    {{elif (!isFllow && token != userId )}}

                    <button class="addButton applyButton" userId="{{userId}}">
                        添加好友
                    </button>
                    {{/if}}

                </div>
            </div>
        </div>
    </div>

    <div class="division-line"></div>

</script>

<script id="tpl-search-group" type="text/html">

<div class="item-row" groupId="{{groupId}}">
    <div class="item-header">
        <img class="user-avatar-image"
             src="{{avatar}}"
             onerror="this.src='../../public/img/msg/default_user.png'"/>
    </div>

    <div class="item-body">
        <div class="item-body-display">
            <div class="item-body-desc">
                <div class="group_name">
                    {{name}}
                </div>
                <div class="group_owner" >
                    {{ownerName}}
                </div>
            </div>

            <div class="item-body-tail">
                {{if isMember}}
                    <button class="addButton {{groupId}}" groupId="{{groupId}}">
                        已入群
                    </button>
                    {{ else}}

                {{if permissionJoin == 0}}
                        <button class="addButton applyButton {{groupId}}" groupId="{{groupId}}">
                            一键入群
                        </button>
                {{ else}}
                        <button class="addButton  disableButton {{groupId}}" groupId="{{groupId}}">
                            非公开群
                        </button>
                {{ /if}}
                {{ /if}}

            </div>
        </div>
    </div>
</div>

<div class="division-line"></div>

</script>
