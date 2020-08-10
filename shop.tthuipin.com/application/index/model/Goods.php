<?php
namespace app\index\model;
use app\index\model\Base;
use think\Db;
class Goods extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->goods = Db('Goods');
        $this->category = Db('Category');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/18 14:11
     * @description:首页推荐
     */
    public function getIndexList(){
        $map[] = ['is_del','eq','0'];
        $map[] = ['index','eq','1'];
        $sort  = ['sort'=>'desc','id'=>'desc'];
        $info = $this->goods
            ->field('id,name,sell_price,market_price,keywords,sale')
            ->where($map)
            ->order($sort)
            ->select();
        returnResponse(200,'请求成功',$info);
    }

    /**
     * @author:xiaohao
     * @time:Times
     * @param $parameter
     * @description:商品分类的列表
     */
    public function getList($parameter){
        empty($parameter['listrow'])   ? $parameter['listrow']   = 30 : $parameter['listrow'];
        empty($parameter['liststart']) ? $parameter['liststart'] = 1  : $parameter['liststart'];
        if(empty($parameter['catid'])){
            returnResponse(100,'参数错误');
        }
        $catid = intval(trim($parameter['catid']));
        $parent = $this->category->field('parent_id')->where('cat_id',$catid)->find();
        if($parent['parent_id']=='0'){

            $catids = Db::query("SELECT `cat_id` FROM `ecs_category` WHERE  `parent_id` = $catid");
            foreach($catids as $v){
                foreach($v as $vv){
                    $cid .= $vv.'|';
                }
            }
            $catidStr = substr($cid,0,strlen($cid)-1);
            $catidArray = explode("|",$catidStr);
            $map[] = ['category_id','in',$catidArray];
        }else{
            $map[] = ['category_id','eq',$catid];
        }
        $map[] = ['is_del','eq','0'];
        $order = ['sort'=>'desc','id'=>'desc'];
        $info = Db('Goods')
            ->field('id,name,sell_price,keywords,sale')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->select();
        $count = Db('Goods')
            ->field('id')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->count();
        $totalPage   = ceil($count/$parameter['listrow']);
        $currentPage = $parameter['liststart'];
        $data = [
            'info'  => $info,
            'total' => $count,
            'totalpage'   => $totalPage,
            'currentpage' => $currentPage
        ];
        returnResponse(200,'请求成功',$data);
    }

    /**
     * @author:xiaohao
     * @time:2019/10/20 11:19
     * @param $parameter
     * @description:商品详情
     */
//'id'              => $parameter['gid'],   #id
//'name'            => $parameter['name'],   #商品名称
//'goods_no'        => setNumber(),  #商品的货号
//'sell_price'      => $parameter['sell_price'],#销售价格
//'market_price'    => $parameter['market_price'],#市场价格
//'cost_price'      => $parameter['cost_price'],#成本价格
//'up_time'         => $parameter['up_time'],#上架时间 时间戳
//'down_time'       => $parameter['down_time'],#下架时间 时间戳
//'create_time'     => time(), #创建时间
//'store_nums'      => $parameter['store_nums'],#库存
//'img'             => $parameter['img'],#原图
//'ad_img'          => $parameter['ad_img'],#宣传图
//'is_del'          => $parameter['is_del'],#商品状态 0正常 1已删除 2下架 3申请上架
//'content'         => $parameter['content'],#商品描述
//'keywords'        => $parameter['keywords'],#SEO关键词
//'description'     => $parameter['description'],#SEO描述
//'search_words'    => $parameter['search_words'],#产品搜索词库,逗号分隔
//'weight'          => $parameter['weight'],#重量
//'unit'            => $parameter['unit'],#计件单位。如:件,箱,个
//'brand_id'        => $parameter['brand_id'],#品牌ID
//'visit'           => $parameter['visit'],#浏览次数
//'sort'            => $parameter['sort'],#排序
//'spec_array'      => $parameter['spec_array'],#商品信息json数据
//'sale'            => $parameter['sale'],#销量
//'is_delivery_fee' => $parameter['is_delivery_fee'],#免运费 0收运费 1免运费
    public function getInfo($parameter){
        $id = intval(trim($parameter['gid']));
        if(empty($id)){
            returnResponse(100,'參數錯誤');//参数错误
        }
//        20191014030157166802
        $map[] = ['id','eq',$id];

        Db::startTrans();   // 启动事务
        try {
            $this->goods->where($map)->setInc('visit'); //添加浏览量
        }catch (\Exception $e) {
            Db::rollback(); // 回滚事务
        }
        $info  =$this->goods
            ->field('id,name,goods_no,sell_price,market_price,cost_price,up_time,down_time,store_nums,img,ad_img,keywords,weight,unit,brand_id,visit,spec_array,sale,is_delivery_fee')
            ->where($map)->find();
//        if($info['down_time'] <= time()){
//            returnResponse(100,'商品已經下架');//商品已经下架
//        }

        if($info['is_del'] !== 0){
            returnResponse(100,'商品不存在');//商品不存在
        }
        returnResponse(200,'请求成功',$info);
    }

}
