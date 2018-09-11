<template>
    <div class="m-l-30 w-1100 edit">
        <el-tabs v-model="activeName" class="w-1000">
            <el-tab-pane label="基本录入" name="first">
                <el-form ref="form" :model="form" :rules="rules" label-width="110px">
                    <el-form-item label="合同所属年度:" prop="year">
                        <el-col class="h-40 fl w-300" >
                            <el-date-picker type="year" placeholder="选择日期" v-model="form.year" style="width: 100%;"></el-date-picker>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="合同编号:" prop="number">
                        <el-input v-model.trim="form.number" class="h-40 fl w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="查找编号:" prop="find_num">
                        <el-input v-model.trim="form.find_num" class="h-40 fl w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="合同类别:" prop="category">
                        <el-autocomplete
                                class="inline-input w-300"
                                v-model="form.category"
                                :fetch-suggestions="querySearchCate"
                                placeholder="请输入合同类别"
                                @select="handleSelect"
                        ></el-autocomplete>
                    </el-form-item>
                    <el-form-item label="合同甲方:" prop="party_a">
                        <el-input v-model.trim="form.party_a" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="第三方:">
                        <el-input v-model.trim="form.thirdparty" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="酒店集团:" prop="group">
                        <el-input v-model.trim="form.group" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="酒店名字:" prop="hotel">
                        <el-input v-model.trim="form.hotel" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="合同总价:" prop="total_price">
                        <el-input v-model.trim="form.total_price" type="number" class="h-40 w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="合同税率:" prop="tax_rate">
                        <el-input v-model.trim="form.tax_rate" class="h-40 w-80"></el-input>
                        <span>%</span>
                    </el-form-item>
                    <el-form-item label="项目验收时间:">
                        <el-col class="h-40 fl w-300">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.check_time" style="width: 100%;"></el-date-picker>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="服务开始时间:">
                        <el-col class="h-40 fl w-300">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.begin_time" style="width: 100%;"></el-date-picker>
                        </el-col>
                    </el-form-item>
                    <el-form-item label="服务结束时间:">
                        <el-col class="h-40 fl w-300">
                            <el-date-picker type="date" placeholder="选择日期" v-model="form.end_time" style="width: 100%;"></el-date-picker>
                        </el-col>
                    </el-form-item>
                    <!--<el-form-item label="合同终止时间:">-->
                        <!--<el-col class="h-40 fl w-300">-->
                            <!--<el-date-picker type="date" placeholder="选择日期" v-model="form.stop_time" style="width: 100%;"></el-date-picker>-->
                        <!--</el-col>-->
                    <!--</el-form-item>-->
                    <el-form-item label="合同清单:">
                        <el-button type="success" @click="activeName = 'second'" plain v-if="isContract">{{ contractText }}</el-button>
                        <el-button type="danger" @click="activeName = 'second'" plain v-else>{{ contractText }}</el-button>
                    </el-form-item>
                    <el-form-item label="发票信息:">
                        <el-button type="success" @click="activeName = 'third'" plain v-if="isBill">{{ invoiceInfoText }}</el-button>
                        <el-button type="danger" @click="activeName = 'third'" plain v-else>{{ invoiceInfoText }}</el-button>
                    </el-form-item>
                    <el-form-item>
                        <el-button class="p-l-40 p-r-40" type="primary" @click="commitContract()" :loading="isLoading">提交</el-button>
                        <el-button class="p-l-40 p-r-40" type="primary" plain @click="commitContract('1')" :loading="isLoading">暂存</el-button>
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
                        <el-input class="h-40 fl w-300" v-model.trim="formContract.product"></el-input>
                    </el-form-item>
                    <el-form-item label="产品线:" prop="cate">
                        <el-input v-model.trim="formContract.cate" class="h-40 fl w-300"></el-input>
                    </el-form-item>
                    <el-form-item label="类别:" prop="brand">
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
                    </el-form-item>
                </el-form>
                <el-table :data="tableData" border style="width: 100%; margin-top: 20px">
                    <el-table-column prop="name" label="收入分类" width="120"></el-table-column>
                    <el-table-column prop="cate" label="产品线"></el-table-column>
                    <el-table-column prop="goods_num" label="货号"></el-table-column>
                    <el-table-column prop="product" label="品名"></el-table-column>
                    <el-table-column prop="brand" label="类别"></el-table-column>
                    <el-table-column prop="model" label="规格型号"></el-table-column>
                    <el-table-column prop="unit" label="单位"></el-table-column>
                    <el-table-column prop="amount" label="数量"></el-table-column>
                    <el-table-column prop="unit_price" label="单价"></el-table-column>
                    <el-table-column label="操作">
                        <template slot-scope="scope">
                            <el-button
                                    size="mini"
                                    type="danger"
                                    @click="handleContractDelete(scope.$index, scope.row)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <el-row>
                    <el-button class="m-t-20 p-l-40 p-r-40" type="primary" @click="keepContract()">提交</el-button>
                </el-row>
            </el-tab-pane>

            <el-tab-pane label="发票信息" name="third">
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
                    <el-table-column prop="timeText" label="时间段" width="100" align = "center"></el-table-column>
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
                <el-row>
                    <el-button class="m-t-20 p-l-40 p-r-40" type="primary" @click="keepBill()">提交</el-button>
                </el-row>
            </el-tab-pane>

        </el-tabs>
    </div>
</template>
<style type="text/css">
    .edit th {
        padding: 6px 0 !important;
    }
    .date-ml {
        margin-left: 40px;
        width: 105px;
    }
    .selet-quarter {
        width: 105px;
    }
</style>
<script>
    import http from '../assets/js/http'
    //定义一个全局的变量，谁用谁知道
    let validTax=(rule, value,callback)=>{
        if (value > 0 && value < 100){
            callback()
        }else {
            callback(new Error('请输入正确的税率'))
        }
    }
    export default {
        data() {
            return {
                defaultRadio: 1,
                isLoading: false,
                isContract: false,
                isBill: false,
                form: {
                    year: '',
                    find_num: '',
                    number: '',
                    category: '',
                    party_a: '',
                    thirdparty: '',
                    group: '',
                    hotel: '',
                    total_price: '',
                    check_time: '',
                    begin_time: '',
                    end_time: '',
                    stop_time: '',
                    hardware: [],
                    software: [],
                    install: [],
                    serve: [],
                    other: [],
                    bill: [],
                    tax_rate: ''
                },
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
                rules: {
                    year: [
                        { type: 'date', required: true, message: '请选择日期', trigger: 'change' }
                    ],
                    number: [
                        { required: true, message: '请填写合同编号', trigger: 'blur' },
                        { min: 3, message: '长度至少三位以上', trigger: 'blur' }
                    ],
                    party_a: [
                        { required: true, message: '请填写合同甲方', trigger: 'blur' },
                    ],
                    group: [
                        { required: true, message: '请填写酒店集团', trigger: 'blur' },
                    ],
                    hotel: [
                        { required: true, message: '请填写酒店名字', trigger: 'blur' },
                    ],
                    category: [
                        { required: true, message: '请选择合同类型', trigger: 'change' },
                    ],
                    total_price: [
                        { required: true, message: '请填写合同总价', trigger: 'blur' },
                    ],
                    tax_rate: [
                        { required: true, type: "number", message: '请输入正确的税率', trigger: 'blur', validator: validTax },
                    ],

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
                contractText: '录入合同清单',
                invoiceInfoText: '录入发票信息',
                dialogContractList: false,
                dialogInvoiceInfo: false,
                activeName: 'first',
                activeNames: '1',
                tableData: [],
                tableBillData: [],
                spanArr: [],
                timeout:  null,
                restaurants: [],

            }
        },
        watch: {
            tableBillData(val) {
                if (val.length > 0) {
                    this.isBill = true
                }
            }
        },
        created() {

        },
        mounted() {
            this.restaurants = this.loadCategory();
        },
        methods: {
            handleContractDelete(index, row) {
                this.tableData.splice(index, 1)
            },
            handleBillDelete(index, row) {
                this.tableBillData.splice(index, 1)
            },
            // 添加基本信息--提交或暂存
            commitContract(data) {
                this.$refs.form.validate((pass) => {
                    if (pass) {
                        if (!this.isContract) {
                            _g.toastMsg('error', '请输入合同清单信息');
                            return
                        }

                        this.form.year = Date.parse(this.form.year)/1000;
                        this.form.check_time = Date.parse(this.form.check_time)/1000;
                        this.form.begin_time = Date.parse(this.form.begin_time)/1000;
                        this.form.end_time = Date.parse(this.form.end_time)/1000;
                        this.form.stop_time = Date.parse(this.form.stop_time)/1000;
                        if (this.form.end_time < this.form.begin_time) {
                            _g.toastMsg('error', '请输入正确服务时间');
                            return
                        }
                        this.isLoading = !this.isLoading;
                        this.tableData.forEach(item => {
                            switch (item.type) {
                                case 1:
                                    this.form.hardware.push(item);
                                    break;
                                case 2:
                                    this.form.software.push(item);
                                    break;
                                case 3:
                                    this.form.install.push(item);
                                    break;
                                case 4:
                                    this.form.serve.push(item);
                                    break;
                                case 5:
                                    this.form.other.push(item);
                                    break

                            }
                        });
                        this.form.hardware = this.form.hardware.length > 0 ? JSON.stringify(this.form.hardware) : '';
                        this.form.software = this.form.software.length > 0 ? JSON.stringify(this.form.software) : '';
                        this.form.install = this.form.install.length > 0 ? JSON.stringify(this.form.install) : '';
                        this.form.serve =  this.form.serve.length > 0 ? JSON.stringify(this.form.serve) : '';
                        this.form.other = this.form.other.length > 0 ? JSON.stringify(this.form.other): '';
                        this.form.bill=   this.tableBillData.length > 0 ? JSON.stringify(this.tableBillData): '';
                        let contract =  Object.assign(this.form, {is_type: data});
                        contract.tax_rate =  contract.tax_rate/100;
                        this.apiPost('admin/contract/add', contract).then((res) => {
                            this.handelResponse(res, (data) => {
                                this.isLoading = !this.isLoading;
                                _g.toastMsg('success', '添加成功');
                                this.form = {
                                        year: '',
                                        find_num: '',
                                        number: null,
                                        category: '',
                                        city: '',
                                        party_a: '',
                                        thirdparty: '',
                                        group: '',
                                        hotel: '',
                                        total_price: '',
                                        check_time: '',
                                        begin_time: '',
                                        end_time: '',
                                        stop_time: '',
                                        hardware: [],
                                        software: [],
                                        install: [],
                                        serve: [],
                                        other: [],
                                        bill: [],
                                        tax_rate: ''
                                };
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
                                        unit_price: '',
                                };
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
                                        billTax0: '',
                                };
                                this.tableData =  [];
                                this.tableBillData = [];
                                this.isContract = false;

                            }, () => {
                                this.isLoading = !this.isLoading;
                                this.form.hardware = [];
                                this.form.software = [];
                                this.form.install = [];
                                this.form.serve =  [];
                                this.form.other = [];
                                this.form.tax_rate = this.form.tax_rate > 1 ? this.form.tax_rate : this.form.tax_rate*100
                                // this.form.year = this.form.year > 0 ? this.format(this.form.year*1000): '';
                                // this.form.check_time = this.form.check_time > 0 ? this.format(this.form.check_time*1000): '';
                                // this.form.begin_time = this.form.begin_time > 0 ? this.format(this.form.begin_time*1000): '';
                                // this.form.end_time = this.form.end_time >0 ? this.format(this.form.end_time*1000): '';
                                // this.form.stop_time = this.form.stop_time > 0 ? this.format(this.form.stop_time*1000): '';
                                this.form.year = '';
                                this.form.check_time = '';
                                this.form.begin_time =  '';
                                this.form.end_time = '';
                                this.form.stop_time = '';
                            })
                        })
                    }
                })
            },
            addContract() {
                this.$refs.formContract.validate((pass) => {
                    if (pass) {
                        switch (this.defaultRadio) {
                            case 1:
                                this.formContract.type = 1;
                                this.formContract.name = '硬件产品';
                                break;
                            case 2:
                                this.formContract.type = 2;
                                this.formContract.name = '软件产品';
                                break;
                            case 3:
                                this.formContract.type = 3;
                                this.formContract.name = '安装调试';
                                break;
                            case 4:
                                this.formContract.type = 4;
                                this.formContract.name = '服务';
                                break;
                            case 5:
                                this.formContract.type = 5;
                                this.formContract.name = '其他';
                                break

                        }
                        let contractData = {};
                        contractData = Object.assign(contractData, this.formContract);
                        this.tableData.push(contractData);
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
            changeAll06() {
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
                    } else {
                        sums[index] = '暂无';
                    }
                });

                return sums;
            },
            keepContract() {
                if (this.tableData.length) {
                    this.isContract = true;
                    this.activeName = 'first'
                }

            },
            keepBill() {
                if (this.tableBillData.length) {
                    this.activeName = 'first'
                }
            },
            querySearchCate(queryString, cb) {
                let restaurants = this.restaurants;
                let results = queryString ? restaurants.filter(this.createFilter(queryString)) : restaurants;
                // 调用 callback 返回建议列表的数据
                cb(results);
            },
            createFilter(queryString) {
                return (restaurant) => {
                    return (restaurant.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
                };
            },
            handleSelect(item) {
                item.value = this.form.category
            },
            loadCategory() {
                return [
                    {"value": "HSIA五星服务"},{"value": "HSIA白金服务"},{"value": "HSIA服务增补"},
                    {"value": "HSIA短信包"},{"value": "HSIA按次计费"},{"value": "HSIA驻店服务"},
                    {"value": "HSIA购销"},{"value": "HSIA购销增补"},{"value": "HSIA运营"},
                    {"value": "WLAN购销"},{"value": "WLAN服务"},
                    {"value": "WLAN增补"},{"value": "WLAN增值服务"},{"value": "网络加速服务"},
                    {"value": "MESS"},{"value": "O2O"},{"value": "Wi-Fi"},
                    {"value": "IDS购销"},{"value": "IDS服务"},{"value": "IDS购销增补"},
                    {"value": "IDS服务增补"},{"value": "IDS维修服务"},
                    {"value": "ISTV购销"},{"value": "ISTV改造"},{"value": "ISTV服务"},{"value": "ISTV购销增补"},
                    {"value": "ISTV服务增补"},{"value": "ISTV维修/护服务"},{"value": "ISTV（点播）服务"},{"value": "ISTV点播分成"},
                    {"value": "RCU购销"},{"value": "RCU增补"},{"value": "RCU服务"},
                    {"value": "RCU升级"},{"value": "RCU其他"}
                ]
            },
            rowLink() {
                router.push({
                    path: '/home/cate'
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
                    return y + '-' + this.add0(m);
                } else {
                    return y + '-' + this.add0(m) + '-' + this.add0(d);
                }

            }
        },
        mixins: [http]
    }
</script>