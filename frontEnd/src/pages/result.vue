<template>
    <div class="m-l-20 w-1100 result">
        <el-form :model="formTab" :inline="true" class="demo-form-inline">
            <el-form-item style="width: 100%" label="时间选择:">
                <el-col :span="8">
                    <el-date-picker type="month" placeholder="选择开始日期" v-model="from_time" style="width: 100%;"></el-date-picker>
                </el-col>
                <el-col class="line" :span="1" align="center">-</el-col>
                <el-col :span="8">
                    <el-date-picker type="month" placeholder="选择结束日期" v-model="to_time" style="width: 100%;"></el-date-picker>
                </el-col>
                <el-col class="line" :span="7" style="text-indent: 20px; color: #ccc">请以季度的范围筛选</el-col>
            </el-form-item>
            <el-form-item label="合同状态:">
                <el-select v-model="formTab.status" placeholder="活动区域" class="h-40 w-200">
                    <el-option label="全部" value="0"></el-option>
                    <el-option label="暂存" value="1"></el-option>
                    <el-option label="未验收" value="2"></el-option>
                    <el-option label="未审核" value="3"></el-option>
                    <el-option label="已审核" value="4"></el-option>
                    <el-option label="已终止" value="5"></el-option>
                    <el-option label="调整项" value="6"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="合同编号:" >
                <el-input v-model.trim="formTab.number" class="h-40 fl w-200"></el-input>
            </el-form-item>
            <el-form-item label="产品线:">
                <el-select v-model.trim="formTab.product" placeholder="请选择产品线" class="h-40 fl w-200">
                    <el-option label="IDS" value="IDS"></el-option>
                    <el-option label="ISTV" value="ISTV"></el-option>
                    <el-option label="RCU" value="RCU"></el-option>
                    <el-option label="HSIA" value="HSIA"></el-option>
                    <el-option label="WLAN" value="WLAN"></el-option>
                    <el-option label="O2O" value="O2O"></el-option>
                    <el-option label="运营" value="运营"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item style="display: block;">
                <el-button type="primary" @click="onSubmit">查询</el-button>
            </el-form-item>
        </el-form>
        <el-row style="text-align: right; margin-bottom: 10px">
            <el-button><i class="el-icon-download el-icon--left" @click="downloadResult"></i>下载</el-button>
        </el-row>
        <el-table
                :data="resultData"
                ref="multipleTable"
                :summary-method="getSummaries"
                show-summary
                border
                @selection-change="handleSelectionChange"
                style="width: 100%">
            <el-table-column
                    align="center"
                    type="selection"
                    :selectable='checkboxInit'
                    width="55">
            </el-table-column>
            <el-table-column prop="number" label="合同编号" height="60" show-overflow-tooltip align="center">
                <template slot-scope="scope">
                    <span @click="rowTitLink(scope.row)" style="display: block;color: #00f; cursor: pointer">{{ scope.row.number }}</span>
                </template>
            </el-table-column>
            <el-table-column prop="year" label="合同年度" height="60" show-overflow-tooltip align="center"></el-table-column>
            <el-table-column prop="category" label="合同类别" height="60" show-overflow-tooltip align="center"></el-table-column>
            <el-table-column prop="party_a" label="合同甲方" height="60" show-overflow-tooltip align="center"></el-table-column>
            <el-table-column prop="thirdparty" label="第三方" height="60" show-overflow-tooltip align="center"></el-table-column>
            <!--<el-table-column prop="group" label="酒店集团" height="60" show-overflow-tooltip align="center"></el-table-column>-->
            <el-table-column prop="hotel" label="酒店名字" height="60" show-overflow-tooltip align="center"></el-table-column>
            <el-table-column label="分摊价" height="60" align= "center">
                <el-table-column prop="total_price" label="合同总价" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right; color: #047ce2" :class="{errText: scope.row.total_price > scope.row.service_price}">{{ scope.row.total_price | NumFormat}}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="all_tax_price" label="税金" min-width="100" height="30" align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right; color: #047ce2;">{{ scope.row.all_tax_price | NumFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="all_hardware_price" label="硬件" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right; color: #047ce2;">{{ scope.row.all_hardware_price | NumFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="all_software_price" label="软件" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right; color: #047ce2">{{ scope.row.all_software_price | NumFormat}}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="all_install_price" label="安装" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right;color: #047ce2">{{ scope.row.all_install_price | NumFormat}}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="all_serve_price" label="服务" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right;color: #047ce2">{{ scope.row.all_serve_price | NumFormat}}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="all_other_price" label="其他" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right; color: #047ce2">{{ scope.row.all_other_price | NumFormat}}</span>
                    </template>
                </el-table-column>
            </el-table-column>
            <el-table-column label="收入确认" height="60" align="center">
                <el-table-column prop="hardware_price" label="硬件" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right;color: #ffa500">{{ scope.row.hardware_price | NumFormat}}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="software_price" label="软件" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right;color: #ffa500">{{ scope.row.software_price | NumFormat}}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="install_price" label="安装" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right;color: #ffa500">{{ scope.row.install_price | NumFormat}}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="serve_price" label="服务" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right; color: #ffa500">{{ scope.row.serve_price | NumFormat}}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="other_price" label="其他" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right; color: #ffa500">{{ scope.row.other_price | NumFormat}}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="contract_price" label="合计" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right; color: #ffa500">{{ scope.row.contract_price | NumFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="tax_price" label="税金" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style=" display: block; text-align: right; color: #ffa500">{{ scope.row.tax_price | NumFormat}}</span>
                    </template>
                </el-table-column>
            </el-table-column>
            <el-table-column label="发票开具(价税总额)" height="60" align="center">
                <el-table-column prop="bill_info.billAll17" label="17%" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right">{{ scope.row.bill_info.billAll17 | NumFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="bill_info.billAll06" label="6%" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right">{{ scope.row.bill_info.billAll06 | NumFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="bill_info.billAll0" label="小计" min-width="100" height="30" show-overflow-tooltip align="right">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right">{{ scope.row.bill_info.billAll0 | NumFormat }}</span>
                    </template>
                </el-table-column>
            </el-table-column>
            <el-table-column label="发票(不含税额)" height="60" align="center">
                <el-table-column prop="bill_info.billNoTax17" label="17%" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right">{{ scope.row.bill_info.billNoTax17 | NumFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="bill_info.billNoTax06" label="6%" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right">{{ scope.row.bill_info.billNoTax06 | NumFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="bill_info.billNoTax0" label="小计" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right">{{ scope.row.bill_info.billNoTax0 | NumFormat }}</span>
                    </template>
                </el-table-column>
            </el-table-column>
            <el-table-column label="发票(税额)" height="60" align="center">
                <el-table-column prop="bill_info.billTax17" label="17%" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right">{{ scope.row.bill_info.billTax17 | NumFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="bill_info.billTax06" label="6%" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right">{{ scope.row.bill_info.billTax06 | NumFormat }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="bill_info.billTax0" label="小计" min-width="100" height="30" show-overflow-tooltip align="center">
                    <template slot-scope="scope">
                        <span style="display: block; text-align: right">{{ scope.row.bill_info.billTax0 | NumFormat }}</span>
                    </template>
                </el-table-column>
            </el-table-column>

        </el-table>
        <el-pagination
                class="h-40 m-t-30"
                @size-change="handleSizeChange"
                @current-change="handleCurrentChange"
                :current-page.sync="page"
                :page-sizes="[10, 20, 50, 80, 100]"
                :page-size="10"
                background
                layout="total, sizes, prev, pager, next"
                :total="page_total">
        </el-pagination>
        <div style="margin-top: 20px">
            <el-button class="p-l-40 p-r-40" type="primary" @click="sendSelection">送审</el-button>
        </div>
    </div>
</template>
<style type="text/css">
    .result th {
        padding: 6px 0 !important;
    }
    .result .errText {
        color: #f00 !important;
    }
</style>
<script>
    import http from '../assets/js/http'
    import Vue from 'vue'

    export default {
        data() {
            return {
                formTab: {
                    status: '0',
                    number: '',
                    product: '',
                    page: 1,
                    page_num: 10,
                    is_excel: ''
                },
                page: 0,
                from_time: '',
                to_time: '',
                page_total: 0,
                resultData: [],
                tableBillData: [],
                multipleSelection: [],
                url: ''
            }
        },
        watch: {

        },
        filters: {
            NumFormat(value) {
                if(!value) {
                    return '0'
                }
                value = value.toFixed(2);
                let intPart = Math.trunc(value); // 获取整数部分
                let intPartFormat = intPart.toString().replace(/(\d)(?=(?:\d{3})+$)/g, '$1,'); // 将整数部分逢三一断
                let floatPart = '.00'; // 预定义小数部分
                let value2Array = value.split('.');
                // =2表示数据有小数位
                if(value2Array.length === 2) {
                    floatPart = value2Array[1].toString(); // 拿到小数部分
                    if(floatPart.length === 1) { // 补0,实际上用不着
                        return intPartFormat + '.' + floatPart + '0'
                    } else {
                        return intPartFormat + '.' + floatPart
                    }
                } else {
                    if (intPartFormat > 0) {
                        return intPartFormat + floatPart
                    }
                }
            }
        },
        created() {

        },
        mounted() {
            let start = new Date('').getTime();
            let end = new Date('').getTime();
            this.from_time = this.format(start);
            this.to_time = this.format(end);
            this.formTab.page = this.formTab.page -1;
            this.onSubmit();
        },
        methods: {
            handleSelectionChange(val) {
                this.multipleSelection = val;

            },
            checkboxInit(row,index){
                if (row.status != 3) {
                    return 0;//不可勾选
                } else {
                    return 1;//可勾选
                }
            },
            // 送审
            sendSelection() {
                let numbers = [];
                this.multipleSelection.forEach(item => {
                    numbers.push({number: item.number})
                });
                if (!numbers.length) {
                    _g.toastMsg('error', '请选择合同');
                    return
                }
                numbers = JSON.stringify(numbers);
                this.apiPost('/admin/contract/auditContract', {numbers: numbers}).then((res) => {
                    _g.toastMsg('success', '送审成功');
                    // this.$refs.multipleTable.clearSelection();
                })
            },
            handleSizeChange(val) {
                console.log(`每页 ${val} 条`);
                this.formTab.page_num = val;
                this.onSubmit()
            },
            handleCurrentChange(val) {
                console.log(`当前页: ${val}`);
                this.formTab.page = val - 1;
                this.onSubmit()
            },
            onSubmit() {
                this.disable = !this.disable;
                this.formTab.from_time = Date.parse(this.from_time)/1000;
                let endTime;
                let endMonth = new Date(this.to_time).getMonth() +1;
                if (+endMonth === 2 || +endMonth === 4 || +endMonth === 4|| +endMonth === 8) {
                    endTime = 3600 *24 *30;
                } else {
                    endTime = 3600 *24 *31;
                }
                this.formTab.to_time = Date.parse(this.to_time)/1000 + endTime;
                if (this.formTab.to_time <= this.formTab.from_time) {
                    _g.toastMsg('error', '结束时间不能小于开始时间');
                    return
                }
                this.apiPost('/admin/contract/showResult', this.formTab).then((res) => {
                    this.handelResponse(res, (data) => {
                        if (data.list.length > 0) {
                            data.list.forEach(item => {
                                item.year =  this.format(item.year*1000);
                                let reg = /(\d{4})\/(\d{2})\/(\d{2})/;
                                item.year  = item.year.replace(reg, '$1年');
                            })
                        }
                        this.resultData = data.list;
                        this.page_total = data.page_total;
                    }, () => {
                        this.disable = !this.disable
                    })
                })
            },
            downloadResult() {
                this.disable = !this.disable;
                this.formTab.from_time = Date.parse(this.from_time)/1000;
                let endTime;
                let endMonth = new Date(this.to_time).getMonth() +1;
                if (+endMonth === 2 || +endMonth === 4 || +endMonth === 4|| +endMonth === 8) {
                    endTime = 3600 *24 *30;
                } else {
                    endTime = 3600 *24 *31;
                }
                this.formTab.to_time = Date.parse(this.to_time)/1000 + endTime;
                if (this.formTab.to_time <= this.formTab.from_time) {
                    _g.toastMsg('error', '结束时间不能小于开始时间');
                    return
                }
                let opt = {
                    status: this.formTab.status,
                    product: this.formTab.product,
                    is_excel: '1',
                    from_time: this.formTab.from_time,
                    to_time: this.formTab.to_time
                };
                this.apiPost('/admin/contract/showResult', opt).then((res) => {
                    this.handelResponse(res, (data) => {
                        this.url = data.url;
                        window.open(this.url);
                    }, () => {
                        this.disable = !this.disable
                    })
                })

            },
            getSummaries(param) {
                const { columns, data } = param;
                const sums = [];
                columns.forEach((column, index) => {
                    if (index === 0) {
                        sums[index] = '总计';
                        return;
                    }
                    if (index === 1 || index === 2 || index === 3 ||index === 4|| index === 5 || index === 6) {
                        sums[index] = '-';
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
                        sums[index] = Number(sums[index]).toFixed(2);
                    } else {
                        sums[index] = '暂无';
                    }
                });

                return sums;
            },
            rowTitLink(row) {
                router.push({
                    path: '/home/detail',
                    query: {number: row.number}
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