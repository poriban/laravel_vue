<template>
    <div class="pt-16">
        <h1 class="text-3xl font-semibold mb-4">Andrewber</h1>
        <div class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left">
            <div class="bg-white px-4 py-5 sm:p-6">
                <div class="flex justify-between">
                    <button
                        @click="handleStartDriving"
                        class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        運転する
                    </button>
                    <button
                        @click="handleFindARide"
                        class="rounded-md border border-transparent bg-black py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none">
                        乗車する
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import http from '@/helpers/http'

// ルート
const router = useRouter()

// 運転手の待機ページへ遷移
const handleStartDriving = () => {
    http().get('/api/driver')
        .then((response) => {
            if (response.data.driver) {
                router.push({
                    name: 'standby'
                })
            } else {
                router.push({
                    name: 'driver'
                })
            }
        })
        .catch((error) => {
            console.error(error)
        })
}

// 地図を検索ページへ遷移
const handleFindARide = () => {
    router.push({
        name: 'location'
    })
}
</script>