<template>
<div class="">
    <form @submit.prevent="submit()" class="customforma">
        <BCard no-body>
            <BCardHeader class="align-items-center d-flex py-0" style="height: 59px;">
                <BCardTitle class="mb-0 flex-grow-1">Menu Management</BCardTitle>
                <div class="flex-shrink-0">
                    <div class="input-group">
                        <!-- <span class="input-group-text"> <i class="ri-search-line search-icon"></i></span>
                        <input type="text" placeholder="Search Menu" v-model="filter.keyword" class="form-control" style="width: 35%;"> -->
                        <b-button type="button" variant="primary" @click="openCreate()">
                            <i class="ri-add-circle-fill align-bottom me-1"></i> Create
                        </b-button>
                    </div>
                </div>
            </BCardHeader>
            <BCardBody class="p-4" style="height: calc(100vh - 280px); overflow: auto;">
                
            </BCardBody>
        </BCard>
    </form>
    <Create ref="create"/>
</div>
</template>
<script>
import Create from '../Modals/Menus/Create.vue';
import Layout from "@/Shared/Layouts/Main.vue";
import profile from "@/Pages/Modules/User/Utility/Index.vue";
export default {
    layout: (h,page) => {
        return h(Layout, [
            h(profile,[page])
        ])
    },
    components: { Create },
    data(){
        return {
            filter: {
                keyword: null
            }
        }
    },
    methods: {
        openCreate(){
            this.$refs.create.show();
        },
    }
}
</script>