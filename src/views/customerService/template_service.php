

<script id="tpl-send-msg-text" type="text/html">
    <div class="msg-row msg-right msg-text msg-id-{{msgId}} "id="msg-row-{{msgId}}" > <div class="msg-avatar"> {{if avatar}}<img class="user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}"onerror="this.src='../../public/img/msg/default_user.png'" />{{else}}<img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png" />{{/if}} </div> <div class="right-msg-body  text-align-right" > <div class="msg_status" style="margin-top: 10px;"> <div class="msg-content hint--bottom msg_content_for_click msg_content_for_click_{{msgId}}" userId="{{userId}}"  aria-label="{{msgTime}}" msgType={{msgType}} sendTime="{{timeServer}}"  msgId="{{msgId}}"> <div class="text-align-left msgContent text-align-left-text "><pre class="msg_content_for_handle" msg_content_for_handle="msg_content_for_handle">{{msgContent}}</pre></div> </div> {{ if msgStatus == "MessageStatusSending"}} <div class="showbox msg_status_loading msg_status_loading_{{msgId}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"   is-display="yes"> <div class="loader"> <svg class="circular" viewBox="25 25 50 50"> <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/> </svg> </div> </div> <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}" > <img src="../../public/img/msg/msg_failed.png"> </div> {{ else if msgStatus == "MessageStatusFailed"}} <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;"> <img src="../../public/img/msg/msg_failed.png"> </div> {{/if}} </div> </div> </div>
</script>

<script id="tpl-send-msg-default" type="text/html">
    <div class="msg-row msg-right msg-text msg-id-{{msgId}}"id="msg-row-{{msgId}}" > <div class="msg-avatar"> {{if avatar}}<img class="user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'"/>{{else}}<img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png" />{{/if}} </div> <div class="right-msg-body  text-align-right" > <div class="msg_status" style="margin-top: 10px;"> <div class="msg-content hint--bottom " aria-label="{{msgTime}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"> <div class="text-align-left msgContent text-align-left-text ">{{msgContent}}</div> </div> {{ if msgStatus == "MessageStatusSending"}} <div class="showbox msg_status_loading msg_status_loading_{{msgId}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"   is-display="yes"> <div class="loader"> <svg class="circular" viewBox="25 25 50 50"> <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/> </svg> </div> </div> <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}" > <img src="../../public/img/msg/msg_failed.png"> </div> {{ else if msgStatus == "MessageStatusFailed"}} <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;"> <img src="../../public/img/msg/msg_failed.png"> </div> {{/if}} </div> </div> </div>
</script>

<script id="tpl-receive-msg-img" type="text/html">

</script>

<script id="tpl-receive-msg-file" type="text/html">

</script>

<script id="tpl-receive-msg-web" type="text/html">

</script>

<script id="tpl-receive-msg-web-notice" type="text/html">
    <div class="msg-row msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}">
        <div class="right-msg-body text-align-center">
            <div class="text-align-right msg-notice">
                <iframe src="{{hrefUrl}}" frameborder="no" height="100%;" class="zalyiframe"></iframe>
            </div>
        </div>
    </div>
</script>

<script id="tpl-receive-msg-notice" type="text/html">
    <div class="msg-row msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}">
        <div class="right-msg-body text-align-center">
            <div class="text-align-right msg-notice">
                {{msgContent}}
            </div>
        </div>
    </div>
</script>

<script id="tpl-receive-msg-text" type="text/html">
    <div class="msg-row msg-left msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}"> <div class="msg-avatar "> {{if avatar}} <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}} " src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" userId="{{userId}}"  msgId="{{msgId}}"/> {{else}} <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}" src="../../public/img/msg/default_user.png"  userId="{{userId}}"  msgId="{{msgId}}"/> {{/if}} </div> <div class="right-msg-body  text-align-left " > <div class="msg-nickname-time"> <div class="msg-nickname nickname_{{userId}}">{{nickname}}</div> </div> <div class="msg-content hint--bottom msg_content_for_click msg_content_for_click_{{msgId}}"  userId="{{userId}}"  msgId="{{msgId}}" msgType="{{msgType}}" aria-label="{{msgTime}}"> <div class="text-align-left msgContent text-align-right-text"><pre class="msg_content_for_handle" msg_content_for_handle="msg_content_for_handle">{{msgContent}}</div> </div> </div> </div>
</script>

<script id="tpl-receive-msg-audio" type="text/html">
    <div class="msg-row msg-left msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}"> <div class="msg-avatar "> {{if avatar}}<img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}" src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" userId="{{userId}}"  msgId="{{msgId}}"/>{{else}}<img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}" src="../../public/img/msg/default_user.png"  userId="{{userId}}"  msgId="{{msgId}}"/>{{/if}} </div> <div class="right-msg-body  text-align-left " > {{if roomType == "MessageRoomGroup"}} <div class="msg-nickname-time"> {{if isMaster}} <div  style="align-items: center; display: flex;justify-content: center;"> <span class="chat_master_tip">站长</span> </div> {{/if}}<div class="msg-nickname nickname_{{userId}}">{{nickname}}</div> </div> <div class="msg-content hint--bottom "  aria-label="{{msgTime}}"> {{else}} <div class="msg-content hint--bottom" aria-label="{{msgTime}}" style="margin-top: 10px;"> {{/if}} <div class="text-align-left msgContent text-align-right-text">[你收到一条语音消息，<span style="color: #4C3BB1;cursor: pointer" onclick="displayDownloadApp()">下载客户端</span>收听语音消息吧！]</div> </div> </div> </div>
</script>

<script id="tpl-receive-msg-default" type="text/html">
    <div class="msg-row msg-left msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}"> <div class="msg-avatar "> {{if avatar}}<img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}" src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" userId="{{userId}}"  msgId="{{msgId}}"/>{{else}}<img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}" src="../../public/img/msg/default_user.png"  userId="{{userId}}"  msgId="{{msgId}}"/>{{/if}} </div> <div class="right-msg-body  text-align-left " > {{if roomType == "MessageRoomGroup"}} <div class="msg-nickname-time"> {{if isMaster}} <div  style="align-items: center; display: flex;justify-content: center;"> <span class="chat_master_tip">站长</span> </div> {{/if}}<div class="msg-nickname nickname_{{userId}}">{{nickname}}</div> </div> <div class="msg-content hint--bottom " aria-label="{{msgTime}}"> {{else}} <div class="msg-content hint--bottom" aria-label="{{msgTime}}" style="margin-top: 10px;"> {{/if}} <div class="text-align-left msgContent text-align-right-text">{{msgContent}}</div> </div> </div> </div>
</script>
