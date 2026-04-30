<template>
    <div class="h-dvh bg-slate-50">

        <p-menubar :model="items">
            <template #start>
                <img src="/images/logo.webp" alt="Logo" class="h-16 w-auto hover:cursor-pointer" @click="this.$router.push({ name: 'home' });" />
            </template>
            <template #end>
                <p-button v-if="$hasPermission('can_see_member_portal')"
                    icon="pi pi-shopping-bag" size="small" severity="secondary" text class="mr-1 relative"
                    @click="cartVisible = true">
                    <span v-if="cartCount > 0"
                        class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center leading-none">
                        {{ cartCount }}
                    </span>
                </p-button>
                <p-button label="Déconnexion" icon="pi pi-fw pi-power-off" class="p-button-text p-button-secondary" @click="logout()" :loading="load_button_logout"/>
            </template>
        </p-menubar>

        <p-dialog v-model:visible="cartVisible" header="Mon panier" :modal="true" style="width: 480px">
            <div v-if="cartItems.length === 0" class="text-center py-8 text-gray-400">
                <i class="pi pi-shopping-bag text-4xl mb-3"></i>
                <p>Votre panier est vide.</p>
                <p class="text-sm mt-1">Ajoutez des livres depuis le catalogue.</p>
            </div>
            <div v-else class="flex flex-col gap-3 mt-2">
                <div v-for="book in cartItems" :key="book.id"
                    class="flex items-center justify-between gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-800 truncate">{{ book.title }}</div>
                        <div class="text-sm text-gray-500">{{ book.author }}</div>
                    </div>
                    <p-button icon="pi pi-times" size="small" text rounded severity="danger"
                        @click="cartStore.removeBook(book.id)" />
                </div>
            </div>
            <template v-if="cartItems.length > 0" #footer>
                <p-button label="Vider" icon="pi pi-trash" text severity="danger" @click="cartStore.clear()" />
                <p-button label="Confirmer la réservation" icon="pi pi-check" severity="success"
                    @click="checkout" :loading="checkingOut" />
            </template>
        </p-dialog>

        <div class="container mx-auto h-4/5 my-4 leading-normal">
            <router-view/>
        </div>

    </div>
</template>

<script>
import { watch } from 'vue';
import { useRoute } from 'vue-router';
import { useCartStore } from '@/store/cart';
export default {
    name:"layout",
    components: {},
    mounted(){
        this.setMenu()
        this.initChangeCurrentRoute()
        this.setChangeCurrentRoute()
        this.cartStore = useCartStore();
    },
    data(){
        return {
            load_button_logout: false,
            items: [],
            tempitems: [],
            route: useRoute(),
            cartVisible: false,
            cartStore: null,
            checkingOut: false,
        }
    },
    computed: {
        cartItems() { return this.cartStore?.items || []; },
        cartCount() { return this.cartStore?.count || 0; },
    },

    methods:{
        initChangeCurrentRoute(){
            this.actualRoute = this.route.path
            watch(() => this.route.name, () => {
                this.setChangeCurrentRoute();
            });
        },
        setChangeCurrentRoute() {
            // remove active style from link to previous page
            if (this.items.find((obj) => obj.to.split('/')[1] === this.actualRoute.split('/')[1])) {
                this.items.find((obj) => obj.to.split('/')[1] === this.actualRoute.split('/')[1]).class = ''
            }

            // add active style to link on current page
            if (this.items.find((obj) => obj.to.split('/')[1] === this.route.path.split('/')[1])) {
                this.items.find((obj) => obj.to.split('/')[1] === this.route.path.split('/')[1]).class =
                'p-focus'
            }

            this.actualRoute = this.route.path
        },
        setMenu(){
            //console.log(this.test)

            if(this.$hasPermission('can_see_home_page')){
                this.items.push({
                    label:'Accueil',
                    icon:'pi pi-fw pi-home',
                    to:'/home',
                    command: () => {
                        this.$router.push('/home');
                    }
                });
            }

            if(this.$hasPermission('can_see_books_page')){
                this.items.push({
                    label:'Ouvrages',
                    icon:'pi pi-fw pi-book',
                    to:'/books',
                    command: () => {
                        this.$router.push('/books');
                    }
                });
            }

            if(this.$hasPermission('can_see_loans_page')){
                this.items.push({
                    label:'Emprunts',
                    icon:'pi pi-fw pi-bookmark',
                    to:'/loans',
                    command: () => {
                        this.$router.push('/loans');
                    }
                });
            }

            if(this.$hasPermission('can_see_reminders_page')){
                this.items.push({
                    label:'Rappels',
                    icon:'pi pi-fw pi-bell',
                    to:'/reminders',
                    command: () => {
                        this.$router.push('/reminders');
                    }
                });
            }

            if(this.$hasPermission('can_see_member_portal')){
                this.items.push({
                    label:'Mon compte',
                    icon:'pi pi-fw pi-user-edit',
                    to:'/account',
                    command: () => { this.$router.push('/account'); }
                });
            }

            if(this.$hasPermission('can_manage_reservations')){
                this.items.push({
                    label:'Réservations',
                    icon:'pi pi-fw pi-shopping-bag',
                    to:'/reservations-mgmt',
                    command: () => { this.$router.push('/reservations-mgmt'); }
                });
            }

            if(this.$hasPermission('can_see_stats_page')){
                this.items.push({
                    label:'Statistiques',
                    icon:'pi pi-fw pi-chart-bar',
                    to:'/stats',
                    command: () => {
                        this.$router.push('/stats');
                    }
                });
            }

            if(this.$hasPermission('can_use_admin_users_page')){
                this.tempitems.push({
                    label:'Utilisateurs',
                    icon:'pi pi-fw pi-user',
                    to:'/admin/users',
                    command: () => {
                        this.$router.push('/admin/users');
                    }
                });
            }

            if(this.$hasPermission('can_use_admin_roles_page')){
                this.tempitems.push({
                    label:'Roles',
                    icon:'pi pi-fw pi-address-book',
                    to:'/admin/roles',
                    command: () => {
                        this.$router.push('/admin/roles');
                    }
                });
            }

            if(this.$hasPermission('can_use_admin_permissions_page')){
                this.tempitems.push({
                    label:'Permissions',
                    icon:'pi pi-fw pi-lock',
                    to:'/admin/permissions',
                    command: () => {
                        this.$router.push('/admin/permissions');
                    }
                });
            }

            if(this.tempitems.length > 0){
                this.items.push({
                    label:'Administration',
                    icon:'pi pi-fw pi-sliders-v',
                    items: this.tempitems,
                    to:'/admin'
                });
            }

            this.tempitems = []
            
            
            

            
            
        },
        checkout() {
            this.checkingOut = true;
            const bookIds = this.cartStore.items.map(b => b.id);
            this.$axios.post('/api/account/reservations', { book_ids: bookIds })
                .then(() => {
                    this.cartStore.clear();
                    this.cartVisible = false;
                    this.$toast.add({ severity: 'success', summary: 'Réservations envoyées !', detail: 'La bibliothécaire sera notifiée.', life: 5000 });
                })
                .catch(() => {
                    this.$toast.add({ severity: 'error', summary: 'Erreur', detail: 'Impossible de confirmer la réservation.', life: 4000 });
                })
                .finally(() => { this.checkingOut = false; });
        },
        async logout(){
            this.load_button_logout = true;
            await axios.post('/logout').then(({data})=>{
                this.$store.logout()
                this.$router.push({name:"login"})
            }).finally(()=>{
                this.load_button_logout = false;
            });
        },
    },
}
</script>