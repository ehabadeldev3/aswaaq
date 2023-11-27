import adminApi from "../../../../api/adminAxios";
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { notify } from "@kyvg/vue3-notification";
import { useRouter } from "vue-router";
import exportFromJSON from "export-from-json";

export function ordersComposable() {
    const order = ref({});
    const products = ref({});
    const setting = ref({});
    const client = ref({});
    const all_orders_for_excel = ref({});
    const client_orders = ref(0);
    const cars = ref({});
    const cars_data = ref([]);
    const order_numbers = ref(0);
    const total_amount = ref(0);
    const paginate = ref(25);
    const supplier_orders = ref(0);
    const order_area_id = ref(0);
    const refund_allowed = ref("");
    const refund_date = ref(0);
    const pagePagination = ref(1);
    const router = useRouter();
    const select_all_orders = ref(false);
    const selected_orders = ref([]);
    const orders_group = ref([]);
    const clicked_on_order = ref([]);
    const representatives_data = ref([]);
    const loading = ref(false); //loading for button
    const loading2 = ref(false); //loading for button
    const message = ref(""); // message for notify
    const area = ref(""); // message for notify
    const state = ref(""); // message for notify
    const country = ref(""); // message for notify

    let orders = ref({}); //data for index page
    let debounce = ref({}); // for search
    const car_search = ref("");
    const order_status = ref("");
    const filter_confirmed_status = ref("");
    const car_id = ref(null);
    const representative_id = ref(null);
    const search = ref(""); //search in index page
    const note = ref(""); //search in index page
    const search2 = ref(""); //search in index page
    const fromDate = ref(""); //search in index page
    const delivery_date = ref(""); //search in index page
    const toDate = ref(""); //search in index page
    const search3 = ref(""); //search in index page
    const rep_search = ref(""); //search in index page
    const representatives = ref({}); //search in index page
    const notes = ref({}); //search in index page
    const filter_order_status = ref(""); //search in index page
    const filter_payment_status = ref(""); //search in index page
    const filter_payment_method = ref(""); //search in index page
    const search4 = ref("");
    const search5 = ref("");
    const all_representatives = ref({});
    const all_areas = ref({});
    const selected_item_for_update = ref({});
    const all_suppliers = ref({});

    const { t, locale } = useI18n();

    watch(search, (search, prevSearch) => {
        clearTimeout(debounce.value);
        debounce.value = setTimeout(() => {
            getOrders(pagePagination.value);
        }, 500);
    });
    watch(search2, (search2, prevSearch) => {
        clearTimeout(debounce.value);
        debounce.value = setTimeout(() => {
            getOrders(pagePagination.value);
        }, 500);
    });

    watch(rep_search, (search, prevSearch) => {
        clearTimeout(debounce.value);
        debounce.value = setTimeout(() => {
            getRepresentatives();
        }, 500);
    });
    watch(car_search, (search, prevSearch) => {
        clearTimeout(debounce.value);
        debounce.value = setTimeout(() => {
            getCars();
        }, 500);
    });

    //get all orders
    const getOrders = async (page_number = 1) => {
        loading.value = true;
        pagePagination.value = page_number;
        await adminApi
            .get(`/v1/dashboard/orders?page=${pagePagination.value}&client_name=${search.value}&filter_confirmed_status=${filter_confirmed_status.value}&delivery_date=${delivery_date.value}&fromDate=${fromDate.value}&toDate=${toDate.value}&order_number=${search2.value}&representative_id=${search3.value}&supplier_id=${search4.value}&area_id=${search5.value}&order_status=${filter_order_status.value}&payment_status=${filter_payment_status.value}&payment_method=${filter_payment_method.value}&paginate=${paginate.value}`)
            .then((res) => {
                orders.value = res.data.data.orders;
                total_amount.value = res.data.data.total_amount;
                all_orders_for_excel.value = res.data.data.all_orders_for_excel;
            })
            .catch((err) => {
                console.log(err.response.data);
            });
        loading.value = false;
    };

    const select_all_orders_method = () => {
        if (select_all_orders.value)
            orders.value.data.forEach((element) => {
                if(element.confirmed_received_amount != 1)
                    selected_orders.value.push(element.id);
            });
        else selected_orders.value = [];
    };
    const getAllAreasForFilterOrders = () => {
        adminApi
            .get(`/v1/dashboard/get_all_areas`)
            .then((res) => {
                all_areas.value = res.data.data.areas;
            })
            .catch((err) => {
                console.log(err.response.data);
            });
    };
    const getAllSuppliersForFilterOrders = () => {
        adminApi
            .get(`/v1/dashboard/activeSupplier`)
            .then((res) => {
                all_suppliers.value = res.data.data.suppliers;
            })
            .catch((err) => {
                console.log(err.response.data);
            });
    };
    const getAllRepresentativesForFilterOrders = () => {
        adminApi
            .get(`/v1/dashboard/activeRepresentative`)
            .then((res) => {
                all_representatives.value = res.data.data.representatives;
            })
            .catch((err) => {
                console.log(err.response.data);
            });
    };
    const getAllCars = async () => {
        loading2.value = true;

        await adminApi
            .get(
                `/v1/dashboard/get_cars?search=${
                    car_search.value
                }&paginate=${false}`
            )
            .then((res) => {
                cars_data.value = res.data.data.cars;
            })
            .catch((err) => {
                console.log(err.response.data);
            });
        loading2.value = false;
    };
    //get representative by area
    const getRepresentatives = async (paginate = true) => {
        loading2.value = true;

        await adminApi
            .get(
                `/v1/dashboard/get_representatives?search=${rep_search.value}&area_id=${order_area_id.value}&paginate=${paginate}`
            )
            .then((res) => {
                if (paginate) representatives.value = res.data.representatives;
                else
                    res.data.representatives.data.forEach((element) => {
                        representatives_data.value.push({
                            id: element.id,
                            text: element.name,
                        });
                    });
            })
            .catch((err) => {
                console.log(err.response.data);
            });
        loading2.value = false;
    };

    //cars
    const getCars = async () => {
        loading2.value = true;

        await adminApi
            .get(
                `/v1/dashboard/get_cars?search=${
                    car_search.value
                }&paginate=${true}`
            )
            .then((res) => {
                cars.value = res.data.data.cars;
            })
            .catch((err) => {
                console.log(err.response.data);
            });
        loading2.value = false;
    };

    //get category by id
    const getOrder = async (id) => {
        loading.value = true;
        await adminApi
            .get(`/v1/dashboard/orders/${id}`)
            .then((res) => {
                selected_orders.value = [id];
                let response = res.data.data;
                order_area_id.value = response.order.area_id;
                order.value = response.order;
                order_status.value = response.order.order_status;
                representative_id.value = response.order.representative_id;
                car_id.value = response.order.car_id;
                orders_group.value = response.orders_group;
                products.value = response.products;
                client.value = response.order.user;
                client_orders.value = response.client_orders;
                supplier_orders.value = response.supplier_orders;
                order_numbers.value = response.order_numbers;
                refund_allowed.value = response.refund_allowed;
                refund_date.value = response.refund_date;
                notes.value = response.order.notes;
            })
            .catch((err) => {
                console.log(err);
            });
        loading.value = false;
    };

    const downloadExcelSheet = async () => {
        loading.value = true;
        await adminApi
            .post(`/v1/dashboard/create_run_sheet`, {
                orders: selected_orders.value,
            })
            .then((res) => {
                const fileName = "orders";
                const exportType = exportFromJSON.types.xls;
                if(res.data.length > 0){
                    exportFromJSON({ data: res.data, fileName, exportType });
                    notify({
                        title: `${t(
                            "global.Downloaded Successfully"
                        )} <i class="fas fa-check-circle"></i>`,
                        type: "success",
                        duration: 5000,
                        speed: 2000,
                    });
                    getOrders(pagePagination.value)
                }else
                    notify({
                        title: `${t(
                            "global.You have to select at least one Pending order"
                        )} <i class="fas fa-circle"></i>`,
                        type: "error",
                        duration: 5000,
                        speed: 2000,
                    });
                
            })
            .catch((err) => {
                console.log(err.response.data);
                if (err.response.status == 422)
                    notify({
                        title: `${t(
                            "global.You have to select at least one Pending order"
                        )} <i class="fas fa-circle"></i>`,
                        type: "error",
                        duration: 5000,
                        speed: 2000,
                    });
                else
                    Swal.fire({
                        icon: "error",
                        title: `${t("global.ThereIsAnErrorInTheSystem")}`,
                    });
            })
            .finally(() => {
                loading.value = false;
            });
    };

    // editItemCard
    const updateOrderItem = async () => {
        Swal.fire({
            title: t(`global.Are You Sure?`),
            // text: t("treasury.YouWontBeAbleToRevertThis"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: t("global.Yes"),
            cancelButtonText: t("global.No"),
        }).then(async (result) => {
            if (result.isConfirmed) {
                loading.value = true;

                await adminApi
                    .post(
                        `/v1/dashboard/updateOrderItem`,
                        selected_item_for_update.value
                    )
                    .then((res) => {
                        notify({
                            title:
                                t("global." + res.data.message) +
                                `<i class="fas fa-check-circle"></i>`,
                            type: "success",
                            duration: 5000,
                            speed: 2000,
                        });
                        getOrder(selected_item_for_update.value.order_id);
                        $("#close-update_item").click();
                    })
                    .catch((err) => {
                        if(err.response.data && err.response.data.message)
                        notify({
                            title:err.response.data.message,
                            type: "error",
                            duration: 5000,
                            speed: 2000,
                        });
                        console.log(err.response);
                    });
                loading.value = false;
            }
        });
    };

    // deleteItemCard
    const deleteItemCard = async (id, order_details_id, measurement_id) => {
        Swal.fire({
            title: t(`global.AreYouSureDelete`),
            text: t("treasury.YouWontBeAbleToRevertThis"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                loading.value = true;

                adminApi
                    .post(`/v1/dashboard/deleteItemCard`, {
                        price_id: id,
                        order_id: order_details_id,
                        measurement_id: measurement_id,
                    })
                    .then((res) => {
                        notify({
                            title:
                                t("global." + res.data.message) +
                                `<i class="fas fa-check-circle"></i>`,
                            type: "success",
                            duration: 5000,
                            speed: 2000,
                        });
                        getOrder(order_details_id);
                    })
                    .catch((err) => {
                        console.log(err.response);
                    });
                loading.value = false;
            }
        });
    };

    const assignRepresentativeToOrder = async (
        order_details_id,
        rep_id,
        type,
        page = ""
    ) => {
        Swal.fire({
            title: t(`global.Are You Sure?`),
            // text: t("treasury.YouWontBeAbleToRevertThis"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: t("global.Yes"),
            cancelButtonText: t("global.No"),
        }).then(async (result) => {
            if (result.isConfirmed) {
                loading.value = true;
                await adminApi
                    .post(`/v1/dashboard/assignRepresentativeToOrder`, {
                        order_id:order_details_id,
                        rep_id,
                        type,
                    })
                    .then((res) => {
                        if (!page) getOrders(pagePagination.value);
                    });
                loading.value = false;
            }
        });
    };
    const assignCarToOrder = async (order_details_id, car_id, type, page = "") => {
        Swal.fire({
            title: t(`global.Are You Sure?`),
            // text: t("treasury.YouWontBeAbleToRevertThis"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: t("global.Yes"),
            cancelButtonText: t("global.No"),
        }).then(async (result) => {
            if (result.isConfirmed) {
                loading.value = true;
                await adminApi
                    .post(`/v1/dashboard/assignCarToOrder`, {
                        order_id:order_details_id,
                        car_id,
                        type,
                    })
                    .then((res) => {
                        if (!page) getOrders(pagePagination.value);
                    });
                loading.value = false;
            }
        });
    };
    const take_action = async (order_details_id = 0) => {
        Swal.fire({
            title: t(`global.Are You Sure?`),
            // text: t("treasury.YouWontBeAbleToRevertThis"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: t("global.Yes"),
            cancelButtonText: t("global.No"),
        }).then(async (result) => {
            if (result.isConfirmed) {
                loading.value = true;
                await adminApi
                    .post(`/v1/dashboard/take_action_to_orders`, {
                        orders: selected_orders.value,
                        order_status: order_status.value,
                        representative_id: representative_id.value,
                        car_id: car_id.value,
                    })
                    .then((res) => {
                        car_id.value = null;
                        order_status.value = null;
                        representative_id.value = null;
                        if (order_details_id) getOrder(order_details_id);
                        else getOrders(pagePagination.value);

                        notify({
                            title:
                                t("global.Updated Successfully") +
                                `<i class="fas fa-check-circle"></i>`,
                            type: "success",
                            duration: 5000,
                            speed: 2000,
                        });
                        $("#close-take_action").click();
                    })
                    .finally(() => {
                        loading.value = false;
                    });
            }
        });
    };
    const confirm_status = async (order_details_id = 0) => {
        Swal.fire({
            title: t(`global.Are You Sure?`),
            // text: t("treasury.YouWontBeAbleToRevertThis"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: t("global.Yes"),
            cancelButtonText: t("global.No"),
        }).then(async (result) => {
            if (result.isConfirmed) {
                loading.value = true;
                await adminApi
                    .post(`/v1/dashboard/confirm_order_status`, {
                        orders: selected_orders.value,
                    })
                    .then((res) => {
                        if (order_details_id){
                            console.log(order_details_id)
                            getOrder(order_details_id);
                        }
                        else{
                            getOrders(pagePagination.value);
                        }
                        if(res.data.number_of_failed_orders != selected_orders.value.length && selected_orders.value.length != 0)
                        notify({
                            title:
                                t("global.Updated Successfully") +
                                `<i class="fas fa-check-circle"></i>`,
                            type: "success",
                            duration: 5000,
                            speed: 2000,
                        });

                        let error_message =  selected_orders.value.length == 0 ? t('global.You have to select at least one order') : (res.data.number_of_failed_orders > 0 ?t('global.Failed to update') +" "+res.data.number_of_failed_orders +" "+t('global.Order') +" "+ t('global.Make sure these orders have status in (Completed , Partial Return, Full Return , Canceled)') : '');
                        if(error_message)
                        notify({
                            title:error_message,
                            type: "error",
                            duration: 5000,
                            speed: 2000,
                        });
                    })
                    .finally(() => {


                        loading.value = false;
                    });
            }
        });
    };
    const saveNoteToOrder = async (order_details_id, page = "show") => {
        Swal.fire({
            title: t(`global.Are You Sure?`),
            // text: t("treasury.YouWontBeAbleToRevertThis"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: t("global.Yes"),
            cancelButtonText: t("global.No"),
        }).then(async (result) => {
            if (result.isConfirmed) {
                loading.value = true;
                adminApi
                    .post(`/v1/dashboard/saveNoteToOrder/${order_details_id}`, {
                        note: note.value,
                    })
                    .then((res) => {
                        note.value = "";
                        if (page == "show") getOrder(order_details_id);
                        else getOrders(pagePagination.value);
                        notify({
                            title:
                                t("global.Added Successfully") +
                                `<i class="fas fa-check-circle"></i>`,
                            type: "success",
                            duration: 5000,
                            speed: 2000,
                        });
                        $("#close-AddNewNote").click();
                    })
                    .finally(() => {
                        loading.value = false;
                    });
            }
        });
    };

    const goToOrderDetails = async (orderId) => {
        router.push({
            name: "showOrderOnline",
            params: {
                lang: locale.value || "ar",
                id: orderId,
            },
        });
    };

    const setSelectedItemForUpdate = (price) => {
        selected_item_for_update.value = {
            price_id: price.id,
            measurement_id: price.pivot.measurement_id,
            order_id: price.pivot.order_id,
            product: price.product.name,
            size: price.product.size.name,
            flavor: price.product.flavor_name,
            measurement:
                price.pivot.measurement_type == "sub"
                    ? price.product.sub_measure_name
                    : price.product.main_measure_name,
            quantity: price.pivot.quantity,
        };
    };
    return {
        setSelectedItemForUpdate,
        goToOrderDetails,
        loading,
        clicked_on_order,
        order_status,
        cars_data,
        car_id,
        representative_id,
        message,
        search,
        confirm_status,
        fromDate,
        paginate,
        toDate,
        total_amount,
        representatives_data,
        all_orders_for_excel,
        select_all_orders_method,
        select_all_orders,
        selected_orders,
        search2,
        downloadExcelSheet,
        updateOrderItem,
        search3,
        search4,
        search5,
        assignCarToOrder,
        supplier_orders,
        getAllAreasForFilterOrders,
        getAllSuppliersForFilterOrders,
        getAllRepresentativesForFilterOrders,
        all_representatives,
        filter_confirmed_status,
        delivery_date,
        all_areas,
        selected_item_for_update,
        all_suppliers,
        filter_order_status,
        filter_payment_status,
        getCars,
        filter_payment_method,
        order_area_id,
        orders,
        order,
        area,
        state,
        country,
        client,
        client_orders,
        order_numbers,
        refund_allowed,
        cars,
        getAllCars,
        refund_date,
        orders_group,
        car_search,
        products,
        getOrders,
        getOrder,
        getRepresentatives,
        representatives,
        rep_search,
        assignRepresentativeToOrder,
        loading2,
        setting,
        // editItemCard,
        deleteItemCard,
        take_action,
        notes,
        note,
        saveNoteToOrder,
    };
}

//set errors dynamic in object
// function setErrors(errors, errors_array) {
//     if (errors_array) {
//         for (const el in errors_array) {
//             errors.value[el] = errors_array[el][0];
//         }
//     } else {
//         notify(
//             i18n.t("Unknown error,please try again later."),
//             "topright",
//             "error"
//         );
//     }
// }
