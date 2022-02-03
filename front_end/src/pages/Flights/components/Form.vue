<template>
    <q-dialog v-model="dialog" persistent transition-show="scale" transition-hide="scale">
        <q-card style="min-width: 400px">
            <q-card-section>
                <div class="text-h5">{{ title_form }}</div>
            </q-card-section>
            <q-card-section>
                <ValidationObserver ref="form">
                    <div class="row">   
                        <div class="col-12 q-pa-xs">
                            <ValidationProvider rules="required" v-slot="{ errors, invalid, validated }"> 
                                <q-input   
                                    outlined
                                    v-model="form.code"  
                                    label="Code" 
                                    :dense="true" 
                                    :error="invalid && validated"
                                    :error-message="errors[0]" 
                                />
                            </ValidationProvider>
                        </div>   
                        <div class="col-12 q-pa-xs">
                            <ValidationProvider rules="required" v-slot="{ errors, invalid, validated }">
                                <q-select 
                                    outlined 
                                    v-model="form.type" 
                                    :options="types" 
                                    label="Type" 
                                    :dense="true" 
                                    :error="invalid && validated"
                                    :error-message="errors[0]" 
                                />
                            </ValidationProvider>
                        </div>

                        <div class="col-12 q-pa-xs">
                            <ValidationProvider rules="required" v-slot="{ errors, invalid, validated }">
                                <q-select 
                                    outlined 
                                    v-model="form.departure" 
                                    :options="airports" 
                                    option-label="name" 
                                    label="Departure"
                                    :dense="true" 
                                    :error="invalid && validated"
                                    :error-message="errors[0]" 
                                />
                            </ValidationProvider>
                        </div>

                        <div class="col-12 q-pa-xs">
                            <ValidationProvider rules="required" v-slot="{ errors, invalid, validated }">
                                <q-select 
                                    outlined 
                                    v-model="form.destination" 
                                    :options="airports" 
                                    option-label="name" 
                                    label="Destination"
                                    :dense="true" 
                                    :error="invalid && validated"
                                    :error-message="errors[0]" 
                                />
                            </ValidationProvider>
                        </div>

                        <div class="col-12 q-pa-xs">
                            <ValidationProvider rules="required" v-slot="{ errors, invalid, validated }">
                                <q-select 
                                    outlined 
                                    v-model="form.airline" 
                                    :options="airlines" 
                                    option-label="name"
                                    label="Airline"
                                    :dense="true" 
                                    :error="invalid && validated"
                                    :error-message="errors[0]" 
                                />
                            </ValidationProvider>
                        </div>
                    </div> 

                </ValidationObserver>
            </q-card-section>
            <q-card-actions align="center"> 
                <q-btn color="red" label="Cancel" class="q-mr-sm" @click="close" />
                <q-btn color="primary" label="Save" @click="save" />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>

<script>
    export default {
        name : 'Form',
        props : {
            form : {
                type : Object,
                default : {}
            },
            close : {
                type : Function
            },
            title_form : {
                type: String,
                default : 'New flight'
            }
        },
        data(){
            return {
                dialog : true,
                types : [ 'PASSENGER', 'FREIGHT' ],
                airlines : [],
                airports : []
            }
        },
        mounted(){
            this.getCatalogs();
        },
        methods:{
            
            async getCatalogs(){
                try {
                    
                    this.$q.loading.show();
                    const {data} = await this.$axios.get('/flights/get-catalogs');
                    this.$q.loading.hide();
                    this.airlines = data.airlines;
                    this.airports = data.airports;

                } catch (error) {           
                    this.close();
                    this.$q.notify({ position: 'top', type: 'negative', message: 'Sorry!' });
                }
            },

            async save(){
                try {

                    const validate = await this.$refs.form.validate();
                    if(!validate){ return false; }

                    this.dialog = false;

                    const form = new FormData();
                    form.append('form', JSON.stringify(this.form));

                    this.$q.loading.show();
                    const {data} = await this.$axios.post('/flights/store', form);
                    this.$q.loading.hide();

                    if(!data.status){ 
                        this.dialog = true;
                        this.$q.notify({ position: 'top', type:'negative', message:data.msg });
                        return false;
                    }

                    this.$q.notify({ position:'top', type:'positive', message:'Success' });
                    this.close();
                } catch (error) {                    
                    this.close();
                    this.$q.loading.hide();
                    this.$q.notify({ position: 'top', type: 'negative', message: 'Sorry!' });
                }
            }
        }
    }
</script>

<style>

</style>