<script setup lang="ts">
import { ref, computed, onUnmounted } from 'vue'
import { sendPayload, type PayloadResponse, type PayloadDiff } from '../services/api'
import DiffDisplay from '../components/DiffDisplay.vue'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import { payload1, payload2 } from '../data'

const payload1Sent = ref(false)
const payload2Sent = ref(false)
const payload1Content = ref<string>(JSON.stringify(payload1, null, 2))
const payload2Content = ref<string>(JSON.stringify(payload2, null, 2))
const payload1Confirmation = ref<string>('')
const payload2Confirmation = ref<string>('')
const comparisonResult = ref<PayloadDiff | null>(null)
const isLoading = ref(false)
const loadingAction = ref<'payload1' | 'payload2' | null>(null)
const error = ref<string>('')
const countdown = ref<number | null>(null)
const countdownInterval = ref<number | null>(null)

const hasComparison = computed(() => comparisonResult.value !== null)

// Helper function to add delay (for demo purposes to see loading animation)
const delay = (ms: number) => new Promise(resolve => setTimeout(resolve, ms))

const sendPayload1 = async () => {
  isLoading.value = true
  loadingAction.value = 'payload1'
  error.value = ''
  payload1Confirmation.value = ''

  try {
    // Add delay to see loading animation better
    await delay(1500)
    const response = await sendPayload(JSON.parse(payload1Content.value))
    payload1Sent.value = true
    payload1Confirmation.value = response.message || 'Payload 1 sent successfully!'
    
    // Start countdown for 30 seconds
    startCountdown()
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to send Payload 1'
    payload1Sent.value = false
  } finally {
    isLoading.value = false
    loadingAction.value = null
  }
}

const startCountdown = () => {
  countdown.value = 4
  
  countdownInterval.value = window.setInterval(() => {
    if (countdown.value !== null && countdown.value > 1) {
      countdown.value--
    } else {
        // Automatically send payload 2 after countdown
      if (!payload2Sent.value) {
        sendPayload2()
      }
      stopCountdown()
    }
  }, 1000)
}

const stopCountdown = () => {
  if (countdownInterval.value) {
    clearInterval(countdownInterval.value)
    countdownInterval.value = null
    countdown.value = null
  }
}

const sendPayload2 = async () => {
  if (!payload1Sent.value) {
    error.value = 'Please send Payload 1 first'
    return
  }

  isLoading.value = true
  loadingAction.value = 'payload2'
  error.value = ''
  payload2Confirmation.value = ''

  try {
    // Add delay to see loading animation better
    await delay(1500)
    const response = await sendPayload(JSON.parse(payload2Content.value))
    payload2Sent.value = true
    payload2Confirmation.value = 'Payload 2 sent successfully!'
    
    if (response.status === 'comparison' && response.diff) {
      comparisonResult.value = response.diff
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to send Payload 2'
    payload2Sent.value = false
  } finally {
    isLoading.value = false
    loadingAction.value = null
    stopCountdown()
  }
}

const reset = () => {
  payload1Sent.value = false
  payload2Sent.value = false
  payload1Confirmation.value = ''
  payload2Confirmation.value = ''
  comparisonResult.value = null
  error.value = ''
  countdown.value = null
  loadingAction.value = null
  if (countdownInterval.value) {
    clearInterval(countdownInterval.value)
    countdownInterval.value = null
  }
}

// Cleanup on unmount
onUnmounted(() => {
  if (countdownInterval.value) {
    clearInterval(countdownInterval.value)
  }
})
</script>

<template>
  <main class="compare-view">
    <h1>Payload Comparison</h1>
    
    <div class="actions">
      <button 
        @click="sendPayload1" 
        :disabled="isLoading || payload1Sent"
        class="btn btn-primary"
      >
        Send Payload 1
      </button>
      
      <button 
        @click="sendPayload2" 
        :disabled="isLoading || !payload1Sent || payload2Sent"
        class="btn btn-secondary"
      >
        Send Payload 2
      </button>
      
      <button 
        @click="reset" 
        :disabled="isLoading"
        class="btn btn-reset"
      >
        Reset
      </button>
    </div>

    <div class="compare-view__content">
      <h2>Payload 1</h2>
      <textarea v-model="payload1Content" class="compare-view__textarea"></textarea>
    </div>
    <div class="compare-view__content"> 
      <h2>Payload 2</h2>
      <textarea v-model="payload2Content" class="compare-view__textarea"></textarea>
    </div>

    <LoadingSpinner 
      v-if="isLoading" 
      :message="loadingAction === 'payload1' ? 'Sending Payload 1...' : loadingAction === 'payload2' ? 'Sending Payload 2 and comparing...' : 'Loading...'" 
    />

    <div v-if="countdown !== null && countdown > 0 && !isLoading" class="countdown">
      <p>Payload 2 will be sent automatically in <strong>{{ countdown }}</strong> seconds...</p>
    </div>

    <div v-if="error" class="error-message">
      <p>{{ error }}</p>
    </div>

    <div v-if="payload1Confirmation" class="confirmation confirmation-1">
      <p>✅ {{ payload1Confirmation }}</p>
    </div>

    <div v-if="payload2Confirmation" class="confirmation confirmation-2">
      <p>✅ {{ payload2Confirmation }}</p>
    </div>

    <DiffDisplay v-if="hasComparison && comparisonResult" :diff="comparisonResult" />
  </main>
</template>

<style scoped>
.compare-view {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  margin-bottom: 2rem;
  color: var(--color-heading);
}

.actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background-color: hsla(160, 100%, 37%, 1);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: hsla(160, 100%, 37%, 0.8);
}

.btn-secondary {
  background-color: hsla(217, 91%, 60%, 1);
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background-color: hsla(217, 91%, 60%, 0.8);
}

.btn-reset {
  background-color: hsla(0, 0%, 50%, 1);
  color: white;
}

.btn-reset:hover:not(:disabled) {
  background-color: hsla(0, 0%, 40%, 1);
}

.countdown {
  padding: 1rem;
  background-color: hsla(217, 91%, 60%, 0.1);
  border-left: 4px solid hsla(217, 91%, 60%, 1);
  border-radius: 4px;
  margin-bottom: 1rem;
}

.countdown strong {
  color: hsla(217, 91%, 60%, 1);
  font-size: 1.2rem;
}

.error-message {
  padding: 1rem;
  background-color: hsla(0, 100%, 50%, 0.1);
  border-left: 4px solid hsla(0, 100%, 50%, 1);
  border-radius: 4px;
  margin-bottom: 1rem;
  color: hsla(0, 100%, 50%, 1);
}

.confirmation {
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}

.confirmation-1 {
  background-color: hsla(160, 100%, 37%, 0.1);
  border-left: 4px solid hsla(160, 100%, 37%, 1);
  color: hsla(160, 100%, 37%, 1);
}

.confirmation-2 {
  background-color: hsla(217, 91%, 60%, 0.1);
  border-left: 4px solid hsla(217, 91%, 60%, 1);
  color: hsla(217, 91%, 60%, 1);
}

@media (max-width: 768px) {
  .compare-view {
    padding: 1rem;
  }

  .actions {
    flex-direction: column;
  }

  .btn {
    width: 100%;
  }
}
</style>

