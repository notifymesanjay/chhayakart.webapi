<template>
    <b-modal ref="my-modal" :title="modal_title" @hidden="$emit('modalClose')" scrollable no-close-on-backdrop no-fade static>
        <div slot="modal-footer">
            <b-button variant="primary" @click="$refs['dummy_submit'].click()" :disabled="isLoading">Save
                <b-spinner v-if="isLoading" small label="Spinning"></b-spinner>
            </b-button>
            <b-button variant="secondary" @click="hideModal">Cancel</b-button>
        </div>
        <form ref="my-form" @submit.prevent="saveRecord">
            <div class="row">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" required v-model="name" placeholder="Enter name">
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    <p class="text-muted">Please choose square image of larger than 350px*350px &amp; smaller than 550px*550px.</p>
                    <input type="file" name="image" id="image" v-on:change="handleFileUpload" ref="file_image" :required="!id" class="file-input">
                    <div class="file-input-div bg-gray-100" @click="$refs.file_image.click()" @drop="dropFile" @dragover="$dragoverFile" @dragleave="$dragleaveFile">
                        <template v-if="image && image.name !== ''">
                            <label>Selected file name:- {{ image.name }}</label>
                        </template>
                        <template v-else>
                            <label><i class="fa fa-cloud-upload fa-2x"></i></label>
                            <label>{{ __('drop_files_here_or_click_to_upload') }}</label>
                        </template>
                    </div>
                    <div class="row" v-if="image_url">
                        <div class="col-md-4">
                            <img class="custom-image" :src="image_url" title='Store Logo' alt='Category Image'/>
                        </div>
                    </div>
                </div>
                <div class="form-group" v-if="id">
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
import axios from 'axios';

export default {
    props: ['record'],
    data: function () {
        return {
            isLoading: false,
            image: "" ,
            id: this.record ? this.record.id : null,
            name: this.record ? this.record.name : null,
            image_url: this.record ? this.record.image_url : "" ,
            status: this.record ? this.record.status : null,
        };
    },
    computed: {
        modal_title: function () {
            let title = this.id ? "Edit" : "Add";
            title += " Brands";
            return title;
        },
    },
    created: function () {

    },
    methods: {
        showModal() {
            this.$refs['my-modal'].show()
        },
        hideModal() {
            this.$refs['my-modal'].hide()
        },
        dropFile(event) {
            event.preventDefault();
            this.$refs.file_image.files = event.dataTransfer.files;
            this.handleFileUpload(); // Trigger the onChange event manually
            // Clean up
            event.currentTarget.classList.add('bg-gray-100');
            event.currentTarget.classList.remove('bg-green-300');
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
                formData.append('id', this.id);
            }
            formData.append('name', this.name);
            formData.append('image', this.image);
            formData.append('status', this.status);

            let url = this.$apiUrl + '/products/brands/save';
            if (this.id) {
                url = this.$apiUrl + '/products/brands/update';
            }

            axios.post(url, formData).then(res => {
                let data = res.data;
                console.log("Response:");
                console.log(data);
                if (data.status === 1) {
                    this.$eventBus.$emit('recordSaved', data.message);
                    this.hideModal();
                } else {
                    vm.showError(data.message);
                    vm.isLoading = false;
                }
            }).catch(error => {
                vm.isLoading = false;
                if (error.request.statusText) {
                    this.showError(error.request.statusText);
                }else if (error.message) {
                    this.showError(error.message);
                } else {
                    this.showError("Something went wrong!");
                }
            });
        }
    },
    mounted() {
        this.showModal();
    }
}
</script>

<style scoped>

.image_preview {
    margin-top: 5px;
}
</style>
