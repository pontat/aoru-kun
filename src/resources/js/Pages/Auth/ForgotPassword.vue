<template>
    <div class="mb-4 text-sm text-gray-600">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset
        link that will allow you to choose a new one.
    </div>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
    </div>

    <breeze-validation-errors class="mb-4" />

    <form @submit.prevent="submit">
        <div>
            <breeze-label for="email" value="Email" />
            <breeze-input
                id="email"
                type="email"
                class="mt-1 block w-full"
                v-model="form.email"
                required
                autofocus
                autocomplete="username"
            />
        </div>

        <div class="flex items-center justify-end mt-4">
            <breeze-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Email Password Reset Link
            </breeze-button>
        </div>
    </form>
</template>

<script>
import BreezeButton from '../../components/Button'
import BreezeGuestLayout from '../../Layouts/Guest'
import BreezeInput from '../../components/Input'
import BreezeLabel from '../../components/Label'
import BreezeValidationErrors from '../../components/ValidationErrors'

export default {
    layout: BreezeGuestLayout,

    components: {
        BreezeButton,
        BreezeInput,
        BreezeLabel,
        BreezeValidationErrors,
    },

    props: {
        auth: Object,
        errors: Object,
        status: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                email: '',
            }),
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('password.email'))
        },
    },
}
</script>
