<template>
    <div class="h-dvh bg-slate-50">
        
        <p-menubar :model="items">
            <template #start>
                <FavIconSVG :mainColor="'var(--p-primary-900)'" class="h-12 w-auto hover:cursor-pointer"  @click="this.$router.push({  name: 'home'});"/>
            </template>
            <template #end>
                <p-button label="Déconnexion" icon="pi pi-fw pi-power-off" class="p-button-text p-button-secondary" @click="logout()" :loading="load_button_logout"/>
            </template>
        </p-menubar>

        <div class="container mx-auto h-4/5 my-4 leading-normal">
            <router-view/>
        </div>
        
    </div>
</template>

<script>
import { watch } from 'vue';
import { useRoute } from 'vue-router';
import FavIconSVG from '../svg/FavIcon.vue'
export default { 
    name:"layout",
    components: { FavIconSVG },
    mounted(){ 
        this.setMenu()
        this.initChangeCurrentRoute()
        this.setChangeCurrentRoute()
    },
    data(){
        return {
            load_button_logout: false,
            items: [],
            tempitems: [],
            route: useRoute()
        }
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

            if(this.$hasPermission('can_use_admin_users_page')){
                this.tempitems.push({
                    label:'Utilisateurs',
                    icon:'pi pi-fw pi-user',
                    to:'/admin/user',
                    command: () => {
                        this.$router.push('/admin/user');
                    }
                });
            }


            if(this.$hasPermission('can_use_admin_roles_page')){
                this.tempitems.push({
                    label:'Roles',
                    icon:'pi pi-fw pi-address-book',
                    to:'/admin/role',
                    command: () => {
                        this.$router.push('/admin/role');
                    }
                });
            }
            
            if(this.$hasPermission('can_use_admin_permissions_page')){
                this.tempitems.push({
                    label:'Permissions',
                    icon:'pi pi-fw pi-lock',
                    to:'/admin/permission',
                    command: () => {
                        this.$router.push('/admin/permission');
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