<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    take: Object,
});

const form = useForm({
    content: props.take.content,
});

function submit() {
    form.patch(route('takes.update', props.take.id));
}
</script>

<template>
    <Head title="Edit Take" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Take</h2>
                <Link :href="route('takes.show', take.id)" class="text-sm text-gray-500 hover:text-gray-700">← Back</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-6">

                        <div>
                            <InputLabel for="content" value="Your Take" />
                            <textarea
                                id="content"
                                v-model="form.content"
                                rows="4"
                                maxlength="280"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm resize-none"
                            />
                            <div class="flex justify-between mt-1">
                                <InputError :message="form.errors.content" />
                                <span class="text-xs text-gray-400 ml-auto">{{ form.content.length }}/280</span>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <Link
                                :href="route('takes.show', take.id)"
                                class="rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 disabled:opacity-50"
                            >
                                Save Changes
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
