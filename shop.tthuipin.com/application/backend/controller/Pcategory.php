<?php

namespace app\backend\controller;

use app\backend\controller\Base;

class Pcategory extends Base

{

    public function __construct(){

        parent::__construct();

        $this->category = Db('Category');

        $this->pcategory = Db('pot_category');

//        $this->check = Validate('Category');

        $this->checkTokenSession = $this->getUserInfoSession();

    }



    /**

     * @author:xiaohao

     * @time:2019/10/25 14:19

     * @return mixed

     * @description:获取列表

     */

    public function getList(){

        $parameter = input();

        empty($parameter['cat_name']) ? $parameter['cat_name'] :$map[] = ['c.cat_name','like','%'.$parameter['cat_name'].'%'];

        empty($parameter['listrow'])  ? $parameter['listrow'] == '16' : $parameter['listrow'];

        $map[] = ['c.is_show','eq','1'];

        $map[] = ['c.parent_id','eq','0'];

        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'asc'];

        $getlist = Db('pot_category')

            ->alias('c')

            ->field('c.cat_id,c.cat_name,c.hot,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')

            ->join('pot_category pc','c.parent_id=pc.cat_id','left')

            ->where($map)

            ->order($sort)

            ->paginate($parameter['listrow']);





        $egetlist = Db('pot_category')

            ->alias('c')

            ->field('c.cat_id,c.cat_name,c.hot,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')

            ->join('pot_category pc','c.parent_id=pc.cat_id','left')

            ->where('c.is_show','1')

            ->order($sort)

            ->select();





        $this->assign([

            'categorylsit' => $getlist,

            'elist' => $egetlist,

        ]);

        return $this->fetch();

    }





    /**

     * @author:xiaohao

     * @time:2019/10/25 15:07

     * @return mixed

     * @description:添加分类

     */

    public function add(){

        $parameter = input();

        $data = [

            'cat_name' => $parameter['cat_name'],

            'parent_id' => $parameter['parent_id'],

            'show_in_nav' => $parameter['show_in_nav'],

            'image' => $parameter['files'],

            'sort_order' => $parameter['sort_order'],

            'hot' => $parameter['hot'],

            'keywords' => $parameter['keywords'],

            'is_people' => $parameter['is_people'],

            'is_shape' => $parameter['is_shape'],

            'is_capacity' => $parameter['is_capacity'],

            'is_theme' => $parameter['is_theme'],

            'is_mud' => $parameter['is_mud'],

            'is_tealeaf' => $parameter['is_tealeaf'],

            'is_ambitus' => $parameter['is_ambitus'],

            'is_iron_kettle' => $parameter['is_iron_kettle'],

            'is_silver_kettle' => $parameter['is_silver_kettle'],

            'is_chinaware' => $parameter['is_chinaware'],

        ];



        if($parameter){

//            returnResponse('200','true',$parameter);

            $info = $this->pcategory->insert($data);

            if($info){

                $cat_id = $this->pcategory->getLastInsID();

                $temp = Db('pot_category')->where('cat_id',$cat_id)->find();

                if($temp['is_people'] == 1){

                    Db('pot_artist')->insert(['cat_id'=>$cat_id]);

                }

                returnResponse('200','true',$info);

            }

            returnResponse('100','false');

        }

        $map[] = ['c.is_show','eq','1'];

        $map[] = ['c.parent_id','eq','0'];

        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'asc'];

        $getlist = Db('pot_category')

            ->alias('c')

            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')

            ->join('pot_category pc','c.parent_id=pc.cat_id','left')

            ->where($map)

            ->order($sort)

            ->paginate($parameter['listrow']);

        $egetlist = Db('pot_category')

            ->alias('c')

            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,c.sort_order,pc.cat_name parent_name')

            ->join('pot_category pc','c.parent_id=pc.cat_id','left')

            ->where('c.is_show','1')

            ->order($sort)

            ->select();



//        dump($egetlist);

        $this->assign([

            'categorylsit' => $getlist,

            'elist' => $egetlist,

        ]);

        return $this->fetch();

    }



    /**

     * @author:xiaohao

     * @time:2019/10/25 15:43

     * @return mixed

     * @throws \think\Exception

     * @throws \think\db\exception\DataNotFoundException

     * @throws \think\db\exception\ModelNotFoundException

     * @throws \think\exception\DbException

     * @throws \think\exception\PDOException

     * @description:修改

     */

    public function edit(){

        $parameter = input();

        $data = [

            'cat_name' => $parameter['cat_name'],

            'parent_id' => $parameter['parent_id'],

            'show_in_nav' => $parameter['show_in_nav'],

            'image' => $parameter['files'],

            'sort_order' => $parameter['sort_order'],

            'hot' => $parameter['hot'],

            'keywords' => $parameter['keywords'],

            'is_people' => $parameter['is_people'],

            'is_shape' => $parameter['is_shape'],

            'is_capacity' => $parameter['is_capacity'],

            'is_theme' => $parameter['is_theme'],

            'is_mud' => $parameter['is_mud'],

            'is_tealeaf' => $parameter['is_tealeaf'],

            'is_ambitus' => $parameter['is_ambitus'],

            'is_iron_kettle' => $parameter['is_iron_kettle'],

            'is_silver_kettle' => $parameter['is_silver_kettle'],

            'is_chinaware' => $parameter['is_chinaware'],

        ];

        if($parameter['id']){

//            returnResponse('200','true',$parameter);

            $info = $this->pcategory->where('cat_id',$parameter['id'])->update($data);

            if($info){

                returnResponse('200','true',$info);

            }

            returnResponse('100','false');

        }

        $map[] = ['c.is_show','eq','1'];

        $map[] = ['c.parent_id','eq','0'];

        $sort = ['c.sort_order'=>'desc','c.cat_id'=>'asc'];

        $getlist = Db('pot_category')

            ->alias('c')

            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,pc.cat_name parent_name')

            ->join('pot_category pc','c.parent_id=pc.cat_id','left')

            ->where($map)

            ->order($sort)

            ->paginate($parameter['listrow']);



        $egetlist = Db('pot_category')

            ->alias('c')

            ->field('c.cat_id,c.cat_name,c.image,c.show_in_nav,c.parent_id,pc.cat_name parent_name')

            ->join('pot_category pc','c.parent_id=pc.cat_id','left')

            ->where('c.is_show','1')

            ->order($sort)

            ->select();

        $info = $this->pcategory->where('cat_id',$parameter['eid'])->find();





        $this->assign([

            'categorylsit' => $getlist,

            'elist' => $egetlist,

            'info' => $info,

        ]);

        return $this->fetch();

    }



    /**

     * @author:xiaohao

     * @time:2019/10/25 15:52

     * @throws \think\Exception

     * @throws \think\exception\PDOException

     * @description:显示隐藏

     */

    public function show(){

        $parameter = input();

        $cat_id = intval(trim($parameter['id']));

        $account = Db('pot_category')->field('show_in_nav')->where('cat_id',$cat_id)->find();

        if($account['show_in_nav']==1){

            $info = Db('pot_category')->where('cat_id',$cat_id)->update(['show_in_nav'=>0]);

            if($info){

                returnResponse(200,'true',$info);

            }

            returnResponse(100,'false');

        }else{

            $info = Db('pot_category')->where('cat_id',$cat_id)->update(['show_in_nav'=>1]);

            if($info){

                returnResponse(200,'true',$info);

            }

            returnResponse(100,'false');

        }

    }



    /**

     * @author:xiaohao

     * @time:2019/10/25 16:52

     * @throws \think\Exception

     * @throws \think\exception\PDOException

     * @description:分类删除

     */

    public function deleteCategory(){

        $parameter = input();

        $cat_id = intval(trim($parameter['id']));

        if($cat_id){

//            // 判断分类下面是否有产品,如果没有才能删除

//            $data = Db('goods')->where('categoryid',$cat_id)->select();

//            if($data){

//                returnResponse(100,'不能删除有产品的分类哦!');

//            }



            // 对产品进行软删除

            $info = Db('pot_category')->where('cat_id',$cat_id)->update(['is_show'=>'0']);

            Db('pot_artist')->where('cat_id',$cat_id)->delete();

            if($info){

                returnResponse(200,'true',$info);

            }

            returnResponse(100,'false');

        }

        returnResponse(100,'网路拥挤，稍后再试');

    }



}

