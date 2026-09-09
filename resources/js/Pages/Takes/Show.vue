<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    take: Object,
});

const page = usePage();
const authUser = page.props.auth?.user;

const isOwner = authUser && authUser.id === props.take.user.id;
const canChallenge = authUser && !isOwner;

const challengeForm = useForm({});

function challenge() {
    challengeForm.post(route('duels.store', props.take.id));
}

const deleteForm = useForm({});

function deleteTake() {
    if (confirm('Delete this take?')) {
        deleteForm.delete(route('takes.destroy', props.take.id));
    }
}
</script>

<template>
    <Head :title="'Take by ' + take.user.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Take</h2>
                <Link :href="route('takes.index')" class="text-sm text-gray-500 hover:text-gray-700">← All Takes</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8 space-y-6">

                <!-- Take card -->
                <div class="bg-white shadow-sm sm:rounded-lg p-8">
                    <p class="text-gray-900 text-lg leading-relaxed mb-4">{{ take.content }}</p>
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>
                            by <span class="font-medium text-gray-700">{{ take.user.name }}</span>
                            · {{ take.created_at }}
                        </span>
                        <span class="text-gray-400">{{ take.duels_count }} duel{{ take.duels_count !== 1 ? 's' : '' }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <!-- Challenge button (non-owners) -->
                    <button
                        v-if="canChallenge"
                        @click="challenge"
                        :disabled="challengeForm.processing"
                        class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 disabled:opacity-50"
                    >
                        ⚔️ Challenge
                    </button>

                    <!-- Owner actions -->
                    <template v-if="isOwner">
                        <Link
                            :href="route('takes.edit', take.id)"
                            class="rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50"
                        >
                            Edit
                        </Link>
                        <button
                            @click="deleteTake"
                            :disabled="deleteForm.processing"
                            class="rounded-md border border-red-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-red-600 hover:bg-red-50 disabled:opacity-50"
                        >
                            Delete
                        </button>
                    </template>
                </div>

                <!-- Not logged in nudge -->
                <div v-if="!authUser" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
                    <Link :href="route('login')" class="font-semibold underline">Log in</Link> to challenge this take or react.
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
