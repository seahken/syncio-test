<script setup lang="ts">
import PropertyChange from './PropertyChange.vue'

interface Props {
  item: {
    id: number | string
    changes: {
      modified: Record<string, { old: any; new: any }>
    }
  }
}

defineProps<Props>()
</script>

<template>
  <div class="array-item array-item-modified">
    <div class="item-id">ID: {{ item.id }}</div>
    <div class="item-changes">
      <PropertyChange
        v-for="(change, propKey) in item.changes?.modified"
        :key="propKey"
        :property-name="propKey"
        :old-value="change.old"
        :new-value="change.new"
      />
    </div>
  </div>
</template>

<style scoped>
.array-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 1rem;
  border-radius: 6px;
  border-left: 4px solid;
}

.array-item-modified {
  background-color: hsla(45, 50%, 95%, 1);
  border-color: hsla(45, 50%, 50%, 1);
}

.item-id {
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--vt-c-black-mute);
  margin-bottom: 0.25rem;
}

.item-changes {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 0.5rem;
}
</style>

