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
                                <p-inputtext id="email" type="email" class="w-full md:w-[30rem]" v-model="auth.email" :invalid="iserror" autocomplete="email" />
                                <label for="email">Adresse e-mail</label>
                            </p-floatlabel>
                            <p-floatlabel class="mb-4">
                                <p-password id="password1" v-model="auth.password" :toggleMask="true" fluid :feedback="false" :invalid="iserror" @keyup.enter="login($event)" :inputProps="{ autocomplete: 'current-password' }"></p-password>
                                <label for="password1">Mot de passe</label>
                            </p-floatlabel>

                            <small v-if="iserror" class="text-red-500">{{error}}</small>
                            <div class="text-right mt-2">
                                <a class="text-sm text-blue-500 hover:underline cursor-pointer" @click="forgotDialog = true">Mot de passe oublié ?</a>
                            </div>
                            <p-button label="Sign In" class="mt-4 w-full" @click="login($event)" :disabled="load_button_logout"><i v-if="load_button_logout" class="pi pi-spin pi-spinner mr-3"></i>Connexion</p-button>
                        </div>
                    </form>

                    <p-dialog v-model:visible="forgotDialog" header="Mot de passe oublié" :modal="true" style="width: 400px">
                        <div class="flex flex-col gap-4 mt-2">
                            <p class="text-sm text-gray-600">Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
                            <p-floatlabel>
                                <p-inputtext id="forgot-email" v-model="forgotEmail" type="email" fluid />
                                <label for="forgot-email">Adresse email</label>
                            </p-floatlabel>
                            <small v-if="forgotSuccess" class="text-green-600">{{ forgotSuccess }}</small>
                            <small v-if="forgotError" class="text-red-500">{{ forgotError }}</small>
                        </div>
                        <template #footer>
                            <p-button label="Annuler" icon="pi pi-times" text @click="forgotDialog = false" />
                            <p-button label="Envoyer" icon="pi pi-send" @click="sendForgot" :loading="forgotLoading" />
                        </template>
                    </p-dialog>
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
                email:"",
                password:""
            },
            load_button_logout:false,
            error: "",
            iserror: false,
            authStore: useAuthStore(),
            forgotDialog: false,
            forgotEmail: '',
            forgotLoading: false,
            forgotSuccess: '',
            forgotError: '',
        }
    },
    methods:{
        sendForgot() {
            this.forgotError = '';
            this.forgotSuccess = '';
            this.forgotLoading = true;
            axios.get('/sanctum/csrf-cookie')
                .then(() => axios.post('/api/forgot-password', { email: this.forgotEmail }))
                .then(r => { this.forgotSuccess = r.data.message; })
                .catch(e => { this.forgotError = e?.data?.message || 'Erreur.'; })
                .finally(() => { this.forgotLoading = false; });
        },
        async login(e){
            e.preventDefault()
            this.load_button_logout = true
            await axios.get('/sanctum/csrf-cookie')
            await axios.post('/login', this.auth).then(() => {
                this.$store.login()
            }).catch((error) => {
                const errors = error.response?.data;
                this.error = errors?.['email']?.[0] ?? 'Identifiants incorrects.';
                this.iserror = true;
            }).finally(() => {
                this.load_button_logout = false;
            })
        },
    }
}
</script>
