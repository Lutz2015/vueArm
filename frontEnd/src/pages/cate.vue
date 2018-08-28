<template>
    <div class="m-l-20 w-1100 detail">
        <el-tabs v-model="activeName" class="w-1000">
            <el-tab-pane label="清单列表" name="first">
                <el-table :data="cateData" border style="width: 100%;">
                    <el-table-column label="序号" align="center" width="60">
                        <template slot-scope="{row,$index}">
                            <span>{{$index + 1}}</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="cate" label="产品线" align="center"></el-table-column>
                    <el-table-column prop="goods_num" label="货号" align="center"></el-table-column>
                    <el-table-column prop="product" label="品名" align="center"></el-table-column>
                    <el-table-column prop="brand" label="品牌" align="center"></el-table-column>
                    <el-table-column prop="model" label="规格型号" align="center"></el-table-column>
                    <el-table-column prop="unit" label="单位" align="center"></el-table-column>
                    <el-table-column prop="unit_price" label="单价" align="center">
                        <template scope="scope">
                            <el-input size="small" v-model="scope.row.unit_price" :min="0" type="number" :disabled="scope.row.isUnit"></el-input>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" width="160" align="center">
                        <template slot-scope="scope">
                            <el-button size="small" @click.native="handleEdit(scope.$index, scope.row)" v-if="scope.row.isUnit" :loading="isLoading">编辑</el-button>
                            <el-button size="small" @click.native="handleCommit(scope.$index, scope.row)" v-else :loading="isLoading">提交</el-button>
                            <el-button size="small" type="danger" @click.native="handleDelete(scope.$index, scope.row)"     v-if="!showBtn[$index]" style="display: none">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>

            </el-tab-pane>
            <el-tab-pane label="产品清单及报价" name="second">
                <el-form ref="formList" :model="formList" :rules="listRules" label-width="110px">

                    <el-form-item label="货号:" prop="goods_num">
                        <el-input v-model.trim="formList.goods_num" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="品名:" prop="product">
                        <el-input v-model.trim="formList.product" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="产品线:" prop="cate">
                        <el-select v-model.trim="formList.cate" placeholder="请选择产品线" class="h-40 fl w-300">
                            <el-option label="IDS" value="IDS"></el-option>
                            <el-option label="ISTV" value="ISTV"></el-option>
                            <el-option label="RCU" value="RCU"></el-option>
                            <el-option label="HSIA" value="HSIA"></el-option>
                            <el-option label="WLAN" value="WLAN"></el-option>
                            <el-option label="O2O" value="O2O"></el-option>
                            <el-option label="运营" value="运营"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="品牌:" prop="brand">
                        <el-input v-model.trim="formList.brand" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="规格型号:" prop="model">
                        <el-input v-model.trim="formList.model" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="单位:" prop="unit">
                        <el-input v-model.trim="formList.unit"  class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="单价:" prop="unit_price">
                        <el-input v-model.trim="formList.unit_price"  class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button class="p-l-40 p-r-40" type="primary" @click="addContractList('formList')" :loading="isLoading">提交</el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>
<style type="text/css">
    .detail th {
        padding: 6px 0 !important;
    }
</style>
<script>
    import http from '../assets/js/http'
    import Vue from  'vue'
    export default {
        data() {
            return {
                formList: {
                    goods_num: '',
                    product: '',
                    cate: '',
                    model: '',
                    brand: '',
                    unit: '',
                    unit_price: ''
                },
                listRules: {
                    goods_num: [
                        { required: true, message: '请填写货号', trigger: 'blur' },
                    ],
                    product: [
                        { required: true, message: '请填写品名', trigger: 'blur' },
                    ],
                    brand: [
                        { required: true, message: '请填写品牌', trigger: 'blur' },
                    ],
                    model: [
                        { required: true, message: '请填写规格型号', trigger: 'blur' },
                    ],
                    cate: [
                        { required: true, message: '请填写产品类别', trigger: 'blur' },
                    ],
                    unit: [
                        { required: true, message: '请填写单位', trigger: 'blur' },
                    ],
                    unit_price: [
                        { required: true, message: '请填写单价', trigger: 'blur' },
                    ]
                },
                cateData: [],
                activeName: 'first',
                showEdit: [],
                showBtn: []
            }
        },
        watch: {

        },
        created() {

        },
        mounted() {
            this.$store.state.number = this.$route.query.number;
            this.username = Lockr.get('userInfo').username;
            this.init();
        },
        methods: {
            init() {
                this.apiPost('admin/contract/findGoodsInfo', {}).then((res) => {
                    this.handelResponse(res, (data) => {
                        if (data && data.length > 0) {
                            data.forEach(item => {
                                item.isUnit = true;
                                this.cateData.push(item);
                            })
                        }

                    })
                });
            },

            //点击编辑
            handleEdit($index, row) {
                this.cateData.forEach((item, index) => {
                    if ($index === index) {
                        item.isUnit = !item.isUnit;
                    }
                })
            },

            //点击提交更新
            handleCommit(index, row) {
                this.isLoading = !this.isLoading;
                this.apiPost('admin/contract/modifyGoodsInfo', {goods_num: row.goods_num}).then((res) => {
                    this.handelResponse(res, (data) => {
                        this.isLoading = !this.isLoading;
                        _g.toastMsg('success', '添加成功');
                        this.cateData.forEach((item, index) => {
                            if ($index === index) {
                                item.isUnit = !item.isUnit;
                            }
                        })

                    }, () => {
                        this.isLoading = !this.isLoading;
                    })
                });
            },
            //点击删除
            handleDelete(index, row) {

            },
            // 添加合同清单基础筛选库
            addContractList() {
                this.$refs.formList.validate((pass) => {
                    if (pass) {
                        this.isLoading = !this.isLoading;
                        this.apiPost('admin/contract/addGoodsInfo', this.formList).then((res) => {
                            this.handelResponse(res, (data) => {
                                _g.toastMsg('success', '添加成功');
                                this.isLoading = !this.isLoading;
                                this.formList = {
                                    goods_num: '',
                                    product: '',
                                    cate: '',
                                    model: '',
                                    brand: '',
                                    unit: '',
                                    unit_price: ''
                                };
                            }, () => {
                                this.isLoading = !this.isLoading;
                            })
                        })
                    }
                })
            },
            add0(m){
                return m < 10 ? '0' + m : m
            },
            format(data, type) {
                let time = new Date(data);
                let y = time.getFullYear();
                let m = time.getMonth() + 1;
                let d = time.getDate();
                let h = time.getHours();
                let mm = time.getMinutes();
                let s = time.getSeconds();
                if (type === 1) {
                    return y
                } else if (type === 2) {
                    return y + '/' + this.add0(m);
                } else {
                    return y + '/' + this.add0(m) + '/' + this.add0(d);
                }

            },

        },
        mixins: [http]
    }
</script>