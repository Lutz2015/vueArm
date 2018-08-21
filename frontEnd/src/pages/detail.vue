<template>
    <div class="m-l-20 w-1100">
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
                <el-form-item label="酒店所属城市:">
                    <el-input v-model.trim="formDetail.city" class="h-40 fl w-300" :disabled="true"></el-input>
                </el-form-item>
                <el-form-item label="合同甲方:">
                    <el-input v-model.trim="formDetail.party_a" class="h-40 w-300" :disabled="true"></el-input>
                </el-form-item>
                <el-form-item label="第三方:" prop="">
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
                    <el-button class="p-l-30 p-r-30" type="primary" @click="commitContract" :loading="isLoading">提交</el-button>
                </el-form-item>
            </el-form>
            </el-tab-pane>
            <el-tab-pane label="清单列表" name="second">
                <el-row style="margin-bottom: 10px; text-align: right">
                    <el-button><i class="el-icon-download el-icon--left"></i>下载</el-button>
                </el-row>
                <el-table :data="tableData" border style="width: 100%;">
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
                    <el-button type="primary" class="p-l-40 p-r-40" @click="editContact">提交</el-button>
                </el-row>

            </el-tab-pane>
            <el-tab-pane label="发票列表" name="third">
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
            </el-tab-pane>
        </el-tabs>
    </div>
</template>
<style type="text/css">

</style>
<script>
    import http from '../assets/js/http'

    export default {
        data() {
            return {
                formDetail: {},
                resultData: [],
                activeName: 'first',
                tableData: [],
                isDisabled: true,
                isEdit: false,
                isUnitEdit: false
            }
        },
        watch: {

        },
        created() {
            this.init();
        },
        mounted() {

        },
        methods: {
            init() {
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
                            this.formDetail.bill = 0;
                        }

                        let hardware = this.formDetail.hardware ? this.formDetail.hardware : [];
                        let software = this.formDetail.software ? this.formDetail.software : [];
                        let install = this.formDetail.install ? this.formDetail.install : [];
                        let serve = this.formDetail.serve ? this.formDetail.serve : [];
                        let other = this.formDetail.other ? this.formDetail.other : [];
                        let arr = [hardware, software, install, serve, other];
                        this.tableData = arr.reduce((a,b) => {
                            return a.concat(b)
                        });
                        this.tableBillData = this.formDetail.bill;

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
            commitContract() {
                let opts = {};
                opts.check_time = Date.parse(this.formDetail.check_time)/1000;
                opts.stop_time = Date.parse(this.formDetail.stop_time)/1000;
                opts.number = this.formDetail.number;
                this.apiPost('/admin/contract/modify', opts).then((res) => {
                    this.handelResponse(res, (data) => {
                        _g.toastMsg('success', '修改成功');
                        this.isEdit = false;
                        this.isDisabled = true;
                    }, () => {
                        this.disable = !this.disable
                    })
                })
            },
            editContact() {
                switch (this.defaultRadio) {
                    case 1:
                        this.formContract.name = '硬件产品';
                        break;
                    case 2:
                        this.formContract.name = '软件产品';
                        break;
                    case 3:
                        this.formContract.name = '安装调试';
                        break;
                    case 4:
                        this.formContract.name = '服务';
                        break;
                    case 5:
                        this.formContract.name = '其他';
                        break

                }
                let opts = {};
                opts.hardware = JSON.stringify(this.tableData);
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
            onSubmit() {

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