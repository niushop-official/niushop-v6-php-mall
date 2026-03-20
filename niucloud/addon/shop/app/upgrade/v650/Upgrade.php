<?php

namespace addon\shop\app\upgrade\v650;

use addon\shop\app\model\delivery\Store;

class Upgrade
{

    public function handle()
    {
        $this->handleStoreData();
    }

    /**
     * 处理门店数据
     * @return true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handleStoreData()
    {
        $store_list = (new Store())->where([['store_no', '=', '']])->select()->toArray();

        foreach ($store_list as $store) {
            $store_id = $store['store_id'];
            (new Store())->where('store_id', $store_id)->update([
                'store_no' => create_no('', $store['store_id']),
            ]);
        }
        return true;
    }

}
