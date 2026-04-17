<template>
	<!-- 内容 -->
	<div class="content-wrap" v-show="diyStore.editTab == 'content'">
		<div class="edit-attr-item-wrap">
			<h3 class="mb-[10px]">{{ t('selectStyle') }}</h3>
			<el-form label-width="80px" class="px-[10px]">
				<el-form-item :label="t('styleLabel')">
					<el-radio-group v-model="diyStore.editComponent.headStyle">
						<el-radio label="style-1">{{ t('headStyle1') }}</el-radio>
						<el-radio label="style-2">{{ t('headStyle2') }}</el-radio>
						<el-radio label="style-3">{{ t('headStyle3') }}</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item :label="t('manyGoodsListAroundRadius')" v-show="diyStore.editComponent.headStyle == 'style-3'">
					<el-slider v-model="diyStore.editComponent.aroundRadius" show-input size="small" class="ml-[10px] graphic-nav-slider" :max="50" />
				</el-form-item>
			</el-form>
		</div>
		<div class="edit-attr-item-wrap">
			<h3 class="mb-[10px]">{{ t('goodsSet') }}</h3>
			<el-form label-width="80px" class="px-[10px]">
				<el-form-item :label="t('sortWay')">
					<el-radio-group v-model="diyStore.editComponent.sortWay">
						<el-radio label="default">{{ t('default') }}</el-radio>
						<el-radio label="sale_num">{{ t('sales') }}</el-radio>
						<el-radio label="price">{{ t('price') }}</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item :label="t('goodsNum')">
					<div class="flex items-center w-full ml-[5px]">
						<el-slider class="flex-1" v-model="diyStore.editComponent.num" max="20" size="small" />
						<span class="ml-[15px]">{{ diyStore.editComponent.num }}</span>
					</div>
				</el-form-item>
			</el-form>
		</div>
		<div class="edit-attr-item-wrap">
			<h3 class="mb-[10px]">{{ t("manyGoodsListCategorySet") }}</h3>
			<el-form label-width="80px" class="px-[10px]">
				<el-form-item :label="t('dataSources')">
					<el-radio-group v-model="diyStore.editComponent.source">
						<el-radio label="custom">{{ t('manyGoodsListSourceDiy') }}</el-radio>
						<el-radio label="goods_category">{{ t('manyGoodsListSourceCategory') }}</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item :label="t('goodsCategoryTitle')" v-show="diyStore.editComponent.source == 'goods_category'">
					<el-input v-model="diyStore.editComponent.goods_category_name" :placeholder="t('selectCategory')" readonly class="select-diy-page-input" @click="firstCategoryShowDialogOpen()">
						<template #suffix>
							<div @click.stop="clearCategory">
								<el-icon v-if="diyStore.editComponent.goods_category_name">
									<Close />
								</el-icon>
								<el-icon v-else>
									<ArrowRight />
								</el-icon>
							</div>
						</template>
					</el-input>
				</el-form-item>

				<div v-show="diyStore.editComponent.source == 'custom'">

					<p class="text-sm text-gray-400 mb-[10px]">{{ t('dragMouseAdjustOrder') }}</p>

					<div ref="goodsBoxRef">
						<div v-for="(item,index) in diyStore.editComponent.list" :key="item.id" class="item-wrap p-[10px] pb-0 relative border border-dashed border-gray-300 mb-[16px]">

							<el-form-item :label="t('manyGoodsListCategoryName')">
								<el-input v-model="item.title" clearable maxlength="4" show-word-limit/>
							</el-form-item>
							<el-form-item :label="t('manyGoodsListSubTitle')" v-show="diyStore.editComponent.headStyle == 'style-1'">
								<el-input v-model="item.desc" clearable maxlength="5" show-word-limit/>
							</el-form-item>
							<el-form-item :label="t('goodsSelectPopupSelectGoodsButton')">
								<el-radio-group v-model="item.source">
									<el-radio label="all">{{ t('goodsSelectPopupAllGoods') }}</el-radio>
									<el-radio label="category">{{ t('selectCategory') }}</el-radio>
									<el-radio label="custom">{{ t('manualSelectionSources') }}</el-radio>
								</el-radio-group>
							</el-form-item>
							<el-form-item :label="t('selectCategory')" v-if="item.source == 'category'">
								<div class="flex items-center w-full">
									<div class="cursor-pointer ml-auto" @click="categoryShowDialogOpen(index)">
										<span class="text-[var(--el-color-primary)]">{{ item.goods_category_name }}</span>
										<span class="iconfont iconxiangyoujiantou"></span>
									</div>
								</div>
							</el-form-item>
							<el-form-item :label="t('customGoods')" v-if="item.source == 'custom'">
								<goods-select-popup ref="goodsSelectPopupRef" v-model="item.goods_ids" :min="1" :max="99" />
							</el-form-item>
							<el-form-item :label="t('image')" v-show="diyStore.editComponent.headStyle == 'style-3'">
								<upload-image v-model="item.imageUrl" :limit="1" />
							</el-form-item>

							<div class="del absolute cursor-pointer z-[2] top-[-8px] right-[-8px]" v-show="diyStore.editComponent.list.length > 1" @click="diyStore.editComponent.list.splice(index,1)">
								<icon name="element CircleCloseFilled" color="#bbb" size="20px"/>
							</div>

						</div>
					</div>

					<el-button class="w-full" @click="addItem">{{ t('manyGoodsLisAddItem') }}</el-button>
				</div>

			</el-form>

			<!-- 选择一级商品分类弹出框 -->
			<el-dialog v-model="firstCategoryShowDialog" :title="t('goodsCategoryTitle')" width="1000px" :close-on-press-escape="false" :destroy-on-close="true" :close-on-click-modal="false">
				<el-table :data="firstCategoryTable.data" ref="firstCategoryTableRef" size="large" v-loading="firstCategoryTable.loading" height="490px" @current-change="handleCurrentCategoryChange" row-key="category_id" highlight-current-row>
					<template #empty>
						<span>{{ !firstCategoryTable.loading ? t('emptyData') : '' }}</span>
					</template>
					<el-table-column :label="t('categoryName')" min-width="120">
						<template #default="{ row }">
							<span class="order-2">{{ row.category_name }}</span>
						</template>
					</el-table-column>
					<el-table-column :label="t('categoryImage')" width="170" align="left">
						<template #default="{ row }">
							<div class="h-[30px]">
								<el-image class="w-[30px] h-[30px] " :src="img(row.image)" fit="contain">
									<template #error>
										<div class="image-slot">
											<img class="w-[30px] h-[30px]" src="@/addon/shop/assets/category_default.png" />
										</div>
									</template>
								</el-image>
							</div>
						</template>
					</el-table-column>
				</el-table>
				<div class="flex items-center justify-end mt-[15px]">
					<el-button type="primary" @click="saveFirstCategoryId">{{ t('confirm') }}</el-button>
					<el-button @click="firstCategoryShowDialog = false">{{ t('cancel') }}</el-button>
				</div>
			</el-dialog>

			<el-dialog v-model="categoryShowDialog" :title="t('goodsCategoryTitle')" width="1000px" :close-on-press-escape="false" :destroy-on-close="true" :close-on-click-modal="false">
				<el-table :data="categoryTable.data" ref="categoryTableRef" size="large" v-loading="categoryTable.loading"
					height="490px" @selection-change="handleSelectionChange" row-key="category_id"
					:expand-row-keys="expand_category_ids"
					:tree-props="{ hasChildren: 'hasChildren', children: 'child_list' }">
					<template #empty>
						<span>{{ !categoryTable.loading ? t('emptyData') : '' }}</span>
					</template>
					<el-table-column type="selection" width="55" />
					<el-table-column :label="t('categoryName')" min-width="120">
						<template #default="{ row }">
							<span class="order-2">{{ row.category_name }}</span>
						</template>
					</el-table-column>
					<el-table-column :label="t('categoryImage')" width="170" align="left">
						<template #default="{ row }">
							<div class="h-[30px]">
								<el-image class="w-[30px] h-[30px] " :src="img(row.image)" fit="contain">
									<template #error>
										<div class="image-slot">
											<img class="w-[30px] h-[30px]" src="@/addon/shop/assets/category_default.png" />
										</div>
									</template>
								</el-image>
							</div>
						</template>
					</el-table-column>
				</el-table>
				<div class="flex items-center justify-end mt-[15px]">
					<el-button type="primary" @click="saveCategoryId">{{ t('confirm') }}</el-button>
					<el-button @click="categoryShowDialog = false">{{ t('cancel') }}</el-button>
				</div>
			</el-dialog>

		</div>
	</div>

	<!-- 样式 -->
	<div class="style-wrap" v-show="diyStore.editTab == 'style'">

		<div class="edit-attr-item-wrap">
			<h3 class="mb-[10px]">{{ t('goodsStyle') }}</h3>
			<el-form label-width="80px" class="px-[10px]">
				<el-form-item :label="t('goodsBgColor')">
					<el-color-picker v-model="diyStore.editComponent.elementBgColor" show-alpha :predefine="diyStore.predefineColors" />
				</el-form-item>
				<el-form-item :label="t('goodsNameColor')">
					<el-color-picker v-model="diyStore.editComponent.goodsNameStyle.color" show-alpha :predefine="diyStore.predefineColors" />
					<div class="mr-[20px]"></div>
					<el-radio-group v-model="diyStore.editComponent.goodsNameStyle.fontWeight">
						<el-radio :label="'normal'">{{ t('fontWeightNormal') }}</el-radio>
						<el-radio :label="'bold'">{{ t('fontWeightBold') }}</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item :label="t('goodsPriceColor')">
					<el-color-picker v-model="diyStore.editComponent.priceStyle.mainColor" show-alpha :predefine="diyStore.predefineColors" />
				</el-form-item>
				<el-form-item :label="t('goodsSaleColor')">
					<el-color-picker v-model="diyStore.editComponent.saleStyle.color" show-alpha :predefine="diyStore.predefineColors" />
				</el-form-item>
				<el-form-item :label="t('topRounded')">
					<el-slider v-model="diyStore.editComponent.topElementRounded" show-input size="small" class="ml-[10px] graphic-nav-slider" :max="50" />
				</el-form-item>
				<el-form-item :label="t('bottomRounded')">
					<el-slider v-model="diyStore.editComponent.bottomElementRounded" show-input size="small" class="ml-[10px] graphic-nav-slider" :max="50" />
				</el-form-item>
			</el-form>
		</div>

		<!-- 组件样式 -->
		<slot name="style"></slot>
	</div>
</template>

<script lang="ts" setup>
import { t } from '@/lang'
import { img } from '@/utils/common'
import useDiyStore from '@/stores/modules/diy'
import { ref, reactive, onMounted, nextTick } from 'vue'
import { ElTable } from 'element-plus'
import { range } from 'lodash-es'
import Sortable from 'sortablejs'
import { getCategoryTree,getCategoryList } from '@/addon/shop/api/goods'
import goodsSelectPopup from '@/addon/shop/views/goods/components/goods-select-popup.vue'

const diyStore:any = useDiyStore()
diyStore.editComponent.ignore = ['componentBgUrl'] // 忽略公共属性

// 组件验证
diyStore.editComponent.verify = (index: number) => {
    const res = { code: true, message: '' }

    if(diyStore.value[index].source == 'custom') {

        diyStore.value[index].list.forEach((item: any) => {
            if (item.source === 'category') {
                if (item.goods_category == '') {
                    res.code = false
                    res.message = t('goodsCategoryPlaceholder')
                    return res
                }
            } else if (item.source == 'custom') {
                if (item.goods_ids.length == 0) {
                    res.code = false
                    res.message = t('goodsPlaceholder')
                }
            }
        });
    }else if(diyStore.value[index].source == 'goods_category') {
        if (diyStore.value[index].goods_category == '') {
            res.code = false
            res.message = t('goodsCategoryPlaceholder')
            return res
        }
    }

    return res
}

diyStore.editComponent.list.forEach((item: any) => {
    if (!item.id) item.id = diyStore.generateRandom()
})

// 一级商品分类
const firstCategoryShowDialog = ref(false)

const firstCategoryTable = reactive({
    loading: true,
    data: [],
    searchParam: {
        level: 1
    }
})

const firstCategoryTableRef = ref<InstanceType<typeof ElTable>>()

/**
 * 获取商品分类列表
 */
const loadCategoryList = () => {
    firstCategoryTable.loading = true

    getCategoryList({
        ...firstCategoryTable.searchParam
    }).then(res => {
        firstCategoryTable.loading = false
        firstCategoryTable.data = res.data
    }).catch(() => {
        firstCategoryTable.loading = false
    })
}

const saveFirstCategoryId = () => {
    diyStore.editComponent.goods_category = currFirstCategory.category_id
    diyStore.editComponent.goods_category_name = currFirstCategory.category_name;
    firstCategoryShowDialog.value = false
}

const firstCategoryShowDialogOpen = () => {
    firstCategoryShowDialog.value = true
    if (currFirstCategory) {
        setTimeout(() => {
            firstCategoryTableRef.value!.setCurrentRow(currFirstCategory)
        }, 200)
    }
}

const clearCategory = ()=> {
    diyStore.editComponent.goods_category = ''
    diyStore.editComponent.goods_category_name = '';
}

// 选择商品分类
let currFirstCategory:any = {}
const handleCurrentCategoryChange = (val: string | any[]) => {
    currFirstCategory = val
}

// 商品分类树结构
const categoryShowDialog = ref(false)

const goodsBoxRef = ref()

const categoryTable = reactive({
    loading: true,
    data: []
})

onMounted(() => {
    loadCategoryTree()

    loadCategoryList()

    nextTick(() => {
        const sortable = Sortable.create(goodsBoxRef.value, {
            group: 'item-wrap',
            animation: 200,
            onEnd: event => {
                const temp = diyStore.editComponent.list[event.oldIndex!]
                diyStore.editComponent.list.splice(event.oldIndex!, 1)
                diyStore.editComponent.list.splice(event.newIndex!, 0, temp)
                sortable.sort(
                    range(diyStore.editComponent.list.length).map(value => {
                        return value.toString()
                    })
                )
            }
        })
    })
})

const categoryTableRef = ref<InstanceType<typeof ElTable>>()

/**
 * 获取商品分类列表
 */
let currCategoryData: any = null
const loadCategoryTree = () => {
    categoryTable.loading = true

    getCategoryTree().then(res => {
        categoryTable.loading = false
        categoryTable.data = res.data
    }).catch(() => {
        categoryTable.loading = false
    })
}

// 选择商品分类
let selectIndex = 0; // 当前选择的下标
const handleSelectionChange = (val: string | any[]) => {
    let data = ''
    if (val) data = val[val.length - 1]
    if (val.length > 1) categoryTableRef.value!.clearSelection()
    if (data) categoryTableRef.value!.toggleRowSelection(data, true)
    currCategoryData = data
}

const saveCategoryId = () => {
    diyStore.editComponent.list[selectIndex].goods_category = currCategoryData.category_id
    diyStore.editComponent.list[selectIndex].goods_category_name = currCategoryData.category_name;
    categoryShowDialog.value = false
}

const categoryShowDialogOpen = (index: any) => {
    selectIndex = index;
    categoryShowDialog.value = true

	nextTick(()=>{
		setRowSelection()
	})
}

//分类数据选中回填,设置展开行
const expand_category_ids = ref<Array<any>>([])
const setRowSelection = ()=>{
	expand_category_ids.value = []
	categoryTable.data.forEach((el:any)=>{
		if(diyStore.editComponent.list[selectIndex].goods_category == el.category_id){
			categoryTableRef.value!.toggleRowSelection(el, true)
		}else if(el.child_list&&el.child_list.length){
			el.child_list.forEach((v:any)=>{
				if(diyStore.editComponent.list[selectIndex].goods_category == v.category_id){
					expand_category_ids.value.push(el.category_id.toString())
					categoryTableRef.value!.toggleRowSelection(v, true)
				}
			})
		}
	})
}

const addItem = () => {
    diyStore.editComponent.list.push({
        id: diyStore.generateRandom(),
        title : "分类",
        desc : "分类描述",
        source : "all",
        goods_category : '',
        goods_category_name : '请选择',
        goods_ids : [],
        imageUrl:''
    })
}

defineExpose({})

</script>

<style lang="scss" scoped></style>
