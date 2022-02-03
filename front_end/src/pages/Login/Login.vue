<template>
    <q-layout>
        <div class="row justify-center container-login">
            <q-card class="login">
                <q-card-section>
                    <ValidationObserver ref="form" >

                        <div class="text-center text-h5 q-my-md">Bienvenido</div>

                        <ValidationProvider rules="required|email" v-slot="{ errors, invalid, validated }">
                            <q-input  
                                autofocus
                                outlined
                                :dense="true" 
                                label="Correo electronico"
                                v-model="credenciales.email" 
                                type="mail" 
                                :error="invalid && validated"
                                :error-message="errors[0]"
                                class="q-py-sm"
                                @keypress.enter="login"
                            >
                                <template v-slot:prepend>
                                    <q-icon name="mail" />
                                </template>
                            </q-input>
                        </ValidationProvider>

                        <ValidationProvider rules="required" v-slot="{ errors, invalid, validated }">
                            <q-input
                                outlined
                                :dense="true"
                                label="Contraseña"
                                v-model="credenciales.password"
                                :type="is_pwd ? 'password' : 'text'"
                                :error="invalid && validated"
                                :error-message="errors[0]"
                                class="q-py-md"
                                @keypress.enter="login"
                            >
                                <template v-slot:prepend>
                                    <q-icon  name="vpn_key" />
                                </template>
                                <template v-slot:append>
                                    <q-icon :name="is_pwd ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="is_pwd = ! is_pwd" />
                                </template>
                            </q-input>
                        </ValidationProvider> 
                        
                        <div class="text-center q-py-lg"> 
                            <q-btn color="primary" class="full-width" flat label="Sign In" @click="login"/>
                        </div>
                    </ValidationObserver> 
                </q-card-section>
            </q-card>
        </div>
    </q-layout>
</template>

<script>
    export default {
        name : 'Login',
        data() { 
            return { 
                credenciales : {
                    email: null,
                    password : null, 
                }, 
                is_pwd : true,
            }
        },
        methods:{
            async login(){
                try {
                    const validate = await this.$refs.form.validate();
                    if(!validate) { return false; }
                    this.$q.loading.show()
                    const {data} = await this.$axios.post('/auth/login', this.credenciales);
                    this.$q.loading.hide()

                    if(!data.status){  
                        this.$q.notify({ position: 'top', type: 'negative', message: data.msg });
                        return false; 
                    }
                    await this.$auth.loginByToken(data.token);
                    
                } catch (error) {
                    this.$q.loading.hide();
                    this.$q.notify({ position: 'top', type: 'negative', message: 'Ocurrio un error' });
                }
            },
        }
    }
</script>

<style scoped>

    .container-login{ 
        position: absolute;
        top: 20vh;
        width: 100%; 
    }

    .login{
        width: 95%;
    }

    @media only screen and (min-width: 600px) {
        .login{
            width: 60%;
        }        
    }
    @media only screen and (min-width: 768px) {
        .login{
            width : 30%;
        }
    }
</style>

 