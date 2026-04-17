<template>
    <div class="w-[full] h-[54px] bg-white tab-index">
        <el-tabs v-model="activeName" class="demo-tabs" @tab-click="handleClick" type="card">
            <el-tab-pane name="first">
                <template #label>今日</template>
            </el-tab-pane>
            <el-tab-pane label="昨日" name="second">
            </el-tab-pane>
            <el-tab-pane label="7日内" name="third">
            </el-tab-pane>
            <el-tab-pane label="30日内" name="fourth">
            </el-tab-pane>
            <el-tab-pane label="自定义" name="fifth">
                <el-dialog v-model="dialogVisible" width="520" draggable="true">
                    <template #header>
                        <div class="text-[14px] color-[#333] h-[42px] leading-[42px]">自定义时间选择</div>
                    </template>
                    <div class="flex items-center">
                        <span class="mr-[10px]">选择时间:</span>
                        <el-date-picker v-model="value2" class="w-[100px]" type="datetimerange" start-placeholder="开始时间" end-placeholder="结束时间" format="YYYY-MM-DD HH:mm:ss" date-format="YYYY/MM/DD ddd" time-format="A hh:mm:ss"/>
                    </div>
                    <template #footer>
                    <div class="dialog-footer">
                        <el-button type="primary" @click="dialogVisible = false">确认</el-button>
                        <el-button @click="dialogVisible = false">取消</el-button>
                    </div>
                    </template>
                </el-dialog>
            </el-tab-pane>
        </el-tabs>
    </div>
    <div class="min-w-[100px] min-h-[40px] px-[20px] bg-white tab-index mt-[15px]">
        <div class="h-[81px] border-b border-solid border-[#eff0f4] flex items-center justify-between py-[15px]">
            <div class="text-[14px]">商品概况</div>
            <div class="flex flex-col">
                <div class="flex">
                    <div class="text-[14px]">在架商品数</div>
                    <el-tooltip class="box-item" effect="light" content="当前时间，状态为上架的商品数量" placement="top-start">
                        <div class="nc-iconfont nc-icon-bangzhuV6xx wenHao text-[#666] ml-[4px]"></div>
                    </el-tooltip>
                </div>
                <div class="text-[20px] font-[600]">234</div>
            </div>
            <div class="flex flex-col">
                <div class="flex">
                    <div class="text-[14px]">在架商品数</div>
                    <el-tooltip class="box-item" effect="light" content="当前时间，状态为上架的商品数量" placement="top-start">
                        <div class="nc-iconfont nc-icon-bangzhuV6xx wenHao text-[#666] ml-[4px]"></div>
                    </el-tooltip>
                </div>
                <div class="text-[20px] font-[600]">234</div>
            </div>
            <div class="flex flex-col mr-[40px]">
                <div class="flex">
                    <div class="text-[14px]">在架商品数</div>
                    <el-tooltip class="box-item" effect="light" content="当前时间，状态为上架的商品数量" placement="top-start">
                        <div class="nc-iconfont nc-icon-bangzhuV6xx wenHao text-[#666] ml-[4px]"></div>
                    </el-tooltip>
                </div>
                <div class="text-[20px] font-[600]">234</div>
            </div>
        </div>
        <div class="h-[81px] border-b border-solid border-[#eff0f4] flex items-center justify-between">
            <div class="text-[14px]">商品流量</div>
            <div class="flex flex-col">
                <div class="flex">
                    <div class="text-[14px]">在架商品数</div>
                    <el-tooltip class="box-item" effect="light" content="当前时间，状态为上架的商品数量" placement="top-start">
                        <div class="nc-iconfont nc-icon-bangzhuV6xx wenHao text-[#666] ml-[4px]"></div>
                    </el-tooltip>
                </div>
                <div class="text-[20px] font-[600]">234</div>
            </div>
            <div class="flex flex-col">
                <div class="flex">
                    <div class="text-[14px]">在架商品数</div>
                    <el-tooltip class="box-item" effect="light" content="当前时间，状态为上架的商品数量" placement="top-start">
                        <div class="nc-iconfont nc-icon-bangzhuV6xx wenHao text-[#666] ml-[4px]"></div>
                    </el-tooltip>
                </div>
                <div class="text-[20px] font-[600]">234</div>
            </div>
            <div class="flex flex-col mr-[40px]">
                <div class="flex">
                    <div class="text-[14px]">在架商品数</div>
                    <el-tooltip class="box-item" effect="light" content="当前时间，状态为上架的商品数量" placement="top-start">
                        <div class="nc-iconfont nc-icon-bangzhuV6xx wenHao text-[#666] ml-[4px]"></div>
                    </el-tooltip>
                </div>
                <div class="text-[20px] font-[600]">234</div>
            </div>
        </div>
        <div class="h-[81px] flex items-center justify-between">
            <div class="text-[14px]">商品转化</div>
            <div class="flex flex-col">
                <div class="flex">
                    <div class="text-[14px]">在架商品数</div>
                    <el-tooltip class="box-item" effect="light" content="当前时间，状态为上架的商品数量" placement="top-start">
                        <div class="nc-iconfont nc-icon-bangzhuV6xx wenHao text-[#666] ml-[4px]"></div>
                    </el-tooltip>
                </div>
                <div class="text-[20px] font-[600]">234</div>
            </div>
            <div class="flex flex-col">
                <div class="flex">
                    <div class="text-[14px]">在架商品数</div>
                    <el-tooltip class="box-item" effect="light" content="当前时间，状态为上架的商品数量" placement="top-start">
                        <div class="nc-iconfont nc-icon-bangzhuV6xx wenHao text-[#666] ml-[4px]"></div>
                    </el-tooltip>
                </div>
                <div class="text-[20px] font-[600]">234</div>
            </div>
            <div class="flex flex-col mr-[40px]">
                <div class="flex">
                    <div class="text-[14px]">在架商品数</div>
                    <el-tooltip class="box-item" effect="light" content="当前时间，状态为上架的商品数量" placement="top-start">
                        <div class="nc-iconfont nc-icon-bangzhuV6xx wenHao text-[#666] ml-[4px]"></div>
                    </el-tooltip>
                </div>
                <div class="text-[20px] font-[600]">234</div>
            </div>
        </div>
    </div>
    <div class="flex justify-between mt-[15px] w-full">
        <div class="w-[50%] h-[101px] bg-[white]">
           <div class="h-[45px] border-b border-solid border-[#eff0f4]">
                <div class="pt-[15px] pl-[15px] text-[14px] font-bold">销售额（元）TOP5</div>
           </div>
           <div class="text-[#999] max-w-[400px] pl-[20px] pt-[15px] text-[14px]">暂无排名</div>
        </div>
        <div class="w-[49%] h-[101px] bg-[white]">
           <div class="h-[45px] border-b border-solid border-[#eff0f4]">
                <div class="pt-[15px] pl-[15px] text-[14px] font-bold">销量（件）TOP5</div>
           </div>
           <div class="text-[#999] max-w-[400px] pl-[20px] pt-[15px] text-[14px]">暂无排名</div>
        </div>
    </div>
    <div class="bg-[white] mt-[10px]">
        <div ref="incomeChartRef" class="h-[400px] w-full pt-[30px]"></div>
    </div>
    
</template>
<script lang="ts" setup>
import { ref, onMounted, nextTick } from 'vue'
import type { TabsPaneContext} from 'element-plus'
import * as echarts from 'echarts'

const activeName = ref('first')
const dialogVisible = ref(false)
const value2 = ref('')

const handleClick = (tab: TabsPaneContext, event: Event) => {
  if(tab.props.name == 'fifth'){
    dialogVisible.value = true
  }
  nextTick(()=>{
    initIncomeChart()
  })
  
}

// 折线图
const incomeChartRef = ref(null);

const initIncomeChart = () => {
  if (incomeChartRef.value !== null) {
    const incomeChart = echarts.init(incomeChartRef.value);

    // 生成随机数据的函数
    function generateRandomData(days) {
    return days.map(() => Math.round(Math.random() * 100));
    }
    const days = ['0时','1时','2时','3时','4时','5时','6时','7时','8时','9时','10时','11时','12时','13时','14时','15时','16时','17时','18时','19时','20时','21时','22时','23时', ];
    // 生成8条折线图的随机数据
    const lineData = Array.from({ length: 8 }, () => generateRandomData(days));


    // 配置项
    const option = {
    tooltip: {
        trigger: 'axis',
    },
    legend: {
        data: ['线1', '线2', '线3', '线4', '线5', '线6', '线7', '线8']
    },
    xAxis: {
        type: 'category',
        data: days,
    },
    yAxis: {
        type: 'value',
    },
    series: lineData.map((data, index) => ({
        name: `线${index + 1}`,
        type: 'line',
        data: data,
        itemStyle: {
        color: `#${((index + 1) * 100).toString(16).padStart(6, '0')}`, // 为每条线生成不同的颜色
        },
    })),
    };
    // 使用配置项初始化图表
    incomeChart.setOption(option);
    incomeChart.resize({
        width: 'auto'
    });
  }
};

onMounted(() => {
  initIncomeChart();
});
</script>
<style lang="scss" scoped>
.wenHao{
    font-size: 14px;
}
.tab-index :deep(.el-tabs__item.is-active){
    background-color: #105CFB;
    color: white;
}
.tab-index :deep(.el-tabs__item):hover{
    background-color: #105CFB;
    color: white;
}
.tab-index :deep(.el-tabs__item){
    line-height: 34px;
    height: 34px !important;
}
.tab-index :deep(.el-tabs__nav){
    border: 1px solid #D2D2D2;
    margin-left: 10px;
    height: 34px;
    margin-top: 10px;
    border-radius: 0;
}
.tab-index :deep(.el-tabs__header) {
  border: none;
}
</style>
