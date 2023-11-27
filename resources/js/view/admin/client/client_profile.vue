<template>
  <div
    class="client-container"
    :class="[
      'page-wrapper',
      this.$i18n.locale == 'ar' ? 'page-wrapper-ar' : '',
    ]"
  >
    <div class="content container-fluid">
      <!-- Page Header -->
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col">
            <h3 class="page-title">{{ $t("global.Client Profile") }}</h3>
            <ul class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link :to="{ name: 'dashboard' }">
                  {{ $t("dashboard.Dashboard") }}
                </router-link>
              </li>
              <li class="breadcrumb-item active">
                {{ $t("global.Client Profile") }}
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- /Page Header -->
      <!-- Table -->
      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <loader v-if="loading" />
            <div class="card-body">
              <div class="card-header pt-0">
                <div class="row justify-content-between">
                  <div class="col-5">
                    {{$t('global.Client Orders')}}
                  </div>
                  <div class="col-5">
                    <router-link
                            :to="{name: 'clients'}"
                            class="btn btn-custom btn-dark w-50 bg-dark"
                        >
                            {{ $t('global.back') }}
                        </router-link>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table mb-0">
                  <thead>
                    <tr>
                      <th>{{$t('global.Date')}}</th>
                      <th>{{ $t("global.State / Area") }}</th>
                      <th>{{ $t("global.TotalTax") }}</th>
                      <th>{{ $t("global.Shipping cost") }}</th>
                      <th>{{ $t("global.Subtotal") }}</th>
                      <th>{{ $t("global.Total Amount") }}</th>
                      <th>{{ $t("global.Show") }}</th>
                    </tr>
                  </thead>
                  <tbody v-if="Object.keys(orders.data ?? []).length">
                    <tr
                      v-for="(order, index) in orders.data"
                      :key="order.id"
                    >
                      <td>{{ order.created_at }}</td>
                      <td>{{ order.area_name }}</td>
                      <td>{{ order.tax_amount }} {{ $t("global.LE") }}</td>
                      <td>{{ order.shipping_cost }} {{ $t("global.LE") }}</td>
                      <td>{{ order.subtotal }} {{ $t("global.LE") }}</td>
                      <td>{{ order.total_amount }} {{ $t("global.LE") }}</td>
                      <td>
                        <a
                              href="javascript:void(0);"
                              class="btn btn-sm btn-info me-2"
                              data-bs-toggle="modal"
                              @click.prevent="setParentOrderDetails(order.orders)"
                              data-bs-target='#showParentOrderDetails'
                            >
                            <i class="fa fa-eye"></i>
                            </a>

                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else>
                    <tr>
                      <th class="text-center" colspan="7">
                        {{ $t("global.NoDataFound") }}
                      </th>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- /Table -->
          <!-- start Pagination -->
          <Pagination :data="orders" @pagination-change-page="getClienOrders" :limit="5">
            <template #prev-nav>
              <span>&lt; {{ $t("global.Previous") }}</span>
            </template>
            <template #next-nav>
              <span>{{ $t("global.Next") }} &gt;</span>
            </template>
          </Pagination>
          <!-- end Pagination -->
        </div>

        <div class="col-lg-4 col-sm-12">
          <div class="text-center card-box">
            <div class="text-left">
              <h4 class="header-title mb-4">{{ $t("global.Client") }}</h4>
            </div>
            <div class="member-card">
              <div class="avatar-xl member-thumb mb-2 mx-auto d-block star-div">
                <img
                  :src="
                    client.image ??
                    'https://ui-avatars.com/api/?name=' +
                      client.name +
                      '&amp;color=7F9CF5&amp;background=EBF4FF'
                  "
                  :onerror="
                    'https://ui-avatars.com/api/?name=' +
                    client.name +
                    '&amp;color=7F9CF5&amp;background=EBF4FF'
                  "
                  class="rounded-circle img-thumbnail"
                  alt="profile-image"
                />
                <i
                  class="fas fa-certificate text-primary small star-icon"
                  title="Featured Agent"
                ></i>
              </div>

              <div class="">
                <h5 class="font-18 mb-1">{{ client.name }}</h5>
              </div>

              <div class="mt-20">
                <ul class="list-inline row">
                  <li class="list-inline-item col-12 mx-0">
                    <h5>{{ $t("global.Email") }}</h5>
                    <p>{{ client.email }}</p>
                  </li>
                  <li class="list-inline-item col-6 mx-0">
                    <h5>{{ $t("global.Number of Orders") }}</h5>
                    <p>{{ client.order_numbers }}</p>
                  </li>

                  <li class="list-inline-item col-6 mx-0">
                    <h5>{{ $t("global.Phone") }}</h5>
                    <p>{{ client.phone }}</p>
                  </li>
                </ul>
              </div>
            </div>
            <!-- end membar card -->
          </div>
        </div>
      </div>
      <!-- /Table -->


        <!-- Edit Modal -->
        <div class="modal fade custom-modal" id="showParentOrderDetails">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">
                    {{ $t("global.Parent order details") }}
                    </h4>
                    <button
                    id="close-showParentOrderDetails"
                    type="button"
                    class="close print-button"
                    data-bs-dismiss="modal"
                    >
                    <span>&times;</span>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="modal-body row">
                    <div class="card bg-white projects-card">
                    <div class="card-body pt-0 table-responsive">
                        <table
                                class="table table-center table-hover mb-0 datatable"
                            >
                                <thead>
                                <tr>
                                    <th>{{$t('global.Order Number')}}</th>
                                    <th>{{ $t("global.Supplier Name") }}</th>
                                    <th>{{ $t("global.State / Area") }}</th>
                                    <!-- <th>{{ $t("global.Sub total") }}</th> -->
                                    <th>{{ $t("global.Total Amount") }}</th>
                                    <th>{{ $t("global.Show") }}</th>
                                </tr>
                                </thead>
                                <tbody v-if="Object.keys(sub_orders ?? []).length">
                                    <tr
                                    v-for="(order, index) in sub_orders"
                                    :key="order.id"
                                    >
                                    <td>{{ order.id }}</td>
                                    <td > <span :class="[order.supplier.is_our_supplier ? 'badge badge-primary' : '']">{{ order.supplier.name }}</span></td>
                                    <td>
                                        {{ order.area_name }}
                                    </td>
                                    <!-- <td>{{ order.subtotal }} {{ $t("global.LE") }}</td> -->
                                    <td>
                                        {{ order.subtotal }} {{ $t("global.LE") }}
                                    </td>
                                    <td>
                                        <a
                                            href="javascript:void(0);"
                                            class="btn btn-sm btn-info me-2"
                                            data-bs-toggle="modal"
                                            @click.prevent="setOrderDetails(order.products)"
                                            data-bs-target='#showOrderDetails'
                                            >
                                            <i class="fa fa-eye"></i>
                                            </a>

                                    </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                    <th class="text-center" colspan="7">
                                        {{ $t("global.NoDataFound") }}
                                    </th>
                                    </tr>
                                </tbody>
                            </table>

                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <!-- /Edit Modal -->


          <!-- Edit Modal -->
        <div class="modal fade custom-modal" id="showOrderDetails">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">
                    {{ $t("global.Order details") }}
                    </h4>
                    <button
                    id="close-showOrderDetails"
                    type="button"
                    class="close print-button"
                    data-bs-dismiss="modal"
                    >
                    <span>&times;</span>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="modal-body row">
                    <div class="card bg-white projects-card">
                    <div class="card-body pt-0 table-responsive">
                        <table
                                class="table table-center table-hover mb-0 datatable"
                            >
                                <thead>
                                <tr>
                                    <th>{{ $t("global.Product") }}</th>
                                    <th>{{ $t("global.Flavor") }}</th>
                                    <th>{{ $t("global.Size") }}</th>
                                    <th>{{ $t("global.Quantity") }}</th>
                                    <th>{{ $t("global.Returned Quantity") }}</th>
                                    <th>{{ $t("global.Measurement Unit Price") }}</th>
                                    <th>{{ $t("global.Measurement Unit") }}</th>
                                    <th>{{ $t("global.Received Amount") }}</th>
                                    <th>{{ $t("global.Refund Amount") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="price in products" :key="price.id">
                                    <td>{{ price.product.name}}</td>
                                    <td>{{ price.product.flavor_name }}</td>
                                    <td>{{ price.product.size.name }}</td>
                                    <td>{{ price.pivot.quantity }}</td>
                                    <td>{{ price.pivot.returned_quantity }}</td>
                                    <td>{{ price.pivot.price }}</td>
                                    <td>{{ price.pivot.measurement_type == 'sub' ?  price.product.sub_measure_name :price.product.main_measure_name }}</td>
                                    <td>{{ price.pivot.sub_total }}</td>
                                    <td>{{ price.pivot.refund_amount }}</td>
                                </tr>
                                </tbody>
                            </table>

                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <!-- /Edit Modal -->
    </div>
  </div>
</template>

<script>
import { computed, onMounted, provide, watch, ref } from "@vue/runtime-core";
import adminApi from "../../../api/adminAxios";
import { useI18n } from "vue-i18n";
export default {
  props: ["id"],
  setup(props) {
    const { t } = useI18n();
    const products = ref({});
    const sub_orders = ref({});
    const client = ref({});
    const orders = ref({});
    const getClient = async (id) => {
      adminApi.get(`/v1/dashboard/client_profile/${id}`).then((response) => {
        client.value = response.data.client;
      });
    };
    const getClienOrders = async (page=1) => {
      adminApi.get(`/v1/dashboard/client_orders?page=${page}&user_id=${props.id}`).then((response) => {
        orders.value = response.data.orders;
      });
    };

    onMounted(() => {
      getClient(props.id);
      getClienOrders()
    });

    const setOrderDetails = (data) => {
      products.value = data
    }

    const setParentOrderDetails = (data) => {
      sub_orders.value = data
    }

    return {
      client,
      getClienOrders,
      setParentOrderDetails,
      setOrderDetails,
      orders,products,sub_orders
    };
  },
};
</script>

<style lang="scss" scoped>
  .card-box {
    background-color: #a1ccff;
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 20px;
  }

.client-container {
  padding-bottom: 20px;
  .card {
    position: relative;
    .btn-custom {
      width: 30%;
    }
    .custom {
      border: 1px solid #d7d7d7;
      padding: 2px;
      border-radius: 5px;
      width: 45%;
    }
    .btn {
      color: #fff;
    }
    .active {
      background: none;
      border: none;
    }
  }

  .star-div {
    position: relative;
  }
  .star-icon {
    position: absolute;
    top: 4px;
    right: 2px;
    font-size: 16px;
    background-color: #f3f3f3;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    line-height: 20px;
    text-align: center;
  }
}
</style>
