<template>
    <div>
        <div class="page-heading">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Bulk Upload</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><router-link to="/dashboard">{{ __('dashboard') }}</router-link></li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Bulk Upload
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 order-md-1 order-last">
                    <div class="card">
                        <div class="card-header">
                            <h4>Product Bulk Upload Form</h4>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <p>Always download and use new sample file if you did updated admin panel version.</p>
                                <p>Read and follow instructions carefully before proceed.</p>
                            </div>
                            <div class="row">
                                <!-- form start -->
                                <form method="post" ref="my-form" @submit.prevent="saveRecord" enctype="multipart/form-data">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="upload_file">CSV File</label>
                                            <input type="file" name="upload_file" id="upload_file" v-on:change="handleFileUpload" ref="file_csv" class="form-control" required accept=".csv"/>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary" id="submit_btn" name="btnAdd" :disabled="isLoading">
                                            <i class="fa fa-upload" v-if="!isLoading"></i> {{ __('upload') }}
                                            <b-spinner v-if="isLoading" small label="Spinning"></b-spinner>
                                        </button>
                                        <button type="reset" class="btn btn btn-secondary">
                                            <i class="fa fa-undo" aria-hidden="true"></i> {{ __('clear') }}
                                        </button>
                                        <a :href="sampleFileurl" class='btn btn-info' download> <em class='fa fa-download'></em> Download Sample File</a>
                                        <a :href="instructionsFileurl" class='btn btn-warning' download> <em class='fa fa-download'></em> Download Instructions</a>
                                    </div>
                                </form>
                                <!-- form end -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";

export default {
    data: function () {
        return {
            isLoading: false,
            /*sampleFileurl : this.$storageUrl + 'products/sample-file/products.csv',
            instructionsFileurl : this.$storageUrl + 'products/sample-file/products.txt',*/

            sampleFileurl : this.$baseUrl + '/sample-file/products.csv',
            instructionsFileurl : this.$baseUrl + '/sample-file/products.txt',
            file: null,
        }
    },
    created: function () {

    },
    mounted() {

    },
    methods: {
        handleFileUpload() {
            this.file = this.$refs.file_csv.files[0];
        },
        saveRecord: function(){
            let vm = this;
            this.isLoading = true;
            let formData = new FormData();
            formData.append('file', this.file);
            let url = this.$apiUrl + '/products/bulk_upload';
            axios.post(url, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(res => {
                let data = res.data;
                if (data.status === 1) {
                    //this.showSuccess(data.message);
                    this.showMessage("success", data.message);
                    this.$refs.file_csv.value=null;
                    this.file = null;
                    vm.isLoading = false;
                }else{
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
    }
};
</script>

