<template>
    <div>
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Web Header</h3>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><router-link to="/dashboard">Dashboard</router-link></li>
                                <li class="breadcrumb-item active" aria-current="page">Web Header</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <section class="section">
                <form method="post" enctype="multipart/form-data" @submit.prevent="saveRecord">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Update Url Settings</h4>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="android_app_url">Android Application's URL</label>
                                    <input type="text" name="android_app_url" id="android_app_url" v-model="header.android_app_url" class="form-control" placeholder='Android App URL' />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="call_back_url">Front Website Url</label>
                                    <input type="text" name="call_back_url" id="call_back_url" v-model="header.call_back_url" class="form-control" placeholder='Front Website Url' />
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="common_meta_keywords">Common Meta Keywords</label>
                                    <textarea name="common_meta_keywords" id="common_meta_keywords" v-model="header.common_meta_keywords" class="form-control" placeholder="Common Meta Keywords" rows="4" cols="30">
                                    </textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="common_meta_description">Common Meta Description</label>
                                    <textarea name="common_meta_description" id="common_meta_description" v-model="header.common_meta_description" class="form-control" placeholder="Common Meta Description" rows="4" cols="30">
                                    </textarea>
                                </div>


                                <div class="form-group col-md-4">
                                    <label for="favicon">Favicon Icon </label>
                                    <div class="row" v-if="header.favicon">
                                        <div class="col-md-6">
                                            <img :src="$storageUrl + header.favicon" title='Favicon Icon' alt='Favicon Icon' style="max-width:100%" />
                                        </div>
                                    </div>
                                    <input type='file' name='favicon' id='favicon' v-on:change="handleFaviconUpload" ref="favicon" accept="image/*" class="form-control" />
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="web_logo">Web Logo</label>
                                    <div class="row" v-if="header.web_logo">
                                        <div class="col-md-6">
                                            <img :src="$storageUrl + header.web_logo" title='Web Logo' alt='Web Logo' style="max-width: 100%;" /><br>
                                        </div>
                                    </div>
                                    <input type='file' name='web_logo' id='web_logo' v-on:change="handleWebLogoUpload" ref="web_logo" accept="image/*" class="form-control" />
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="loading">Loading Icon</label>
                                    <div class="row" v-if="header.loading">
                                        <div class="col-md-6">
                                            <img :src="$storageUrl + header.loading" title='Loading Icon' alt='Loading Icon' style="max-width: 100%;" /><br>
                                        </div>
                                    </div>
                                    <input type='file' name='loading' id='loading' v-on:change="handleLoadingUpload" ref="loading" accept="image/*" class="form-control" />
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="color">Color Picker:</label>
                                    <input type="color" id="color" name='color' v-model="header.color" class="form-control">
                                    <div class="mt-2">
                                        <input class='form-check-input' id="show_color_picker_in_website" type='checkbox'
                                               v-model="header.show_color_picker_in_website"
                                               :checked="header.show_color_picker_in_website">
                                        <label for="show_color_picker_in_website">Show Color Picker In Website.</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <input type="submit" class="btn-primary btn" :value="__('update')" name="btn_update" v-if="$can('manage_header')">
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</template>
<script>
import axios from "axios";
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
    data: function () {
        return {
            editor : ClassicEditor,
            editorConfig: {},
            header: {},
            record: null
        }
    },
    created: function () {
        this.getHeader()
    },
    methods: {
        getHeader() {
            axios.get(this.$apiUrl + '/web_header').then((response) => {
                this.header = response.data.data.headerObject;
                this.record = response.data.data.header;
                this.record.map((item, index)=>{
                    this.header[item.variable] = item.value;
                });
            });
        },
        handleFaviconUpload: function (){
            this.header.favicon = this.$refs.favicon.files[0];
        },
        handleWebLogoUpload: function (){
            this.header.web_logo = this.$refs.web_logo.files[0];
        },
        handleLoadingUpload: function (){
            this.header.loading = this.$refs.loading.files[0];
        },
        saveRecord: function(){
            let headerObject = this.header;
            let formData = new FormData();
            for(let key in headerObject){
                formData.append(key, headerObject[key]);
            }
            let url = this.$apiUrl + '/web_header/save';
            let vm = this;
            axios.post(url, formData).then(res => {
                let data = res.data;
                if (data.status === 1) {
                    //this.showSuccess(data.message);
                    this.showMessage("success", data.message);
                    this.getHeader();
                    setTimeout(
                        function() {
                            vm.$swal.close();
                            vm.$router.push({path:'/web_header'})
                        }, 2000);
                }else{
                    vm.showError(data.message);
                    vm.isLoading = false;
                }
            }).catch(error => {
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
}
</script>
