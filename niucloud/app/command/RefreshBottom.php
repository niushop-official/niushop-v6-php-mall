<?php
declare (strict_types=1);
namespace app\command;

use app\upgrade\v156\Upgrade;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class RefreshBottom extends Command
{
    public function configure()
    {
        // 指令配置
        $this->setName('refresh:bottom')
            ->setDescription('升级156版本底部导航异常，刷新底部导航。');
    }

    /**
     * 执行任务
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('开始处理');
        (new Upgrade())->handle();
        $output->writeln('刷新完成，请重新发布小程序');

    }
}
