<template>
  <div
    class="client-container"
    :class="['page-wrapper', this.$i18n.locale == 'ar' ? 'page-wrapper-ar' : '']"
  >
    <div class="content container-fluid">
      <!-- Page Header -->
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col">
            <h3 class="page-title">{{ $t("global.Clients") }}</h3>
            <ul class="breadcrumb">
              <li class="breadcrumb-item">
                <router-link :to="{ name: 'dashboard' }">
                  {{ $t("dashboard.Dashboard") }}
                </router-link>
              </li>
              <li class="breadcrumb-item active">{{ $t("global.Clients") }}</li>
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
              <div class="card-header pt-0">
                <div class="row justify-content-between">
                  <div class="col-md-4 col-sm-12">
                    <label for="">{{ $t("global.Search") }}:</label>
                    <input type="search" v-model="search" class="form-control" />
                  </div>

                  <div class="col-md-4 col-sm-12 ">
                    <a
                        href="javascript:void(0);"
                        class="btn btn-sm btn-info me-2 mt-4"
                        data-bs-toggle="modal"
                        @click.prevent="notification_type='toAll'"
                        data-bs-target='#sendNotification'
                      >
                        {{$t('global.Send Notification to All Clients')}}<i class="fa fa-comment-dots"></i>
                      </a>
                      <a
                            class="btn btn-sm btn-secondary mx-2 mt-4"
                            @click.prevent="printSection()"
                            ><i class="fa fa-print"></i> </a
                        >
                  </div>

                </div>
                <div class="row justify-content-between">
                    <div class=" col-6 row">
                        <div class="form-group col-6">
                            <label for="">{{ $t('global.From') }}</label>
                            <input type="date" v-model="from_date" class="form-control" @change="getClients">
                        </div>
                        <div class="form-group col-6">
                            <label for="">{{ $t('global.To') }}</label>
                            <input type="date" v-model="to_date" class="form-control" @change="getClients">
                        </div>
                    </div>
                    <div class="form-group col-3">
                        <label for="">{{ $t('global.Filter Clients') }}</label>
                        <select v-model="product_filter" class="form-control" @change="getClients">
                            <option value="">{{ $t('global.All Clients') }}</option>
                            <!-- <option value="most_bought_quantity">{{ $t('global.Most Bought Quantity') }}</option>
                            <option value="least_bought_quantity">{{ $t('global.Least Bought Quantity') }}</option> -->
                            <option value="most_amount">{{ $t('global.Most Amount') }}</option>
                            <option value="least_amount">{{ $t('global.Least Amount') }}</option>
                        </select>
                    </div>
                    <div class="form-group col-3">
                        <label for="">{{ $t('global.Paginate Clients') }}</label>
                        <select v-model="pagination" class="form-control" @change="getClients">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                        </select>
                    </div>

                </div>
              </div>
              <div class="table-responsive">
                <table class="table mb-0" id="div">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>{{ $t("global.Name") }}</th>
                      <th>{{ $t("global.Phone") }}</th>
                      <th>{{ $t("global.Email") }}</th>
                      <th>{{ $t("global.Total Amount") }}</th>
                      <!-- <th>{{ $t("global.bought_quantity") }}</th> -->
                      <th>{{ $t("global.Date") }}</th>
                      <th>{{ $t("global.Status") }}</th>
                      <th>{{ $t("global.Show") }}</th>
                    </tr>
                  </thead>
                  <tbody v-if="Object.keys(clients.data ?? []).length">
                    <tr v-for="(client, index) in clients.data" :key="client.id">
                      <td>{{ index + 1 }}</td>
                      <td>{{ client.name }}</td>
                      <td>{{ client.phone }}</td>
                      <td>{{ client.email }}</td>
                      <td>{{ client.total_amount }}</td>
                      <!-- <td>{{ client.bought_quantity }}</td> -->
                      <td>{{ printDate(client.created_at) }}</td>
                      <td>
                        <button
                          class="active"
                          href="#"
                          @click="
                            toggleActivation(
                              client.id,
                              client.name,
                              client.status,
                              index
                            )
                          "
                        >
                          <span
                            :class="[
                              parseInt(client.status)
                                ? 'text-success hover'
                                : 'text-danger hover',
                            ]"
                            >{{
                              parseInt(client.status)
                                ? $t("global.Active")
                                : $t("global.Inactive")
                            }}</span
                          >
                        </button>
                      </td>
                      <td>
                        <router-link :to="{name:'client_profile',params:{id:client.id,lang:this.$i18n.locale}}" class="btn btn-success btn-sm">
                            <i class="fa fa-eye"></i>
                        </router-link>
                        <a
                              href="javascript:void(0);"
                              class="btn btn-sm btn-info me-2"
                              data-bs-toggle="modal"
                              @click.prevent="client_notification_id = client.id;notification_type='client'"
                              data-bs-target='#sendNotification'
                            >
                              <i class="fa fa-comment-dots"></i>
                            </a>
                        <a
                              href="javascript:void(0);"
                              class="btn btn-sm btn-primary me-2"
                              data-bs-toggle="modal"
                              @click.prevent="shops = client.client.shops"
                              data-bs-target='#shopsDetails'
                            >
                              <i class="fa fa-store"></i>
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
      <!-- /Table -->
      <!-- start Pagination -->
      <Pagination :data="clients" @pagination-change-page="getClients">
        <template #prev-nav>
          <span>&lt; {{ $t("global.Previous") }}</span>
        </template>
        <template #next-nav>
          <span>{{ $t("global.Next") }} &gt;</span>
        </template>
      </Pagination>
      <!-- end Pagination -->
    </div>
  </div>



     <!-- Edit Modal -->
     <div class="modal fade custom-modal" id="sendNotification">
          <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
              <!-- Modal Header -->
              <div class="modal-header">
                <h4 class="modal-title">
                  {{ $t("global.Send Notification") }}
                </h4>
                <button
                  id="close-sendNotification"
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
                  <div class="card-body pt-0">

                      <div class="form-group col-12">
                        <label for="title">{{$t('global.Title')}}</label>
                        <input type="text" :class="['form-control',errors.title?'is-invalid':'']" v-model="title">
                        <span v-if="errors.title" class="text-danger">{{errors.title}}</span>
                      </div>
                      <div class="form-group col-12">
                        <label for="title">{{$t('global.Image')}}</label>
                        <input type="file" :class="['form-control',errors.image?'is-invalid':'']" id="file_upload">
                        <span v-if="errors.image" class="text-danger">{{errors.image}}</span>
                      </div>
                      <div class="form-group col-12">
                        <label for="body">{{$t('global.Message')}}</label>
                        <textarea :class="['form-control' ,errors.notification ? 'is-invalid':'' ]" id="body" cols="30" rows="10" v-model="notification"></textarea>
                        <span v-if="errors.notification" class="text-danger">{{errors.notification}}</span>
                      </div>
                      <div class="form-group col-12">
                        <button class="btn btn-warning" @click.prevent="sendNotification">
                            {{$t('global.Submit')}}
                        </button>
                      </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
    </div>
    <!-- /Edit Modal -->

    <!-- Edit Modal -->
    <div class="modal fade custom-modal" id="shopsDetails">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">
                    {{ $t("global.Shops details") }}
                    </h4>
                    <button
                    id="close-shopsDetails"
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
                                    <th>{{ $t("global.Name") }}</th>
                                    <th>{{ $t("global.State / Area") }}</th>
                                    <th>{{ $t("global.Address") }}</th>
                                    <th>{{ $t("global.Phone") }}</th>
                                    <th>{{ $t("global.Land Line") }}</th>
                                    <th>{{ $t("global.Feature Sign") }}</th>
                                    <th>{{ $t("global.Date") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="shop in shops" :key="shop.id">
                                    <td>{{ shop.name}}</td>
                                    <td>{{ shop.area_name }}</td>
                                    <td>{{ shop.address }}</td>
                                    <td>{{ shop.phone}}</td>
                                    <td>{{ shop.land_line }}</td>
                                    <td>{{ shop.feature_sign }}</td>
                                    <td>{{ shop.created_at }}</td>
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
</template>

<script>
import { computed, onMounted, provide, watch,ref } from "@vue/runtime-core";
import adminApi from "../../../api/adminAxios";
import { useI18n } from "vue-i18n";
import { useRouter } from 'vue-router'
import { notify } from "@kyvg/vue3-notification";

export default {
  setup() {
    const {t,locale} = useI18n()
    const errors = ref({})
    const shops = ref({})
    const clients = ref({})
    const router = useRouter()
    const debounce = ref('')
    const title = ref('')
    const notification = ref('')
    const notification_type = ref('')
    const from_date = ref('')
    const to_date = ref('')
    const product_filter = ref('')
    const pagination = ref(25)
    const client_notification_id = ref(0)
    const search = ref('')
    const getClients = async(page = 1 ) => {
      adminApi.get(`/v1/dashboard/clients?page=${page}&search=${search.value}&from_date=${from_date.value}&to_date=${to_date.value}&product_filter=${product_filter.value}&pagination=${pagination.value}`).then((response) => {
        clients.value = response.data.clients;
      })
    }
    let printSection = () => {
          $("#div").printThis({});
        }

    onMounted(() => {
      getClients()
    })

    function toggleActivation(id, name, active, index) {
      Swal.fire({
        title: `${
          active ? t("global.AreYouSureInactive") : t("global.AreYouSureActive")
        }  (${name})`,
        text: `${t("global.YouWontBeAbleToRevertThis")}`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: t("global.Yeas"),
        cancelButtonText: t("global.No"),
      }).then((result) => {
        if (result.isConfirmed) {
          httpToggleActivation(id, active, index);
        }
      });
    }

    function httpToggleActivation(id, active, index) {
        adminApi.get(`/v1/dashboard/activationClient/${id}`)
        .then((res) => {
          Swal.fire({
            icon: "success",
            title: `${
              active ? t("global.InactiveSuccessfully") : t("global.ActiveSuccessfully")
            }`,
            showConfirmButton: false,
            timer: 1500,
          });
          clients.value['data'][index]["status"] = active == 0 ? 1 : 0;
        })
        .catch((err) => {
          console.log(err.response);
          Swal.fire({
            icon: "error",
            title: `${t("global.ThereIsAnErrorInTheSystem")}`,
            text: `${t("global.YouCanNotModifyThisSafe")}`,
          });
        });
    }
    watch(search,()=>{
      // clear timeout variable
      clearTimeout(debounce.value);
      debounce.value = setTimeout(() => {
          getClients();
        }, 500);
    })


    const sendNotification = async () => {
      errors.value ={}
      let data = new FormData();
      data.append('title',title.value)
      data.append('user_id',client_notification_id.value)
      data.append('notification',notification.value)
      data.append('image',$('#file_upload').prop('files')[0]??'')
      let uri = notification_type.value == 'client' ? '/v1/dashboard/sendNotificationToClient' : '/v1/dashboard/sendNotificationToAllClients'
        adminApi.post(uri,data).then((response) => {
            $('#close-sendNotification').click()
            Swal.fire({
                icon: "success",
                title: `تم الارسال بنجاح <i class="fas fa-check-circle"></i>`,
                showConfirmButton: false,
                timer: 1500,
              });
              title.value=''
              client_notification_id.value=0
              notification.value=''
        }).catch((e) => {
          if(e.response &&e.response.data.title&& e.response.data.title[0])
            errors.value['title']= e.response.data.title[0]
          if(e.response &&e.response.data.notification&& e.response.data.notification[0])
            errors.value['notification']= e.response.data.notification[0]
          if(e.response && e.response.data.user_id && e.response.data.user_id[0])
            errors.value['user_id']= e.response.data.user_id[0]

        })
    }

    function printDate(date){
        return new Date(date).toDateString();
    }



    return {
      getClients,
      toggleActivation,
      printDate,
      sendNotification,
      product_filter,
      shops,
      from_date,
      to_date,
      printSection,
      pagination,
      client_notification_id,title,notification,errors,
      clients,notification_type,search
    };
  },
};
</script>

<style lang="scss" scoped>
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
}
</style>
