<template>
    <div class="m-l-20 w-1100 detail">
        <el-row v-if="username == 'admin' && this.formDetail.status ==4">
            <el-button @click="cancelAudit" :disabled="isAudit"><i class="el-icon-edit el-icon--left"></i>取消审核</el-button>
        </el-row>
        <el-tabs v-model="activeName" class="w-1000">
            <el-tab-pane label="基本详情" name="first">
                <el-form  :model="formDetail" :inline="true" class="demo-form-inline dynamic-form" label-width="100px">
                <el-form-item label="编辑" style="display: block">
                    <el-switch v-model="isEdit" :disabled="this.formDetail.status ==4 && username !== 'admin'" @change="openEdit"></el-switch>
                </el-form-item>
                <el-form-item label="合同编号:">
                        <el-input v-model.trim="formDetail.number" class="h-40 fl w-300" :disabled="true"></el-input>
                </el-form-item>
                <el-form-item label="查找编号:">
                    <el-input v-model.trim="formDetail.find_num" class="h-40 fl w-300" :disabled="isZanCun"></el-input>
                </el-form-item>
                <el-form-item label="所属年度:">
                    <el-col class="h-40 fl w-300">
                        <el-date-picker type="year" v-model="formDetail.year" style="width: 100%;" :disabled="isZanCun"></el-date-picker>
                    </el-col>
                </el-form-item>

                <el-form-item label="合同总价:">
                        <el-input v-model.trim="formDetail.total_price" class="h-40 fl w-300" :disabled="isZanCun"></el-input>
                    </el-form-item>
                <el-form-item label="合同税率:">
                    <el-input v-model.trim="formDetail.tax_rate" class="h-40 fl w-300" :disabled="isDisabled"></el-input>
                </el-form-item>
                <el-form-item label="合同类别:">
                    <el-input v-model.trim="formDetail.category" class="h-40 fl w-300" :disabled="isZanCun"></el-input>
                </el-form-item>
                <el-form-item label="合同甲方:">
                    <el-input v-model.trim="formDetail.party_a" class="h-40 w-300" :disabled="isZanCun"></el-input>
                </el-form-item>
                <el-form-item label="第三方:">
                    <el-input v-model.trim="formDetail.thirdparty" class="h-40 w-300" :disabled="isZanCun"></el-input>
                </el-form-item>
                <el-form-item label="酒店集团:">
                    <el-input v-model.trim="formDetail.group" class="h-40 w-300" :disabled="isDisabled"></el-input>
                </el-form-item>
                <el-form-item label="酒店名称:">
                    <el-input v-model="formDetail.hotel" class="h-40 w-300" :disabled="isDisabled"></el-input>
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
                        <el-date-picker type="date" placeholder="选择日期" @change="changeDate" v-model="formDetail.end_time" style="width: 100%;" :disabled="isDisabled"></el-date-picker>
                    </el-col>
                </el-form-item>
                <el-form-item style="display: block">
                    <el-button class="p-l-30 p-r-30" type="primary" @click="editContractInfo" :loading="isLoading">提交</el-button>
                </el-form-item>
            </el-form>
            </el-tab-pane>
            <el-tab-pane label="合同清单" name="second">
                <el-form ref="formContract" :model="formContract" :rules="contractRules" label-width="100px">
                    <el-form-item label="收入分类:">
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
                        <el-input class="h-40 fl w-300" v-model.trim="formContract.product" :disabled="true"></el-input>
                        <!--<el-button @click.prevent="loadAll('2')">查找</el-button>-->
                    </el-form-item>
                    <el-form-item label="产品线:" prop="cate">
                        <el-input v-model.trim="formContract.cate" class="h-40 fl w-300" :disabled="false"></el-input>
                    </el-form-item>
                    <el-form-item label="类别:" prop="brand">
                        <el-input v-model.trim="formContract.brand" class="h-40 w-300" :disabled="true"></el-input>
                    </el-form-item>
                    <el-form-item label="规格型号:" prop="model">
                        <el-input v-model.trim="formContract.model" class="h-40 w-300" :disabled="true"></el-input>
                    </el-form-item>
                    <el-form-item label="单位:" prop="unit">
                        <el-input v-model.trim="formContract.unit" class="h-40 w-300" :disabled="true"></el-input>
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
                <el-row style="margin-bottom: 10px; text-align: right; display: none">
                    <el-button><i class="el-icon-download el-icon--left"></i>下载</el-button>
                </el-row>
                <el-table :data="tableContractData" border style="width: 100%;">
                    <el-table-column prop="type" label="收入分类" width="120"></el-table-column>
                    <el-table-column label="产品线">
                        <template scope="scope">
                            <el-input size="small" v-model="scope.row.cate"></el-input>
                        </template>
                    </el-table-column>
                    <el-table-column prop="goods_num" label="货号"></el-table-column>
                    <el-table-column prop="product" label="品名"></el-table-column>
                    <el-table-column prop="brand" label="类别"></el-table-column>
                    <el-table-column prop="model" label="规格型号"></el-table-column>
                    <el-table-column prop="unit" label="单位"></el-table-column>
                    <el-table-column prop="amount" label="数量"></el-table-column>
                    <el-table-column prop="unit_price" label="单价" min-width="160">
                        <template scope="scope">
                            <el-input size="small"  v-model="scope.row.unit_price" :min="0" type="number" ></el-input>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    type="danger"
                                    @click="handleContractDelete(scope.$index, scope.row)">删除</el-button>
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
                    <el-table-column label="操作">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    type="danger"
                                    @click="handleBillDelete(scope.$index, scope.row)">删除</el-button>
                        </template>
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
                isZanCun: true,
                isUnitEdit: false,
                formContract: {
                    type: '',
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
                other: [],
                username: '',
                isAudit: false,
                isChange: false
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
                // 请求某个订单的详情
                this.apiPost('/admin/contract/showDetail', {number: this.$store.state.number}).then((res) => {
                    this.handelResponse(res, (data) => {
                        data[0].year = data[0].year ? this.format(data[0].year*1000): '';
                        data[0].begin_time = data[0].begin_time ? this.format(data[0].begin_time*1000) : '';
                        data[0].stop_time = data[0].stop_time? this.format(data[0].stop_time*1000): '';
                        data[0].check_time = data[0].check_time ? this.format(data[0].check_time*1000): '';
                        data[0].end_time = data[0].end_time ? this.format(data[0].end_time*1000): '';
                        data[0].tax_rate = data[0].tax_rate*100 + '%';
                        this.formDetail = data[0];
                        if (this.formDetail.bill && this.formDetail.bill.length) {
                            this.formDetail.bill.forEach(item => {
                                item.check_time = this.format(item.check_time*1000);
                                let reg = /(\d{4})\-(\d{2})\-(\d{2})/;
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
                if (+this.formDetail.status === 1) {
                    if (this.isEdit) {
                        this.isZanCun = false
                    } else {
                        this.isZanCun = true
                    }
                }
            },
            // 反审核
            cancelAudit() {
                if (!this.isAudit) {
                    this.$confirm('确认取消审核吗?', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消'
                    }).then(() => {
                        _g.openGlobalLoading();
                        let data = {
                            number: this.$route.query.number,
                            username: this.username
                        };
                        this.apiPost('admin/contract/cancelContract', data).then((res) => {
                            this.handelResponse(res, (data) => {
                                _g.closeGlobalLoading();
                                this.isAudit = true
                            }, () => {

                            })

                        })
                    }).catch(() => {

                    })
                }
            },
            // 添加合同清单信息
            addContract() {
                this.$refs.formContract.validate((pass) => {
                    let opt= {};
                    if (pass) {
                        switch (this.defaultRadio) {
                            case 1:
                                // this.formContract.type = 1;
                                this.formContract.type = '硬件产品';
                                opt = Object.assign(opt, this.formContract);
                                this.hardware.push(opt);
                                break;
                            case 2:
                                // this.formContract.type = 2;
                                this.formContract.type = '软件产品';
                                opt = Object.assign(opt, this.formContract);
                                this.software.push(opt);
                                break;
                            case 3:
                                // this.formContract.type = 3;
                                this.formContract.type = '安装调试';
                                opt = Object.assign(opt, this.formContract);
                                this.install.push(opt);
                                break;
                            case 4:
                                // this.formContract.type = 4;
                                this.formContract.type = '服务';
                                opt = Object.assign(opt, this.formContract);
                                this.serve.push(opt);
                                break;
                            case 5:
                                // this.formContract.type = 5;
                                this.formContract.type = '其他';
                                opt = Object.assign(opt, this.formContract);
                                this.other.push(opt);
                                break

                        }
                        let arr = [this.hardware, this.software, this.install, this.serve, this.other];
                        this.tableContractData = arr.reduce((a,b) => {
                            return a.concat(b)
                        });
                        this.formContract = {
                                type: '',
                                goods_num: '',
                                product: '',
                                cate: '',
                                brand: '',
                                model: '',
                                unit: '',
                                amount: '',
                                unit_price: '',
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
                this.formBill.check_time = time + '年' + this.formBill.quarter;
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
                if (+this.formDetail.status === 4 && this.username !== 'admin') {
                    _g.toastMsg('error', '暂无修改权限');
                }
                let opts = {};
                if (+this.formDetail.status === 1) {
                    opts.total_price = this.formDetail.total_price;
                    opts.category = this.formDetail.category;
                    opts.party_a = this.formDetail.party_a;
                    opts.thirdparty = this.formDetail.thirdparty;
                    opts.change_status = this.formDetail.status;
                    opts.year = Date.parse(this.formDetail.year)/1000;
                    opts.find_num = this.formDetail.find_num;
                }
                opts.check_time = Date.parse(this.formDetail.check_time)/1000;
                opts.stop_time = Date.parse(this.formDetail.stop_time)/1000;
                opts.begin_time = Date.parse(this.formDetail.begin_time)/1000;
                opts.end_time = Date.parse(this.formDetail.end_time)/1000;
                if (this.isChange && opts.end_time > 0) {
                    opts.end_time = opts.end_time + 24 * 3600 -1
                }
                if (opts.end_time < opts.begin_time) {
                    _g.toastMsg('error', '请输入正确服务时间');
                    return
                }
                let is_serve_right = 0;
                this.tableContractData.forEach((item, index) => {
                    if (item.type === '服务') {
                        is_serve_right = 1;
                        return
                    }
                });
                if (opts.begin_time > 0 && is_serve_right !== 1) {
                    _g.toastMsg('error', '请先去修改服务');
                    return
                }
                opts.hotel = this.formDetail.hotel;
                opts.group = this.formDetail.group;
                opts.tax_rate = parseInt(this.formDetail.tax_rate)/100;
                opts.number = this.formDetail.number;
                this.apiPost('/admin/contract/modify', opts).then((res) => {
                    this.handelResponse(res, (data) => {
                        _g.toastMsg('success', '修改成功');
                        this.isEdit = false;
                        this.isZanCun = true;
                        this.isDisabled = true;
                    }, () => {
                        this.disable = !this.disable
                    })
                })
            },
            // 修改合同清单
            editContractList() {
                if (this.formDetail.status == 4) {
                    _g.toastMsg('error', '暂无修改权限');
                    return
                }
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
                let arr = [];
                let opts = {};
                this.tableBillData.forEach((item) => {
                    let cur = {};
                    cur.billAll17 = item.billAll17;
                    cur.billAll06 = item.billAll06;
                    cur.billAll0 = item.billAll0;
                    cur.billNoTax17 = item.billNoTax17;
                    cur.billNoTax06 = item.billNoTax06;
                    cur.billNoTax0 = item.billNoTax0;
                    cur.billTax17 = item.billTax17;
                    cur.billTax06 = item.billTax06;
                    cur.billTax0 = item.billTax0;
                    cur.check_time = item.check_time;
                    cur.date = item.date;
                    cur.quarter = item.quarter;
                    let time = new Date(item.date);
                    time = this.format(time, 1);
                    switch (cur.quarter) {
                        case 'Q1':
                            cur.check_time = Date.parse(time + '-01')/1000;
                            break;
                        case 'Q2':
                            cur.check_time = Date.parse(time + '-04')/1000;
                            break;
                        case 'Q3':
                            cur.check_time = Date.parse(time + '-07')/1000;
                            break;
                        case 'Q4':
                            cur.check_time = Date.parse(time + '-10')/1000;
                            break;
                    }
                    arr.push(cur)
                });
                opts.bill = JSON.stringify(arr);
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
            // 输入价税合计17%
            changeAll17() {
                this.formBill.billNoTax17 =  (this.formBill.billAll17/1.17).toFixed(2);
                this.formBill.billTax17 =  (this.formBill.billAll17 - this.formBill.billNoTax17).toFixed(2);
                this.formBill.billAll0 = this.formBill.billAll17 + this.formBill.billAll06;
                this.formBill.billNoTax0 = this.formBill.billNoTax17 + this.formBill.billNoTax06;
                this.formBill.billTax0 = this.formBill.billTax17 + this.formBill.billTax06;

            },
            // 输入价税合计6%
            changeAll06(val) {
                this.formBill.billNoTax06 = (this.formBill.billAll06/1.06).toFixed(2);
                this.formBill.billTax06 = (this.formBill.billAll06 - this.formBill.billNoTax06).toFixed(2);
                this.formBill.billAll0 = Number(this.formBill.billAll06) + Number(this.formBill.billAll17);
                this.formBill.billNoTax0 = Number(this.formBill.billNoTax17) + Number(this.formBill.billNoTax06);
                this.formBill.billTax0 = Number(this.formBill.billTax17) + Number(this.formBill.billTax06);
            },
            // 输入不含税额17%
            changeNoTax17(val) {
                this.formBill.billTax17 = (Number(this.formBill.billAll17) - val).toFixed(2);
                this.formBill.billAll0 = Number(this.formBill.billAll06) + Number(this.formBill.billAll17);
                this.formBill.billNoTax0 = Number(this.formBill.billNoTax17) + Number(this.formBill.billNoTax06);
                this.formBill.billTax0 = Number(this.formBill.billTax17) + Number(this.formBill.billTax06);
            },
            // 输入不含税额6%
            changeNoTax06(val) {
                this.formBill.billTax06 = (Number(this.formBill.billAll06) - val).toFixed(2);
                this.formBill.billAll0 = Number(this.formBill.billAll06) + Number(this.formBill.billAll17);
                this.formBill.billNoTax0 = Number(this.formBill.billNoTax17) + Number(this.formBill.billNoTax06);
                this.formBill.billTax0 = Number(this.formBill.billTax17) + Number(this.formBill.billTax06);
            },
            handleContractDelete(index, row) {
                if (this.tableContractData.length === 1) {
                    _g.toastMsg('error', '这是最后一条合同信息');
                    return
                }
                this.tableContractData.splice(index, 1);
                this.hardware = [];
                this.software = [];
                this.install= [];
                this.serve = [];
                this.other = [];
                this.tableContractData.forEach(item => {
                    if (item.type === '硬件收入') {
                        this.hardware.push(item);
                    } else if (item.type === '软件收入') {
                        this.software.push(item)
                    } else if (item.type === '安装调试') {
                        this.install.push(item)
                    } else if (item.type === '服务') {
                        this.serve.push(item)
                    } else if (item.type === '其他') {
                        this.other.push(item)
                    }
                })
            },
            handleBillDelete(index, row) {
                this.tableBillData.splice(index, 1)
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
                            const value = Number(curr).toFixed(2);
                            if (!isNaN(value)) {
                                return prev + curr;
                            } else {
                                return prev;
                            }
                        }, 0);
                        if (sums[index]) {
                            sums[index] = Number(sums[index]).toFixed(2);
                        }
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
                    return y + '-' + this.add0(m);
                } else {
                    return y + '-' + this.add0(m) + '-' + this.add0(d);
                }

            },
            changeDate() {
                this.isChange = true
            }
        },
        mixins: [http]
    }
</script>