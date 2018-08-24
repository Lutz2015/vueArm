<template>
    <div class="m-l-20 w-1100 detail">
        <el-tabs v-model="activeName" class="w-1000">
            <el-tab-pane label="基本详情" name="first">
                <el-form  :model="formDetail" :inline="true" class="demo-form-inline dynamic-form" label-width="100px">
                <el-form-item label="编辑" style="display: block">
                    <el-switch v-model="isEdit" @change="openEdit"></el-switch>
                </el-form-item>
                <el-form-item label="合同编号:">
                        <el-input v-model.trim="formDetail.number" class="h-40 fl w-300" :disabled="true"></el-input>
                    </el-form-item>
                <el-form-item label="所属年度:">
                    <el-col class="h-40 fl w-300">
                        <el-date-picker type="year" v-model="formDetail.year" style="width: 100%;" :disabled="true"></el-date-picker>
                    </el-col>
                </el-form-item>
                <el-form-item label="合同类别:">
                    <el-select v-model="formDetail.category" class="h-40 fl w-300" :disabled="true">
                        <el-option label="合同类型一" value="0"></el-option>
                        <el-option label="合同类型一" value="1"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="合同税率:">
                    <el-input v-model.trim="formDetail.tax_rate" class="h-40 fl w-300" :disabled="true"></el-input>
                </el-form-item>
                <el-form-item label="合同甲方:">
                    <el-input v-model.trim="formDetail.party_a" class="h-40 w-300" :disabled="true"></el-input>
                </el-form-item>
                <el-form-item label="第三方:">
                    <el-input v-model.trim="formDetail.thirdparty" class="h-40 w-300" :disabled="true"></el-input>
                </el-form-item>
                <el-form-item label="酒店集团:">
                    <el-input v-model.trim="formDetail.group" class="h-40 w-300" :disabled="true"></el-input>
                </el-form-item>
                <el-form-item label="酒店名字:">
                    <el-input v-model="formDetail.hotel" class="h-40 w-300" :disabled="true"></el-input>
                </el-form-item>
                <el-form-item label="项目验收时间:">
                    <el-col class="h-40 fl w-300">
                        <el-date-picker type="date" placeholder="选择日期" v-model="formDetail.check_time" style="width: 100%;" :disabled="isDisabled"></el-date-picker>
                    </el-col>
                </el-form-item>
                <el-form-item label="合同终止时间:">
                        <el-col class="h-40 fl w-300">
                            <el-date-picker type="date" placeholder="选择日期" v-model="formDetail.stop_time" style="width: 100%;" :disabled="isDisabled"></el-date-picker>
                        </el-col>
                    </el-form-item>
                <el-form-item label="服务开始时间:">
                    <el-col class="h-40 fl w-300">
                        <el-date-picker type="date" placeholder="选择日期" v-model="formDetail.begin_time" style="width: 100%;" :disabled="isDisabled"></el-date-picker>
                    </el-col>
                </el-form-item>
                <el-form-item label="服务结束时间:">
                    <el-col class="h-40 fl w-300">
                        <el-date-picker type="date" placeholder="选择日期" v-model="formDetail.end_time" style="width: 100%;" :disabled="isDisabled"></el-date-picker>
                    </el-col>
                </el-form-item>
                <el-form-item style="display: block">
                    <el-button class="p-l-30 p-r-30" type="primary" @click="editContractInfo" :loading="isLoading">提交</el-button>
                </el-form-item>
            </el-form>
            </el-tab-pane>
            <el-tab-pane label="清单列表" name="second">
                <el-form ref="formContract" :model="formContract" :rules="contractRules" label-width="100px">
                    <el-form-item label="产品名称:">
                        <el-radio-group v-model="defaultRadio">
                            <el-radio :label="1">硬件产品</el-radio>
                            <el-radio :label="2">软件产品</el-radio>
                            <el-radio :label="3">安装调试</el-radio>
                            <el-radio :label="4">服务</el-radio>
                            <el-radio :label="5">其他</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="货号:" prop="goods_num">
                        <el-input class="h-40 fl w-300" v-model.trim="formContract.goods_num"></el-input>
                        <el-button @click.prevent="loadAll('1')">查找</el-button>
                    </el-form-item>
                    <el-form-item label="品名:" prop="product">
                        <el-input class="h-40 fl w-300" v-model.trim="formContract.product"></el-input>
                        <el-button @click.prevent="loadAll('2')">查找</el-button>
                    </el-form-item>
                    <el-form-item label="产品线:" prop="cate">
                        <el-input v-model.trim="formContract.cate" class="h-40 fl w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="品牌:" prop="brand">
                        <el-input v-model.trim="formContract.brand" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="规格型号:" prop="model">
                        <el-input v-model.trim="formContract.model" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="单位:" prop="unit">
                        <el-input v-model.trim="formContract.unit" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="单价:" prop="unit_price">
                        <el-input v-model.trim="formContract.unit_price" :min="1" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="数量:" prop="amount">
                        <el-input-number v-model="formContract.amount" label="描述文字" :min="1"></el-input-number>
                    </el-form-item>
                    <el-form-item>
                        <el-button class="p-l-40 p-r-40" type="primary" @click="addContract()">添加</el-button>
                        <!--<span style="color: #f00; opacity: 0.6">暂时不开放删除，请谨慎添加哦！</span>-->
                    </el-form-item>
                </el-form>
                <el-row style="margin-bottom: 10px; text-align: right">
                    <el-button><i class="el-icon-download el-icon--left"></i>下载</el-button>
                </el-row>
                <el-table :data="tableContractData" border style="width: 100%;">
                    <el-table-column prop="type" label="产品名称" width="120"></el-table-column>
                    <el-table-column label="产品类别">
                        <template scope="scope">
                            <el-input size="small" v-model="scope.row.cate"></el-input>
                        </template>
                    </el-table-column>
                    <el-table-column prop="goods_num" label="货号"></el-table-column>
                    <el-table-column prop="product" label="品名"></el-table-column>
                    <el-table-column prop="brand" label="品牌"></el-table-column>
                    <el-table-column prop="model" label="规格型号"></el-table-column>
                    <el-table-column prop="unit" label="单位"></el-table-column>
                    <el-table-column prop="amount" label="数量"></el-table-column>
                    <el-table-column prop="unit_price" label="单价">
                        <template scope="scope">
                            <el-input size="small"  v-model="scope.row.unit_price" :min="0" type="number" ></el-input>
                        </template>
                    </el-table-column>
                </el-table>
                <el-row class="m-t-10">
                    <el-button type="primary" class="p-l-40 p-r-40" @click="editContractList">确认修改</el-button>
                </el-row>
            </el-tab-pane>
            <el-tab-pane label="发票列表" name="third">
                <el-form ref="formBill" :model="formBill" label-width="80px">
                    <el-form-item label="时间选择:">
                        <el-col class="fl date-ml">
                            <el-date-picker type="year" placeholder="年度" v-model="formBill.date" style="width: 100%; "></el-date-picker>
                        </el-col>
                        <el-select class="fl selet-quarter" v-model="formBill.quarter" placeholder="选择季度">
                            <el-option label="Q1" value="Q1"></el-option>
                            <el-option label="Q2" value="Q2"></el-option>
                            <el-option label="Q3" value="Q3"></el-option>
                            <el-option label="Q4" value="Q4"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="价税合计:">
                        <el-col class="line" :span="1">17%:</el-col>
                        <el-col class="line m-r-30" :span="4">
                            <el-input v-model.trim="formBill.billAll17" :min="0" type="number" @input="changeAll17(formBill.billAll17)"></el-input>
                        </el-col>
                        <el-col class="line" :span="1">6%:</el-col>
                        <el-col class="line m-r-30" :span="4">
                            <el-input v-model.trim="formBill.billAll06" :min="0" type="number"  @input="changeAll06(formBill.billAll06)"></el-input>
                        </el-col>
                    </el-form-item>

                    <el-form-item label="不含税额:">
                        <el-col class="line" :span="1">17%:</el-col>
                        <el-col class="line m-r-30" :span="4">
                            <el-input v-model.trim="formBill.billNoTax17" :min="0"  type="number" @input="changeNoTax17(formBill.billNoTax17)"></el-input>
                        </el-col>
                        <el-col class="line" :span="1">6%:</el-col>
                        <el-col class="line m-r-30" :span="4">
                            <el-input v-model.trim="formBill.billNoTax06" :min="0"  type="number"  @input="changeNoTax06(formBill.billNoTax06)"></el-input>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="税额:">
                        <el-col class="line" :span="1">17%:</el-col>
                        <el-col class="line m-r-30" :span="4">
                            <el-input v-model.trim="formBill.billTax17"  :disabled="true"></el-input>
                        </el-col>
                        <el-col class="line" :span="1">6%:</el-col>
                        <el-col class="line m-r-30" :span="4">
                            <el-input v-model.trim="formBill.billTax06"  :disabled="true"></el-input>
                        </el-col>
                    </el-form-item>
                    <el-form-item>
                        <el-button class="p-l-40 p-r-40" type="primary" @click="addBill()">添加</el-button>
                    </el-form-item>
                </el-form>
                <el-table
                        :data="tableBillData"
                        :summary-method="getSummaries"
                        show-summary
                        border
                        style="width: 100%">
                    <el-table-column prop="check_time" label="时间段" width="100" align = "center"></el-table-column>
                    <el-table-column prop="billAll" label="价税合计" align = "center">
                        <el-table-column prop="billAll17" label="17%" width="100" align = "center"></el-table-column>
                        <el-table-column prop="billAll06" label="6%" width="100" align = "center"></el-table-column>
                        <el-table-column prop="billAll0" label="小计" width="100" align = "center"></el-table-column>
                    </el-table-column>
                    <el-table-column prop="billNoTax" label="不含税额" align = "center">
                        <el-table-column prop="billNoTax17" label="17%" width="100" align = "center"></el-table-column>
                        <el-table-column prop="billNoTax06" label="6%" width="100" align = "center"></el-table-column>
                        <el-table-column prop="billNoTax0" label="小计" width="100" align = "center"></el-table-column>
                    </el-table-column>
                    <el-table-column prop="billTax" label="税额" align = "center">
                        <el-table-column prop="billTax17" label="17%" width="100" align = "center"></el-table-column>
                        <el-table-column prop="billTax06" label="6%" width="100" align = "center"></el-table-column>
                        <el-table-column prop="billTax0" label="小计" width="100" align = "center"></el-table-column>
                    </el-table-column>
                </el-table>
                <el-row class="m-t-10">
                    <el-button type="primary" class="p-l-40 p-r-40" @click="editBillContract">确认修改</el-button>
                </el-row>
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

    export default {
        data() {
            return {
                formDetail: {},
                resultData: [],
                activeName: 'first',
                tableContractData: [],
                tableBillData: [],
                isDisabled: true,
                isEdit: false,
                isUnitEdit: false,
                formContract: {
                    type: '',
                    name: '',
                    goods_num: '',
                    product: '',
                    cate: '',
                    brand: '',
                    model: '',
                    unit: '',
                    amount: '',
                    unit_price: '',
                },
                formBill: {
                    date: '2018',
                    quarter: 'Q1',
                    billAll17: '',
                    billAll06: '',
                    billAll0: '',
                    billNoTax17: '',
                    billNoTax06: '',
                    billNoTax0: '',
                    billTax17: '',
                    billTax06: '',
                    billTax0: '',
                },
                contractRules: {
                    goods_num: [
                        { required: true, message: '请填写货号', trigger: 'change' },
                    ],
                    product: [
                        { required: true, message: '请填写品名', trigger: 'change' },
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
                    ]
                },
                defaultRadio: 1,
                hardware: [],
                software: [],
                install: [],
                serve: [],
                other: []
            }
        },
        watch: {

        },
        created() {

        },
        mounted() {
            this.$store.state.number = this.$route.query.number;
            _g.clearVuex('setNumber');
            this.init();
        },
        methods: {
            init() {
                // 请求某个订单的详情
                this.apiPost('/admin/contract/showDetail', {number: this.$route.query.number}).then((res) => {
                    this.handelResponse(res, (data) => {
                        data[0].year = this.format(data[0].year*1000);
                        data[0].begin_time = this.format(data[0].begin_time*1000);
                        data[0].stop_time = this.format(data[0].stop_time*1000);
                        data[0].check_time = this.format(data[0].check_time*1000);
                        data[0].end_time = this.format(data[0].end_time*1000);
                        this.formDetail = data[0];
                        if (this.formDetail.bill && this.formDetail.bill.length) {
                            this.formDetail.bill.forEach(item => {
                                item.check_time = this.format(item.check_time*1000);
                                let reg = /(\d{4})\/(\d{2})\/(\d{2})/;
                                let time1 = item.check_time.replace(reg, '$1年');
                                let time2 = item.check_time.replace(reg, '$2');
                                if (time2 == '01') {
                                    time2 = 'Q1';
                                } else if (time2 == '04') {
                                    time2 = 'Q2';
                                } else if (time2 == '07') {
                                    time2 = 'Q3';
                                } else  {
                                    time2 = 'Q4';
                                }
                                item.check_time = time1 + time2;
                            });
                        } else {
                            this.formDetail.bill = [];
                        }

                        this.hardware = this.formDetail.hardware ? this.formDetail.hardware : [];
                        this.software = this.formDetail.software ? this.formDetail.software : [];
                        this.install = this.formDetail.install ? this.formDetail.install : [];
                        this.serve = this.formDetail.serve ? this.formDetail.serve : [];
                        this.other = this.formDetail.other ? this.formDetail.other : [];
                        let arr = [this.hardware, this.software, this.install, this.serve, this.other];
                        this.tableContractData = arr.reduce((a,b) => {
                            return a.concat(b)
                        });
                        this.tableBillData = this.formDetail.bill || [];

                    })
                })
            },
            openEdit() {
                if (this.isEdit) {
                    this.isDisabled = false

                } else {
                    this.isDisabled = true;
                }
            },
            // 添加合同清单信息
            addContract() {
                this.$refs.formContract.validate((pass) => {
                    if (pass) {
                        switch (this.defaultRadio) {
                            case 1:
                                // this.formContract.type = 1;
                                this.formContract.type = '硬件产品';
                                this.hardware.push(this.formContract);
                                break;
                            case 2:
                                // this.formContract.type = 2;
                                this.formContract.type = '软件产品';
                                this.software.push(this.formContract);
                                break;
                            case 3:
                                // this.formContract.type = 3;
                                this.formContract.type = '安装调试';
                                this.install.push(this.formContract);
                                break;
                            case 4:
                                // this.formContract.type = 4;
                                this.formContract.type = '服务';
                                this.serve.push(this.formContract);
                                break;
                            case 5:
                                // this.formContract.type = 5;
                                this.formContract.type = '其他';
                                this.other.push(this.formContract);
                                break

                        }
                        let arr = [this.hardware, this.software, this.install, this.serve, this.other];
                        this.tableContractData = arr.reduce((a,b) => {
                            return a.concat(b)
                        });
                        this.formContract = {
                            type: '',
                            name: '',
                            goods_num: '',
                            product: '',
                            cate: '',
                            brand: '',
                            model: '',
                            unit: '',
                            amount: '',
                            unit_price: ''
                        }
                    }
                });

            },
            // 添加发票信息
            addBill() {
                this.formBill.billAll0 =  Number(this.formBill.billAll06) +  Number(this.formBill.billAll17);
                this.formBill.billNoTax0 =  Number(this.formBill.billNoTax17) +  Number(this.formBill.billNoTax06);
                this.formBill.billTax0 =  Number(this.formBill.billTax17) +  Number(this.formBill.billTax06);
                let time = new Date(this.formBill.date);
                time = this.format(time, 1);
                this.formBill.timeText = time + '年' + this.formBill.quarter;
                switch (this.formBill.quarter) {
                    case 'Q1':
                        this.formBill.check_time = Date.parse(time + '-01')/1000;
                        break;
                    case 'Q2':
                        this.formBill.check_time = Date.parse(time + '-04')/1000;
                        break;
                    case 'Q3':
                        this.formBill.check_time = Date.parse(time + '-07')/1000;
                        break;
                    case 'Q4':
                        this.formBill.check_time = Date.parse(time + '-10')/1000;
                        break;
                }
                let billData = {};
                billData = Object.assign(billData, this.formBill);
                this.tableBillData.push(billData);
                this.formBill =  {
                    date: '2018',
                    quarter: 'Q1',
                    billAll17: '',
                    billAll06: '',
                    billAll0: '',
                    billNoTax17: '',
                    billNoTax06: '',
                    billNoTax0: '',
                    billTax17: '',
                    billTax06: '',
                    billTax0: ''
                }
            },
            // 修改基本信息
            editContractInfo() {
                let opts = {};
                opts.check_time = Date.parse(this.formDetail.check_time)/1000;
                opts.stop_time = Date.parse(this.formDetail.stop_time)/1000;
                opts.begin_time = Date.parse(this.formDetail.begin_time)/1000;
                opts.end_time = Date.parse(this.formDetail.end_time)/1000;
                opts.hardware = JSON.stringify(this.tableContractData);
                opts.number = this.formDetail.number;
                this.apiPost('/admin/contract/modify', opts).then((res) => {
                    this.handelResponse(res, (data) => {
                        _g.toastMsg('success', '修改成功');
                        this.isEdit = false;
                    }, () => {
                        this.disable = !this.disable
                    })
                })
            },
            // 修改合同清单
            editContractList() {
                let opts = {};
                opts.hardware = this.hardware.length > 0 ? JSON.stringify(this.hardware) : '';
                opts.software = this.software.length > 0 ? JSON.stringify(this.software) : '';
                opts.install = this.install.length > 0 ? JSON.stringify(this.install) : '';
                opts.serve =  this.serve.length > 0 ? JSON.stringify(this.serve) : '';
                opts.other = this.other.length > 0 ? JSON.stringify(this.other): '';
                opts.number = this.formDetail.number;
                this.apiPost('/admin/contract/modify', opts).then((res) => {
                    this.handelResponse(res, (data) => {
                        _g.toastMsg('success', '修改成功');
                    }, () => {
                        this.disable = !this.disable
                    })
                })
            },
            // 修改发票信息
            editBillContract() {
                let opts = {};
                opts.bill = JSON.stringify(this.tableBillData);
                opts.number = this.formDetail.number;
                this.apiPost('/admin/contract/modify', opts).then((res) => {
                    this.handelResponse(res, (data) => {
                        _g.toastMsg('success', '修改成功');
                        this.isEdit = false;
                    }, () => {
                        this.disable = !this.disable
                    })
                })
            },
            // 根据货号或品名查询
            loadAll(type) {
                let opts ={};
                if (type ==1) {
                    opts.goods_num = this.formContract.goods_num;
                } else {
                    opts.product = this.formContract.product
                }
                this.apiPost('admin/contract/findGoodsInfo', opts).then((res) => {
                    this.handelResponse(res, (data) => {
                        if (data && data.length > 0) {
                            if (type == 1) {
                                this.formContract.product = data[0].product;
                            } else {
                                this.formContract.goods_num = data[0].goods_num;
                            }

                            this.formContract.cate = data[0].cate;
                            this.formContract.brand = data[0].brand;
                            this.formContract.model = data[0].model;
                            this.formContract.unit = data[0].unit;
                            this.formContract.unit_price = data[0].unit_price;
                        }

                    })
                });
            },
            changeAll17() {
                this.formBill.billNoTax17 =  (this.formBill.billAll17/1.17).toFixed(2);
                this.formBill.billTax17 =  (this.formBill.billAll17 - this.formBill.billNoTax17).toFixed(2);
                this.formBill.billAll0 = this.formBill.billAll17 + this.formBill.billAll06;
                this.formBill.billNoTax0 = this.formBill.billNoTax17 + this.formBill.billNoTax06;
                this.formBill.billTax0 = this.formBill.billTax17 + this.formBill.billTax06;

            },
            changeAll06(val) {
                this.formBill.billNoTax06 = (this.formBill.billAll06/1.06).toFixed(2);
                this.formBill.billTax06 = (this.formBill.billAll06 - this.formBill.billNoTax06).toFixed(2);
                this.formBill.billAll0 = Number(this.formBill.billAll06) + Number(this.formBill.billAll17);
                this.formBill.billNoTax0 = Number(this.formBill.billNoTax17) + Number(this.formBill.billNoTax06);
                this.formBill.billTax0 = Number(this.formBill.billTax17) + Number(this.formBill.billTax06);
            },
            changeNoTax17() {
                this.formBill.billAll17 = (this.formBill.billNoTax17*1.17).toFixed(2);
                this.formBill.billTax17 = (this.formBill.billAll17 - billNoTax17).toFixed(2);
                this.formBill.billAll0 = Number(this.formBill.billAll17) + Number(this.formBill.billAll06);
                this.formBill.billNoTax0 = Number(this.formBill.billNoTax17) + Number(this.formBill.billNoTax06);
                this.formBill.billTax0 = Number(this.formBill.billTax17) + Number(this.formBill.billTax06);

            },
            changeNoTax06(val) {
                this.formBill.billAll06 = (this.formBill.billNoTax06*1.06).toFixed(2);
                this.formBill.billTax06 = (this.formBill.billAll06 - this.formBill.billNoTax06).toFixed(2);
                this.formBill.billAll0 = Number(this.formBill.billAll06) + Number(this.formBill.billAll17);
                this.formBill.billNoTax0 = Number(this.formBill.billNoTax17) + Number(this.formBill.billNoTax06);
                this.formBill.billTax0 = Number(this.formBill.billTax17) + Number(this.formBill.billTax06);
            },
            getSummaries(param) {
                const { columns, data } = param;
                const sums = [];
                columns.forEach((column, index) => {
                    if (index === 0) {
                        sums[index] = '总计';
                        return;
                    }
                    const values = data.map(item => Number(item[column.property]));
                    if (!values.every(value => isNaN(value))) {
                        sums[index] = values.reduce((prev, curr) => {
                            const value = Number(curr);
                            if (!isNaN(value)) {
                                return prev + curr;
                            } else {
                                return prev;
                            }
                        }, 0);
                    } else {
                        sums[index] = '暂无';
                    }
                });

                return sums;
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