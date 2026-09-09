<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    takes: Object,
});

const authUser = usePage().props.auth?.user;
</script>

<template>
    <Head title="Takes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Takes</h2>
                <Link
                    v-if="authUser"
                    :href="route('takes.create')"
                    class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700"
                >
                    Post a Take
                </Link>
                <Link
                    v-else
                    :href="route('login')"
                    class="rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50"
                >
                    Log in to post
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8 space-y-4">

                <div v-if="takes.data.length === 0" class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-500 text-center">
                    No takes yet. Be the first to post one.
                </div>

                <div
                    v-for="take in takes.data"
                    :key="take.id"
                    class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-col gap-2"
                >
                    <p class="text-gray-900 text-base">{{ take.content }}</p>
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>
                            by <span class="font-medium text-gray-700">{{ take.user?.name ?? 'Unknown' }}</span>
                            · {{ take.created_at }}
                        </span>
                        <Link
                            :href="route('takes.show', take.id)"
                            class="text-indigo-600 hover:text-indigo-800 font-medium"
                        >
                            View →
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="takes.last_page > 1" class="flex justify-center gap-2 pt-4">
                    <Link
                        v-for="link in takes.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="px-3 py-1 rounded text-sm border"
                        :class="[
                            link.active ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50',
                            !link.url ? 'opacity-40 pointer-events-none' : ''
                        ]"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
