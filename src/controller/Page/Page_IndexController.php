<?php
/**
 * Created by PhpStorm.
 * User: childeYin<尹少爷>
 * Date: 19/07/2018
 * Time: 2:07 PM
 */

class Page_IndexController extends HttpBaseController
{

    public $headers;

    public function index()
    {
        echo $this->display("msg_groupMsg");
        return;
    }
}