<?php 
abstract class aUpload {
	public $allowExt = array('jpg' , 'jpeg' , 'png' , 'rar');
	public $maxSize = 1; // 最大上传大小,以M为单位
	protected $error = ''; // 错误信息

	/**
	* 分析$_FILES中$name域的信息,比例$_FILES中的['pic']
	* @param string $name 表单中file表单项的name值
	* @return array 上传文件的信息,包含(tmp_name,oname[不含后缀的文件名称] , ext[后缀],size)
	*/
	abstract public function getInfo($name);

	/**
	* 创建目录 在当前网站的根目录的upload目录中,按年/月日 创建目录
	* @return string 目录路径 例 /upload/2015/0331
	*/
	abstract public function createDir();

	/**
	* 生成随机文件名
	* @param int $len 随机字符串的长度
	* @return string 指定长度的随机字符串
	*/
	abstract public function randStr($len = 8);

	/**
	* 上传文件
	* @param string $name 表单中file表单项的name值
	* @return string 上传文件的路径,从web根目录开始计,如/upload/2015/0331/a.jpg
	*/
	abstract public function up($name);



	/*
	判断 $_FILES[$name]
	调用getInfo 分析文件的大小,后缀等
	调用checkType
	调用checkSize
	调用createDir
	调用randStr生成随机文件名
	移动,返回路径
	*/
	/**
	* 检测文件的类型,如只允许jpg,jpeg,png,rar,不允许exe
	* @param $ext 文件的后缀
	* @return boolean
	*/
	abstract protected function checkType($ext);


	/**
	* 检测文件的大小
	* @param $size 文件的大小
	* @return boolean
	*/
	abstract protected function checkSize($size);


	/**
	* 读取错误信息
	*/
	public function getError() {
		return $this->error;
	}
}
 
class upload extends aUpload {
	public $allowExt = array('jpg' , 'jpeg' , 'png' , 'rar');
	public $maxSize = 1; // 最大上传大小,以M为单位
	protected $error = ''; // 错误信息

	/**
	* 分析$_FILES中$name域的信息,比例$_FILES中的['pic']
	* @param string $name 表单中file表单项的name值
	* @return array 上传文件的信息,包含(tmp_name,oname[不含后缀的文件名称] , ext[后缀],size)
	*/
	public function getInfo($name){
		return $file = $_FILES[$name];

	}

	/**
	* 创建目录 在当前网站的根目录的upload目录中,按年/月日 创建目录
	* @return string 目录路径 例 /upload/2015/0331
	*/
	public function createDir(){
		$path = './upload/'.date('Y/md',time());
		if(!is_dir($path)){
			mkdir($path,0777,true);
		}
		return $path;
	}

	/**
	* 生成随机文件名
	* @param int $len 随机字符串的长度
	* @return string 指定长度的随机字符串
	*/
	public function randStr($len = 8){
		$str = str_shuffle(md5(time().mt_rand(100,999)));
		return substr($str,0,$len);
	}





	
	/**
	* 检测文件的类型,如只允许jpg,jpeg,png,rar,不允许exe
	* @param $ext 文件的后缀
	* @return boolean
	*/
	protected function checkType($ext){
		return in_array($ext,$this->allowExt);
	}


	/**
	* 检测文件的大小
	* @param $size 文件的大小
	* @return boolean
	*/
	protected function checkSize($size){
		return $size<=($this->maxSize*1024*1024);
	}

	/*
	判断 $_FILES[$name]
	调用getInfo 分析文件的大小,后缀等
	调用checkType
	调用checkSize
	调用createDir
	调用randStr生成随机文件名
	移动,返回路径
	*/


	/**
	* 上传文件
	* @param string $name 表单中file表单项的name值
	* @return string 上传文件的路径,从web根目录开始计,如/upload/2015/0331/a.jpg
	*/
	public function up($name){
		if($_FILES[$name]['name'] == ''){
			echo $this->error = "不好意思,您没有任何文件上传,请选择文件后上传";
			exit();
		}

		$info = $this->getInfo($name);//$info传递过来的二维数组

		$e = ltrim(strrchr($info['name'],'.'),'.');//后缀名已经拿到

		if(!$this->checkType($e)){
			echo $this->error = "类型不允许,我们只允许图片类型和压缩文件";
			exit();
		}

		if(!$this->checkSize($info['size'])){
			echo $this->error = "文件太大,我们只需要小于1M的文件";
			exit();
		}

		$dir = $this->createDir();//路径
		$filename = $this->randStr().'.'.$e;//带后缀的文件名

		if (move_uploaded_file($info['tmp_name'],$dir.'/'.$filename)) {
			return $dir.'/'.$filename;
		}
	}


}

$up = new upload();
$up->up('pic');
