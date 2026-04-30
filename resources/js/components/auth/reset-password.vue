<template>
    <div class="bg-surface-50 flex items-center justify-center min-h-screen min-w-[100vw]">
        <div class="flex flex-col items-center justify-center">
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--p-primary-900) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full bg-surface-0 py-20 px-8 sm:px-20" style="border-radius: 53px">
                    <div class="text-center mb-8">
                        <img src="/images/logo.webp" alt="Logo" class="w-28 h-28 mx-auto mb-4">
                        <div class="text-2xl font-medium mb-2">Réinitialisation du mot de passe</div>
                    </div>
                    <div v-if="success" class="text-center">
                        <i class="pi pi-check-circle text-4xl text-green-500 mb-4"></i>
                        <p class="text-gray-600 mb-4">Mot de passe réinitialisé avec succès.</p>
                        <p-button label="Se connecter" @click="$router.push({ name: 'login' })" />
                    </div>
                    <form v-else class="flex flex-col gap-4 w-full md:w-[28rem]">
                        <p-floatlabel>
                            <p-inputtext id="email" v-model="form.email" type="email" fluid :disabled="true" />
                            <label for="email">Email</label>
                        </p-floatlabel>
                        <p-floatlabel>
                            <p-password id="pwd" v-model="form.password" toggleMask fluid :feedback="true" />
                            <label for="pwd">Nouveau mot de passe</label>
                        </p-floatlabel>
                        <p-floatlabel>
                            <p-password id="pwd2" v-model="form.password_confirmation" toggleMask fluid :feedback="false" />
                            <label for="pwd2">Confirmer le mot de passe</label>
                        </p-floatlabel>
                        <small v-if="error" class="text-red-500">{{ error }}</small>
                        <p-button label="Réinitialiser" @click="submit" :loading="loading" class="mt-2" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                token: '',
                email: '',
                password: '',
                password_confirmation: '',
            },
            loading: false,
            error: '',
            success: false,
        };
    },
    mounted() {
        const params = new URLSearchParams(window.location.search);
        this.form.token = params.get('token') || '';
        this.form.email = params.get('email') || '';
    },
    methods: {
        submit() {
            this.error = '';
            this.loading = true;
            axios.post('/api/reset-password', this.form)
                .then(() => { this.success = true; })
                .catch(e => { this.error = e?.data?.message || 'Lien invalide ou expiré.'; })
                .finally(() => { this.loading = false; });
        },
    },
};
</script>
