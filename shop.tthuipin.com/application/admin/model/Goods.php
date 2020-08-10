<?php
namespace app\admin\model;
use app\admin\model\Base;
use Exception;
class Goods extends Base
{
    protected $tableName = "Goods";

    /**
     * AdminUser constructor.实例化自动执行
     */
    public function __construct()
    {
        parent::__construct();
        $this->goods     = Db('Goods');
        $this->check     = validate('Goods');
        $this->checkToken = $this->checkTokenUserDatas();
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 16:05
     * @param $parameter
     * @description:商品列表
     */
    public function getList($parameter){
        empty($parameter['listrow'])   ? $parameter['listrow']   = 30 : $parameter['listrow'];
        empty($parameter['liststart']) ? $parameter['liststart'] = 1  : $parameter['liststart'];
        empty($parameter['keywords'])  ? $parameter['keywords'] = ''  : $parameter['keywords'];
        empty($parameter['sort'])      ? $parameter['sort'] = 'sort'  : $parameter['sort']; //销量 点击量

        $map[] = ['name|goods_no|content','like','%'.$parameter['keywords'].'%'];
        $order = [$parameter['sort']=>'desc','id'=>'desc'];
        $info = Db('Goods')
            ->page($parameter['liststart'],$parameter['listrow'])
            ->where($map)
            ->order($order)
            ->select();
        $count = Db('Goods')
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
            'currentPage' => $currentPage
        ];
        returnResponse(200,'请求成功',$data);
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 15:02
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:商品添加和修改
     */
    public function addEdit($parameter){
        if(!$this->check->scene('addEdit')->check($parameter)){
            returnResponse('100',$this->check->getError());
        }
        $data = [
            'id'              => $parameter['gid'],   #id
            'name'            => $parameter['name'],   #商品名称
            'goods_no'        => setNumber(),  #商品的货号
            'sell_price'      => $parameter['sell_price'],#销售价格
            'market_price'    => $parameter['market_price'],#市场价格
            'cost_price'      => $parameter['cost_price'],#成本价格
            'up_time'         => $parameter['up_time'],#上架时间 时间戳
            'down_time'       => $parameter['down_time'],#下架时间 时间戳
            'create_time'     => time(), #创建时间
            'store_nums'      => $parameter['store_nums'],#库存
            'img'             => $parameter['img'],#原图
            'ad_img'          => $parameter['ad_img'],#宣传图
            'is_del'          => $parameter['is_del'],#商品状态 0正常 1已删除 2下架 3申请上架
            'content'         => $parameter['content'],#商品描述
            'keywords'        => $parameter['keywords'],#SEO关键词
            'description'     => $parameter['description'],#SEO描述
            'search_words'    => $parameter['search_words'],#产品搜索词库,逗号分隔
            'weight'          => $parameter['weight'],#重量
            'unit'            => $parameter['unit'],#计件单位。如:件,箱,个
            'brand_id'        => $parameter['brand_id'],#品牌ID
            'visit'           => $parameter['visit'],#浏览次数
            'sort'            => $parameter['sort'],#排序
            'spec_array'      => $parameter['spec_array'],#商品信息json数据
            'sale'            => $parameter['sale'],#销量
            'is_delivery_fee' => $parameter['is_delivery_fee'],#免运费 0收运费 1免运费
        ];
        $shift  = array_shift($data);
//        $byTokenGetUserInfo = checkTokenUserData();
//        $data['user_id'] = $byTokenGetUserInfo['user_id'];
//        具体到每个人
        if(empty($parameter['gid'])){
            $res  = $this->insert($data);
            $res == true ? returnResponse(200,'添加成功',$res) : returnResponse(100,'添加失败');
        }
        $there[]  = ['id','eq',$shift];
        $data['update_time'] = microtime();
        $res    = $this->where($there)->update($data);
        if($res){
            returnResponse(200,'商品信息修改成功',$res);
        }
        returnResponse(100,'商品信息修改失败');
    }

    /**
     * @author:xiaohao
     * @time:2019/10/14 15:22
     * @param $parameter
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @description:商品状态
     */
    public function deleteGoods($parameter){
        $id   = json_decode($parameter['gid'],true);
        switch ($parameter['status']){
            case 1:
                foreach($id as $k => $v){
                    $res = $this->where('id',$v)->update(['is_del'=>'1']);
                }
                returnResponse(200,'删除成功',$res);
                break;
            case 2:
                foreach($id as $k => $v){
                    $res = $this->where('id',$v)->update(['is_del'=>'2']);
                }
                returnResponse(200,'下架成功',$res);
                break;
            case 3:
                foreach($id as $k => $v){
                    $res = $this->where('id',$v)->update(['is_del'=>'3']);
                }
                returnResponse(200,'申请上架',$res);
                break;
            case 0:
                foreach($id as $k => $v){
                    $res = $this->where('id',$v)->update(['is_del'=>'0']);
                }
                returnResponse(200,'正常状态',$res);
                break;
        }
    }

}
