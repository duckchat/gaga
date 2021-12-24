
<div class="right-head">
    <div class="title chatsession-title"></div>
    <div class="actions">
        <img src="./public/img/msg/invite_people.png"  class="invite_people"/>
        <img src="./public/img/msg/add_friend.png"  class="add_friend add-friend-btn"/>
        <img src="./public/img/msg/setting.png" class="see_group_profile" is_show_profile="0"/>
    </div>
</div>

<div class="right-body">

    <div class="right-body-chat" style="position: relative;">
        <div class="right-chatbox" style="position: relative">

        </div>

        <div id="emojies" style="display: none;position: absolute; bottom:11rem;">
            <div class="emoji-row" style="margin-top: 1rem;">
                <item class="emotion-item">🙂</item>
                <item  class="emotion-item">😂</item>
                <item  class="emotion-item">😊</item>
                <item  class="emotion-item">😉</item>
                <item  class="emotion-item">😋</item>
                <item  class="emotion-item">😎</item>
                <item  class="emotion-item">😀</item>
                <item  class="emotion-item">😍</item>
                <item  class="emotion-item">🤩</item>
            </div>
            <div class="emoji-row">
                    <item  class="emotion-item">😤</item>
                    <item  class="emotion-item" >😬</item>
                    <item class="emotion-item" >😡</item>
                    <item class="emotion-item">😠</item>
                    <item class="emotion-item">🙁</item>
                    <item class="emotion-item">😞</item>
                    <item class="emotion-item">😰</item>
                    <item class="emotion-item">😭</item>
                    <item class="emotion-item">😱</item>
                </div>

            <div class="emoji-row">
                <item  class="emotion-item">😘</item>
                <item  class="emotion-item">👄</item>
                <item  class="emotion-item">💋</item>
                <item  class="emotion-item">💘</item>
                <item  class="emotion-item">❤️</item>
                <item  class="emotion-item">💔</item>
                <item  class="emotion-item">🌹</item>
                <item  class="emotion-item">🥀</item>
                <item  class="emotion-item">😷</item>
            </div>

            <div class="emoji-row">
                <item  class="emotion-item">🙃</item>
                <item  class="emotion-item">🙄</item>
                <item  class="emotion-item">😴</item>
                <item  class="emotion-item">😓</item>
                <item  class="emotion-item">😳</item>
                <item  class="emotion-item">🤔</item>
                <item  class="emotion-item">😐</item>
                <item  class="emotion-item">🤫</item>
                <item  class="emotion-item">🤧</item>
            </div>

            <div class="emoji-row">
                <item  class="emotion-item">🤮</item>
                <item  class="emotion-item">🤪</item>
                <item  class="emotion-item">😇</item>
                <item  class="emotion-item">😏</item>
                <item  class="emotion-item">💀</item>
                <item  class="emotion-item">👻</item>
                <item  class="emotion-item">💩</item>
                <item  class="emotion-item">😝</item>
                <item  class="emotion-item">💪</item>
            </div>

            <div class="emoji-row">
                <item  class="emotion-item">✌</item>
                <item  class="emotion-item">👌</item>
                <item  class="emotion-item">👍</item>
                <item  class="emotion-item">👎</item>
                <item  class="emotion-item">✊</item>
                <item  class="emotion-item">👏</item>
                <item  class="emotion-item">🙏</item>
                <item  class="emotion-item">🍻</item>
                <item  class="emotion-item">👅</item>

            </div>
            <div class="emoji-row">
                <item  class="emotion-item">💢</item>
                <item  class="emotion-item">💣</item>
                <item  class="emotion-item">👙</item>
                <item  class="emotion-item">👑</item>
                <item  class="emotion-item">💍</item>
                <item  class="emotion-item">💎</item>
                <item  class="emotion-item">🌼</item>
                <item  class="emotion-item">💩</item>
                <item  class="emotion-item">💊</item>
            </div>
            <div class="emoji-row">
                <item  class="emotion-item">💰</item>
                <item  class="emotion-item">💳</item>
                <item  class="emotion-item">🐵</item>
                <item  class="emotion-item">🐶</item>
                <item  class="emotion-item">🦊</item>
                <item  class="emotion-item">🐱</item>
                <item  class="emotion-item">🐷</item>
            </div>
        </div>
        <div id="chat_plugin" style="display: none; position: absolute; bottom: 11rem;">
            <iframe class="chat_plugin_iframe" src=""> </iframe>
        </div>
        <div class="right-input">
                <div class="input-tools">
                    <img src="./public/img/msg/emotions.png" class="emotions"/>
                    <img src="./public/img/msg/images.png" style="height: 2.06rem;" onclick="uploadFile('file1')" class="upload-img" accept="image/gif,image/jpeg,image/jpg,image/png,image/svg"/>
                    <input type="file" id="file1" style="display:none" onchange="uploadMsgFileFromInput(this, FileType.FileImage)" accept="image/gif,image/jpeg,image/jpg,image/png,image/svg">
                    <img src="./public/img/msg/file.png" style="height: 2.06rem;" onclick="uploadFile('file3')" class="upload-img" accept="image/gif,image/jpeg,image/jpg,image/png,image/svg"/>
                    <input type="file" id="file3" style="display:none" onchange="uploadMsgFileFromInput(this, FileType.FileDocument)">
                    <div class="input-plugin-tools">
                    </div>
                </div>

                <div class="input-box">
                    <div id="msgImage">
                    </div>
                    <textarea class="input-box-text msg_content"  onkeydown="sendMsgByKeyDown(event)" placeholder="输入消息…."data-local-placeholder="enterMsgContentPlaceholder"  id="msg_content"></textarea>

                    <div class="input-btn">
                        <button class="send_msg" data-local-value="sendTip">发送</button>
                    </div>
                </div>
                <div class="input-line"></div>
        </div>
    </div>
    <div class="right-body-sidebar" style="display: none;" >

        <div style="position: relative">
            <div class="group-profile-desc">
                <div class="group-desc">
                    <div class="group-desc-title" style="position: relative" ><span data-local-value="groupMemberTip">群成员</span><span style="margin-left: 0.5rem;" class="group-member-count"></span></div>
                    <div class="group-member-body">

                    </div>
                    <div class="see_all_group_member" data-local-value="allGroupMemberTip">查看全部</div>
                </div>


                <div class="group-desc">
                    <div class="group-desc-title" style="position: relative">
                        <span  data-local-value="groupProfileDescTip">群介绍</span>
                        <img src="./public/img/msg/icon_disclosure.png" class="icon_discosure"/>

                    </div>

                    <div class="group-desc-body">
                        <textarea class="group-introduce"></textarea>
                    </div>
                </div>

                <div class="action-group">

                    <div class="action-row action-row-disclosure edit_group_name">
                        <div class="action-title" data-local-value="groupProfileNameTip">群名称</div>
                        <div class="action-btn groupName" style="width: auto;cursor: pointer;">
                        </div>
                        <img src="./public/img/edit.png" class="edit_img"/>
                    </div>

                    <div class="action-row mute-group">
                        <div class="action-title" data-local-value="muteTip">静音</div>
                        <div class="action-btn ">
                            <img src="./public/img/msg/icon_switch_off.png" class="group_mute" />
                        </div>
                    </div>

                    <div class="action-row permission-join" style="display: none"  >
                        <div class="action-title" data-local-value="joinGroupPermissionsTip">邀请入群权限</div>
                    </div>

                    <div class="action-row group_speakers" >
                        <div class="action-title" data-local-value="groupsBannedTip">禁言设置</div>
                        <div class="action-btn ">
                            <img src="./public/img/msg/icon_disclosure.png" class="icon_discosure"/>
                        </div>
                    </div>

                    <div class="action-row clear_room_chat" >
                        <div class="action-title" data-local-value="clearChatTip">清空聊天记录</div>
                    </div>

                    <div class="action-row quit-group" style="display: none;border-bottom: 1px solid rgba(223,223,223,1);" >
                        <div class="action-title" data-local-value="quitGroupTip">退群</div>
                    </div>

                    <div class="action-row delete-group" style="display: none;border-bottom: 1px solid rgba(223,223,223,1);">
                        <div class="action-title" data-local-value="disbandGroupTip">解散群</div>
                    </div>
                </div>
            </div>

            <div class="user-profile-desc" style="position:absolute; visibility:hidden;">
                <div class="user-desc" >
                    <div style="text-align: center">
                        <img class="user-image-for-add " src="./public/img/msg/default_user.png">
                        <div class="user-desc-body">
                        </div>
                    </div>

                </div>

                <div class="action-user">
                    <div class="action-row action-row-disclosure edit-remark">
                        <div class="action-title" data-local-value="editRemarkTip">修改备注</div>
                        <div class="action-btn ">
                            <img src="./public/img/msg/icon_disclosure.png" class="icon_discosure" />
                        </div>
                    </div>

                    <div class="action-row action-row-disclosure more-info">
                        <div class="action-title" data-local-value="moreInfoTip">更多资料</div>
                        <div class="action-btn ">
                            <img src="./public/img/msg/icon_disclosure.png" class="icon_discosure" />
                        </div>
                    </div>

                    <div class="action-row mute-friend">
                        <div class="action-title" data-local-value="muteTip">静音</div>
                        <div class="action-btn ">
                            <img src="./public/img/msg/icon_switch_off.png" class="friend_mute" />
                        </div>
                    </div>

                    <div class="action-row clear_room_chat" >
                        <div class="action-title" data-local-value="clearChatTip">清空聊天记录</div>
                    </div>

                    <div class="action-row delete-friend" style=" border-bottom: 1px solid rgba(223,223,223,1);">
                        <div class="action-title" data-local-value="deleteFriendTip">删除好友</div>
                    </div>

                    <div class="action-row add-friend add-friend-btn">
                        <div class="action-title"  data-local-value="addFriendTip">添加好友</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



