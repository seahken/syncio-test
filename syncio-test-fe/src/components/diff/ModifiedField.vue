<script setup lang="ts">
import SimpleFieldModification from './SimpleFieldModification.vue'
import ArrayModification from './ArrayModification.vue'

interface Props {
  field: {
    key: string
    oldValue: any
    newValue: any
    isNested: boolean
    isArray: boolean
    nestedData?: any
  }
}

defineProps<Props>()
</script>

<template>
  <div class="modified-item">
    <div class="modified-key">
      <span class="property-name">{{ field.key }}</span>
      <span class="property-type" v-if="field.isArray">(Array)</span>
    </div>
    
    <!-- Simple property modification -->
    <SimpleFieldModification
      v-if="!field.isNested"
      :property-name="field.key"
      :old-value="field.oldValue"
      :new-value="field.newValue"
    />
    
    <!-- Array modification -->
    <ArrayModification
      v-else-if="field.isArray"
      :property-name="field.key"
      :data="field.nestedData"
    />
    
    <!-- Other nested structures -->
    <div v-else class="modified-nested">
      <pre>{{ JSON.stringify(field.nestedData, null, 2) }}</pre>
    </div>
  </div>
</template>

<style scoped>
.modified-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.modified-key {
  font-weight: 600;
  font-size: 1.1rem;
  color: var(--color-heading);
  margin-bottom: 0.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid hsla(45, 50%, 50%, 0.3);
}

.property-name {
  font-weight: 700;
  text-transform: capitalize;
  color: var(--vt-c-black-mute);
}

.property-type {
  font-weight: 400;
  font-size: 0.85rem;
  color: hsla(0, 0%, 50%, 1);
  margin-left: 0.5rem;
}

.modified-nested {
  margin-left: 1rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.modified-nested pre {
  margin: 0;
  padding: 1rem;
  background-color: rgba(0, 0, 0, 0.05);
  border-radius: 4px;
  overflow-x: auto;
  font-size: 0.9rem;
  line-height: 1.5;
  text-wrap: wrap;
}
</style>

