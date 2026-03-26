<template>
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--p-primary-900) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <img src="/images/logo.webp" alt="Logo" class="w-40 h-40 mx-auto mb-4">
                        <div class="text-3xl font-medium mb-4">Bienvenue dans un monde merveilleux!</div>
                        <span class="text-muted-color font-medium">Connectez-vous pour continuer</span>
                    </div>

                    <form>
                        <div>
                            <p-floatlabel class="mb-8">
                                <p-inputtext id="username1" type="text"  class="w-full md:w-[30rem]" v-model="auth.username" :invalid="iserror" autocomplete="username" />
                                <label for="username1">Nom d'utilisateur</label>
                            </p-floatlabel>
                            <p-floatlabel class="mb-4">
                                <p-password id="password1" v-model="auth.password" :toggleMask="true" fluid :feedback="false" :invalid="iserror" @keyup.enter="login($event)" :inputProps="{ autocomplete: 'current-password' }"></p-password>
                                <label for="password1">Mot de passe</label>
                            </p-floatlabel>
                            
                            
                            <small v-if="iserror" class="text-red-500">{{error}}</small>
                            <!--<div class="flex items-center justify-between mt-2 mb-8 gap-8">
                                <div class="flex items-center">
                                    <Checkbox v-model="checked" id="rememberme1" binary class="mr-2"></Checkbox>
                                    <label for="rememberme1">Remember me</label>
                                </div>
                                <span class="font-medium no-underline ml-2 text-right cursor-pointer text-primary">Forgot password?</span>
                            </div>-->
                            <p-button label="Sign In" class="mt-4 w-full" @click="login($event)" :disabled="load_button_logout"><i v-if="load_button_logout" class="pi pi-spin pi-spinner mr-3"></i>Connexion</p-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

  </template>

<script>
import { useAuthStore } from '@/store/auth'
import LogoSVG from '../svg/Logo.vue'
export default {
    components: { LogoSVG },
    name:"login",
    data(){
        return {
            auth:{
                username:"",
                password:""
            },
            username: 'username',
            load_button_logout:false,
            error: "",
            iserror: false,
            authStore: useAuthStore()
        }
    },
    methods:{
        async login(e){
            e.preventDefault()
            this.load_button_logout = true
            await axios.get('/sanctum/csrf-cookie')
            await axios.post('/login', this.auth).then(() => {
                this.$store.login()
            }).catch((error) => {
                const errors = error.response?.data;
                this.error = errors?.[this.username]?.[0] ?? 'Identifiants incorrects.';
                this.iserror = true;
            }).finally(() => {
                this.load_button_logout = false;
            })
        },
    }
}
</script>
