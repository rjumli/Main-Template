<template>
    <b-modal v-model="showModal" title="Two Factor Authentication" style="--vz-modal-width: 650px;" header-class="p-3 bg-light" class="v-modal-custom" modal-class="zoomIn" centered no-close-on-backdrop>    
        <div class="p-2">
            <form class="customform" @submit.prevent="submit">
                <div class="row g-3">
                    <div class="col-md-12 text-muted fs-12">
                        <h5 class="mb-3 fs-14 text-success" v-if="twoFactorEnabled && !confirming">You have enabled two factor authentication.</h5>
                        <h5 class="mb-3 fs-14 text-warning" v-else-if="twoFactorEnabled && confirming">Finish enabling two factor authentication.</h5>
                        <h5 class="mb-3 fs-14 text-danger" v-else>You have not enabled two factor authentication.</h5>
                        <p class="text-muted fs-12">When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.</p>
                    </div>
                    <div class="col-md-12 mt-n1" v-if="twoFactorEnabled">
                        <div v-if="qrCode">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mt-2" v-html="qrCode" />
                                </div>
                                <div class="col-md-8">
                                     <p v-if="confirming" class="text-muted mt-3 mb-n2 fs-12">
                                    To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application or enter the setup key and provide the generated OTP code.
                                </p>
                                    <div v-if="setupKey" class="mt-4 text-muted">
                                        <div class="form-floating">
                                            <input ref="input" class="form-control" v-model="setupKey" readonly>
                                            <label class="form-label fw-semibold fs-12">SETUP KEY</label>
                                        </div>
                                        <div class="form-floating">
                                            <input ref="input" class="form-control" v-model="form.code" :class="{ 'is-invalid': form.errors.code}">
                                            <label class="form-label fw-semibold fs-12">CODE</label>
                                            <div class="invalid-feedback mt-n1">
                                                <span class="fs-10">{{ form.errors.code }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <template v-slot:footer>
            <b-button @click="hide()" variant="light" block>Cancel</b-button>
            <div v-if="!twoFactorEnabled">
                <b-button @click="enableTwoFactorAuthentication" variant="success" :disabled="form.processing" block><i class="ri-save-3-line align-bottom me-1"></i>Enable</b-button>
            </div>
            <div v-else>
                <Confirm @confirmed="confirmTwoFactorAuthentication">
                    <b-button v-if="confirming" variant="warning" :disabled="form.processing" block><i class="ri-save-3-line align-bottom me-1"></i>Confirm</b-button>
                </Confirm>
                <Confirm @confirmed="disableTwoFactorAuthentication">
                    <b-button v-if="!confirming" variant="danger" :disabled="form.processing" block><i class="ri-save-3-line align-bottom me-1"></i>Disable</b-button>
                </Confirm>
            </div>
        </template>
    </b-modal>
</template>
<script>
import Confirm from './Confirm.vue';
import { router,useForm } from '@inertiajs/vue3'
export default {
    components: { Confirm },
    data(){
        return {
            showModal: false,
            showPassword: false,
            enabling: false,
            confirming: false,
            disabling: false,
            qrCode: null,
            setupKey: null,
            confirmed: false,
            twoFactorEnabled: false,
            recoveryCodes: [],
            form: useForm({
                code: '',
                key: '',
                option: 'confirm'
            }),
        }
    },
    methods : {
        show() {
            this.showModal = true;
        },
        enableTwoFactorAuthentication(){
            this.enabling = true;

            // router.post('/profile', {}, {
            //     preserveScroll: true,
            //     onSuccess: () => Promise.all([
            //         // this.showQrCode(),
            //         // this.showSetupKey(),
            //         // this.showRecoveryCodes(),
            //     ]),
            //     onFinish: () => {
            //         this.enabling = false;
            //         this.confirming = this.requiresConfirmation;
            //     },
            // });
            return axios.post('/profile').then(response => {
                this.twoFactorEnabled = true;
                this.confirming = true;
                this.qrCode = response.data.url;
                this.setupKey = response.data.key;
                this.form.key =  response.data.key;
            });
        },
        showQrCode(){
            return axios.get('/profile',{
                params : { option: 'two-factor' }
            }).then(response => {
                this.qrCode = response.data.img;
            });
        },
        // showSetupKey(){
        //     return axios.get('/user/two-factor-secret-key').then(response => {
        //         this.setupKey = response.data.secretKey;
        //     });
        // },
        // showRecoveryCodes(){
        //     return axios.get('/user/two-factor-recovery-codes').then(response => {
        //         this.recoveryCodes = response.data;
        //     });
        // },
        confirmTwoFactorAuthentication(){
            this.form.post('/profile', {
                errorBag: "confirmTwoFactorAuthentication",
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    this.confirming = false;
                    this.qrCode = null;
                    this.setupKey = null;

                    
                },
            });
        },
        disableTwoFactorAuthentication(){
            this.disabling = true;
            router.delete('/user/two-factor-authentication', {
                preserveScroll: true,
                onSuccess: () => {
                    this.disabling = false;
                    this.confirming = false;
                },
            });
        },
        confirmPassword(){
            // axios.get('/user/confirmed-password-status').then(response => {
            //     if (!response.data.confirmed) {
            //        this.$refs.confirm.show();
            //     }
            // });
            (!$page.props.user.data.password_changed_at) ? this.$refs.confirm.show() : '';
        },
        hide(){
            this.form.reset();
            this.showModal = false;
        },
    }
}
</script>
