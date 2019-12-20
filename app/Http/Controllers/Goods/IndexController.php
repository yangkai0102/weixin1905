<?php

namespace App\Http\Controllers\Goods;

use App\Http\Controllers\Controller;
use App\WeiXin\GoodsModel;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    //商品详情页
    public function detail(Request $request){
        $goods_id=$request->input('id');
//        echo $goods_id;die;
        $goods=GoodsModel::find($goods_id);
//        print_r($goods);
        $data=[
            'goods'=>$goods
        ];
        return view('goods.detail',$data);
    }
}
