<?php
namespace app\admin\controller;
use app\admin\controller\Base;
class Category extends Base
{
    public function __construct(){
        parent::__construct();
        $this->category = model('Category');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 11:19
     * @return mixed
     * @description:获取分类列表(一级)
     */
    public function getListOne(){
        $parameter = getInput();
        $info = $this->category->getList($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 11:19
     * @return mixed
     * @description:获取分类列表(N级)
     */
    public function getListMore(){
        $parameter = getInput();
        $info = $this->category->getListn($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 9:38
     * @return mixed
     * @description:添加删除分类
     */
    public function addEdit(){
        $parameter = getInput();
        $info = $this->category->addEdit($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 11:14
     * @return bool
     * @throws \Exception
     * @description:删除分类（假删）即隐藏
     */
    public function deleteCategory(){
        $parameter = getInput();
        $info = $this->category->deleteCategory($parameter);
        return $info;
    }

    /**
     * @author:xiaohao
     * @time:2019/10/13 11:14
     * @return bool
     * @throws \Exception
     * @description:删除分类（恢复假删）即恢复隐藏
     */
    public function showCategory(){
        $parameter = getInput();
        $info = $this->category->showCategory($parameter);
        return $info;
    }
}
