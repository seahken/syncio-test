<script setup lang="ts">
import ArrayItemList from './ArrayItemList.vue'
import ModifiedArrayItem from './ModifiedArrayItem.vue'

interface Props {
  propertyName: string
  data: {
    added?: any[]
    removed?: any[]
    modified?: Array<{
      id: number | string
      changes: {
        modified: Record<string, { old: any; new: any }>
      }
    }>
  }
}

const props = defineProps<Props>()

const hasArrayItems = (arr: any[] | undefined): boolean => {
  return Array.isArray(arr) && arr.length > 0
}
</script>

<template>
  <div class="modified-array">
    <ArrayItemList 
      v-if="hasArrayItems(data.added)" 
      :items="data.added!" 
      type="added" 
    />
    
    <ArrayItemList 
      v-if="hasArrayItems(data.removed)" 
      :items="data.removed!" 
      type="removed" 
    />
    
    <div v-if="hasArrayItems(data.modified)" class="array-section array-modified">
      <div class="array-header">
        <span class="array-icon">✏️</span>
        <span class="array-label">Modified Items ({{ data.modified!.length }})</span>
      </div>
      <div class="array-items">
        <ModifiedArrayItem
          v-for="(modifiedItem, index) in data.modified"
          :key="`modified-${index}`"
          :item="modifiedItem"
        />
      </div>
    </div>
  </div>
</template>

<style scoped>
.modified-array {
  margin-left: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.array-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.array-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  font-size: 1rem;
}

.array-icon {
  font-size: 1.2rem;
}

.array-label {
  color: var(--vt-c-black-mute);
}

.array-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-left: 1rem;
}
</style>

