<template>
    <q-layout view="hhh LpR fFf" style="background: #ededed;">
        <q-header elevated class="bg-blue-grey-8">
            <q-toolbar>
                <q-btn flat dense round icon="menu" aria-label="Menu" @click="leftDrawerOpen = !leftDrawerOpen" />
                <q-toolbar-title>
                    Airport
                </q-toolbar-title>
                 
                {{ $auth.user.name }}
                <q-btn round flat>
                    <template v-if="$auth.loggedIn">
                        <div> 
                            <q-avatar color="bg-blue-grey-8"> 
                                <q-icon name="account_circle" />
                            </q-avatar>
                        </div>
                    </template>

                    <q-menu>
                        <q-list style="min-width: 200px">
                            <q-item clickable v-close-popup @click="logout()">
                                <q-item-section>Sign out</q-item-section>
                            </q-item>
                        </q-list>
                    </q-menu>
                </q-btn>
            </q-toolbar>
        </q-header>

        <q-drawer v-model="leftDrawerOpen" side="left"  overlay  behavior="mobile" elevated >
            <q-scroll-area class="fit">
                <q-list>
                    <q-item-label header class="text-grey-8" >
                        Menú
                    </q-item-label> 
                    <template v-for="(item, index) in modules">
                        <q-item clickable @click="leftDrawerOpen = false" v-ripple :to="{name: item.route_name}" :key="index">
                            <q-item-section avatar>
                                <q-icon :name="item.icon" />
                            </q-item-section>
                            <q-item-section>{{ item.name }} </q-item-section>
                        </q-item> 
                    </template>
                </q-list>
            </q-scroll-area>
        </q-drawer>
        <q-page-container >
            <router-view />
        </q-page-container>
    </q-layout>
</template>

<script> 
 
export default {
    name: 'MainLayout',
    data () {
        return {
            leftDrawerOpen: false, 
            modules : [
                {
                    icon : 'home',
                    name : 'Home',
                    route_name : 'home'
                },
                {
                    icon : 'flight_takeoff',
                    name : 'Airports',
                    route_name : 'airports'
                },
                {
                    icon : 'business',
                    name : 'Airlines',
                    route_name : 'airlines'
                },
            ],
        }
    },
    mounted(){
        if( this.$auth.user.role.name == 'admin'){
            this.modules.push(
                {
                    icon : 'flight',
                    name : 'Flights',
                    route_name : 'flights'
                }
            );
        }
    },
    methods : {
        async logout(){
            try {
                this.$q.loading.show(); 
                await this.$auth.logout();
                this.$q.loading.hide();
                
            } catch (error) {
                this.$q.loading.hide(); 
            }
        }
    }
}
</script>
