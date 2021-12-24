
<script id="tpl-send-msg-img" type="text/html">
    <div class="msg-row msg-right msg-text">
        <div class="msg-avatar user-info-avatar">
            <img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/user3.png" />
        </div>
        <div class="right-msg-body text-align-right">
                <div class="msg_status" style="margin-top: 1rem;">
                    <div class="msg-content-img justify-content-end hint--bottom" aria-label="{{msgTime}}">
                        <div class="text-align-left" style="width: {{width}}; height:{{height}}">
                            <img class="msg_img msg-img-{{msgId}}"  onload="autoMsgImgSize(this, 300, 200)" />
                        </div>
                    </div>
                    {{ if msgStatus == "MessageStatusSending"}}
                    <div class="showbox  msg_status_loading msg_status_loading_{{msgId}}" sendTime="{{timeServer}}" msgId="{{msgId}}"  is-display="yes">
                        <div class="loader">
                            <svg class="circular" viewBox="25 25 50 50">
                                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="msg_status_img msg_status_failed_{{msgId}}"  msgId="{{msgId}}" >
                        <img src="../../public/img/msg/msg_failed.png">
                    </div>
                    {{ else if msgStatus == "MessageStatusFailed"}}
                    <div class="msg_status_img msg_status_failed_{{msgId}}"  msgId="{{msgId}}" style="display: flex;" >
                        <img src="../../public/img/msg/msg_failed.png">
                    </div>
                    {{/if}}
                </div>
            </div>
        </div>
    </div>
</script>

<script id="tpl-send-msg-text" type="text/html">
    <div class="msg-row msg-right msg-text" >
        <div class="msg-avatar">
            <img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/user3.png" />
        </div>
        <div class="right-msg-body  text-align-right" >
                <div class="msg_status" style="margin-top: 1rem;">
                    <div class="msg-content hint--bottom" aria-label="{{msgTime}}">
                        <div class="text-align-left msgContent">{{msgContent}}</div>
                    </div>
                    {{ if msgStatus == "MessageStatusSending"}}
                    <div class="showbox msg_status_loading msg_status_loading_{{msgId}}"  sendTime="{{timeServer}}" msgId="{{msgId}}"   is-display="yes">
                        <div class="loader">
                            <svg class="circular" viewBox="25 25 50 50">
                                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                            </svg>
                        </div>
                    </div>
                    <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}" >
                        <img src="../../public/img/msg/msg_failed.png">
                    </div>
                    {{ else if msgStatus == "MessageStatusFailed"}}
                    <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;">
                        <img src="../../public/img/msg/msg_failed.png">
                    </div>
                    {{/if}}
                </div>
            </div>
        </div>
</script>

<script id="tpl-send-msg-web" type="text/html">
    <div class="msg-row msg-right msg-text" >
        <div class="msg-avatar">
            <img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/user3.png" />
        </div>
        <div class="right-msg-body  text-align-right" >
                <div class="msg_status" style="margin-top: 1rem;">
                    <div class="msg-content hint--bottom" aria-label="{{msgTime}}">
                        <div class="text-align-left" style=" width: 19rem; height:19rem;"><iframe src="{{hrefURL}}" frameborder="no" width="19rem" height="19rem"></iframe></div>
                    </div>
                    {{if hrefURL}}
                    <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;">
                        <img src="../../public/img/msg/web_msg_click.png" style="width:2rem;height:2rem; left: -3rem;">
                    </div>
                    {{else}}
                    <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;">
                        <img src="../../public/img/msg/web_msg_unclick.png" style="width:2rem;height:2rem; left: -3rem ;">
                    </div>
                    {{/if}}

                    {{ if msgStatus == "MessageStatusSending"}}
                    <div class="showbox msg_status_loading msg_status_loading_{{msgId}}"  sendTime="{{timeServer}}" msgId="{{msgId}}"   is-display="yes">
                        <div class="loader">
                            <svg class="circular" viewBox="25 25 50 50">
                                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                            </svg>
                        </div>
                    </div>
                    <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}" >
                        <img src="../../public/img/msg/msg_failed.png">
                    </div>
                    {{ else if msgStatus == "MessageStatusFailed"}}
                    <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;">
                        <img src="../../public/img/msg/msg_failed.png">
                    </div>
                    {{/if}}
                </div>
            </div>
</script>


<script id="tpl-receive-msg-img" type="text/html">
    <div class="msg-row msg-left msg-text">
        <div class="msg-avatar">
            <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/user3.png"  userId="{{userId}}" />
        </div>
        <div class="right-msg-body text-align-left">
            {{if roomType == "MessageRoomGroup"}}
            <div class="msg-nickname-time">
                <div class="msg-nickname nickname_{{userId}}">{{nickname}}</div>
            </div>
            <div class="msg-content-img justify-content-end hint--bottom" aria-label="{{msgTime}}" >
                {{else}}
                <div class="msg-content-img justify-content-end hint--bottom" aria-label="{{msgTime}}" style="margin-top:1rem;" >
                    {{/if}}
                    <div class="text-align-right" style="width: {{width}}; height:{{height}}">
                        <img class="msg_img msg-img-{{msgId}}" onload="autoMsgImgSize(this, 300, 200)" />
                    </div>
                </div>
            </div>
        </div>
</script>

<script id="tpl-receive-msg-web" type="text/html">
    <div class="msg-row msg-left msg-text">
        <div class="msg-avatar">
            <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/user3.png"  userId="{{userId}}" />
        </div>
        <div class="right-msg-body text-align-left">
            {{if roomType == "MessageRoomGroup"}}
            <div class="msg-nickname-time">
                <div class="msg-nickname nickname_{{userId}}">{{nickname}}</div>
            </div>
            <div class="msg-content hint--bottom" aria-label="{{msgTime}}">
                {{else }}
                <div>
                    <div class="msg-content hint--bottom" aria-label="{{msgTime}}" style="margin-top: 1rem;">
                        {{/if}}

                        <div class="text-align-right" style=" width: 19rem; height:19rem;"><iframe src="{{hrefURL}}" frameborder="no" width="19rem" height="19rem"></iframe></div>
                    </div>


                    {{if hrefURL}}
                    <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;">
                        <img src="../../public/img/msg/web_msg_click.png" style="width:2rem;height:2rem; left: 22rem ;">
                    </div>
                    {{else}}
                    <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;">
                        <img src="../../public/img/msg/web_msg_unclick.png" style="width:2rem;height:2rem;  left: 22rem;">
                    </div>
                    {{/if}}
                </div>
            </div>
</script>

<script id="tpl-receive-msg-notice" type="text/html">
    <div class="msg-row msg-text">
        <div class="right-msg-body text-align-center">
            <div class="text-align-right msg-notice">
                {{msgContent}}
            </div>
        </div>
    </div>
</script>

<script id="tpl-receive-msg-text" type="text/html">
    <div class="msg-row msg-left msg-text">
        <div class="msg-avatar ">
            <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/user3.png"  userId="{{userId}}" />
        </div>
        <div class="right-msg-body  text-align-left" >
            {{if roomType == "MessageRoomGroup"}}
            <div class="msg-nickname-time">
                <div class="msg-nickname nickname_{{userId}}">{{nickname}}</div>
            </div>
            <div class="msg-content hint--bottom" aria-label="{{msgTime}}">
                {{else}}
                <div class="msg-content hint--bottom" aria-label="{{msgTime}}" style="margin-top: 1rem;">
                    {{/if}}
                    <div class="text-align-right msgContent">{{msgContent}}</div>
                </div>
            </div>
        </div>
</script>




<script id="tpl-chatSession" type="text/html">
    <div class="chatsession-row {{className}} {{chatSessionId}}  chat_session_id_{{chatSessionId}}" chat-session-id="{{chatSessionId}}" msg_time="{{timeServer}}" roomType="{{roomType}}" >
        <div class="chatsession-row-img">
            {{if className == "groupProfile"}}
                <img class="{{groupUserImg}}"  groupId="{{chatSessionId}}"  src="../../public/img/msg/group_default_avatar.png"  />
            {{else}}
                <img class="{{groupUserImg}} user-info-avatar info-avatar-{{chatSessionId}}"  userId="{{chatSessionId}}"  src="../../public/img/msg/default_user.png"  />
            {{/if}}
            {{ if isMute == 0 }}
            {{ if unReadNum > 0}}
            <div class="room-chatsession-unread unread-num room-chatsession-unread_{{chatSessionId}}">{{unReadNum}}</div>
            <div class="room-chatsession-mute  room-chatsession-mute-num_{{chatSessionId}} mute_div" style="display:none;"></div>
            {{ else }}
            <div class="room-chatsession-unread unread-num room-chatsession-unread_{{chatSessionId}}" style="display: none;">{{unReadNum}}</div>
            <div class="room-chatsession-mute  room-chatsession-mute-num_{{chatSessionId}} mute_div" style="display:none;"></div>
            {{/if}}
            {{else}}
            {{ if isMuteMsgNum > 0}}
            <div class="room-chatsession-unread unread-num room-chatsession-unread_{{chatSessionId}}" style="display: none;">{{unReadNum}}</div>
            <div class="room-chatsession-mute room-chatsession-mute-num_{{chatSessionId}} mute_div"></div>
            {{ else }}
            <div class="room-chatsession-unread unread-num room-chatsession-unread_{{chatSessionId}}" style="display: none;">{{unReadNum}}</div>
            <div class="room-chatsession-mute  room-chatsession-mute-num_{{chatSessionId}} mute_div" style="display:none;"></div>
            {{/if}}

            {{/if}}

        </div>
        <div class="chatsession-row-header">
            <div class="chatsession-row-title nickname_{{chatSessionId}}">{{name}}</div>
            <div class="chatsession-row-time" >{{msgTime}}</div>
        </div>
        <div class="chatsession-row-desc">{{msgContent}}</div>
        {{ if isMute >0 }}
        <div class="room-chatsession-mute  room-chatsession-mute_{{chatSessionId}}" >
            <img src="../../public/img/msg/ic_notification_off.png" class="mute">
        </div>
        {{else}}
        <div class="room-chatsession-mute  room-chatsession-mute_{{chatSessionId}}" style="display: none" >
            <img src="../../public/img/msg/ic_notification_off.png" class="mute" >
        </div>

        {{/if}}

    </div>
</script>

<script id="tpl-group-contact" type="text/html">
    <div class="pw-contact-row {{className}} group-profile {{groupId}}" chat-session-id="{{groupId}}">
        <div class="pw-contact-row-image">
            <img class="user-info-avatar info-avatar-{{groupId}}" groupId="{{groupId}}" src="../../public/img/msg/user1.png" />
        </div>
        <div class="pw-contact-row-name">{{groupName}}</div>
    </div>
</script>


<script id="tpl-friend-contact" type="text/html">
    <div class="pw-contact-row {{className}} u2-profile {{userId}}" chat-session-id="{{userId}}">
        <div class="pw-contact-row-image">
            <img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/user1.png"  userId="{{userId}}" />
        </div>
        <div class="pw-contact-row-name">{{nickname}}</div>
    </div>
</script>

<script id="tpl-room-no-data" type="text/html">
    <div class="no-room-data">
        <img src="../../public/img/msg/room_no_data.png">
    </div>
</script>


<script id="tpl-group-no-data" type="text/html">
    <div class="no-room-data">
        <img src="../../public/img/msg/group_no_data.png">
    </div>
</script>

<script id="tpl-apply-friend-info" type="text/html">
    <div class="apply-friend-item">
        <div class="apply-friend-row">
            <div class="apply-friend-img">
                <img class="useravatar info-avatar-{{userId}}" src="../../public/img/msg/user1.png" />
            </div>
            <div class="apply-body">
                <div class="apply-friend-body">
                    <div class="apply-friend-desc">{{nickname}} <span>请求加你为好友</span></div>
                    <div class="apply-friend-operation" userId="{{userId}}">
                        <button class="refused-apply"> 拒绝</button>
                        <button class="agreed-apply"> 同意</button>
                    </div>
                </div>
                <div class="apply-friend-msg">
                    <span>附言</span>{{greetings}}
                </div>
            </div>
        </div>
        <div class="apply-friend-line" ></div>
    </div>
</script>

