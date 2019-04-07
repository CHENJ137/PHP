<?php
/**
* 分页类
*/
abstract class aPage {
    public $size = 5; // 显示多少个页码
    public $error = '';
    public $offset = 0; // limit offset,
    /**
     * 计算分页代码
     * @param int $num 总条数
     * @param int $cnt 每页条数
     * @param int $curr 当前页
     */
    abstract public function pagnation($num , $cnt , $curr);
}

class Page extends aPage{
    public $size = 5; // 显示多少个页码
    public $error = '';
    public $offset = 0;
    public function pagnation($num , $cnt , $curr=1){
        $a=ceil($this->size/2);
        $max=ceil($num/$cnt);//最大页码数
        $left=max($curr-$a,1);
        $right=min($left+$this->size-1,$max);
        $left=max($right-$this->size+1,1);
        for($i=$left,$pages=array();$i<=$right;$i+=1){
            $_GET['page']=$i;
            $pages[$i]=http_build_query($_GET);
        }
    
        $this->offset = ($curr-1)*$cnt;

        return $pages;
    }
}
