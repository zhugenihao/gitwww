<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Queue;
use think\Cache;
use think\Exception;
use app\index\model\Goods;
use app\index\model\GoodsLog;
use think\Log;

class Index extends Controller {

    public function index(){
//        Log::record(array(2,323,545,767));
        return $this->fetch();
    }
    private function redisConnect() {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        return $redis;
    }
    /**
     * 初始化库存到队列
     */
    public function initialize_store() {
        $redis = $this->redisConnect();
        $redis->del('goods_store');
        $res = $redis->lLen('goods_store');
        $goods = db::name("goods")->where(['goods_id' => 1])->find();
        $count = $goods['goods_store'] - $res;
        for ($i = 0; $i < $count; $i++) {
            $redis->lPush('goods_store', 1); //加入队列
            echo $i . "库存入列成功！," . "\n";
        }
    }

    /**
     * 防高并发超出----redis队列
     * @return type
     */
    public function goods_store() {
        $redis = $this->redisConnect();
        $listres = $redis->lPop('goods_store'); //减去队列
        if (!$listres) {
            exit("库存不足，商品已购买完毕！");
        } else {
            $setRes = db::name("goods")->where(['goods_id' => 1])->setDec('goods_store', 1);
            if ($setRes) {
                db::name("goods_log")->insert(['goods_id' => 1, 'log_time' => time(), 'goods_store' => $redis->lLen('goods_store')]);
                exit("你已购买到此商品！");
            }
        }
    }

    /**
     * 防高并发超出----数据表锁(要结合事务)
     * @return type
     */
    public function goods_store2() {
        Db::startTrans(); // 启动事务
        
        $goods = Goods::where(['goods_id' => 1])->lock(true)->find();
        if ($goods['goods_store'] < 1) {
            exit("库存不足，商品已购买完毕！");
        }
        try {
            
            $setRes = Goods::where(['goods_id' => 1])->setDec('goods_store', 1);
            
            Db::commit(); // 提交事务
            if ($setRes) {
                GoodsLog::insert(['goods_id' => 1, 'log_time' => time()]);
                exit("你已购买到此商品！");
            }
        } catch (\Exception $e) {
            
            // 回滚事务
            Db::rollback();
        }
    }

    /**
     * 防高并发超出----文件锁
     * @return type
     */
    public function goods_store3($i=1) {
        Db::startTrans(); // 启动事务
        $fp = fopen(__DIR__ . "/lock.txt", "w+");
        flock($fp, LOCK_EX); // 排他锁 
//        if (!flock($fp, LOCK_EX | LOCK_NB)) {
//            echo "系统繁忙，请稍后再试";
//            return;
//        }
        $goods = db::name("goods")->where(['goods_id' => 1])->find();
        if ($goods['goods_store'] < 1) {
            exit("库存不足，商品已购买完毕！");
        }
        try {
            $setRes = db::name("goods")->where(['goods_id' => 1])->setDec('goods_store', 1);
            if ($setRes) {
                db::name("goods_log")->insert(['goods_id' => 1, 'log_time' => time()]);
                Db::commit(); // 提交事务
                flock($fp, LOCK_UN); //释放锁
//                exit("你已购买到此商品！");
                echo $i."你已购买到此商品！"."\t";
            }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        fclose($fp);
    }

    public function goodslist() {
        for ($i = 0; $i < 2000; $i++) {
            $this->goods_store3($i);
        }
    }

    /**
     * 一个使用了队列的 action
     */
    public function actionWithHelloJob() {
        // 1.当前任务将由哪个类来负责处理。 
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName = 'app\index\job\Hello';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName = "helloJobQueue";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，存储其public属性的键值对 )
        $jobData = ['ts' => time(), 'bizId' => uniqid(), 'a' => 1];
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push($jobHandlerClassName, $jobData, $jobQueueName);
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if ($isPushed !== false) {
            echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ" . "<br>";
        } else {
            echo 'Oops, something went wrong.';
        }
    }
    public function nihao(){
        echo "你好！";
    }

}
