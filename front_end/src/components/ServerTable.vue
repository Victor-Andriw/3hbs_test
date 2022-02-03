<template>
    <q-table :data="data" :columns="columns" :row-key="rowKey" :loading="(load != undefined) ? load : false " @request="onRequest" :pagination.sync="options" hide-pagination class="full-width no-shadow" separator="horizontal">
        
        <template v-slot:top>
            <div class="row">
                <div class="col-12">
                    <label for="">{{ lang.limit || 'Registros' }}</label>
                    <q-select dense v-model="options.rowsPerPage" :options="options.perPages" @input="changePerPage"/>
                </div>
            </div>
        </template>

        <template v-slot:header="props">
            <q-tr :props="props">
                <q-th v-for="col in props.cols" :key="col.name" :props="props" class="text-weight-bolder">
                    {{ col.label }}
                </q-th>
            </q-tr>
            <q-tr v-if="filterColumns.length > 0" :props="props" >
                <template v-for="column in columns" >
                    <q-th :key="column.name">
                        <slot :name="'h-'+column.name" :column="column" :handler="onQuery">
                            <q-input 
                                dense
                                outlined
                                :type="column.type || 'text'" 
                                v-if="column.filterable" 
                                :label="column.label" 
                                v-model="column.model" 
                                @input="(value) => onQuery(value,column.name)" />
                        </slot>
                        
                    </q-th>
                </template>
            </q-tr>                    
        </template>

        <template v-slot:body="props">
            <template v-if="$slots.body || $scopedSlots.body">
                <slot name="body" :props="props" ></slot>
            </template>
            <template v-else>
                <q-tr :props="props">
                    <template v-if="$slots['body-cell'] || $scopedSlots['body-cell']">
                        <slot name="body-cell" :props="props"></slot>
                    </template>
                    <template v-else>
                       <q-td v-for="(column,index) in columns" :key="index" :style="{textAlign: column.align ? column.align : 'left'}">
                            <slot :name="column.name" :col="props.col" :row="props.row" :pageIndex="props.pageIndex" :value="props.value">
                                {{ format(props.row,column) }} 
                            </slot>
                        </q-td>

                    </template>
                </q-tr>
            </template>
            
            
            
        </template>

        <template v-slot:no-data="{ icon, message, filter }">
            <slot name="no-data" :no-data="{icon, message, filter}" >
                <div class="full-width row flex-center text-weight-bold q-gutter-sm">
                    <!-- <q-icon size="2em" name="sentiment_dissatisfied" /> -->
                    <span>
                        {{ lang.noData || 'No se encontrarón datos' }}
                    </span>
                    <q-icon size="2em" :name="filter ? 'filter_b_and_w' : icon" />
                </div>
                </slot>
        </template>

        <template v-slot:bottom>
            <div class="col-12">
                <div class="row justify-center">
                    <div class="col-12 row justify-center">
                        <q-pagination
                            v-model="options.page"
                            :max="pagesNumber"
                            :direction-links="true"
                            :boundary-links="true"
                            :max-pages="10"
                            icon-first="skip_previous"
                            icon-last="skip_next"
                            icon-prev="fast_rewind"
                            icon-next="fast_forward"
                            :color="colorPaginate"
                            @input="(value) => paginate(value)"
                        />
                    </div>
                    
                </div>
                <div class="row q-my-md">
                    <div class="col-12 text-center">{{ footer }}</div>
                </div>
            </div>
        </template>

    </q-table>
</template>
<script>
export default {
    name:'Table',
    props:{
        url: {
            type:String
        },
        columns: {
            type:Array
        },
        loading: {
            type:Boolean,
            default:true
        },
        pagination: {
            type:Object,
            default: () => { return {
                sortBy: 'desc',
                descending: false,
                page: 1,
                rowsPerPage: 5,
                rowsNumber: 10,
                perPages:[5,10,15,20,25,50]
            }}
        },
        timeOut: {
            type:Number,
            default:500
        },
        rowKey: {
            type:String,
            default:'id'
        },
        lang:{

            type:Object,
            default:() => { return { 
                    footer:'Mostrando resultados {from} - {to} de {count}',
                    limit:'Registros',
                    noData:'No se encontrarón datos'
                } 
            }
            
        },
        colorPaginate:{
            type:String,
            default:'grey'
        }
    },
    created(){
        this.options = this.initPagination = this.pagination;

        this.options.perPages = this.pagination.perPages || [5,10,15,20];
        this.options.rowsPerPage = this.pagination.rowsPerPage || 5;
        this.options.page = this.pagination.page || 1;
       
        
        this.load = this.loading;

        this.columns.forEach( item => {
            if(item.filterable) this.filterColumns.push(item);
        });

        if(this.url != null) this.onRequest()

    },
    watch:{
        url(newVal,oldVal){
            this.onRequest();
        }
    },
    computed:{
        pagesNumber () {
            return Math.ceil(this.count / this.options.rowsPerPage)
        },
        limit(){
            let final = Math.ceil(this.count / this.options.rowsPerPage);
            let calc = (this.options.rowsPerPage * this.options.page);
            return (final != this.options.page) ? calc : this.count;
        },
        footer(){
            let text = this.lang.footer || this.language.footer;

            let footer = text.replace(/{from}/g,this.current)
            .replace(/{to}/g,this.limit)
            .replace(/{count}/g,this.count);
            return footer;
        }
    },
    data(){
        return {
            query:{},
            data:[],
            options:{},
            load:false,
            filterColumns:[],
            call:null,
            value:null,
            initPagination:{},
            count:0,
            current:1,
            language:{
                footer:'Mostrando resultados {from} - {to} de {count}',
                limit:'Registros'
            }
        };
    },
    methods:{
        changePerPage(ev){
            this.options.page = 1;
            this.onRequest();
        },
        paginate(value){
            this.$emit('paginate',value);
            this.onRequest();
        },
        onInput(){
            this.onRequest();
        },
        onQuery(value,name){
            clearTimeout(this.call);
            if(value != ''){
                this.query[name] = value;
            }else{
                delete this.query[name];
            }
            this.call = setTimeout(this.onInput, this.timeOut);
        },
        async onRequest () {
            this.load = true;
            const { page, rowsPerPage, sortBy, descending } = this.options
            const filter = (this.filter != undefined) ? this.filter : '';

            
            const fetchCount = rowsPerPage === 0 ? this.options.rowsNumber : rowsPerPage;

            
            const { data, count } = await this.fetchFromServer(page, fetchCount, filter, sortBy, descending);

            this.data = data.data;
            this.count = data.count;

            this.$emit('loaded', data );
            this.options.rowsNumber = this.getRowsNumberCount(filter);
            

            
            this.options.page = page
            this.options.rowsPerPage = rowsPerPage
            this.options.sortBy = sortBy
            this.options.descending = descending

            let limit = this.limit;

            let diferencia = (this.options.rowsPerPage * this.options.page) - limit; 

            this.current = ((limit - this.options.rowsPerPage ) + 1) + diferencia;

            this.load = false;
        },

        async fetchFromServer (startRow, count, filter, sortBy, descending) {
            let params = String(this.url).indexOf('?');
            let url  = (params != -1) 
            ? `${this.url}&limit=${count}&page=${startRow}&query=${ JSON.stringify(this.query) }`
            : `${this.url}?limit=${count}&page=${startRow}&query=${ JSON.stringify(this.query) }`;

            let data = await this.$axios.get(`${url}`)
            .then( response => response );
            return data;
        },
        refresh(){
            this.options.page = 1;
            this.onRequest();
        },

        refreshOnPage(){
            this.onRequest();
        },
        getRowsNumberCount (filter) {
            return this.data.length;
        },
        setDataOnPage(page){
            this.options.page = page;
            this.onRequest();
        },
        format(row,column){
            let format = column.format ? column.format(row) : row[column.name];
            return format ? format : '';
        }
    }
    
}
</script>

<style lang="sass">
.my-sticky-header-table
  /* height or max-height is important */
  height: 310px

  .q-table__top,
  .q-table__bottom,
  thead tr:nth-child(1) th,
  thead tr:nth-child(2) th
    /* bg color is important for th; just specify one */
    background-color: #ffffff

  thead tr th
    position: sticky
    z-index: 1

  thead tr:nth-child(1) th
    top: 0 !important

  thead tr:nth-child(2) th
    top: 48px !important

  /* this is when the loading indicator appears */
  &.q-table--loading 
    /* height of all previous header rows */
    thead tr:nth-child(1) th
        top: 0 !important

    thead tr:nth-child(2) th
        top: 48px !important

    thead tr.q-table__progress th
        top: 103px !important
</style>
