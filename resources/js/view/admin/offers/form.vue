<template>
    <div class="offer-form">
        <notifications :position="locale == 'ar' ? 'top left' : 'top right'" />
        <div class="modal fade" id="offerFormModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form @submit.prevent="save" enctype="multipart/form-data">
                        <div class="modal-header">
                            <button type="button" class="btn-close" aria-label="Close" data-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <div class="image">
                                        <img v-if="previewImage" class="border-bottom" :src="previewImage" />
                                        <img v-else class="border-bottom" src="/images/empty.jpg" />
                                        <div class="image-upload">
                                            <label class="icon" for="image">
                                                <i class="fa fa-camera"></i>
                                            </label>
                                            <label @click="deleteImage" v-if="uploadedImage"
                                                class="icon text-secondary px-2">
                                                <i class="fa fa-window-close" aria-hidden="true"></i>
                                            </label>
                                            <input @change="uploadImage"
                                                accept="image/apng, image/avif, image/gif, image/jpeg, image/png, image/svg+xml, image/webp"
                                                type="file" id="image" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="row">

                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">{{ $t("global.DescriptionAr") }}</label>
                                                <textarea :class="{
                                                        'is-invalid': v$.description_ar.$error,
                                                    }" name="" v-model="v$.description_ar.$model" id="exampleInputEmail1" class="form-control" cols="30" rows="10"></textarea>

                                                <div class="invalid-feedback">
                                                    <div v-for="error in v$.description_ar.$errors" :key="error">
                                                        {{  $t(error.$validator) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">{{ $t("global.DescriptionEn") }}</label>
                                                <textarea :class="{
                                                        'is-invalid': v$.description_en.$error,
                                                    }" name="" v-model="v$.description_en.$model" id="exampleInputEmail1" class="form-control" cols="30" rows="10"></textarea>

                                                <div class="invalid-feedback">
                                                    <div v-for="error in v$.description_en.$errors" :key="error">
                                                        {{  $t(error.$validator) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="exampleInputEmail22">{{ $t("global.expiryDate") }}</label>
                                                <input type="date" :class="{
                                                        'is-invalid': v$.ex_date.$error,
                                                    }" name="" v-model="v$.ex_date.$model" id="exampleInputEmail22" class="form-control" />

                                                <div class="invalid-feedback">
                                                    <div v-for="error in v$.ex_date.$errors" :key="error">
                                                        {{  $t(error.$validator) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div v-if="external != 0 " class="form-group">
                                                <label for="exampleInputEmail1">{{ $t("global.Url") }}</label>
                                                <input type="text" class="form-control" v-model="v$.url.$model" :class="{
                                                    'is-invalid': v$.url.$error,
                                                }" />
                                                <div class="invalid-feedback">
                                                    <div v-for="error in v$.url.$errors" :key="error">
                                                        {{ $t("global.Url") + " " + $t(error.$validator) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="external != 1" class="form-group">
                                                <label for="sel1">{{ $t("global.Products") }}</label>

                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle w-100  " type="button"
                                                        id="dropdownMenuButton" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <span v-if="product_id">
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
                                                            product.id == product_id
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
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox mr-sm-2">
                                                    <input id="workflow" name="workflow" class="custom-control-input"
                                                        type="checkbox" v-model="external" />
                                                    <label class="custom-control-label" for="workflow">{{
                                                        $t("global.External")
                                                    }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ $t("global.Submit") }}
                            </button>
                            <button id="close-button" type="button" class="btn btn-secondary" data-dismiss="modal">
                                {{ $t("global.Close") }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { required } from "@vuelidate/validators";
import offerClient from "../../../http-clients/offer-client";
import { inject, reactive, toRefs, watch , ref, onMounted} from "@vue/runtime-core";
import { useI18n } from "vue-i18n";
import { notify } from "@kyvg/vue3-notification";
import adminApi from '../../../api/adminAxios';
export default {
    setup(props, context) {
        const product_search = ref('')
        const products = ref({})
        const debounce = ref({})
        const loading2 = ref(false)
        const selected_product_image = ref('')
        const selected_product_name = ref('')
        const { t, locale } = useI18n({ useScope: "global" });
        const offer_store = inject("offer_store");
        const data = reactive({
            uploadedImage: null,
            previewImage: "",
        });
        const form = reactive({
            description_ar: "",
            description_en: "",
            ex_date: "",
            url: "",
            external: true,
            product_id: null,
        });
        const rules = {
            description_ar: { required },
            description_en: { required },
            ex_date: { required },
            product_id: {
                required(value) {
                    return form.external == 0? value : true;
                },
            },
            url: {
                required(value) {
                    return form.external == 1 ? value : true;
                },
            },
        };

        onMounted(() => {
            getProducts()
        })
        const v$ = useVuelidate(rules, form);
        //Methods
        function uploadImage(e) {
            const image = e.target.files[0];
            data.uploadedImage = image;
            const reader = new FileReader();
            reader.readAsDataURL(image);
            reader.onload = (e) => {
                data.previewImage = e.target.result;
            };
        }
        function deleteImage() {
            data.uploadedImage = null;
            data.previewImage = props.selectedOffer
                ? `/upload/${props.selectedOffer.image}`
                : "";
        }

        function save() {
            if (v$.value.$invalid) {
                v$.value.$touch();
                return;
            }
            if (!props.selectedOffer) {
                if (!data.uploadedImage) {
                    alertMessage(
                        `${t("global.Image")} ${t("required")} <i class="fas fa-close"></i>`,
                        "error"
                    );
                    return;
                }
                store();
            } else {
                update();
            }
        }

        //Commons
        function getProduct() {
            let product = null;
            props.allProducts.forEach(function (_product) {
                if (_product.id == form.product_id) {
                    return (product = _product);
                }
            });
            return product;
        }

        function alertMessage(message, type) {
            notify({
                title: message,
                type: type,
                duration: 5000,
                speed: 2000,
            });
        }
        function store() {
            context.emit("loading", true);
            offerClient
                .store(getForm(false))
                .then((response) => {
                    context.emit("loading", false);
                    context.emit("created", response.data);
                    $("#close-button").click();
                    alertMessage(
                        `${t("global.CreatedSuccessfully")} <i class="fas fa-check-circle"></i>`,
                        "success"
                    );
                })
                .catch((error) => {
                    context.emit("loading", false);
                });
        }
        function update() {
            context.emit("loading", true);
            offerClient
                .update(getForm(true))
                .then((response) => {
                    context.emit("loading", false);
                    context.emit("updated", response.data);
                    $("#close-button").click();
                    alertMessage(
                        `${t("global.UpdatedSuccessfully")} <i class="fas fa-check-circle"></i>`,
                        "success"
                    );
                })
                .catch((error) => {
                    context.emit("loading", false);
                });
        }
        function getForm(method) {
            let formData = new FormData();
            if (props.selectedOffer) {
                formData.append("id", props.selectedOffer.id);
            }
            formData.append("description_en", form.description_en);
            formData.append("ex_date", form.ex_date);
            formData.append("description_ar", form.description_ar);
            if (form.url) formData.append("url", form.url);
            if (form.product_id) formData.append("product_id", form.product_id);
            formData.append("external", form.external);
            let product = getProduct();
            formData.append("product_name_ar", product ? product.name : "");
            formData.append("product_name_en", product ? product.nameEn : "");
            if (data.uploadedImage) {
                formData.append("image", data.uploadedImage);
            }
            if (method) {
                formData.append("_method", 'put');
            }
            return formData;
        }
        function setForm() {
            v$.value.$reset();
            form.product_id = getProductId();
            form.description_en = props.selectedOffer ? props.selectedOffer.description_en : "";
            form.ex_date = props.selectedOffer ? props.selectedOffer.ex_date : "";
            form.description_ar = props.selectedOffer ? props.selectedOffer.description_ar : "";
            form.url = props.selectedOffer ? props.selectedOffer.url : "";
            form.external =
                props.selectedOffer && props.selectedOffer.external != 0 ? true : false;
            data.previewImage = props.selectedOffer
                ? `/upload/${props.selectedOffer.image}`
                : "";
            data.uploadedImage = null;

            selected_product_image.value = props.selectedOffer &&props.selectedOffer.product ? props.selectedOffer.product.image_path:null
            selected_product_name.value = props.selectedOffer &&props.selectedOffer.product ? props.selectedOffer.product.name:null
        }
        function getProductId() {
            // let firstProductId = props.allProducts.length > 0 ? props.allProducts[0].id : null;
            return props.selectedOffer ? props.selectedOffer.product_id : null;
        }
        watch(product_search, () => {
            clearTimeout(debounce.value);
            debounce.value = setTimeout(() => {
                getProducts();
            }, 500);
        });
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
            form.product_id = productId
        }
        //Watchers
        watch(
            () => {
                offer_store.onFormShow;
            },
            (value) => {
                setForm();
            },
            { deep: true }
        );
        return {
            ...toRefs(data),
            ...toRefs(form),
            v$,
            uploadImage,
            deleteImage,
            save,
            select_product,
            selected_product_image,
            selected_product_name,
            product_search,
            locale,
            getProducts,
            products,
            loading2,
        };
    },
    props: ["selectedOffer",'allProducts'],
};
</script>

<style scoped lang="scss">
.offer-form {
    .form-control {
        padding: 10px;
    }

    .form-group {
        margin-bottom: 10px;

        label {
            margin-bottom: 5px;
        }
    }

    .icons {
        i {
            font-size: 20px;
        }

        i:hover {
            cursor: pointer;
        }
    }

    .image {
        box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px,
            rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
        padding-bottom: 5px;

        img {
            width: 100%;
            height: 150px;
        }

        .image-upload {
            i {
                margin-top: 10px;
                color: #888888;
            }

            .icon {
                &:hover {
                    cursor: pointer !important;
                }

                i {
                    font-size: 14px;
                    position: relative;
                }
            }

            text-align: center;

            input[type="file"] {
                display: none;
            }
        }
    }

    .modal-footer {
        button {
            width: 80px;
        }
    }
}
</style>
