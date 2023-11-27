import adminApi from "../api/adminAxios";
const BASE_URL = `/v1/dashboard/offers`;
export default {
    store(formValue) {
        return adminApi.post(BASE_URL, formValue);
    },
    update(formValue) {
        return adminApi.post(`${BASE_URL}/update`, formValue);
    },
    delete(id) {
        return adminApi.delete(`${BASE_URL}/${id}`);
    },
    getProducts() {
        return adminApi.get(`/v1/dashboard/offers/products`);
    },
    getPage(pageNo, pageSize, text) {
        return adminApi.get(`${BASE_URL}?page=${pageNo}&page_size=${pageSize}&text=${text}`);
    },
}
