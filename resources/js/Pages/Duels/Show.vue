<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    duel: Object,
});

const page = usePage();
const authUser = page.props.auth?.user;

// Who the current user is in this duel
const isChallenger = computed(() => authUser?.id === props.duel.challenger.id);
const isOpponent = computed(() => authUser?.id === props.duel.opponent.id);
const isParticipant = computed(() => isChallenger.value || isOpponent.value);

// Is it my turn?
const isMyTurn = computed(() => {
    if (props.duel.status !== 'active') return false;
    if (isChallenger.value && props.duel.turn === 'challenger') return true;
    if (isOpponent.value && props.duel.turn === 'opponent') return true;
    return false;
});

// Can the viewer vote?
const canVote = computed(() =>
    authUser && !isParticipant.value && props.duel.status === 'finished'
);

// Move form
const moveForm = useForm({ response: '' });
function submitMove() {
    moveForm.post(route('duels.move', props.duel.id), {
        onSuccess: () => moveForm.reset(),
    });
}

// Vote form
const voteForm = useForm({ voted_for: null });
function vote(userId) {
    voteForm.voted_for = userId;
    voteForm.post(route('duels.vote', props.duel.id));
}

// Helper: participant name by role
function name(role) {
    return role === 'challenger' ? props.duel.challenger.name : props.duel.opponent.name;
}

// Vote counts
function voteCount(userId) {
    return props.duel.votes_stats?.[userId] ?? 0;
}

const totalVotes = computed(() =>
    Object.values(props.duel.votes_stats ?? {}).reduce((a, b) => a + b, 0)
);

function votePercent(userId) {
    if (!totalVotes.value) return 0;
    return Math.round((voteCount(userId) / totalVotes.value) * 100);
}

// Winner name
const winnerName = computed(() => {
    if (!props.duel.winner_id) return null;
    if (props.duel.winner_id === props.duel.challenger.id) return props.duel.challenger.name;
    if (props.duel.winner_id === props.duel.opponent.id) return props.duel.opponent.name;
    return null;
});

const statusLabel = computed(() => ({
    active: '🔥 In Progress',
    finished: '✅ Finished',
    pending: '⏳ Pending',
}[props.duel.status] ?? props.duel.status));
</script>

<template>
    <Head title="Duel" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ duel.challenger.name }} vs {{ duel.opponent.name }}
                </h2>
                <span class="text-sm text-gray-500">{{ statusLabel }}</span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8 space-y-6">

                <!-- Status bar -->
                <div class="bg-white shadow-sm sm:rounded-lg p-4 flex items-center justify-between text-sm">
                    <span class="text-gray-600">
                        Round <strong>{{ duel.current_round }}</strong> of <strong>{{ duel.total_rounds }}</strong>
                    </span>
                    <span v-if="duel.status === 'active'" class="text-gray-600">
                        Turn: <strong>{{ name(duel.turn) }}</strong>
                        <span v-if="isMyTurn" class="ml-2 text-green-600 font-semibold">(Your turn)</span>
                    </span>
                    <span v-if="duel.status === 'finished' && winnerName" class="font-semibold text-indigo-700">
                        🏆 {{ winnerName }} wins
                    </span>
                    <span v-else-if="duel.status === 'finished' && !winnerName" class="font-semibold text-gray-500">
                        Tied — no winner yet
                    </span>
                </div>

                <!-- Rounds -->
                <div
                    v-for="round in duel.rounds"
                    :key="round.round_number"
                    class="bg-white shadow-sm sm:rounded-lg overflow-hidden"
                >
                    <div class="bg-gray-50 border-b border-gray-100 px-6 py-3 text-sm font-semibold text-gray-500 uppercase tracking-wide">
                        Round {{ round.round_number }}
                    </div>
                    <div class="divide-y divide-gray-100">
                        <!-- Challenger response -->
                        <div class="p-6">
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">{{ duel.challenger.name }}</p>
                            <p v-if="round.challenger_response" class="text-gray-800">{{ round.challenger_response }}</p>
                            <p v-else class="text-gray-400 italic text-sm">Waiting for response…</p>
                        </div>
                        <!-- Opponent response -->
                        <div class="p-6">
                            <p class="text-xs font-semibold text-gray-400 uppercase mb-2">{{ duel.opponent.name }}</p>
                            <p v-if="round.opponent_response" class="text-gray-800">{{ round.opponent_response }}</p>
                            <p v-else class="text-gray-400 italic text-sm">Waiting for response…</p>
                        </div>
                    </div>
                </div>

                <!-- Submit move (participant, active duel, my turn) -->
                <div v-if="isMyTurn" class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-semibold text-gray-700 mb-3">Your response</p>
                    <form @submit.prevent="submitMove" class="space-y-4">
                        <textarea
                            v-model="moveForm.response"
                            rows="4"
                            maxlength="500"
                            placeholder="Make your move..."
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm resize-none"
                        />
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-400">{{ moveForm.response.length }}/500</span>
                            <button
                                type="submit"
                                :disabled="moveForm.processing || moveForm.response.trim().length === 0"
                                class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 disabled:opacity-50"
                            >
                                Submit Move
                            </button>
                        </div>
                        <p v-if="moveForm.errors.response" class="text-sm text-red-600">{{ moveForm.errors.response }}</p>
                    </form>
                </div>

                <!-- Voting section (finished duel, non-participants) -->
                <div v-if="duel.status === 'finished'" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <p class="text-sm font-semibold text-gray-700">Votes</p>

                    <!-- Vote bars -->
                    <div class="space-y-3">
                        <div v-for="participant in [duel.challenger, duel.opponent]" :key="participant.id">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ participant.name }}</span>
                                <span class="text-gray-500">{{ voteCount(participant.id) }} vote{{ voteCount(participant.id) !== 1 ? 's' : '' }} ({{ votePercent(participant.id) }}%)</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-gray-800 rounded-full transition-all duration-500"
                                    :style="{ width: votePercent(participant.id) + '%' }"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Vote buttons -->
                    <div v-if="canVote" class="flex gap-3 pt-2">
                        <button
                            v-for="participant in [duel.challenger, duel.opponent]"
                            :key="participant.id"
                            @click="vote(participant.id)"
                            :disabled="voteForm.processing"
                            class="flex-1 rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50 disabled:opacity-50"
                        >
                            Vote {{ participant.name }}
                        </button>
                    </div>

                    <p v-else-if="isParticipant" class="text-sm text-gray-400 italic">
                        Participants can't vote on their own duel.
                    </p>
                    <p v-else-if="!authUser" class="text-sm text-gray-400 italic">
                        Log in to vote.
                    </p>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
