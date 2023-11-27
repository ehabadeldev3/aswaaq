<template>
    <div :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{ $t('global.showPrices') }}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <router-link :to="{name: 'dashboard', params: {lang: locale || 'ar'}}">
                                    {{ $t('dashboard.Dashboard') }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item active">{{ $t('global.showPrices') }}</li>
                        </ul>
                    </div>

                </div>
            </div>
            <!-- /Page Header -->
            <!-- Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <loader v-if="loading"/>
                        <div class="card-body">
                            <div class="card-header pt-0">
                                <div class="row justify-content-between">
                                    <div class="col-12 row justify-content-end">
                                        <form @submit.prevent="getDataPrices" class="needs-validation">
                                            <div class="form-group row">

                                                <div class="col-md-5 mb-3 position-relative">
                                                    <label> {{ $t('global.ChooseProduct') }} </label>

                                                    <input type="text"
                                                           class="form-control mb-1 input-Sender"
                                                           v-model="searchSupplier"
                                                           @input="searchSender"
                                                           @blur="isButton = true"
                                                           @focus="searchSender"
                                                           autofocus
                                                           :placeholder="$t('treasury.Search')"
                                                    >
                                                    <ul class="dropdown-search sender-search" v-if="dropDownSenders.length">
                                                        <li
                                                            class="Sender"
                                                            v-for="(dropDownSender,index) in dropDownSenders"
                                                            :key="index"
                                                            @click="showSenderName(index)"
                                                            @mouseenter="senderHover"
                                                        >
                                                            {{ dropDownSender.name }}
                                                        </li>
                                                    </ul>

                                                    <input type="text"
                                                           :class="['form-control']"
                                                           disabled
                                                           v-model="nameSender"
                                                    >
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label>طريقة البيع</label>
                                                    <select
                                                        name="type"
                                                        class="form-control"
                                                        v-model="selling_method_id">

                                                        <option v-for="sellingMethod in sellingMethods" :value="sellingMethod.id" >
                                                            {{ sellingMethod.name }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1 mt-5">
                                                    <button class="btn btn-primary" type="submit" v-if="isButton">{{$t('global.Submit')}}</button>
                                                </div>

                                            </div>

                                        </form>
                                    </div>
                                    <div class="col-12 row mt-3">
                                        <div class="col-6 d-flex">
                                            <button @click="printRestriction" class="btn btn-info print-button" v-if="isButton">
                                                {{$t('global.Print')}}
                                                <i class="fa fa-print"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive" id="printRestriction">
                                <table class="table mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ $t('global.ProductName') }}</th>
                                        <th>{{ $t('global.UnitMeasurement') }}</th>
                                        <th>{{ $t('global.priceDiscount') }}</th>
<!--                                        <th>{{ $t('global.availableQuantity') }}</th>-->
                                        <th>{{ $t('global.LessimumOrderQuantity') }}</th>
                                        <th>{{ $t('global.MaximumOrderQuantity') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="products.length">
                                    <tr v-for="(item,index) in products"  :key="item.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.product.name }}</td>
                                        <td>{{ item.measurement_unit.name }}</td>
                                        <td>{{ item.price }}</td>
<!--                                        <td>{{ item.available_quantity }}</td>-->
                                        <td>{{ item.less_quantity }}</td>
                                        <td>{{ item.max_quantity }}</td>
                                    </tr>

                                    </tbody>
                                    <tbody v-else>
                                    <tr >
                                        <th class="text-center" colspan="7">{{ $t('global.NoDataFound') }}</th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table -->
        </div>
        <!-- /Page Wrapper -->
    </div>
</template>

<script>
import {onMounted, inject, watch, ref, computed} from "vue";
import adminApi from "../../../api/adminAxios";
import {useI18n} from "vue-i18n";
import {useStore} from "vuex";
import {numeric, required} from "@vuelidate/validators";

export default {
    name: "index",
    setup() {
        const emitter = inject('emitter');
        const {t} = useI18n({});
        let store = useStore();
        let permission = computed(() => store.getters['authAdmin/permission']);

        // get packages
        let accounts = ref([]);
        let supplier_id = ref(0);
        let selling_method_id = ref(1);
        let nameSender= ref('');
        let dropDownSenders= ref([]);
        let suppliers= ref([]);
        let searchSupplier= ref('');
        let loading = ref(false);
        let isButton = ref(true);
        let products= ref([]);
        let sellingMethods= ref([]);

        let searchSender = () => {
            dropDownSenders.value = [];
            if(searchSupplier.value){
                let thisString = new RegExp(searchSupplier.value,'i');
                let items = suppliers.value.filter(e => e.name.match(thisString) || e.id == searchSupplier.value);
                dropDownSenders.value = items.splice(0,10);
            }else{
                dropDownSenders.value = [];
            }

            isButton.value = false;
        };

        let showSenderName = (index) => {
            let item = dropDownSenders.value[index];
            nameSender.value = item.name ;
            supplier_id.value = item.id;
            searchSupplier.value = '';
            dropDownSenders.value = [];
        };

        let senderHover = (e) => {
            let items = document.querySelectorAll('.sender-search .Sender');
            items.forEach(e => e.classList.remove('active'));
            e.target.classList.add('active');
        };

        document.addEventListener('keyup',(e) => {

            if(e.keyCode == 38){ //top arrow
                if(dropDownSenders.value.length > 0){
                    let items = document.querySelectorAll('.sender-search .Sender');
                    let isTrue = false;
                    let index = null;
                    items.forEach((e,i) => {
                        if(e.classList.contains('active')) {
                            isTrue = true;
                            index = i;
                        }
                    });
                    if(isTrue && index != 0){
                        items[index].classList.remove('active');
                        items[index - 1].classList.add('active');
                    }else if(isTrue && index == 0){
                        items[index].classList.remove('active');
                        items[items.length - 1].classList.add('active');
                    }
                    if(!isTrue) items[0].classList.add('active');
                }else {
                    dropDownSenders.value = [];
                }
            };

            if(e.keyCode == 40){ //down arrow
                if(dropDownSenders.value.length > 0){
                    let items = document.querySelectorAll('.sender-search .Sender');
                    let isTrue = false;
                    let index = null;
                    items.forEach((e,i) => {
                        if(e.classList.contains('active')) {
                            isTrue = true;
                            index = i;
                        }
                    });
                    if(isTrue && index != (items.length - 1)){
                        items[index].classList.remove('active');
                        items[index + 1].classList.add('active');
                    }else if(isTrue && index == (items.length - 1)){
                        items[index].classList.remove('active');
                        items[0].classList.add('active');
                    }
                    if(!isTrue) items[items.length - 1].classList.add('active');
                }else {
                    dropDownSenders.value = [];
                }
            };

            if(e.keyCode == 13){ //enter

                if(dropDownSenders.value.length > 0){
                    let items = document.querySelectorAll('.sender-search .Sender');
                    items.forEach((e,i) => {
                        if(e.classList.contains('active')) showSenderName(i);
                    });
                }else {
                    dropDownSenders.value = [];
                }
                e.target.blur();
            };

        });

        document.addEventListener('click',(e) => {
            if(dropDownSenders.value.length > 0){
                if(!e.target.classList.contains('Sender') && !e.target.classList.contains('input-Sender') && e.pointerType){
                    dropDownSenders.value = [];
                }
            }
        });

        let getData = () => {
            loading.value = true;
            adminApi.get(`/v1/dashboard/mobileProduct`)
                .then((res) => {
                    let l = res.data.data;
                    suppliers.value = l.products;
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    loading.value = false;
                });
        }

        let getSellingMethod = () => {
            loading.value = true;
            adminApi.get(`/v1/dashboard/activeSellingMethod`)
                .then((res) => {
                    let l = res.data.data;
                    sellingMethods.value = l.sellingMethods;
                })
                .catch((err) => {
                    console.log(err.response.data);
                })
                .finally(() => {
                    loading.value = false;
                });
        }

        onMounted(() => {
            getData();
            getSellingMethod();
        });

        watch(supplier_id, (supplier_id, prevSearch) => {
            getDataPrices();
        });

        let getDataPrices = () => {
            let v = suppliers.value.find((el)=> el.id == supplier_id.value);
            v.product_price.forEach((el,index) =>{
                if (parseInt(el.selling_method_id) == parseInt(selling_method_id.value) && parseInt(el.active) == 1)
                {
                    products.value.unshift(el);
                }
            });
            console.log(products.value)
        }

        let printRestriction = () => {
            var printContents = document.getElementById('printRestriction').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }

        let dateFormat = (item) => {
            let now = new Date(item);
            let st = `
                 ${now.getUTCHours()}:${now.getUTCMinutes()}:${now.getUTCSeconds()}
                ${now.getUTCFullYear().toString()}
                 /${(now.getUTCMonth() + 1).toString()}
                 /${now.getUTCDate()}
            `;
            return st;
        }

        return {
            nameSender,
            searchSupplier,
            dropDownSenders,
            suppliers,
            printRestriction,
            showSenderName,
            searchSender,
            senderHover,
            permission,
            getData,
            accounts,
            supplier_id,
            loading,
            dateFormat,
            isButton,
            getDataPrices,
            products,
            sellingMethods,
            selling_method_id
        };
    },
    data() {
        return {
            locale: localStorage.getItem("langAdmin")
        }
    }
}
</script>

<style scoped>
.card {
    position: relative;
}
.custom {
    border: 1px solid #D7D7D7;
    padding: 2px;
    border-radius: 5px;
    width: 35%;
}

.btn {
    color: #fff;
}
.hover:hover{
    border: 2px solid;
    padding: 3px;
    border-radius: 7px;
}

.amount h5{
    font-weight: 700;
    text-align: center;
}

.date-input{
    width: 135px !important;
    display: inline-block !important;
    margin: 0px 8px 0 8px !important;
}
.dropdown-search{
    position: absolute;
    width: 97%;
    background-color: #fff;
    border: 1px solid #d9d3d3;
    top: 83px;
    z-index: 10;
    padding: 0;
}

.dropdown-search li{
    padding: 5px;
}

.dropdown-search li:not(:last-child){
    border-bottom: 1px solid #d9d3d3;
}

.dropdown-search li:hover{
    background-color: #f1f1f1;
    cursor: pointer;
}

.dropdown-search li.active{
    background-color: #f1f1f1;
    cursor: pointer;
}
.balance{
    padding: 0 25px 0 0;
}

</style>
