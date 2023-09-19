<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">Enter your phone number</h1>
        <form v-if="!waitingOnVerification" action="#" @submit.prevent="handleLogin">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <input type="text" v-maska data-maska="+81 (##) ####-####" v-model="credentials.phone" name="phone" id="phone" placeholder="+81 (12) 3456-7890"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button type="submit" @submit.prevent="handleLogin"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">送信</button>
                </div>
            </div>
        </form>
        <form v-else action="#" @submit.prevent="handleVerification">
            <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
                <div class="bg-white px-4 py-5 sm:p-6">
                    <div>
                        <input type="text" v-maska data-maska="######" v-model="credentials.login_code" name="login_code" id="login_code" placeholder="123456"
                            class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none">
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                    <button type="submit" @submit.prevent="handleVerification"
                        class="inline-flex justify-center rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">認証</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { vMaska } from 'maska'
import { reactive, ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router';

const router = useRouter()

const credentials = reactive({
    phone: null,
    login_code: null
})

const waitingOnVerification = ref(false)

// ローカルストレージからトークンがあるか確認
onMounted(() => {
    if (localStorage.getItem('token')) {
        router.push({
            name: 'landing'
        })
    }
})

// 入力値を変換/返却
const getFormattedCredentials = () => {
    return {
        phone: credentials.phone.replaceAll(' ', '').replace('+', '').replace('(', '').replace(')', '').replace('-', ''),
        login_code: credentials.login_code
    }
}

// 携帯番号をバックエンドへ送信
const handleLogin = () => {
    axios.post('http://localhost/api/login', getFormattedCredentials())
        .then((response) => {
            console.log(response.data)
            waitingOnVerification.value = true
        })
        .catch((error) => {
            console.error(error)
            alert(error.response.data.message)
        })
}

// 携帯番号とログインコードをバックエンドに送信
const handleVerification = () => {
    axios.post('http://localhost/api/login/verify', getFormattedCredentials())
        .then((response) => {
            console.log(response.data) // auth token
            localStorage.setItem('token', response.data)
            router.push({
                name: 'landing'
            })
        })
        .catch((error) => {
            console.error(error)
            alert(error.response.data.message)
        })
}
</script>