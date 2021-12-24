<?php

class File_Manager
{
    private $attachmentDir = "attachment";
    private $defaultSuffix = ".duckchat";
    private $gifDir = "gif";
    private $mimeConfig = array(
        "image/png" => "png",
        "image/jpeg" => "jpeg",
        "image/jpg" => "jpg",
        "image/gif" => "gif",
//        "image/bmp" => "bmp", //文件太大，不支持此格式
        "audio/mp4" => "mp4",
        "audio/x-m4a" => "m4a",
        "video/mp4" => "mp4",
        'application/pdf' => "pdf",
        'application/x-rar-compressed' => "rar",
        'application/zip' => "zip",
        'application/msword' => "word",
        'application/xml' => "xml",
        'application/vnd.ms-powerpoint' => "ppt"
    );

    private $defaultFileType = [
        "image/jpeg",
        "image/jpg",
        "image/png",
        "image/gif",
        "audio/mp4",
        "audio/x-m4a",
        "video/mp4",
    ];

    public function __construct()
    {
        $this->wpf_Logger = new Wpf_Logger();
    }

    public function getPath($dateDir, $fileId, $isCreateFolder = true)
    {
        $fileId = str_replace("../", "", $fileId);
        $dateDir = str_replace("../", "", $dateDir);
        $dirName = WPF_LIB_DIR . "/../{$this->attachmentDir}/$dateDir";
        if (!is_dir($dirName) && $isCreateFolder) {
            mkdir($dirName, 0665, true);
        }
        return $dirName . "/" . $fileId;
    }

    public function readFile($fileId)
    {
        if (strlen($fileId) < 1) {
            return "";
        }
        // 需要hash目录，防止单目录文件过多
        $fileName = explode("-", $fileId);
        $dirName = $fileName[0];
        $fileId = $fileName[1];

        $path = $this->getPath($dirName, $fileId, false);
        return file_get_contents($path);
    }

    public function getFileSize($fileId)
    {
        try {
            if (strlen($fileId) < 1) {
                return false;
            }
            // 需要hash目录，防止单目录文件过多
            $fileName = explode("-", $fileId);
            $dirName = $fileName[0];
            $fileId = $fileName[1];

            $path = $this->getPath($dirName, $fileId, false);
            return getimagesize($path);
        } catch (Exception $ex) {
            return false;
        }
    }

    public function contentType($fileId)
    {
        if (strlen($fileId) < 1) {
            return "";
        }
        // 需要hash目录，防止单目录文件过多
        $fileName = explode("-", $fileId);
        $dirName = $fileName[0];
        $fileId = $fileName[1];
        $path = $this->getPath($dirName, $fileId, false);
        return mime_content_type($path);
    }

    public function saveFile($content, $dateDir = false)
    {
        if (!$dateDir) {
            $dateDir = date("Ymd");
        }

        $fileName = sha1(uniqid());

        $path = $this->getPath($dateDir, $fileName);
        file_put_contents($path, $content);

        $mime = mime_content_type($path);

        if (!in_array($mime, $this->defaultFileType)) {
            throw new Exception("file type error");
        }

        $ext = isset($this->mimeConfig[$mime]) ? $this->mimeConfig[$mime] : "";
        if (false == empty($ext)) {
            $fileName = $fileName . "." . $ext;
            rename($path, $this->getPath($dateDir, $fileName));
        }

        return $dateDir . "-" . $fileName;
    }

    public function saveDocument($content, $ext, $dateDir = false)
    {
        if (!$dateDir) {
            $dateDir = date("Ymd");
        }
        $fileName = sha1(uniqid());
        $path = $this->getPath($dateDir, $fileName);
        file_put_contents($path, $content);
        if (false == empty($ext)) {
            $fileName = $fileName . "." . $ext;
        }
        $fileName = $fileName . $this->defaultSuffix;
        rename($path, $this->getPath($dateDir, $fileName));
        return $dateDir . "-" . $fileName;
    }

    public function buildGroupAvatar($fileIdList = array())
    {
        if (empty($fileIdList)) {
            return "";
        }

        $picList = [];
        foreach ($fileIdList as $fileId) {
            $userAvatarPath = $this->turnFileId2FilePath($fileId);
            if (isset($userAvatarPath)) {
                $picList[] = $userAvatarPath;
            }
        }

        $dateDir = date("Ymd");
        $fileName = sha1(uniqid()) . "." . "jpeg";
        $groupImagePath = $this->getPath($dateDir, $fileName);

        $gorupAvatarPath = $this->splicingGroupAvatar($picList, $groupImagePath);

        if (empty($gorupAvatarPath)) {
            return null;
        }
        return $dateDir . "-" . $fileName;
    }

    /**
     * avatar from fileId to path
     * @param $fileId
     * @return string
     */
    private function turnFileId2FilePath($fileId)
    {
        $tag = __CLASS__ . "->" . __FUNCTION__;
        if (empty($fileId)) {
            return null;
        }
        try {
            $fileNameArray = explode("-", $fileId);
            $dirName = $fileNameArray[0];
            $fileName = $fileNameArray[1];
            return $this->getPath($dirName, $fileName, false);
        } catch (Exception $e) {
            $this->wpf_Logger->error($tag, $e->getMessage());
        }
        return null;
    }

    /**
     *
     * @param array $picList
     * @param $outImagePath
     * @return bool|string
     */
    private function splicingGroupAvatar($picList = array(), $outImagePath)
    {
        $tag = __CLASS__ . '-' . __FUNCTION__;
        if (!function_exists("imagecreatetruecolor")) {
            $this->wpf_Logger->error($tag, "php need support gd library, please check local php environment");
            return null;
        }

        if (empty($picList)) {
            return "";
        }

        $default_width = 500;
        $default_height = 500;

        $picList = array_slice($picList, 0, 9); // 只操作前9个图片

        $defaultImage = imagecreatetruecolor($default_width, $default_height); //创建图片大小

        //int imagecolorallocate ( resource $image , int $red , int $green , int $blue ) 为一幅图像分配颜色
        $color = imagecolorallocate($defaultImage, 229, 229, 229); // 为真彩色画布创建白色背景，再设置为透明
        imagefill($defaultImage, 0, 0, $color);      //区域填充
        imageColorTransparent($defaultImage, $color);     // 将某个颜色定义为透明色

        $pic_count = count($picList);
        $newLineArr = array();  // 需要换行的位置
        $space_x = 10;
        $space_y = 10;

        $start_x = 0;
        $start_y = 0;

        $picElements = [];

        switch ($pic_count) {
            case 1://ok
                $start_x = 100;
                $start_y = $start_x;
                $pic_w = 300;//width
                $pic_h = $pic_w;
                $picElements = $this->buildImageElements(1, $start_x, $start_y, $pic_w, $pic_h, []);
                break;
            case 2://ok
                $start_y = 128.5;
                $pic_w = intval($default_width / 2) - 5;//width
                $pic_h = $pic_w;
                $picElements = $this->buildImageElements(2, $start_x, $start_y, $pic_w, $pic_h, []);
                break;
            case 3:
                $start_x = 127.5;
                $pic_w = intval($default_width / 2) - 5;//width
                $pic_h = $pic_w;
                $picElements = $this->buildImageElements(3, $start_x, $start_y, $pic_w, $pic_h, [1]);
                break;
            case 4: //OK
                $pic_w = intval($default_width / 2) - 5; // 宽度
                $pic_h = intval($default_height / 2) - 5; // 高度
                $picElements = $this->buildImageElements(4, $start_x, $start_y, $pic_w, $pic_h, [2]);
                break;
            case 5:
                $start_x = 83.33;
                $start_y = 83.33;
                $pic_w = intval($default_width / 3) - 5; // 宽度
                $pic_h = intval($default_height / 3) - 5; // 高度
                $picElements = $this->buildImageElements(5, $start_x, $start_y, $pic_w, $pic_h, [2]);

                break;
            case 6://ok
                $start_y = 83.33;
                $pic_w = intval($default_width / 3) - 5; // 宽度
                $pic_h = intval($default_height / 3) - 5; // 高度
                $picElements = $this->buildImageElements(6, $start_x, $start_y, $pic_w, $pic_h, [3]);
                break;
            case 7://ok
                $start_x = 166.5;   // 开始位置X
                $pic_w = intval($default_width / 3) - 5; // 宽度
                $pic_h = intval($default_height / 3) - 5; // 高度
                $picElements = $this->buildImageElements(7, $start_x, $start_y, $pic_w, $pic_h, [1, 4]);
                break;
            case 8://ok
                $start_x = 80.5;   // 开始位置X
                $pic_w = intval($default_width / 3) - 5; // 宽度
                $pic_h = intval($default_height / 3) - 5; // 高度
                $picElements = $this->buildImageElements(8, $start_x, $start_y, $pic_w, $pic_h, [2, 5]);
                break;
            case 9://ok
                $pic_w = intval($default_width / 3) - 5; // 宽度
                $pic_h = intval($default_height / 3) - 5; // 高度
                $picElements = $this->buildImageElements(9, $start_x, $start_y, $pic_w, $pic_h, [3, 6]);
                break;
            default:
                return false;
        }


        //设置每张图片的尺寸
        foreach ($picList as $k => $pic_path) {
            $element = $picElements[$k];
            $resource = false;
            $mime = mime_content_type($pic_path);

            if ($mime == "image/jpg" | $mime == "image/jpeg") {
                $resource = imagecreatefromjpeg($pic_path);
            } else if ($mime == "image/png") {
                $resource = imagecreatefrompng($pic_path);
            } else {
                $this->wpf_Logger->error($tag, "unsupport image type [" . $mime . "]");
            }
            if ($resource == false) {
                continue;
            }
            // $start_x,$start_y copy图片在背景中的位置 0,0 被copy图片的位置   $pic_w,$pic_h copy后的高度和宽度
            imagecopyresized($defaultImage, $resource, $element['x'], $element['y'], 0, 0, $element['w'], $element['h'], imagesx($resource), imagesy($resource)); // 最后两个参数为原始图片宽度和高度，倒数两个参数为copy时的图片宽度和高度
        }

//        header("Content-type: image/jpg");

        $res = imagejpeg($defaultImage, $outImagePath);

        // 释放内存
        imagedestroy($defaultImage);

        if (false === $res) {
            return false;
        }

        return $outImagePath;
    }

    /**
     * @param $imageSize
     * @param $start_x
     * @param $start_y
     * @param $pic_w
     * @param $pic_h
     * @param $newLineArr
     * @return array
     */
    private function buildImageElements($imageSize, $start_x, $start_y, $pic_w, $pic_h, $newLineArr)
    {
        $picElements = [];

        for ($i = 0; $i < $imageSize; $i++) {
            $element = [
                "x" => $start_x,
                "y" => $start_y,
                "w" => $pic_w,
                "h" => $pic_h,
            ];
            $picElements[] = $element;

            if (in_array($i + 1, $newLineArr)) {
                $start_x = 0;
                $start_y = $start_y + $pic_h + 10;
            } else {
                $start_x = $start_x + $pic_w + 10;
            }
        }

        return $picElements;
    }

    /**
     * @param $fileSize 单位bytes
     * @param $maxFileSize 单位M
     * @return bool
     */
    public function judgeFileSize($fileSize, $maxFileSize)
    {
        if ($maxFileSize) {
            $maxFileSizeBytes = $maxFileSize * 1024 * 1024;
            if ($maxFileSizeBytes < $fileSize) {
                return false;
            }
        }
        return true;
    }

    /**
     * 通过file，把文件移动到指定目录
     *
     * @param $fileId
     * @param $newDir
     * @return string
     */
    public function moveImage($fileId, $newDir)
    {

        $fileContent = $this->readFile($fileId);


        if (!is_dir($newDir)) {
            mkdir($newDir, 0665, true);
        }

        $lastStr = substr($newDir, -1);
        $path = $newDir . "/" . $fileId;
        if ($lastStr == "/") {
            $path = $newDir . $fileId;
        }

        file_put_contents($path, $fileContent);

        return $path;
    }

    public function getCustomPathByFileId($fileId)
    {
        if (empty($fileId)) {
            return '';
        }
        $fileName = explode("-", $fileId);
        $dirName = $fileName[0];
        $fileId  = $fileName[1];
        $fileId = str_replace("../", "", $fileId);
        $dateDir = str_replace("../", "", $dirName);
        $dirName =  "./{$this->attachmentDir}/$dateDir";
        return $dirName . "/" . $fileId;
    }

    public function fileIsExists($fileId)
    {
        //处理异常，异常return true
        $fileName = explode("-", $fileId);
        $dateDir = $fileName[0];
        $fileId = $fileName[1];
        $fileId = str_replace("../", "", $fileId);
        $dateDir = str_replace("../", "", $dateDir);

        $filePathName = WPF_LIB_DIR . "/../{$this->attachmentDir}/$dateDir/$fileId";

        if (!file_exists($filePathName)) {
            return false;
        }

        return true;
    }
}