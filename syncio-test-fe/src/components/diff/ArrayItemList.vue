<script setup lang="ts">
interface Props {
  items: any[]
  type: 'added' | 'removed'
}

defineProps<Props>()
</script>

<template>
  <div class="array-section" :class="`array-${type}`">
    <div class="array-header">
      <span class="array-icon">{{ type === 'added' ? '➕' : '➖' }}</span>
      <span class="array-label">
        {{ type === 'added' ? 'Added' : 'Removed' }} Items ({{ items.length }})
      </span>
    </div>
    <div class="array-items">
      <div 
        v-for="(item, index) in items" 
        :key="`${type}-${index}`"
        class="array-item"
        :class="`array-item-${type}`"
      >
        <div v-if="item.id" class="item-id">ID: {{ item.id }}</div>
        <pre>{{ JSON.stringify(item, null, 2) }}</pre>
      </div>
    </div>
  </div>
</template>

<style scoped>
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

.array-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 1rem;
  border-radius: 6px;
  border-left: 4px solid;
}

.array-item-added {
  background-color: hsla(120, 50%, 95%, 1);
  border-color: hsla(120, 50%, 50%, 1);
}

.array-item-removed {
  background-color: hsla(0, 50%, 95%, 1);
  border-color: hsla(0, 50%, 50%, 1);
}

.item-id {
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--vt-c-black-mute);
  margin-bottom: 0.25rem;
}

.array-item pre {
  margin: 0;
  padding: 0.5rem;
  background-color: rgba(255, 255, 255, 0.7);
  border-radius: 4px;
  overflow-x: auto;
  font-size: 0.85rem;
  line-height: 1.4;
  text-wrap: wrap;
}
</style>

