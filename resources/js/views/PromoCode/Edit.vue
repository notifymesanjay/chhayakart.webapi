<template>
  <b-modal ref="my-modal" :title="modal_title" @hidden="$emit('modalClose')" size="lg" scrollable no-close-on-backdrop no-fade static>
    <div slot="modal-footer">
      <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="isLoading">
        Save
        <b-spinner v-if="isLoading" small label="Spinning"></b-spinner>
      </b-button>
      <b-button variant="secondary" @click="hideModal">Cancel</b-button>
    </div>
    <form ref="my-form" @submit.prevent="saveRecord">
      <div class="row">
        <div class="form-group col-md-6">
          <label>Promo Code</label>
          <input type="text" class="form-control" required v-model="promo_code" placeholder="Enter promo code." />
        </div>
        <div class="form-group col-md-6">
          <label>Message</label>
          <input type="text" class="form-control" required v-model="message" placeholder="Enter message." />
        </div>

        <div class="form-group col-md-6">
          <label>Start Date</label>
          <input type="date" class="form-control" required v-model="start_date" placeholder="Enter start date." />
        </div>
        <div class="form-group col-md-6">
          <label>End Date</label>
          <input type="date" class="form-control" required v-model="end_date" placeholder="Enter end date." />
        </div>

        <div class="form-group col-md-6">
          <label>No. of Users</label>
          <input type="number" class="form-control" required v-model="no_of_users" placeholder="Enter no. of users" />
        </div>
        <div class="form-group col-md-6">
          <label>Minimum Order Amount</label>
          <input type="number" class="form-control" required v-model="minimum_order_amount" placeholder="Enter minimum order amount." />
        </div>

        <div class="form-group col-md-6">
          <label>Discount</label>
          <input type="number" class="form-control" required v-model="discount" placeholder="Enter discount." />
        </div>
        <div class="form-group col-md-6">
          <label>Discount Type</label>
          <select class="form-control form-select" required v-model="discount_type">
            <option value="">Select discount type</option>
            <option value="percentage">Percentage</option>
            <option value="amount">Amount</option>
          </select>
        </div>

        <div class="form-group col-md-6">
          <label>Max Discount Amount</label>
          <input type="number" class="form-control" required v-model="max_discount_amount" placeholder="Enter max discount amount." />
        </div>
        <div class="form-group col-md-6">
          <label>Repeat Usage</label>
          <select class="form-control form-select" required v-model="repeat_usage">
            <option value="">Select </option>
            <option value="1">Allowed</option>
            <option value="0">Not Allowed</option>
          </select>
        </div>

        <div class="form-group col-md-6" v-if="repeat_usage === 1 || repeat_usage === '1'">
          <label>No. Of Repeat Usage</label>
          <input type="number" class="form-control" required v-model="no_of_repeat_usage" placeholder="Enter no. of repeat user" />
        </div>
          <div class="form-group">
              <label>Image</label>
              <p class="text-muted">Please choose square image of larger than 350px*350px &amp; smaller than 550px*550px.</p>
              <input type="file" name="image" v-on:change="handleFileUpload" ref="file_image" required v-if="!id" class="file-input">
              <input type="file" name="image" v-on:change="handleFileUpload" ref="file_image" v-if="id" class="file-input">


              <div class="file-input-div" @click="$refs.file_image.click()">
                  <label><i class="fa fa-cloud-upload fa-2x"></i></label>
                  <label>{{ __('drop_files_here_or_click_to_upload') }}</label>
              </div>
              <div class="row" v-if="image_url">
                  <div class="col-md-2">
                      <img class="custom-image" :src="image_url" title='Store Logo' alt='Promo Image'/>
                  </div>
              </div>

          </div>
        <div class="form-group col-md-12" v-if="id">
          <label>Status</label>
          <div class="col-md-9 text-left mt-1">
            <b-form-radio-group
              v-model="status"
              :options="[
                                { text: ' Deactivated', 'value': 0 },
                                { text: ' Activated', 'value': 1 },
                            ]"
              buttons
              button-variant="outline-primary"
              required
            ></b-form-radio-group>
          </div>
        </div>
      </div>
      <button ref="dummy_submit" style="display:none;"></button>
    </form>
  </b-modal>
</template>

<script>
import axios from "axios";

export default {
  props: ["record"],
  data: function () {
    return {
      isLoading: false,
      id: this.record ? this.record.id : null,
      promo_code: this.record ? this.record.promo_code : null,
      message: this.record ? this.record.message : null,
      start_date: this.record ? this.record.start_date : null,
      end_date: this.record ? this.record.end_date : null,
      no_of_users: this.record ? this.record.no_of_users : null,
      minimum_order_amount: this.record
        ? this.record.minimum_order_amount
        : null,
      discount: this.record ? this.record.discount : null,
      discount_type: (this.record && this.record.length !== 0) ? this.record.discount_type:"",
      max_discount_amount: this.record ? this.record.max_discount_amount : null,
      repeat_usage: this.record ? this.record.repeat_usage:"",
      no_of_repeat_usage: this.record ? this.record.no_of_repeat_usage : 0,
      status: this.record ? this.record.status : 1,
      image: this.record ? this.record.image_url : null,
      image_url: this.record ? this.record.image_url : null,
    };
  },
  computed: {
    modal_title: function () {
      let title = this.id ? "Edit" : "Add";
      title += " Promo Code";
      return title;
    },
  },
  methods: {
    showModal() {
      this.$refs["my-modal"].show();
    },
    hideModal() {
      console.log("hideModal");
      this.$refs["my-modal"].hide();
    },
    handleFileUpload() {
      this.image = this.$refs.file_image.files[0];
      this.image_url = URL.createObjectURL(this.image);
    },
    saveRecord: function () {
      let vm = this;
      this.isLoading = true;
      let formData = new FormData();
      if (this.id) {
        formData.append("id", this.id);
      }
      formData.append("promo_code", this.promo_code);
      formData.append("message", this.message);
      formData.append("start_date", this.start_date);
      formData.append("end_date", this.end_date);
      formData.append("no_of_users", this.no_of_users);
      formData.append("minimum_order_amount", this.minimum_order_amount);
      formData.append("discount", this.discount);
      formData.append("discount_type", this.discount_type);
      formData.append("max_discount_amount", this.max_discount_amount);
      formData.append("repeat_usage", this.repeat_usage);
      formData.append("no_of_repeat_usage", this.no_of_repeat_usage);
      formData.append("status", this.status);
      formData.append("image", this.image);

      let url = this.$apiUrl + "/promo_code/save";
      if (this.id) {
        url = this.$apiUrl + "/promo_code/update";
      }

      axios.post(url, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then((res) => {
          let data = res.data;
          console.log("Response:");
          console.log(data);
          if (data.status === 1) {
            console.log("Response : Success");
            /*vm.$eventBus.$emit('categorySaved', data.message);
                    vm.hideModal();*/
            this.$eventBus.$emit("PromoCodeSaved", data.message);
            this.hideModal();
            /*if(this.id) {
                        vm.$eventBus.$emit('categorySaved', data.message, data.plan);
                    }else{
                        vm.$eventBus.$emit('planCreated', data.message, data.plan);
                    }*/
          } else {
            vm.showError(data.message);
            vm.isLoading = false;
          }
        }).catch((error) => {
          vm.isLoading = false;
          if (error.request.statusText) {
              this.showError(error.request.statusText);
          }else if (error.message) {
              this.showError(error.message);
          } else {
              this.showError(__('something_went_wrong'));
          }
        });
    },
  },
  mounted() {
    this.showModal();
  },
};
</script>

<style scoped>

</style>
