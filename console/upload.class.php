<?php
/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-7-18
 * Time: 上午11: 32
 * UEditor编辑器通用上传类
 */

//wxlog('include uploadClass ok');
include_once '../includePackage.php';
define('imgPath','img');
define('maxSize',500);
define('FILE_FIELD','upfile');
class uploader
{
    private $md5;
    private $fileField;            //文件域名
    private $file;                 //文件上传对象
    private $config;               //配置信息
    private $oriName;              //原始文件名
    private $fileName;             //新文件名
    private $fullName;             //完整文件名,即从当前配置目录开始的URL
    private $fileSize;             //文件大小
    private $fileType;             //文件类型
    private $stateInfo;            //上传状态信息,
    private $stateMap = array(    //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS" ,                //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制" ,
        "文件大小超出 MAX_FILE_SIZE 限制" ,
        "文件未被完整上传" ,
        "没有文件被上传" ,
        "上传文件为空" ,
        "POST" => "文件大小超出 post_max_size 限制" ,
        "SIZE" => "文件大小超出网站限制" ,
        "TYPE" => "不允许的文件类型" ,
        "DIR" => "目录创建失败" ,
        "IO" => "输入输出错误" ,
        "UNKNOWN" => "未知错误" ,
        "MOVE" => "文件保存时出错",
        "DIR_ERROR" => "创建目录失败"
    );

    /**
     * 构造函数
     * @param string $fileField 表单名称
     * @param array $config  配置项
     * @param bool $base64  是否解析base64编码，可省略。若开启，则$fileField代表的是base64编码的字符串表单名
     */
    public function __construct( $fileField=FILE_FIELD,array $config=null,$base64 = false )
    {

//        $this->fileName=$fileName;
        $this->fileField = $fileField;
        if(!$config){
            $this->config = array(
                "savePath" => imgPath ,             //存储文件夹
                "maxSize" => maxSize,                   //允许的文件最大尺寸，单位KB
                "allowFiles" => array( ".gif" , ".png" , ".jpg" , ".jpeg" , ".bmp" )  //允许的文件格式
            );
        }else{
            $this->config=$config;
        }

        $this->stateInfo = $this->stateMap[ 0 ];
//        $this->upFile( $base64 );

    }

    /**
     * 上传文件的主处理方法
     * @param $base64
     * @return mixed
     */
    public function upFile( $fileName ,  $base64=false )
    {
        //处理base64上传
        if ( "base64" == $base64 ) {
            $content = $_POST[ $this->fileField ];
            $this->base64ToImage( $content );
            return;
        }

        //处理普通上传
        $this->fileName=$fileName;
        $file = $this->file = $_FILES[ $this->fileField ];
//        mylog(getArrayInf($file));
//        mylog($file['tmp_name']);
        if ( !$file ) {
            $this->stateInfo = $this->getStateInfo( 'POST' );
            return;
        }
        if ( $this->file[ 'error' ] ) {
            $this->stateInfo = $this->getStateInfo( $file[ 'error' ] );
            return;
        }
        if ( !is_uploaded_file( $file[ 'tmp_name' ] ) ) {
            $this->stateInfo = $this->getStateInfo( "UNKNOWN" );
            return;
        }

        $this->oriName = $file[ 'name' ];
        $this->fileSize = $file[ 'size' ];
        $this->fileType = $this->getFileExt();

        if ( !$this->checkSize() ) {
            $this->stateInfo = $this->getStateInfo( "SIZE" );
            return;
        }
        if ( !$this->checkType() ) {
            $this->stateInfo = $this->getStateInfo( "TYPE" );
            return;
        }

        $folder = $this->getFolder();

        if ( $folder === false ) {
            $this->stateInfo = $this->getStateInfo( "DIR_ERROR" );
            return;
        }
        $this->md5=md5_file($file['tmp_name']);


        $this->fullName = $folder . $this->getName();

        if ( $this->stateInfo == $this->stateMap[ 0 ] ) {
            if ( !move_uploaded_file( $file[ "tmp_name" ] , '../'.$this->fullName ) ) {
                $this->stateInfo = $this->getStateInfo( "MOVE" );
                return;
            }
        }


    }



    /**
     * 比对上传文件的Md5值，如重复则直接返回原文件信息
     * @param $news_id 图文信息名
     * @param $md5 上传文件的md5值
     */
    public function checkFileMd5($md5){
        $query=pdoQuery('image_view',null,array('remark'=>$md5),'limit 1');
        if($row=$query->fetch()){
            $this->fileName=$row['id'];
            $this->fullName=$row['url'];
            return true;
        }else{
            return false;
        }
    }

    /**
     * 处理base64编码的图片上传
     * @param $base64Data
     * @return mixed
     */
    private function base64ToImage( $base64Data )
    {
        $img = base64_decode( $base64Data );
        $this->fileName .=".png";
        $this->fullName = $this->getFolder()  . $this->fileName;
        if ( !file_put_contents( '../'.$this->fullName , $img ) ) {
            $this->stateInfo = $this->getStateInfo( "IO" );
            return;
        }
        $this->oriName = "";
        $this->fileSize = strlen( $img );
        $this->fileType = ".png";
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            "originalName" => $this->oriName ,
            'md5'=>$this->md5,
            "name" => $this->fileName ,
            "url" => $this->fullName ,
            "urlInDb"=>$this->fullName,
            "size" => $this->fileSize ,
            "type" => $this->fileType ,
            'cover'=>false,
            "state" => $this->stateInfo
        );
    }

    /**
     * 上传错误检查
     * @param $errCode
     * @return string
     */
    private function getStateInfo( $errCode )
    {
        return !$this->stateMap[ $errCode ] ? $this->stateMap[ "UNKNOWN" ] : $this->stateMap[ $errCode ];
    }

    /**
     * 重命名文件
     * @return string
     */
    private function getName()
    {
        return $this->fileName .=$this->getFileExt() ;
    }

    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType()
    {
        return in_array( $this->getFileExt() , $this->config[ "allowFiles" ] );
    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function  checkSize()
    {
        return $this->fileSize <= ( $this->config[ "maxSize" ] * 1024 );
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        return strtolower( strrchr( $this->file[ "name" ] , '.' ) );
    }

    private function getFolder()
    {
        $pathStr = $this->config[ "savePath" ];
        if ( strrchr( $pathStr , "/" ) != "/" ) {
            $pathStr .= "/";
        }
        return $pathStr;
    }
    public function getUrl(){
        return $this->fullName;
    }
}