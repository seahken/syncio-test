<script setup lang="ts">
import { computed } from 'vue'
import type { PayloadDiff } from '../services/api'
import { isSimpleModification, isArrayModification } from '../utils/diffHelpers'
import ModifiedField from './diff/ModifiedField.vue'

interface Props {
  diff: PayloadDiff
}

const props = defineProps<Props>()

// Format modified fields for display
const formattedModified = computed(() => {
  const result: Array<{
    key: string
    oldValue: any
    newValue: any
    isNested: boolean
    isArray: boolean
    nestedData?: any
  }> = []
  
  Object.entries(props.diff.modified).forEach(([key, value]) => {
    if (isSimpleModification(value)) {
      result.push({
        key,
        oldValue: value.old,
        newValue: value.new,
        isNested: false,
        isArray: false
      })
    } else if (isArrayModification(value)) {
      result.push({
        key,
        oldValue: null,
        newValue: null,
        isNested: true,
        isArray: true,
        nestedData: value
      })
    } else {
      // Other nested structures
      result.push({
        key,
        oldValue: null,
        newValue: null,
        isNested: true,
        isArray: false,
        nestedData: value
      })
    }
  })
  
  return result
})
</script>

<template>
  <div class="comparison-results">
    <h2>Comparison Results</h2>
    
    <div v-if="Object.keys(diff.added).length > 0" class="diff-section added">
      <h3>➕ Added Fields</h3>
      <pre>{{ JSON.stringify(diff.added, null, 2) }}</pre>
    </div>

    <div v-if="Object.keys(diff.removed).length > 0" class="diff-section removed">
      <h3>➖ Removed Fields</h3>
      <pre>{{ JSON.stringify(diff.removed, null, 2) }}</pre>
    </div>

    <div v-if="Object.keys(diff.modified).length > 0" class="diff-section modified">
      <h3>✏️ Modified Fields</h3>
      <div class="modified-content">
        <ModifiedField
          v-for="item in formattedModified"
          :key="item.key"
          :field="item"
        />
      </div>
    </div>

    <div v-if="Object.keys(diff.unchanged).length > 0" class="diff-section unchanged">
      <h3>✓ Unchanged Fields</h3>
      <pre>{{ JSON.stringify(diff.unchanged, null, 2) }}</pre>
    </div>
  </div>
</template>

<style scoped>
.comparison-results {
  margin-top: 2rem;
  color: var(--vt-c-black-mute);
}

h2 {
  margin-top: 2rem;
  margin-bottom: 1rem;
  color: var(--color-heading);
}

h3 {
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.diff-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  border-radius: 8px;
  border: 2px solid;
}

.diff-section h3 {
  margin-top: 0;
  margin-bottom: 1rem;
}

.diff-section.added {
  background-color: hsla(120, 50%, 95%, 1);
  border-color: hsla(120, 50%, 50%, 1);
}

.diff-section.removed {
  background-color: hsla(0, 50%, 95%, 1);
  border-color: hsla(0, 50%, 50%, 1);
}

.diff-section.modified {
  background-color: hsla(45, 50%, 95%, 1);
  border-color: hsla(45, 50%, 50%, 1);
}

.modified-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.diff-section.unchanged {
  background-color: hsla(0, 0%, 95%, 1);
  border-color: hsla(0, 0%, 70%, 1);
  opacity: 0.7;
}

.diff-section pre {
  margin: 0;
  padding: 1rem;
  background-color: rgba(0, 0, 0, 0.05);
  border-radius: 4px;
  overflow-x: auto;
  font-size: 0.9rem;
  line-height: 1.5;
}
</style>

