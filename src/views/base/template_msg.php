
<script id="tpl-send-msg-img" type="text/html">
    <div class="msg-row msg-right msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}">
        <div class="msg-avatar user-info-avatar">
            {{if avatar}}
                <img class="user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" />
            {{else}}
                <img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png" />
            {{/if}}
        </div>
        <div class="right-msg-body text-align-right">
                 <div class="msg_status" style="margin-top: 1rem;">
                    <div class="msg-content-img justify-content-end hint--bottom msg_content_for_click msg_content_for_click_{{msgId}}" userId="{{userId}}" msgType={{msgType}}   sendTime="{{timeServer}}"  msgId="{{msgId}}"  aria-label="{{msgTime}}">
                        <div class="text-align-left" style="width: {{width}}; height:{{height}}">
                            <img class="msg_img msg-img-{{msgId}} " style="width: {{width}}; height:{{height}}"  src="{{msgImgUrl}}" />
                        </div>
                    </div>
                    {{ if msgStatus == "MessageStatusSending"}}
                        <div class="showbox  msg_status_loading msg_status_loading_{{msgId}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"  is-display="yes">
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


<script id="tpl-send-msg-file" type="text/html">
    <div class="msg-row msg-right msg-text msg-id-{{msgId}} " id="msg-row-{{msgId}}">
        <div class="msg-avatar user-info-avatar">
            {{if avatar}}
            <img class="user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'"/>
            {{else}}
            <img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png" />
            {{/if}}
        </div>
        <div class="right-msg-body text-align-right">
            <div class="msg_status" style="margin-top: 1rem;">
                <div class="msg-content-img justify-content-end hint--bottom " aria-label="{{msgTime}}"">
                    <div class="text-align-left left_msg_file_div msg_content_for_click_{{msgId}} msg_content_for_click"  userId="{{userId}}" msgType="{{msgType}}"   sendTime="{{timeServer}}"  url="{{url}}" msgId="{{msgId}}" originName="{{originName}}">
                        <div class="file_img">
                            <img src="../../public/img/msg/msg_file.png"/>
                        </div>
                        <div class="msg_file_info " >
                            <div class="msg_file_name">{{fileName}}</div>
                            <div class="msg_file_size">{{fileSize}}</div>
                        </div>
                    </div>
                </div>
                {{ if msgStatus == "MessageStatusSending"}}
                <div class="showbox  msg_status_loading msg_status_loading_{{msgId}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"  is-display="yes">
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
    <div class="msg-row msg-right msg-text msg-id-{{msgId}} "id="msg-row-{{msgId}}" > <div class="msg-avatar"> {{if avatar}}<img class="user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}"onerror="this.src='../../public/img/msg/default_user.png'" />{{else}}<img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png" />{{/if}} </div> <div class="right-msg-body  text-align-right" > <div class="msg_status" style="margin-top: 1rem;"> <div class="msg-content hint--bottom msg_content_for_click msg_content_for_click_{{msgId}}" userId="{{userId}}"  aria-label="{{msgTime}}" msgType={{msgType}} sendTime="{{timeServer}}"  msgId="{{msgId}}"> <div class="text-align-left msgContent text-align-left-text "><pre class="msg_content_for_handle" msg_content_for_handle="msg_content_for_handle">{{msgContent}}</pre></div> </div> {{ if msgStatus == "MessageStatusSending"}} <div class="showbox msg_status_loading msg_status_loading_{{msgId}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"   is-display="yes"> <div class="loader"> <svg class="circular" viewBox="25 25 50 50"> <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/> </svg> </div> </div> <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}" > <img src="../../public/img/msg/msg_failed.png"> </div> {{ else if msgStatus == "MessageStatusFailed"}} <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;"> <img src="../../public/img/msg/msg_failed.png"> </div> {{/if}} </div> </div> </div>
</script>

<script id="tpl-send-msg-audio" type="text/html">
    <div class="msg-row msg-right msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}"> <div class="msg-avatar"> {{if avatar}}<img class="user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'"/>{{else}}<img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png" />{{/if}} </div> <div class="right-msg-body  text-align-right" > <div class="msg_status" style="margin-top: 1rem;"> <div class="msg-content hint--bottom " aria-label="{{msgTime}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"> <div class="text-align-left msgContent  "> [你发了一条语音消息，<span style="color: #FFAF5D;cursor: pointer"onclick="displayDownloadApp()">下载客户端</span>收听语音消息吧！] </div> </div> {{ if msgStatus == "MessageStatusSending"}} <div class="showbox msg_status_loading msg_status_loading_{{msgId}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"   is-display="yes"> <div class="loader"> <svg class="circular" viewBox="25 25 50 50"> <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/> </svg> </div> </div> <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}" > <img src="../../public/img/msg/msg_failed.png"> </div> {{ else if msgStatus == "MessageStatusFailed"}} <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;"> <img src="../../public/img/msg/msg_failed.png"> </div> {{/if}} </div> </div> </div>
</script>

<script id="tpl-send-msg-default" type="text/html">
    <div class="msg-row msg-right msg-text msg-id-{{msgId}}"id="msg-row-{{msgId}}" > <div class="msg-avatar"> {{if avatar}}<img class="user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'"/>{{else}}<img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png" />{{/if}} </div> <div class="right-msg-body  text-align-right" > <div class="msg_status" style="margin-top: 1rem;"> <div class="msg-content hint--bottom " aria-label="{{msgTime}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"> <div class="text-align-left msgContent text-align-left-text ">{{msgContent}}</div> </div> {{ if msgStatus == "MessageStatusSending"}} <div class="showbox msg_status_loading msg_status_loading_{{msgId}}" sendTime="{{timeServer}}"  msgId="{{msgId}}"   is-display="yes"> <div class="loader"> <svg class="circular" viewBox="25 25 50 50"> <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/> </svg> </div> </div> <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}" > <img src="../../public/img/msg/msg_failed.png"> </div> {{ else if msgStatus == "MessageStatusFailed"}} <div  class="msg_status_img msg_status_failed_{{msgId}}" msgId="{{msgId}}"  style="display: flex;"> <img src="../../public/img/msg/msg_failed.png"> </div> {{/if}} </div> </div> </div>
</script>

<script id="tpl-send-msg-web" type="text/html">
    <div class="msg-row msg-right msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}">
        <div class="msg-avatar">
            {{if avatar}}
                <img class="user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}"onerror="this.src='../../public/img/msg/default_user.png'" />
            {{else}}
                <img class="user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png" />
            {{/if}}
        </div>
        <div class="right-msg-body  text-align-right" >

            <div class="msg_status" style="margin-top: 1rem;">
                <div class="msg-content hint--bottom " sendTime="{{timeServer}}"    msgId="{{msgId}}"   style="background-color:rgba(244,244,249,1); " aria-label="{{msgTime}}">
                    <div class="text-align-left " style=" width: {{webWidth}}px; height:{{webHeight}}px;">
                        <iframe src="{{linkUrl}}"   frameborder="no" width="{{webWidth}}" height="{{webHeight}}"></iframe>
                        <div class="msg_content_for_click msg_content_for_click_{{msgId}}" userId="{{userId}}" sendTime="{{timeServer}}"  msgType="{{msgType}}" msgId="{{msgId}}"  style="position: absolute; width: 100%; height: 100%; top: 0rem; bottom: 0rem; margin: auto;">
                        </div>
                    </div>
                </div>
                {{if hrefURL}}
                    <div  class="msg_status_img" msgId="{{msgId}}"  style="display: flex;">
                        <img src="../../public/img/msg/web_msg_click.png"  class="web-msg-click" style="width:2rem;height:2rem; left:-3rem;" src-data="{{hrefURL}}">
                    </div>
                {{else}}
                    <div  class="msg_status_img " msgId="{{msgId}}"  style="display: flex;">
                        <img src="../../public/img/msg/web_msg_unclick.png" style="width:2rem;height:2rem; left:-3rem;">
                    </div>
                {{/if}}

                {{ if msgStatus == "MessageStatusSending"}}
                <div class="showbox msg_status_loading msg_status_loading_{{msgId}}" sendTime="{{timeServer}}"    msgId="{{msgId}}"   is-display="yes">
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
    <div class="msg-row msg-left msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}">
        <div class="msg-avatar">
            {{if avatar}}
                 <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" userId="{{userId}}"  msgId="{{msgId}}"/>
            {{ else }}
                <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png"  userId="{{userId}}"  msgId="{{msgId}}"/>
            {{/if}}
        </div>
        <div class="right-msg-body text-align-left">
            {{if roomType == "MessageRoomGroup"}}
                <div class="msg-nickname-time">
                    {{if isMaster}}
                        <div  style="align-items: center; display: flex;justify-content: center;">
                            <span class="chat_master_tip">站长</span>
                        </div>
                    {{/if}}
                    <div class="msg-nickname nickname_{{userId}}">{{nickname}}</div>
                </div>
                <div class="msg-content-img justify-content-end hint--bottom msg_content_for_click msg_content_for_click_{{msgId}}" userId="{{userId}}" msgId="{{msgId}}" imgUrl="{{msgImgUrl}}" msgType="{{msgType}}" aria-label="{{msgTime}}" >
            {{else}}
                 <div class="msg-content-img justify-content-end hint--bottom msg_content_for_click msg_content_for_click_{{msgId}}" userId="{{userId}}" msgId="{{msgId}}"  imgUrl="{{msgImgUrl}}" msgType="{{msgType}}"  aria-label="{{msgTime}}" style="margin-top:1rem;" >
             {{/if}}
                <div class="text-align-right" style="width: {{width}}; height:{{height}}">
                    <img class="msg_img msg-img-{{msgId}} " style="width: {{width}}; height:{{height}}" src="{{msgImgUrl}}"  />
                </div>
            </div>
        </div>
    </div>
</script>


<script id="tpl-receive-msg-file" type="text/html">
    <div class="msg-row msg-left msg-text msg-id-{{msgId}} msg-row-{{msgId}}" id="msg-row-{{msgId}}">
        <div class="msg-avatar">
            {{if avatar}}
                <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" userId="{{userId}}"  msgId="{{msgId}}"/>
            {{else}}
                <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png"  userId="{{userId}}"  msgId="{{msgId}}"/>
            {{/if}}
        </div>
        <div class="right-msg-body text-align-left">
            {{if roomType == "MessageRoomGroup"}}

            <div class="msg-nickname-time">
                {{if isMaster}}
                <div  style="align-items: center; display: flex;justify-content: center;">
                    <span class="chat_master_tip">站长</span>
                </div>
                {{/if}}
                <div class="msg-nickname nickname_{{userId}}">{{nickname}}</div>
            </div>
            <div class="msg-content-img justify-content-end hint--bottom " aria-label="{{msgTime}}"  >
                {{else}}
                <div class="msg-content-img justify-content-end hint--bottom"  aria-label="{{msgTime}}" style="margin-top:1rem;" >
                    {{/if}}
                    <div class="text-align-right right_msg_file_div msg_content_for_click msg_content_for_click_{{msgId}}" userId="{{userId}}" msgType="{{msgType}}"  url="{{url}}" msgId="{{msgId}}" originName="{{originName}}">
                        <div class="file_img">
                            <img src="../../public/img/msg/msg_file.png"/>
                        </div>
                        <div class="right_msg_file_info">
                            <div class="msg_file_name">{{fileName}}</div>
                            <div class="msg_file_size">{{fileSize}}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</script>

<script id="tpl-receive-msg-web" type="text/html">
        <div class="msg-row msg-left msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}">
            <div class="msg-avatar">
                {{if avatar}}
                    <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}"  src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" userId="{{userId}}"  msgId="{{msgId}}"/>
                {{else}}
                    <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png"  userId="{{userId}}"  msgId="{{msgId}}"/>
                {{/if}}
            </div>
            <div class="right-msg-body text-align-left">
                {{if roomType == "MessageRoomGroup"}}
                    <div class="msg-nickname-time">
                        {{if isMaster}}
                        <div  style="align-items: center; display: flex;justify-content: center;">
                            <span class="chat_master_tip">站长</span>
                        </div>
                        {{/if}}
                        <div class="msg-nickname nickname_{{userId}}">{{nickname}}</div>
                    </div>
                    <div class="msg-content hint--bottom" aria-label="{{msgTime}}">
                {{else }}
                    <div>
                        <div class="msg-content hint--bottom " aria-label="{{msgTime}}" style="margin-top: 1rem;">
                {{/if}}

                        <div class="text-align-right " style=" width: {{webWidth}}px; height:{{webHeight}}px">
                            <iframe src="{{linkUrl}}" frameborder="no" width="{{webWidth}}" height="{{webHeight}}"></iframe>
                            <div class="msg_content_for_click msg_content_for_click_{{msgId}}" userId="{{userId}}"  msgType="{{msgType}}" msgId="{{msgId}}"  style="position: absolute; width: 100%; height: 100%; top: 0rem; bottom: 0rem; margin: auto;">
                            </div>
                        </div>
                    </div>

                    {{if hrefURL}}
                        <div  class="msg_status_img  web-msg-click" msgId="{{msgId}}" src-data="{{hrefURL}}" style="display: flex;">
                            <img src="../../public/img/msg/web_msg_click.png"  class="web-msg-click" src-data="{{hrefURL}}" style="width:2rem;height:2rem; left: {{leftWebWidth}}px ;">
                        </div>
                    {{else}}
                        <div  class="msg_status_img " msgId="{{msgId}}"  style="display: flex;">
                            <img src="../../public/img/msg/web_msg_unclick.png" style="width:2rem;height:2rem;  left:{{leftWebWidth}}px;">
                        </div>
                    {{/if}}
            </div>
        </div>
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
    <div class="msg-row msg-left msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}"> <div class="msg-avatar ">{{if avatar}} <img class="{{groupUserImg}} user-info-avatar " src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" userId="{{userId}}"  msgId="{{msgId}}"/>{{else}} <img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}" src="../../public/img/msg/default_user.png"  userId="{{userId}}"  msgId="{{msgId}}"/> {{/if}}</div> <div class="right-msg-body  text-align-left " > {{if roomType == "MessageRoomGroup"}} <div class="msg-nickname-time">  {{if isMaster}} <div  style="align-items: center; display: flex;justify-content: center;"> <span class="chat_master_tip">站长</span> </div> {{/if}}<div class="msg-nickname nickname_{{userId}}">{{nickname}}</div> </div> <div class="msg-content hint--bottom msg_content_for_click msg_content_for_click_{{msgId}}"  userId="{{userId}}"  msgId="{{msgId}}" msgType="{{msgType}}" aria-label="{{msgTime}}"> {{else}} <div class="msg-content hint--bottom msg_content_for_click msg_content_for_click_{{msgId}}" msgId="{{msgId}}" msgType="{{msgType}}" aria-label="{{msgTime}}" style="margin-top: 1rem;"> {{/if}} <div class="text-align-left msgContent text-align-right-text"><pre class="msg_content_for_handle" msg_content_for_handle="msg_content_for_handle">{{msgContent}}</div> </div> </div> </div>
</script>


<script id="tpl-receive-msg-audio" type="text/html">
    <div class="msg-row msg-left msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}"> <div class="msg-avatar "> {{if avatar}}<img class="{{groupUserImg}} user-info-avatar" src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" userId="{{userId}}"  msgId="{{msgId}}"/>{{else}}<img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}" src="../../public/img/msg/default_user.png"  userId="{{userId}}"  msgId="{{msgId}}"/>{{/if}} </div> <div class="right-msg-body  text-align-left " > {{if roomType == "MessageRoomGroup"}} <div class="msg-nickname-time"> {{if isMaster}} <div  style="align-items: center; display: flex;justify-content: center;"> <span class="chat_master_tip">站长</span> </div> {{/if}}<div class="msg-nickname nickname_{{userId}}">{{nickname}}</div> </div> <div class="msg-content hint--bottom "  aria-label="{{msgTime}}"> {{else}} <div class="msg-content hint--bottom" aria-label="{{msgTime}}" style="margin-top: 1rem;"> {{/if}} <div class="text-align-left msgContent text-align-right-text">[你收到一条语音消息，<span style="color: #4C3BB1;cursor: pointer" onclick="displayDownloadApp()">下载客户端</span>收听语音消息吧！]</div> </div> </div> </div>
</script>


<script id="tpl-receive-msg-default" type="text/html">
    <div class="msg-row msg-left msg-text msg-id-{{msgId}}" id="msg-row-{{msgId}}"> <div class="msg-avatar "> {{if avatar}}<img class="{{groupUserImg}} user-info-avatar " src="{{avatar}}" onerror="this.src='../../public/img/msg/default_user.png'" userId="{{userId}}"  msgId="{{msgId}}"/>{{else}}<img class="{{groupUserImg}} user-info-avatar info-avatar-{{userId}}" src="../../public/img/msg/default_user.png"  userId="{{userId}}"  msgId="{{msgId}}"/>{{/if}} </div> <div class="right-msg-body  text-align-left " > {{if roomType == "MessageRoomGroup"}} <div class="msg-nickname-time"> {{if isMaster}} <div  style="align-items: center; display: flex;justify-content: center;"> <span class="chat_master_tip">站长</span> </div> {{/if}}<div class="msg-nickname nickname_{{userId}}">{{nickname}}</div> </div> <div class="msg-content hint--bottom " aria-label="{{msgTime}}"> {{else}} <div class="msg-content hint--bottom" aria-label="{{msgTime}}" style="margin-top: 1rem;"> {{/if}} <div class="text-align-left msgContent text-align-right-text">{{msgContent}}</div> </div> </div> </div>
</script>

<script id="tpl-chatSession" type="text/html">
    <div class="chatsession-row  {{chatSessionId}}  chat_session_id_{{chatSessionId}}" chat-session-id="{{chatSessionId}}" msg_time="{{msgServerTime}}" roomType="{{roomType}}" >
        <div class="chatsession-row-img">
            {{if className == "group-profile"}}
                {{if avatar}}
                    <img class="user-info-avatar info-avatar-{{chatSessionId}}" groupId="{{chatSessionId}}"  src="{{avatar}}" onerror="this.src='../../public/img/msg/group_default_avatar.png'" />
                {{else}}
                    <img class="user-info-avatar info-avatar-{{chatSessionId}}" groupId="{{chatSessionId}}"  src="../../public/img/msg/group_default_avatar.png"  />
                {{/if}}
            {{else}}
                {{if avatar}}
                    <img class="user-info-avatar info-avatar-{{chatSessionId}}"  userId="{{chatSessionId}}"  src="{{avatar}}"  onerror="this.src='../../public/img/msg/default_user.png'"/>
                {{else}}
                    <img class="user-info-avatar info-avatar-{{chatSessionId}}"  userId="{{chatSessionId}}"  src="../../public/img/msg/default_user.png"  />
                {{/if}}
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
            <div class="chatsession-row-title ">
                <div style="display: flex;justify-content: center;align-items: center">
                    {{if isSiteMaster }}
                    <div style="width:28px;height:14px;background:rgba(249,183,38,1); text-align:center; border-radius:2px;font-size:10px; font-family:PingFangSC-Regular; font-weight:400; color:rgba(255,255,255,0.87); line-height:14px;margin-right: 0.5rem;">站长</div>
                    {{/if}}
                    <span class="chatsession_nickname_{{chatSessionId}}"> {{name}}</span>

                </div>

            </div>
            <div class="chatsession-row-time" msgTime="{{msgServerTime}}">{{msgTime}}</div>
        </div>
        <div class="chatsession-row-desc chatsession-row-desc-{{chatSessionId}}">{{msgContent}}</div>
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
    <div class="pw-contact-row   contact-row-group-profile {{groupId}}" chat-session-id="{{groupId}}" group-name="{{groupName}}" group-name-latin="{{nameInLatin}}">
        <div class="pw-contact-row-image">
            {{if groupAvatarImg}}
                <img class="user-info-avatar " src="{{groupAvatarImg}}"  onerror="this.src='../../public/img/msg/group_default_avatar.png'"/>
            {{else}}
                <img class="user-info-avatar " src="../../public/img/msg/group_default_avatar.png" />
            {{/if}}
        </div>
        <div class="pw-contact-row-name">{{groupName}}</div>
    </div>
</script>


<script id="tpl-friend-contact" type="text/html">
    <div class="pw-contact-row  contact-row-u2-profile {{userId}}" chat-session-id="{{userId}}" friend-name="{{nickname}}" friend-name-latin="{{nicknameInLatin}}">
        <div class="pw-contact-row-image">
            {{if friendAvatarImg}}
                <img class="user-info-avatar"  src="{{friendAvatarImg}}"  onerror="this.src='../../public/img/msg/default_user.png'"/>
            {{else}}
                <img class="user-info-avatar"  src="../../public/img/msg/default_user.png" />
            {{/if}}
        </div>
        <div class="pw-contact-row-name nickname_{{userId}} profile_nickname_{{userId}}">{{nickname}}</div>
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
                <img class="useravatar info-avatar-{{userId}}" src="../../public/img/msg/default_user.png" />
            </div>
            <div class="apply-body">
                <div class="apply-friend-body">
                    <div class="apply-friend-desc">{{nickname}} <span data-local-value="applyFriendTip">新朋友</span></div>
                    <div class="apply-friend-operation" userId="{{userId}}">
                        <button class="refused-apply" data-local-value="refuseTip"> 拒绝</button>
                        <button class="agreed-apply" data-local-value="agreeTip"> 接受 </button>
                    </div>
                </div>
                <div class="apply-friend-msg">
                    <span data-local-value="introductionTip">附言</span>{{greetings}}
                </div>
            </div>
        </div>
        <div class="apply-friend-line" ></div>
    </div>
</script>

<script id="tpl-group-user-menu" type="text/html">
    <div id="group-user-menu" userId="{{userId}}" >
        {{if isFriend == 1 }}
            <div class="item p-2" id="open-temp-chat" data-local-value="openChatTip">发起聊天</div>
        {{else if(isCanAddFriend  || (!isCanAddFriend && isAdmin == 1))}}
            <div class="item p-2" id="add-friend" data-local-value="addFriendTip"> 添加好友</div>
        {{/if}}

        {{if isAdmin == 1}}
            {{if (isOwner == 1 && !memberIsAdmin)}}
                <div class="item p-2" id="set-admin" data-local-value="setAdminTip">设为管理员</div>
            {{else if (isOwner == 1 && memberIsAdmin != false)}}
                 <div class="item p-2" id="remove-admin" data-local-value="removeAdminTip">移除管理员</div>
            {{/if}}

            {{if (isOwner == 1 || (!memberIsAdmin && !memberIsOwner))}}
                <div class="item p-2" id="remove-group-chat" data-local-value="removeGroupMemberTip">移除成员</div>
            {{/if}}

        {{/if}}
    </div>

</script>


<script id="tpl-msg-menu" type="text/html">
    <div id="msg-menu" style="position: absolute;top:{{top}}px; left:{{left}}px"  msgId="{{msgId}}">

        <div style="margin-top:1rem;margin-bottom: 1rem;">
            {{ if isCopy }}
            <div class="item p-2"  onclick="copyMsg('{{msgId}}', event)" data-local-value="copyTip">复制</div>
            {{/if}}
            {{if isSee }}
            <div class="item p-2" onclick="seeMsg('{{msgId}}', event)" data-local-value="seeBigImgTip">查看大图</div>
            {{/if}}
            {{ if isSave }}
            <div class="item p-2" onclick="downloadMsg('{{msgId}}', event)" data-local-value="downloadTip">下载</div>
            {{/if}}
            {{ if isRecall }}
            {{if recallDisabled }}
            <div class="item p-2" onclick="recallMsg('', event)"  style="color:rgba(180,180,180,1);"  data-local-value="disableRecallTip">撤回</div>
            {{else}}
            <div class="item p-2" onclick="recallMsg('{{msgId}}', event)"   data-local-value="recallTip">撤回</div>
            {{/if}}
            {{/if}}
        </div>
    </div>


</script>


<script id="tpl-self-info" type="text/html">
<div id="selfInfo" class="siteSelfInfo" style="z-index: 10001;">
    <div id="triangle_left"></div>
    <div id="selfInfoDiv" class="selfInfoDiv siteSelfInfo" style="position: absolute;width: 100%;">
        <div class="edit_self_custom siteSelfInfo" onclick="editSelfCustom('edit')" data-local-value="editTip" type="edit">编辑</div>

        <div id="selfAvatarUploadDiv" class="d-flex flex-row justify-content-center siteSelfInfo" style="margin-top: 3rem; text-align: center;position: relative" >
            <img id="user-image-upload" class="user-image-upload info-avatar-{{userId}} siteSelfInfo" src="{{avatar}}" onerror="src='./public/img/msg/default_user.png'"  style="width: 5rem; height: 5rem;" onclick="uploadFile('file2', 'user_avatar')" />
        </div>
        <div class="d-flex flex-row justify-content-center selfNickNameDiv"  >
            <div style="margin-left: 1rem;" class="siteSelfInfo nickname_{{userId}}">{{nickname}}</div>
        </div>

        <div class="d-flex flex-row justify-content-center selfNickNameDiv siteSelfInfo"  >
             <input type="text"    style="padding: 0rem;" class="loginName create_group_box_div_input siteSelfInfo"  value="ID: {{loginName}}" disabled>
        </div>
        {{if isMaster}}
            <div class="d-flex flex-row siteSelfInfo" style="display: flex;justify-content: center;align-items: center;text-align: center;margin-bottom: 0.5rem;" >
                <div class="siteSelfInfo master_tip">
                    站长
                </div>
            </div>
        {{/if}}

        <div style="text-align: center;margin:0.12rem auto;width: 34rem; height:1px;background:rgba(223,223,223,1);margin-top: 1rem" ></div>

        <div class="d-flex flex-row justify-content-center">
            <div class="self_profile_item self-qrcode siteSelfInfo" id="logout" >
                <span class="logout-span siteSelfInfo" id="logout-span" data-local-value="logoutTip" onclick="logout(event)">退出</span>
            </div>
        </div>
    </div>
    <div id="selfCutsomInfoDiv" class="selfInfoDiv siteSelfInfo" >
        <div class="edit_self_custom siteSelfInfo" onclick="editSelfCustom('finish')" data-local-value="finishTip" type="finsh">{{finishTip}}</div>
        <div id="selfAvatarUploadDiv" class="d-flex flex-row justify-content-center siteSelfInfo" style="margin-top: 3rem; text-align: center;position: relative" >
                        <img id="user-image-upload" class="user-image-upload info-avatar-{{userId}} siteSelfInfo" src="{{avatar}}" onerror="src='./public/img/msg/default_user.png'"  style="width: 5rem; height: 5rem;" onclick="uploadFile('file2', 'user_avatar')" />
                        <img id="user-img-carmera" class="user-img-carmera siteSelfInfo" src="./public/img/camera.png" style="width: 5rem; height: 5rem; position: absolute;
                                 margin-left: -5rem;" onclick="uploadFile('file2', 'user_avatar')" />
                        <input type="file" id="file2" style="display:none" onchange="uploadUserImgFromInput(this)" accept="image/gif,image/jpeg,image/jpg,image/png,image/svg">
        </div>
        <div class="d-flex flex-row justify-content-center selfNickNameDiv editSelfNickNameDiv siteSelfInfo"  >
            {{if !nickname }}
            <div style="margin-left: 1rem;" class="nickNameDiv siteSelfInfo "> <img src="./public/img/edit.png" class="siteSelfInfo" style="width: 1rem;height:1rem"/></div>
            {{else}}
                        <div style="margin-left: 1rem;" class="nickNameDiv siteSelfInfo">{{nickname}} <img src="./public/img/edit.png" style="width: 1rem;height:1rem"/></div>
            {{/if}}
        </div>

        <div class="d-flex flex-row justify-content-center selfNickNameDiv siteSelfInfo"  >
            <input type="text" style="padding: 0rem;" class="loginName create_group_box_div_input siteSelfInfo"  value="ID: {{loginName}}" disabled>
        </div>
        {{if isMaster}}
        <div class="d-flex flex-row siteSelfInfo" style="display: flex;justify-content: center;align-items: center;text-align: center;margin-bottom: 0.5rem;" >
            <div class="siteSelfInfo master_tip">
                站长
            </div>
        </div>
        {{/if}}
        <div style="text-align: center;margin:0.12rem auto;width: 34rem; height:1px;background:rgba(223,223,223,1);" ></div>

        <div style="width: 100%;margin-bottom: 3rem;siteSelfInfo">
            {{each customs custom}}
            <div class="siteSelfInfo self_custom_info">
                <div class="custom_name_tip siteSelfInfo">{{custom['customName']}}</div> <input type="text" value="{{custom['customValue']}}" onkeydown="updateUserCustomInfo(event, this)" class="siteSelfInfo edit_custom_info customs self_custom_edit_{{custom['customKey']}} siteSelfInfo" customName = "{{custom['customName']}}" customKey="{{custom['customKey']}}" >
            </div>
            {{/each}}
        </div>

    </div>
    <div id="selfQrcodeDiv" class="selfQrcodeDiv"  style="position: absolute;display: none;">
        <div id="selfQrcodeCanvas">

        </div>
    </div>
</div>

</script>

<script id="tpl-remove-member" type="text/html">

<div class="pw-contact-row choose-member {{userId}} "  user-id="{{userId}}">
    <div class="pw-contact-row-image">
        <img class="useravatar info-avatar-{{userId}}" src="../../public/img/msg/default_user.png" />
    </div>
    <div class="pw-contact-row-name">{{nickname}}</div>
    <div class="pw-contact-row-checkbox remove_people" user-id="{{userId}}">
        <img  src="../../public/img/msg/member_unselect.png" />
    </div>
</div>

</script>


<script id="tpl-speaker-member" type="text/html">
       {{if isSpeaker == true}}
        <div class="pw-contact-row choose-member remove-speaker {{userId}} "  userId="{{userId}}"  nickname="{{nickname}}" avatar="{{avatar}}">
        {{else}}
        <div class="pw-contact-row choose-member {{userId}} "  userId="{{userId}}"  nickname="{{nickname}}" avatar="{{avatar}}">
        {{/if}}
        <div class="pw-contact-row-image">
            {{if memberAvatarImg }}
                <img class="useravatar info-avatar-{{userId}}" src="{{memberAvatarImg}}"  onerror="this.src='../../public/img/msg/default_user.png'"/>
            {{else}}
                <img class="useravatar info-avatar-{{userId}}" src="../../public/img/msg/default_user.png" />
            {{/if}}
        </div>
        <div class="pw-contact-row-name">{{nickname}}</div>
            {{if isMemberType == "member" && isSelfAdminRole == true}}
                {{if isSpeaker == true}}
                <div class="pw-contact-row-btn speaker_remove_people" >
                    <button class="remove_speaker_btn" userId="{{userId}}"  nickname="{{nickname}}" avatar="{{avatar}}" data-local-value="cancelTip">取消</button>
                </div>
                {{else}}
                <div class="pw-contact-row-btn speaker_add_people" >
                    <button class="add_speaker_btn" userId="{{userId}}" nickname="{{nickname}}" avatar="{{avatar}}" data-local-value="addTip">添加</button>
                </div>
                {{/if}}
            {{/if}}
        </div>
</script>

<script id="tpl-group-member-info" type="text/html">
    <div style="position: relative;">
        <img style="width: 1rem;height:1rem; position: absolute;right: 2rem;top: 1rem;cursor: pointer" onclick="closeGroupMemberInfo()"  src="../../public/img/msg/btn-close.png">
    </div>
    <div class="group-member-img">
        {{if memberAvatarImg}}
            <img  class="useravatar info-avatar-{{userId}}"  src="{{memberAvatarImg}}"  onerror="this.src='../../public/img/msg/default_user.png'"/>
        {{else}}
            <img  class="useravatar info-avatar-{{userId}}"  src="../../public/img/msg/default_user.png" />
        {{/if}}
    </div>
    <div class="group-member-nickname">
        {{nickname}}
    </div>
    <div class="group-member-loginname">
        {{loginName}}
    </div>

    {{if isSelf == false}}
        <div class="group-member-line">
        </div>
        {{if relation == "FriendRelationFollow"}}
            <button class="group-member-btn open_chat" data-local-value="openChatTip" userId="{{userId}}"> 发起聊天</button>
        {{else relation != "FriendRelationFollow" }}
            {{if isCanAddFriend || isAdmin}}
                <button class="group-member-btn add-friend-by-group-member" data-local-value="addFriendTip" userId="{{userId}}">添加好友</button>
            {{/if}}
        {{/if}}
    {{/if}}

</script>



<script id="tpl-group-member-for-speaker" type="text/html">
    <div class="speaker-group-member">
        <div class="sub-speaker-div">
            <div class="sub-speaker-title" data-local-value="otherGroupMemberTip"> 其他成员 </div>
        </div>
        <div class="speaker-line"></div>
        <div class="speaker-group-member-div" style="width: 100%;margin-bottom: 5rem;">
        </div>
    </div>
</script>

<script id="tpl-group-member-list" type="text/html">
        <div class="pw-contact-row choose-member  group-member {{userId}} "  userId="{{userId}}" nickname="{{nickname}}"  loginName="{{loginName}}">
            <div class="pw-contact-row-image">
                {{if  memberAvatarImg}}
                <img  class="useravatar"  src="{{memberAvatarImg}}" onerror="this.src='../../public/img/msg/default_user.png'" />
                {{else}}
                <img  class="useravatar"  src="../../public/img/msg/default_user.png" />
                {{/if}}
            </div>
            <div class="pw-contact-row-name">{{nickname}}</div>
            {{if isType == "owner"}}
            <div class="pw-contact-row-btn speaker_remove_people" >
                <button class="group_owner" userId="{{userId}}"  data-local-value="groupOwnerTip" disabled>群主</button>
            </div>
            {{else if isType == "admin" }}
            <div class="pw-contact-row-btn speaker_add_people" >
                <button class="group_admin" userId="{{userId}}" data-local-value="groupAdminTip"   disabled>管理员</button>
            </div>
            {{else}}
            {{if isPermission == "admin"}}
                <div class="remove_member_btn_div" >
                    <button class="remove_group_btn" userId="{{userId}}" data-local-value="removeMemberTip" >移除</button>
                </div>
            {{/if}}
            {{/if}}
        </div>
</script>

<script id="tpl-group-member-body" type="text/html">
    <div class="group-member-body-div member_body_{{num}}">
    </div>
</script>
<script id="tpl-group-member-body-detail" type="text/html">
    <div style="display: flex" class="hint--bottom-left aria-lable-{{userId}}" aria-label="{{nickname}}">
        {{if memberAvatarImg}}
            <img class="useravatar group-member-avatar "  src="{{memberAvatarImg}}" />
        {{else}}
            <img class="useravatar group-member-avatar "  src="../../public/img/msg/default_user.png" />
        {{/if}}
    </div>
</script>


<script id="tpl-invite-member" type="text/html">

    <div class="pw-contact-row choose-member {{userId}} "  user-id="{{userId}}">
        <div class="pw-contact-row-image">
            {{if friendAvatarImg}}
                <img  class="useravatar"  src="{{friendAvatarImg}}"  onerror="this.src='{{siteAddress}}/public/img/msg/default_user.png'"/>
            {{else}}
                <img  class="useravatar"  src="{{siteAddress}}/public/img/msg/default_user.png" />
            {{/if}}
        </div>
        <div class="pw-contact-row-name">{{nickname}}</div>
        <div class="pw-contact-row-checkbox select_people">
            <img src="{{siteAddress}}/public/img/msg/member_unselect.png" />
        </div>
    </div>

</script>

<script id="tpl-invite-member-no-data" type="text/html">

    <div class="no_data">
        <div class="d-flex">
            <div class="p-2">
                <img class="no_data_img" src="{{siteAddress}}/public/img/no_data.png"/>
            </div>
            <div class="p-2 no_data_tip" data-local-value="noFriendDataTip">暂无好友</div>
        </div>
    </div>
</script>

<script id="tpl-nickname-div" type="text/html">
   <input type="text" id="selfNickname"  style="padding: 0rem;" class="nickname create_group_box_div_input siteSelfInfo"  value="{{nickname}}" onkeydown="updateSelfNickName(event);" />
</script>

<script id="tpl-group-name-div" type="text/html">
    {{if editor == 1 }}
        <input type="text" id="groupName" maxlength="10" style="padding: 0rem;height:2rem;outline:none;margin-top:1rem;"  value="{{groupName}}" onkeydown="updateGroupNameName(event);" />
    {{else}}
            <div class="action-btn groupName">
        {{groupName}}
            </div>
    {{/if}}
</script>

<script id="tpl-share-group-div" type="text/html">
    <div class="close_div">
        <img src="../../public/img/close.png" onclick="closeMaskDiv('#share_group');">
    </div>

        <div  style="width: 23rem;margin: 0 auto;margin-top: 3rem; ">
            <div class="qrcodeCanvas-title" >
                <div class="header" style="width: 5rem;height: 5rem;margin-right: 1rem">
                    <img class="group_avatar info-avatar-{{groupId}}" src="../../public/img/msg/group_default_avatar.png" style="width: 5rem;height: 5rem;">
                </div>
                <div>
                    <div class="name" style="margin-top: 1rem;">
                        <span style="font-size:1.69rem;font-family:PingFangSC-Regular;color:rgba(20,16,48,1);">{{groupName}} </span>
                    </div>
                    <div class="name" >
                        <span style="font-size:1.31rem;font-family:PingFangSC-Regular;color:rgba(153,153,153,1);"> {{siteName}}</span>
                    </div>
                </div>
            </div>
            <div id="qrcodeCanvas" >
            </div>
        </div>

        <div class="d-flex flex-row justify-content-center width-percent100" style="margin-top: 2rem;" >
            <button type="button" class="btn create_button copy-share-group"  data-local-value="copyGroupQrcodeUrlTip">复制链接</button>
            <button type="button" class="btn create_button save-share-group" data-local-value="saveGroupQrcodeImg">保存图片</button>
        </div>
</script>

<script id="tpl-download-app-div" type="text/html">
   <div>
       <div class="app_download_header" data-local-value="shareSiteTip" >下载APP，随时随地保持联系</div>
       <div class="app_download_subheader" data-local-value="shareSiteCommentTip">扫描安装APP，在APP内访问站点收发消息，体验更多功能！</div>
       <div id="qrcodeCanvas"></div>
       <div class="download_button_div" style="margin-left:26rem;margin-top:2rem">
           <div class="ios_info">
               <img src="../../public/img/msg/ios.png" style="width: 2.1rem;height:2.8rem;margin-right:1rem;">iOS

           </div>
           <div class="android_info">
               <img src="../../public/img/msg/android.png" style="width: 2.3rem;height:2.8rem;margin-right: 1rem;">Android
           </div>
       </div>
   </div>
</script>

<script id="tpl-add-friend-div" type="text/html">

        <div class="close_div">
            <img src="../../public/img/close.png" onclick="closeMaskDiv('#add-friend-div');">
        </div>
        <div class="flex-container justify-content-center" >
            <div class="header_tip_font  align-items-center" style="margin-top: 6rem;" data-local-value="addFriendTip">添加好友</div>
        </div>

        <div class="d-flex flex-row justify-content-center add-friend-div-img"  >
            <img  class="user-image-for-add info-avatar-{{userId}}" src="../../public/img/msg/default_user.png" style="width: 8rem; height: 8rem;" />
        </div>
        <div class="d-flex flex-row justify-content-center user-nickname-for-add">
            {{nickname}}
        </div>

        <div class="d-flex flex-row justify-content-center" >
            <input type="text" class="form-control  create_group_box_div_input apply-friend-reason" onkeydown="addFriendByKeyDown(event)" data-local-placeholder="addFriendReasonPlaceholder"  placeholder="请输入附言" >
        </div>

        <div class="line"></div>

        <div class="d-flex flex-row justify-content-center width-percent100 margin-top10" style="text-align:center; ">
            <button type="button" class="btn create_button apply-friend" data-local-value="sendTip">发送</button>
        </div>

    </script>



<script id="tpl-string" type="text/html">
   {{string}}
</script>

<script id="tpl-search-user-info" type="text/html">
    {{if userId != token}}
        <div class="search-user-detail">
            <div class="search-user-info">
                <img src="../../public/img/msg/default_user.png" class="user-image-for-search info-avatar-{{userId}}"/>
                <span class="search-user-title">{{nickname}}</span>
            </div>
            <div style="width: 5.63rem;height: 2.63rem;margin-right: 2rem;">
                <button class="search-add-friend-btn search-friend-{{userId}}" data-local-value="addTip" userId="{{userId}}">添加</button>
            </div>
        </div>
    {{else}}
    <div class="search-user-img">
        <img src="../../public/img/no_data.png">
    </div>
    {{/if}}
</script>

<script id="tpl-search-user-info-void" type="text/html">
    <div class="search-user-img">
        <img src="../../public/img/no_data.png">
        </div>
</script>

<script id="tpl-search-user-div" type="text/html">
    <div class="search-user-header">
        <div class="search-user-header-content">
        <input type="text" class="form-control create_group_box_div_input search-user-input" onkeydown="searchUserByKeyDown(event)" onblur="searchUserByOnBlur(event)"  data-local-placeholder="searchFriendPlaceholder" placeholder="搜索好友">
        </div>
        <img src="../../public/img/msg/search_icon.png" style="width:2rem; height:2rem;">
        </div>
        <div class="search-user-content">
        <div class="search-user-img">
        <img src="../../public/img/msg/search_friend.png">
        </div>

        </div>
</script>

<script type="text/html" id="tpl-create-group-div">
    <div>
        <div class="close_div">
            <img src="../../public/img/close.png" onclick="closeMaskDiv('#create-group');">
        </div>
        <div class="flex-container justify-content-center" >
            <div class="header_tip_font align-items-center"  data-local-value="createGroupTip">创建群组</div>
        </div>

        <div class="d-flex flex-row justify-content-center">
            <input type="text" class="form-control group_name create_group_box_div_input" maxlength="10"  onkeydown="createGroupByKeyDown(event)"  data-local-placeholder="enterGroupNamePlaceholder" placeholder="请输入群组名称" >
        </div>

        <div class="line"></div>

        <div  class="d-flex flex-row justify-content-center  data_tip" data-local-value="createGroupNameTip">
            群组名称长度限制1-10
        </div>
        {{if enableCreateGroup }}
            <div class="d-flex flex-row justify-content-center width-percent100 margin-top10" style="text-align:center; ">
                <button type="button" class="btn create_button create_group_button" data-local-value="createTip">创建</button>
            </div>
        {{else}}
            <div class="d-flex flex-row justify-content-center width-percent100 margin-top10" style="text-align:center; ">
                <button type="button" class="btn create_button " onclick="enableCreateGroupTip()" data-local-value="createTip" style="background: rgba(201,201,201,1);">创建</button>
            </div>
        {{/if}}
    </div>
</script>


<script type="text/html" id="tpl-desc-group-div">
    <div class="group-desc-div">
        <div class="group-desc-header" >
            <span data-local-value="groupProfileDescTip">群介绍</span>
            {{ if isAdmin }}
            <button class="edit_group_desc" data-local-value="groupProfileEditTip" type="edit">编辑</button>
            {{/if}}
        </div>
        <div class="group-desc-detail">
            <div class="group-desc-area">
                {{descBody}}
            </div>
        </div>
        <div class="group-desc-footer">
            <div class="group-desc-line"></div>
        </div>
    </div>
</script>


<script type="text/html" id="tpl-desc-group-textarea">
    <textarea class="textarea_desc">{{descBody}}</textarea>
</script>


<script type="text/html" id="tpl-plugin-item">
    <div class="pw-home-row mini-program plugin-info pw-contact-row" plugin-name="{{name}}" plugin-id="{{id}}" plugin-landingPageUrl="{{landingPageUrl}}" plugin-duckchatSessionId="{{duckchatSessionId}}" plugin-loadingType="{{loadingType}}">
        <div class="pw-home-row-image" style="position: relative;">
            {{if logo}}
                <img class="plugin_logo" src="{{logo}}" onerror="this.src='{{siteAddress}}/public/img/plugin/default.png'"/>
            {{else}}
                    <img class="plugin_logo" src="{{siteAddress}}/public/img/plugin/default.png"/>
            {{/if}}
        </div>
        <div class="pw-home-row-name" data-local-value="miniProgramTip">{{name}}</div>
    </div>
</script>

<script type="text/html" id="tpl-input-tools-item">

        <img src="{{logo}}" class="chat_plugin plugin_logo" plugin-id="{{id}}" plugin-name="{{name}}"
             plugin-duckchatSessionId="{{duckchatSessionId}}"
             plugin-loadingType="{{loadingType}}"
             plugin-landingPageUrl="{{landingPageUrl}}" onerror="this.src='../../public/img/plugin/default.png'">

</script>

<script type="text/html" id="tpl-friend-profile">
    <div>
        <div style="width: 100%;text-align: center">{{nickname}}</div>
        <div class="user-desc-loginName">    ID: {{loginName}}</div>
        {{if isMaster}}
        <div style="width: 100%;text-align: center;display: flex;justify-content: center;align-items: center;margin-top:0.5rem;">
            <div class="master_tip" style="align-items: center;padding-bottom: 2px;">
                站长
            </div>
        </div>
        {{/if}}
    </div>

</script>


<script type="text/html" id="tpl-friend-profile-more-info">
    <div class="more-info-title" data-local-value="moreInfoTip">更多资料</div>
    <div style="overflow-y: scroll;" class="friend_more_info">
        {{each customs custom}}
        <div class="custom_info">
            <div class="custom_name">{{custom['customName']}}</div>
            <div class="custom_value">{{custom['customValue']}}</div>
        </div>
        {{/each}}
    </div>
</script>