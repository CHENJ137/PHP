<?php 
abstract class aDB {

	/**
	* 连接数据库,从配置文件读取配置信息
	*/
	abstract public function conn();


	/**
	* 发送query查询
	* @param string $sql sql语句
	* @return mixed
	*/
	abstract public function query1($sql);


	/**
	* 查询多行数据
	* @param string $sql sql语句
	* @return array
	*/
	abstract public function getAll($sql);


	/**
	* 单行数据
	* @param string $sql sql语句
	* @return array
	*/
	abstract public function getRow($sql);


	/**
	* 查询单个数据 如 count(*)
	* @param string $sql sql语句
	* @return mixed
	*/
	abstract public function getOne($sql);


	/**
	* 自动创建sql并执行
	* @param array $data 关联数组 键/值与表的列/值对应
	* @param string $table 表名字
	* @param string $act 动作/update/insert
	* @param string $where 条件,用于update
	* @return int 新插入的行的主键值或影响行数
	*/
	abstract public function Exec($data , $table , $act='insert' , $where='0');

	/**
	* 返回上一条insert语句产生的主键值
	*/
	abstract public function lastId();

	/**
	* 返回上一条语句影响的行数
	*/
	abstract public function affectRows();
}

class Mysql extends aDB {
	public $link;
		
	public function __construct(){
		$this->conn();
	}
	/**
	* 连接数据库,从配置文件读取配置信息
	*/
	public function conn(){
		$cfg = require './config.php';
	    $this->link = new mysqli($cfg['host'],$cfg['user'],$cfg['pwd'],$cfg['db']);
	    $this->link->set_charset($cfg['charset']);
	}


	/**
	* 发送query查询
	* @param string $sql sql语句
	* @return mixed
	*/
	public function query1($sql){
		return $this->link->query($sql);
	}


	/**
	* 查询多行数据
	* @param string $sql sql语句
	* @return array
	*/
	public function getAll($sql){
		$res = $this->query1($sql);
		$data = array();
		while($row = $res->fetch_assoc()){
			$data[] = $row;
		}

		return $data;
	}


	/**
	* 单行数据
	* @param string $sql sql语句
	* @return array
	*/
	public function getRow($sql){
		$res = $this->query1($sql);
		return $res->fetch_assoc();
	}


	/**
	* 查询单个数据 如 count(*)
	* @param string $sql sql语句
	* @return mixed
	*/
	public function getOne($sql){
		$res = $this->query1($sql);
		return $res->fetch_row()[0];
	}


	/**
	* 自动创建sql并执行
	* @param array $data 关联数组 键/值与表的列/值对应
	* @param string $table 表名字
	* @param string $act 动作/update/insert
	* @param string $where 条件,用于update
	* @return int 新插入的行的主键值或影响行数
	*/
	public function Exec($data , $table , $act='insert' , $where='0'){
		//insert into xxxx(xxx,xxx) values('xxxx','xxxx','xxxx'....);
		if($act == 'insert'){
			$sql = "insert into $table (";
			$sql .= implode(',',array_keys($data)).') ';
			$sql .= " values ('";
			$sql .= implode("','",array_values($data))."')";
			$this->query1($sql);
			return $this->lastId();
		}else if($act == 'update'){
			//update xxx set xxx='xxx',xxx='xxxx' where xxx=xxx;
			$sql = "update $table set ";
			foreach($data as $k=>$v){
				$sql.= $k."='".$v."',";
			}
			$sql = rtrim($sql,',').' where '.$where;
			$this->query1($sql);
			return $this->affectRows();
		}
	}

	/**
	* 返回上一条insert语句产生的主键值
	*/
	public function lastId(){
		return $this->link->insert_id;
	}

	/**
	* 返回上一条语句影响的行数
	*/
	public function affectRows(){
		return $this->link->affected_rows;
	}
}

$mysql = new Mysql();
var_dump($mysql->link);

$mysql->Exec(array('username'=>'123','password'=>'123'),'user');
