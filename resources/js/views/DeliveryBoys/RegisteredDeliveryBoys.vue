<template>
    <div>
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Delivery Boys</h3>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><router-link to="/dashboard">Dashboard</router-link></li>
                                <li class="breadcrumb-item active" aria-current="page">Delivery Boys</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <section class="section">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Delivery Boys</h4>
                        <span class="pull-right">
                            <button class="btn btn-primary"  @click="create_new=true" v-if="$can('delivery_boy_create')">Add</button>
                        </span>
                    </div>
                    <div class="card-body">

                        <b-row class="mb-2">
                            <b-col md="3" offset-md="8">
                                <h6 class="box-title">Search</h6>
                                <b-form-input
                                    id="filter-input"
                                    v-model="filter"
                                    type="search"
                                    placeholder="Search"
                                ></b-form-input>
                            </b-col>
                            <b-col md="1" class="text-center">
                                <button class="btn btn-primary btn_refresh" v-b-tooltip.hover :title="__('refresh')" @click="getDeliveryBoys()">
                                    <i class="fa fa-refresh" aria-hidden="true"></i>
                                </button>
                            </b-col>
                        </b-row>

                        <b-table
                            :items="deliveryBoys"
                            :fields="fields"
                            :current-page="currentPage"
                            :per-page="perPage"
                            :filter="filter"
                            :filter-included-fields="filterOn"
                            :sort-by.sync="sortBy"
                            :sort-desc.sync="sortDesc"
                            :sort-direction="sortDirection"
                            :bordered="true"
                            :busy="isLoading"
                            stacked="md"
                            show-empty
                            small>

                            <template #table-busy>
                                <div class="text-center text-black my-2">
                                    <b-spinner class="align-middle"></b-spinner>
                                    <strong>{{ __('loading') }}...</strong>
                                </div>
                            </template>
                            <template #head(balance)="row">
                                {{'Balance ('+$currency+')' }}
                            </template>
                            <template #cell(status)="row">
<!--                                <span v-if="row.item.status === 1" class="badge bg-success">Active</span>
                                <span v-else class="badge bg-danger">Deactive</span>-->

                                <label v-if="row.item.status === 0" class='badge bg-primary'>Registered</label>
                                <label v-else-if="row.item.status === 1" class='badge bg-success'>Active</label>
                                <label v-else-if="row.item.status === 2" class='badge bg-warning'>Not-Approved</label>
                                <label v-else-if="row.item.status === 3" class='badge bg-danger'>Deactive</label>
                                <label v-else-if="row.item.status === 7" class='badge bg-danger'>Removed</label>
                            </template>

                            <template #cell(is_available)="row">
                                <span v-if="row.item.status === 1" class="badge bg-success">Yes</span>
                                <span v-else class="badge bg-danger">No</span>
                            </template>

                            <template #cell(actions)="row">
                                <!--<router-link :to="`/subcategories/${row.item.id}`" class="btn btn-primary">View Subcategories</router-link>-->

                                <button class="btn btn-sm btn-success"
                                        type="button"
                                        @click="updateStatus(row.index,row.item.id, 1)" v-if="$can('delivery_boy_update')" v-b-tooltip.hover title="Change Status">
                                    Approved
                                </button>
                                <button class="btn btn-sm btn-danger"
                                        type="button"
                                        @click="updateStatus(row.index,row.item.id, 2)" v-if="$can('delivery_boy_update')" v-b-tooltip.hover title="Change Status">
                                    Reject
                                </button>

<!--                                <button class="btn btn-sm btn-primary" @click="edit_record = row.item" v-if="$can('delivery_boy_update')" v-b-tooltip.hover :title="__('edit')"><i class="fa fa-pencil"></i></button>
                                <button class="btn btn-sm btn-danger" @click="deleteDeliveryBoys(row.index,row.item.id)" v-if="$can('delivery_boy_delete')" v-b-tooltip.hover :title="__('delete')"><i class="fa fa-trash"></i></button>-->
                            </template>

                        </b-table>

                        <b-row>
                            <b-col  md="2" class="my-1">
                                <b-form-group
                                    :label="__('per_page')"
                                    label-for="per-page-select"
                                    label-align-sm="right"
                                    label-size="sm"
                                    class="mb-0">
                                    <b-form-select
                                        id="per-page-select"
                                        v-model="perPage"
                                        :options="pageOptions"
                                        size="sm"
                                        class="form-control form-select"
                                    ></b-form-select>
                                </b-form-group>
                            </b-col>
                            <b-col  md="4" class="my-1" offset-md="6">
                                <b-pagination
                                    v-model="currentPage"
                                    :total-rows="totalRows"
                                    :per-page="perPage"
                                    align="fill"
                                    size="sm"
                                    class="my-0"
                                ></b-pagination>
                            </b-col>
                        </b-row>

                    </div>
                </div>
            </section>
        </div>
        <!-- Add / Edit -->
        <app-edit-record
            v-if="create_new || edit_record"
            :record="edit_record"
            @modalClose="hideModal()"
        ></app-edit-record>
    </div>
</template>
<script>
import EditRecord from './Edit';
export default {
    components: {
        'app-edit-record' : EditRecord,
    },
    data: function() {
        return {
            fields: [
                { key: 'id', label: 'ID', sortable: true, sortDirection: 'desc' },
                { key: 'name', label: 'Name', sortable: true, class: 'text-center' },
                { key: 'mobile', label: 'Mobile', sortable: true, class: 'text-center' },
                { key: 'address', label: 'Address', sortable: true,  class: 'text-center' },
                { key: 'bonus', label: 'Bonus(%)', sortable: true, class: 'text-center' },
                { key: 'balance', label: 'Balance', sortable: true,  class: 'text-center' },
                { key: 'other_payment_information', sortable: true, label: 'Other Payment Information',  class: 'text-center' },
                { key: 'status', label: 'Status', sortable: true, class: 'text-center' },
                { key: 'is_available', label: 'Available',  class: 'text-center' },
                { key: 'actions', label: 'Action' }
            ],
            totalRows: 1,
            currentPage: 1,
            perPage: this.$perPage,
            pageOptions: this.$pageOptions,
            sortBy: '',
            sortDesc: false,
            sortDirection: 'asc',
            filter: null,
            filterOn: [],
            page: 1,

            isLoading: false,
            sectionStyle : 'style_1',
            max_visible_units : 12,
            max_col_in_single_row : 3,
            create_new : null,
            edit_record : null,

            categories: null,
            products: null,

            deliveryBoys: [],
            filterStatus : 0
        }
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter(f => f.sortable)
                .map(f => {
                    return { text: f.label, value: f.key }
                })
        }
    },
    mounted() {
        // Set the initial number of items
        this.totalRows = this.deliveryBoys.length
    },
    watch: {
        $route(to, from) {
            this.showCreateModal();
        }
    },
    created: function() {
        this.showCreateModal();
        this.$eventBus.$on('deliveryBoysSaved', (message) => {
            //this.showSuccess(message);
            this.showMessage("success", message);
            this.getDeliveryBoys();
            this.create_new = null;
        });
        this.getDeliveryBoys();
    },
    methods: {
        getDeliveryBoys(){
            this.isLoading = true
            /*console.log("hello");*/
            axios.get(this.$apiUrl + '/delivery_boys',{
                params: {
                    filterStatus: this.filterStatus
                }
            })
                .then((response) => {
                    this.isLoading = false
                    //console.log("response =>",response.data.data);
                    this.deliveryBoys = response.data.data;
                    this.totalRows = this.deliveryBoys.length
                });
        },
        updateStatus(index, id, selectedStatus){
            this.$swal.fire({
                title: "Are you Sure?",
                text: "You want be able to revert this",
                confirmButtonText: "Yes, Sure",
                cancelButtonText: "Cancel",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#37a279',
                cancelButtonColor: '#d33',
            }).then(async result => {
                if (result.value) {
                    let remarks = "";
                    if (selectedStatus === 2) {
                        const {value: text} = await this.$swal.fire({
                            title: 'Remarks',
                            input: 'textarea',
                            /*inputLabel: 'Remarks',*/
                            inputPlaceholder: 'Type your remarks here...',
                            inputAttributes: {
                                'aria-label': 'Type your remarks here'
                            },
                            confirmButtonText: "Submit",
                            cancelButtonText: "Cancel",
                            showCancelButton: true,

                            inputValidator: (value) => {
                                return new Promise((resolve) => {
                                    if (value !== '') {
                                        resolve()
                                    } else {
                                        resolve('The Remarks field is required')
                                    }
                                })
                            }
                        })
                        if (text) {
                            remarks = text;
                        }
                    }
                    if (selectedStatus === 1 || (selectedStatus === 2 && remarks !== "") ){
                        this.isLoading = true
                        let postData = {
                            id : id,
                            status: selectedStatus,
                            remark : remarks
                        }
                        console.log("postData =>", postData);

                        axios.post(this.$apiUrl + '/delivery_boys/update-status',postData)
                            .then((response) => {
                                this.isLoading = false
                                let data = response.data;
                                //this.records.splice(index, 1)
                                this.getDeliveryBoys();
                                //this.showSuccess(data.message)
                                this.showMessage('success', data.message);
                            });
                    }
                }
            });
        },



        deleteDeliveryBoys(index, id){
            this.$swal.fire({
                title: "Are you Sure?",
                text: "You want be able to revert this",
                confirmButtonText: "Yes, Sure",
                cancelButtonText: "Cancel",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#37a279',
                cancelButtonColor: '#d33',
            }).then(result => {

                if (result.value) {
                    this.isLoading = true
                    let postData = {
                        id : id
                    }
                    axios.post(this.$apiUrl + '/delivery_boys/delete',postData)
                        .then((response) => {
                            this.isLoading = false
                            this.deliveryBoys.splice(index, 1)
                            this.showSuccess(response.data.message)
                        });
                }
            });
        },
        showCreateModal(){
            let create = this.$route.params.create;
            if(create){
                this.create_new = true;
            }
        },
        hideModal() {
            this.create_new = false
            this.edit_record = false
            this.$router.push({path: '/delivery_boys'});
        },
    }
};
</script>
