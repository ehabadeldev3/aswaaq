<template>
    <div  :class="['page-wrapper',this.$i18n.locale == 'ar'? 'page-wrapper-ar':'']">

        <div class="content container-fluid">

            <notifications :position="'top left'"  />

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">{{$t('global.secondSliderAds')}}</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{name: 'indexSecondSliderAds'}">{{$t('global.secondSliderAds')}}</router-link></li>
                            <li class="breadcrumb-item active">{{$t('global.Edit')}}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->
            <!-- Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <loader v-if="loading" />
                        <div class="card-body">
                            <div class="card-header pt-0 mb-4">
                                <router-link
                                    :to="{name: 'indexSecondSliderAds'}"
                                    class="btn btn-custom btn-dark"
                                >
                                    {{$t('global.back')}}
                                </router-link>
                            </div>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="alert alert-danger text-center" v-if="errors['product_id']">
                                        {{ errors['product_id'][0] }}<br/></div>
                                    <div class="alert alert-danger text-center" v-if="errors['place']">
                                        {{ errors['place'][0] }}<br/></div>
                                    <div class="alert alert-danger text-center" v-if="errors['file']">
                                        {{ errors['file'][0] }}<br/></div>
                                    <form @submit.prevent="editEmployee" class="needs-validation">
                                        <div class="form-row row">
                                            <div class="col-md-6 mb-3 position-relative">
                                                <div class="form-check">
                                                    <input
                                                    v-model.trim="v$.type.$model"
                                                    class="form-check-input"
                                                    type="radio"
                                                    value="product"
                                                    id="defaultCheck2"
                                                    />
                                                    <label class="form-check-label" for="defaultCheck2">
                                                    {{ $t("global.Product") }}
                                                    </label>
                                                </div>

                                                <div class="form-check">
                                                    <input
                                                    v-model.trim="v$.type.$model"
                                                    class="form-check-input"
                                                    type="radio"
                                                    value="sub_category"
                                                    id="defaultCheck1"
                                                    />
                                                    <label class="form-check-label" for="defaultCheck1">
                                                    {{ $t("global.Sub Category") }}
                                                    </label>
                                                </div>

                                            </div>

                                            <div class="col-md-6 mb-3 position-relative" v-if="data.type == 'product'">
                                                <label>{{ $t('global.ChooseProduct') }}</label>

                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle w-100  " type="button"
                                                        id="dropdownMenuButton" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <span v-if="data.product_id">
                                                            <img :src="selected_product_image ? '/upload/' + selected_product_image : '/admin/img/Logo Dashboard.png'"
                                                                alt="product-image" style="
                                                                width: 50px;
                                                                height: 50px;
                                                                border-radius: 50%;
                                                            " />
                                                            {{ selected_product_name }}</span>
                                                        <span v-else>{{
                                                            $t("global.Product")
                                                        }}</span>
                                                    </button>
                                                    <div :class="[
                                                        'dropdown-menu bg-white',
                                                        this.$i18n.locale == 'en' ? 'drop_ltr' : '',
                                                                            ]" style="
                                                            height: 400px;
                                                            overflow-y: scroll;
                                                            z-index: 999999;
                                                            width: 500px;
                                                        " aria-labelledby="dropdownMenuButton">
                                                        <input type="text" :placeholder="$t('global.Search')"
                                                            v-model="product_search"
                                                            :class="['form-control  w-100']"
                                                            onchange="event.stopPropagation()" />
                                                        <!-- <button class="btn btn-danger mx-4" v-if="product_id"
                                                            @click="assignRepresentativeToOrder(item.id, 0, 'cancel')">{{
                                                                $t('global.Cancel')
                                                            }}</button> -->
                                                        <loader v-if="loading2" />

                                                        <div v-for="product in products" :key="product.id" :class="[
                                                            'dropdown-item px-2 d-flex justify-content-between',
                                                            product.id == data.product_id
                                                                ? 'bg-secondary'
                                                                : '',
                                                        ]" @click="select_product(product.id,product.name,product.image)">
                                                            <img :src="product.image ? '/upload/' + product.image : '/admin/img/Logo Dashboard.png'"
                                                                alt="product-image" style="width: 50px; height: 50px" />
                                                            <span style="
                                                                overflow: hidden;
                                                                height: 34px;
                                                                font-size: 24px;
                                                                word-break: break-word;
                                                            ">{{ product.name }}</span>
                                                        </div>

                                                        <h5 v-if="
                                                            Object.keys(products ?? []).length ==
                                                            0
                                                        " class="text-center">
                                                            {{ $t("global.NoDataFound") }}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3 position-relative" v-if="data.type == 'sub_category'">
                                                <label>{{ $t('global.Choose Sub Category') }}</label>

                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle w-100  " type="button"
                                                        id="dropdownMenuButton" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <span v-if="data.sub_category_id">
                                                            <img :src="selected_sub_category_image ?? '/admin/img/Logo Dashboard.png'"
                                                                alt="sub_category-image" style="
                                                                width: 50px;
                                                                height: 50px;
                                                                border-radius: 50%;
                                                            " />
                                                            {{ selected_sub_category_name }}</span>
                                                        <span v-else>{{
                                                            $t("global.subCategory")
                                                        }}</span>
                                                    </button>
                                                    <div :class="[
                                                        'dropdown-menu bg-white',
                                                        this.$i18n.locale == 'en' ? 'drop_ltr' : '',
                                                                            ]" style="
                                                            height: 400px;
                                                            overflow-y: scroll;
                                                            z-index: 999999;
                                                            width: 500px;
                                                        " aria-labelledby="dropdownMenuButton">
                                                        <input type="text" :placeholder="$t('global.Search')"
                                                            v-model="sub_category_search"
                                                            :class="['form-control  w-100']"
                                                            onchange="event.stopPropagation()" />
                                                        <!-- <button class="btn btn-danger mx-4" v-if="sub_category_id"
                                                            @click="assignRepresentativeToOrder(item.id, 0, 'cancel')">{{
                                                                $t('global.Cancel')
                                                            }}</button> -->
                                                        <loader v-if="loading2" />

                                                        <div v-for="sub_category in sub_categories" :key="sub_category.id" :class="[
                                                            'dropdown-item px-2 d-flex justify-content-between',
                                                            sub_category.id == data.sub_category_id
                                                                ? 'bg-secondary'
                                                                : '',
                                                        ]" @click="select_sub_category(sub_category.id,sub_category.name,sub_category.image_path)">
                                                            <img :src="sub_category.image_path ?? '/admin/img/Logo Dashboard.png'"
                                                                alt="sub_category-image" style="width: 50px; height: 50px" />
                                                            <span style="
                                                                overflow: hidden;
                                                                height: 34px;
                                                                font-size: 24px;
                                                                word-break: break-word;
                                                            ">{{ sub_category.name }}</span>
                                                        </div>

                                                        <h5 v-if="
                                                            Object.keys(sub_categories ?? []).length ==
                                                            0
                                                        " class="text-center">
                                                            {{ $t("global.NoDataFound") }}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-12 row flex-fill">
                                                <div class="btn btn-outline-primary waves-effect">
                                                    <span>
                                                        {{$t('global.ChooseImage')}}
                                                        <i class="fas fa-cloud-upload-alt ml-3" aria-hidden="true"></i>
                                                    </span>
                                                    <input
                                                        name="mediaPackage"
                                                        type="file"
                                                        @change="preview"
                                                        id="mediaPackage"
                                                        accept="image/png,jepg,jpg"
                                                    >
                                                </div>
                                                <p class="num-of-files">{{
                                                        numberOfImage ? numberOfImage + $t('global.FilesSelected') : $t('global.NoFilesChosen')
                                                    }}</p>
                                                <div class="container-images" v-show="numberOfImage" id="container-images"></div>
                                                <div class="container-images" v-show="!numberOfImage">
                                                    <figure>
                                                        <figcaption>
                                                            <img :src="`/upload/${image.file_name}`">
                                                        </figcaption>
                                                    </figure>
                                                </div>
                                            </div>

                                        </div>

                                        <button class="btn btn-primary" type="submit" >{{ $t('global.Submit') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table -->
        </div>
    </div>
</template>

<script>
import {computed, onBeforeMount, reactive,toRefs,watch,ref} from "vue";
import useVuelidate from '@vuelidate/core';
import {required, minLength, maxLength, email, alphaNum, sameAs} from '@vuelidate/validators';
import adminApi from "../../../../api/adminAxios";
import { notify } from "@kyvg/vue3-notification";
import {useI18n} from "vue-i18n";


export default {
    name: "edit",
    data(){
        return {
            errors:{}
        }
    },
    props:["id"],
    setup(props){
        const {id} = toRefs(props)
        const {t} = useI18n({});
        const products = ref({})
        const debounce = ref({})
        const imageUpload = ref({});
        const numberOfImage = ref(0);
        const image = ref('');
        const loading = ref(false)
        const loading2 = ref(false)
        const selected_product_image = ref('')
        const selected_product_name = ref('')
        const product_search = ref('')
        const sub_categories = ref({})
        const selected_sub_category_image = ref('')
        const selected_sub_category_name = ref('')
        const sub_category_search = ref('')

        watch(product_search, () => {
            clearTimeout(debounce.value);
            debounce.value = setTimeout(() => {
                getProducts();
            }, 500);
        });
        watch(sub_category_search, () => {
            clearTimeout(debounce.value);
            debounce.value = setTimeout(() => {
                getSubCategories();
            }, 500);
        });

        const getSubCategories = async () => {
            loading2.value = true;
            adminApi
            .get(
            `/v1/dashboard/subCategory?search=${sub_category_search.value}`
            )
            .then((res) => {
                sub_categories.value = res.data.data.categories.data;
            })
            .finally(() => {
            loading2.value = false;
            });

        }
        const getProducts = async () => {
            loading2.value = true;
            adminApi
            .get(
            `/v1/dashboard/product?search=${product_search.value}`
            )
            .then((res) => {
                products.value = res.data.data.products.data;
            })
            .finally(() => {
            loading2.value = false;
            });

        }

        const select_product  = (productId,name,image) => {
            selected_product_image.value = image
            selected_product_name.value = name
            addEmployee.data.product_id = productId
        }
        const select_sub_category  = (sub_categoryId,name,image) => {
            selected_sub_category_image.value = image
            selected_sub_category_name.value = name
            addEmployee.data.sub_category_id = sub_categoryId
        }

        let preview = (e) => {

            let containerImages = document.querySelector('#container-images');
            if(numberOfImage.value){
                containerImages.innerHTML = '';
            }

            addEmployee.data.file = {}
            numberOfImage.value = e.target.files.length;

            addEmployee.data.file = e.target.files[0];

            let reader = new FileReader();
            let figure = document.createElement('figure');
            let figcap = document.createElement('figcaption');

            figcap.innerText = addEmployee.data.file.name;
            figure.appendChild(figcap);

            reader.onload = () => {
                let img = document.createElement('img');
                img.setAttribute('src',reader.result);
                figure.insertBefore(img,figcap);
            }

            containerImages.appendChild(figure);
            reader.readAsDataURL(addEmployee.data.file);

        };

        let getMainEmployeeViews = () => {
            loading.value = true;

            adminApi.get(`/v1/dashboard/ads/${id.value}/edit`)
                .then((res) => {
                    let l = res.data.data;
                    addEmployee.data.product_id = l.ad.product_id;
                    addEmployee.data.sub_category_id = l.ad.sub_category_id;
                    addEmployee.data.type = l.ad.product_id ? 'product' : 'sub_category';
                    addEmployee.data.place = l.ad.place;
                    addEmployee.nameSender = l.ad.product ? l.ad.product.name : '';
                    image.value = l.ad.media;
                })
                .catch((err) => {
                    console.log(err.response);
                })
                .finally(() => {
                    loading.value = false;
                })
        }


        onBeforeMount(() => {
            getMainEmployeeViews();
            getProducts();
            getSubCategories();
        });

        let addEmployee =  reactive({
            data:{
                product_id: '',
                sub_category_id: '',
                type: '',
                place:2,
                file: {}
            },
        })

        const rules = computed(() => {
            return {
                file:{},
                product_id:{},
                sub_category_id:{},
                type:{}
            }
        });




        const v$ = useVuelidate(rules,addEmployee.data);


        return {t,preview,imageUpload,image,numberOfImage,products,sub_categories,loading,...toRefs(addEmployee),v$,select_product,loading2,product_search,selected_product_image,selected_product_name,select_sub_category,sub_category_search,selected_sub_category_image,selected_sub_category_name};
    },
    methods: {
        editEmployee(){
            this.v$.$validate();

            if(!this.v$.$error){

                this.loading = true;
                this.errors = {};
                let formData = new FormData();
                formData.append('product_id',this.data.product_id);
                formData.append('sub_category_id',this.data.sub_category_id);
                formData.append('type',this.data.type);
                formData.append('place',this.data.place);
                formData.append('file',this.data.file);
                formData.append('_method','PUT');

                adminApi.post(`/v1/dashboard/ads/${this.id}`,formData)
                    .then((res) => {

                        notify({
                            title: `${this.t('global.EditSuccessfully')} <i class="fas fa-check-circle"></i>`,
                            type: "success",
                            duration: 5000,
                            speed: 2000
                        });

                    })
                    .catch((err) => {
                        this.errors = err.response.data.errors;
                    })
                    .finally(() => {
                        this.loading = false;
                    });

            }
        }
    }
}
</script>

<style scoped>
.coustom-select {
    height: 100px;
}

.card {
    position: relative;
}

.waves-effect {
    position: relative;
    overflow: hidden;
    cursor: pointer;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    width: 200px;
    height: 50px;
    text-align: center;
    line-height: 34px;
    margin: auto;
}

input[type="file"] {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    padding: 0;
    margin: 0;
    cursor: pointer;
    filter: alpha(opacity=0);
    opacity: 0;
}

.num-of-files {
    text-align: center;
    margin: 20px 0 30px;
}

.container-images {
    width: 90%;
    position: relative;
    margin: auto;
    display: flex;
    justify-content: space-evenly;
    gap: 20px;
    flex-wrap: wrap;
    padding: 10px;
    border-radius: 20px;
    background-color: #f7f7f7;
}
.sec-body{
    border: 3px solid #fcb00c;
    border-radius: 20px;
    padding: 10px;
}
.sec-head{
    background-color: #fcb00c;
    color: #000;
    border-radius: 11px;
    padding: 5px;
    text-align: center;
    margin-bottom: 8px;
    margin-top: 10px;
}
.sec-body:hover .sec-head{
    border: 3px solid #fcb00c;
    padding: 2px;
    border-radius: 11px;
    background-color: #ffff;
}
.sec-head h3{
    font-weight: 700;
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
</style>
