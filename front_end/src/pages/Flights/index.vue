<template>
    <div class="q-pa-md">
        <q-card>
            <q-card-section>
                <div class="row justify-between">
                    <div class="col-auto text-h5">List of flights</div> 
                    <div class="col-xs-12 col-sm-3 col-md-2">
                        <q-btn color="blue-grey-8 full-width" icon="add" label="Add" @click="open()" > <q-tooltip>Add flights</q-tooltip> </q-btn>
                    </div>
                </div>
            </q-card-section>

            <q-card-section class="q-mt-md">
                <server-table ref="table" :url="'/flights/get-list'" :columns="columns" :loading="true" :pagination="pagination">                   
                    <template v-slot:actions="{row}">
                        <div class="text-center">
                            <div>
                                <q-fab text-color="blue-grey-13" icon="more_vert" direction="left" padding="xs" unelevated>
                                    <q-fab-action padding="5px" color="primary" icon="edit" @click="open(row)">
                                        <q-tooltip anchor="top middle" self="bottom middle">
                                            Edit
                                        </q-tooltip>
                                    </q-fab-action>
                                    <q-fab-action padding="5px" color="red" icon="delete" @click="itemDelete(row)">
                                        <q-tooltip anchor="top middle" self="bottom middle">
                                            Delete
                                        </q-tooltip>
                                    </q-fab-action>
                                </q-fab>
                                
                            </div>
                        </div>
                    </template>

                    <template v-slot:departure="{row}">
                        <div class="text-center">
                            {{ row.departure.name }}
                        </div>
                    </template>

                    <template v-slot:destination="{row}">
                        <div class="text-center">
                            {{ row.destination.name }}
                        </div>
                    </template>

                    <template v-slot:airline="{row}">
                        <div class="text-center">
                            {{ row.airline.name }}
                        </div>
                    </template>
                    
                    <template v-slot:no-data>
                        <div class="text-center">
                            No records
                        </div>
                    </template> 
                </server-table>
            </q-card-section>
        </q-card>
        <formulario :form="form" :close="close" :title_form="title_form" v-if="show_form" /> 
    </div>
</template>

<script>
    import Form  from './components/Form.vue';
    export default {
        name : 'Flights',
        components : {
            'formulario' : Form
        },
        data(){
            return {
                // DATATABLE
                columns : [   
                    { label: 'Code', name: 'code', align: 'left', field:'code', filterable: true },
                    { label: 'Type', name: 'type', align: 'left', field:'type', sortable: true, filterable: true },

                    { label: 'Departure', name: 'departure', align: 'left', field:'departure', sortable: true, filterable: true },
                    { label: 'Destination', name: 'destination', align: 'left', field:'destination', sortable: true, filterable: true },
                    { label: 'Airline', name: 'airline', align: 'left', field:'airline', sortable: true, filterable: true },

                    { label: 'Actions', name: 'actions', field:'actions',  align: 'left' },
                ],
                pagination: { sortBy: 'desc', descending: true, page: 1, rowsPerPage: 5, rowsNumber: 10 }, 

                //form
                form : null,
                title_form : null,
                show_form : false,
            }
        },
        mounted(){

        }, 
        methods : {
            open(item){ 
                this.title_form = (item !== undefined) ? 'Edit flight' : 'New flight'; 
                this.form = Object.assign({}, item);  
                this.show_form = true;
            },
            close(){
                this.form = null;
                this.show_form = false;
                this.title_form = null;
                this.$refs.table.refresh();
            },
            async itemDelete(item){
                let question = await this.$question_swal('Do you want to delete the record?');
                if(!question){return};

                this.$q.loading.show();
                const {data} = await this.$axios.post('/flights/delete', item);
                this.$q.loading.hide();
                
                if(!data.status){ 
                    this.dialog = true;
                    this.$q.notify({ position: 'top', type:'negative', message:data.msg });
                    return false;
                }

                this.$q.notify({ position:'top', type:'positive', message:'Success' });

                this.close();
            }
        }
    }
</script> 