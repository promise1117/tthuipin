<?php
namespace app\index\controller;
use app\index\controller\Base;
class Index extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->banner    = Db('Banner'); //banner表
        $this->category  = Db('Category'); //分类表
        $this->goods     = Db('Goods'); //商品表
        $this->comments  = Db('Comments'); //评论表
        $this->goodsinfo = Db('Goodsinfo'); //商品详情表
    }


    /**
     * @author:xiaohao
     * @time:2019/11/06 14:18
     * @return mixed
     * @description:首页banner和列表
     */
    public function index(){
        //banner获取
        $bmap[] = ['is_show','eq','0'];
        $bmap[] = ['show_in_nav','eq','0'];
        $bsort  = ['order'=>'desc','id'=>'desc'];
        $res = $this->banner->where($bmap)->order($bsort)->select();
        //获取一级分类名
        $catList = $this->getCategoryParentList();
        //获取所有分类
        $catListt = $this->getCategoryParentListt();
        //获取热卖 分类
        $catHotList = $this->getCategoryHotList();
        //热销商品
        $hotwhere[] = ['index','eq','1'];
        $hotwhere[] = ['is_del','eq','0'];
        $hotsort    = ['sort'=>'desc','id'=>'desc'];
        $hotGoods = Db('Goods')->field('id,img,name,keywords,sell_price,market_price,sale')->where($hotwhere)->order($hotsort)->select();

        $this->assign([
            'banner'    => $res, //banner
            'catlist'   => $catList, //一级分类
            'catlistt'  => $catListt, //所有分类
            'hotlist'   => $catHotList, //获取热卖
            'hotgoods'  => $hotGoods, //热销商品
            'oncatid'  => '1', //热销商品
        ]);
        return $this->fetch();
    }

    /**
     * @author:xiaohao
     * @time:2019/11/06 14:17
     * @description:最近浏览的商品
     */
    public function getVisitGoodsList(){
        $parameter = $this->request->param('goodids');
        $visitwhere[] = ['id','in',$parameter];
        $visitsort = ['sort'=>'desc','id'=>'desc'];
        $visitList = Db('Goods')->field('id,img,name,sell_price,market_price')->where($visitwhere)->order($visitsort)->select();
        returnResponse(200,'请求成功',$visitList);
    }

    /**
     * @author:xiaohao
     * @time:2019/11/05 20:20
     * @param $catid    分类的id
     * @param string $page  分页数
     * @return mixed
     * @description:获取商品列表
     */
    public function getGoodsList(){
        $catid = $this->request->param('catid');
        $page  = $this->request->param('page');
        $pid = Db('Category')->where('cat_id',$catid)->find();
        if(empty($pid['parent_id'])){
            $catids = Db('Category')->where('parent_id',$catid)->where('is_show','1')->column('cat_id');
            $golimap[] = $golimap[] = ['categoryid','in',$catids];
        }else{
            $golimap[] = ['categoryid','eq',$catid];
        }
        $golimap[] = ['is_del','eq','0'];
        $golisort  = ['sort'=>'desc','id'=>'desc'];
        $goodslist = Db('Goods')->field('id,img,name,keywords,sell_price,market_price,sale')->where($golimap)->order($golisort)->limit($page,10)->select();
        foreach ($goodslist as $k=>$v){
            $word = explode(',',$v['keywords']);
            $v['keyword'] = $word[0];
            $goodslist[$k] = $v;
        }
//        if($goodslist){
        returnResponse(200,'请求成功',$goodslist);
//        }
//        returnResponse(100,'暫無商品,敬請期待！！！');
    }


    /**
     * @author:xiaohao
     * @time:2019/11/04 14:01
     * @description:获取二级分类
     */
    public function getTwoCateList(){
        //获取二级分类名
        $parameter = request()->param('catid');
        if($parameter){
            $catTwoList = $this->getCategoryTwoList($parameter);
        }
        if($catTwoList){
            returnResponse(200,'请求成功',$catTwoList);
        }
        returnResponse(100,'异常！！！');
    }

    /**
     * @author:xiaohao
     * @time:Times
     * @param $catid
     * @description:获取一级分类图片
     */
    public function getParentImage(){
        $catid = request()->param('catid');
        $catTowLists = $this->getParentImageLevel($catid);
        if($catTowLists){
            returnResponse(200,'请求成功',$catTowLists);
        }
        returnResponse(100,'异常！！！');
    }

    //商品详情
    public function getGoodsInfo(){
        $gid = request()->param('goodsid');
        //商品浏览量+1
        $this->goods->where('id',$gid)->setInc('liulan');
        //商品内容
        $goodsInfo = $this->goods
            ->where('id',$gid)
            ->find();
        //猜你喜欢
        $likeid = $goodsInfo['like'];
        $likemap[] = ['id','in',$likeid];
        $likemap[] = ['is_del','eq','0'];
        $likesort  = ['sort'=>'desc','id'=>'desc'];
        $likeGoods = Db('Goods')->where($likemap)->order($likesort)->limit(10)->select();
        //评论内容
        $commap[] = ['goods_id','eq',$gid];
        $commap[] = ['username','neq',''];
        $comsort = ['sort'=>'desc','id'=>'desc'];
        $comments = $this->comments->where($commap)->order($comsort)->select();
        //商品编号详情
        $infogsp[] = ['pid','eq','0'];
        $infogsp[] = ['taocan','neq','0'];
        $infogs[] = ['pid','neq','0'];
        $infogs[] = ['goods_id','eq',$gid];
        $infosort = ['order'=>'desc','id'=>'asc'];
//        echo $this->goodsinfo->getLastSql();
        $goodsinfo = Db('Goodsinfo')->where($infogs)->order($infosort)->select();
        array_shift($infogs);
        $goodstc = $this->goodsinfo->where($infogs)->where($infogsp)->order($infosort)->select();

        // 绑定套餐
        foreach ($goodstc as $k => $v) {
            $join = $v['join'];
            if($join == null){
                $join = [];
            }else{
                $join = explode(',',$join);
            }
            array_push($join,$v['id']);

            $sell_price = 0;
            $market_price = 0;

            for($i=0;$i<count($join);$i++){
                $goodsinfo_bind = Db('Goodsinfo')->where('pid',$join[$i])->order($infosort)->find();

                $goodstc[$k]['goodstc_bind'][$i] = $goodsinfo_bind;
                if(is_null($goodstc[$k]['goodstc_bind'][$i]['sell_price'])){
                    $goodstc[$k]['goodstc_bind'][$i]['sell_price'] = 0;
                }
                if(is_null($goodstc[$k]['goodstc_bind'][$i]['market_price'])){
                    $goodstc[$k]['goodstc_bind'][$i]['market_price'] = 0;
                }

                $sell_price += intval($goodstc[$k]['goodstc_bind'][$i]['sell_price']);

                $market_price += intval($goodstc[$k]['goodstc_bind'][$i]['market_price']);
            }
            $goodstc[$k]['sell_price'] = $sell_price;

            $goodstc[$k]['market_price'] = $market_price;
            $goodstc[$k]['cost_price'] = $market_price - $sell_price;
        }


        $this->assign([
            'info'      => $goodsInfo, //商品详情
            'comments'  => $comments,  //评论
            'likegoods' => $likeGoods, //猜你喜欢
            'goodsinfo' => $goodsinfo, //商品
            'taocan'    => $goodstc,   //套餐
        ]);


        return $this->fetch();

    }
    public function shortCut(){
        return $this->fetch();
    }

}
