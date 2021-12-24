<?php
/**
 * Created by PhpStorm.
 * User: anguoyue
 * Date: 18/10/2018
 * Time: 11:17 AM
 */

class LoginConfig
{

    const LOGIN_NAME_ALIAS = "loginNameAlias"; //登录名别称

    const NICK_NAME_REQUIRED = "nicknameRequired"; //昵称是否必填

    const PASSWORD_RESET_WAY = "passwordResetWay";//密码重置方式

    const PASSWORD_RESET_REQUIRED = "passwordResetRequired";//找回密码方式是否必填

    const LOGIN_PAGE_WELCOME_TEXT = "loginWelcomeText";//登陆页面欢迎文案

    const LOGIN_PAGE_BACKGROUND_COLOR = "loginBackgroundColor";//登陆页面背景颜色

    const LOGIN_PAGE_BACKGROUND_IMAGE = "loginBackgroundImage";//登陆页面背景图片

    const LOGIN_PAGE_BACKGROUND_IMAGE_DISPLAY = "loginBackgroundImageDisplay";//登陆页面背景图片展示方式

    const PASSWORD_ERROR_NUM = "passwordErrorNum";//错误次数

    const PASSWORD_CONTAIN_CHARACTERS = "passwordContainCharacters"; //密码的组成校验规则

    const PASSWORD_MAXLENGTH = "passwordMaxLength"; //密码最大长度

    const PASSWORD_MINLENGTH = 'passwordMinLength';//密码最小长度

    const LOGINNAME_MINLENGTH = 'loginNameMinLength';//用户名最小长度

    const LOGINNAME_MAXLENGTH = 'loginNameMaxLength';//用户名最大长度

    const PASSWORD_CONTAIN_CHARACTER_TYPE = "passwodContainCharactersType";//密码快速配置 pwd_default, pwd_convinence, pwd_security

}