<script setup lang="ts">
interface Props {
  propertyName: string
  oldValue: any
  newValue: any
}

defineProps<Props>()
</script>

<template>
  <div class="modified-values">
    <div class="value-old">
      <span class="value-label">❌ Old Value:</span>
      <pre>{{ JSON.stringify(oldValue, null, 2) }}</pre>
    </div>
    <div class="value-new">
      <span class="value-label">✅ New Value:</span>
      <pre>{{ JSON.stringify(newValue, null, 2) }}</pre>
    </div>
  </div>
</template>

<style scoped>
.modified-values {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-left: 1rem;
}

.value-old,
.value-new {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.value-label {
  font-weight: 500;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.value-old {
  background-color: hsla(0, 50%, 95%, 1);
  border-left: 4px solid hsla(0, 50%, 50%, 1);
  border-radius: 4px;
  padding: 0.75rem;
}

.value-old .value-label {
  color: hsla(0, 50%, 40%, 1);
}

.value-old pre {
  background-color: hsla(0, 50%, 98%, 1);
  color: hsla(0, 50%, 30%, 1);
  margin: 0;
  padding: 0.5rem;
  border-radius: 4px;
  overflow-x: auto;
  font-size: 0.9rem;
  line-height: 1.5;
  text-wrap: wrap;
}

.value-new {
  background-color: hsla(120, 50%, 95%, 1);
  border-left: 4px solid hsla(120, 50%, 50%, 1);
  border-radius: 4px;
  padding: 0.75rem;
}

.value-new .value-label {
  color: hsla(120, 50%, 40%, 1);
}

.value-new pre {
  background-color: hsla(120, 50%, 98%, 1);
  color: hsla(120, 50%, 30%, 1);
  margin: 0;
  padding: 0.5rem;
  border-radius: 4px;
  overflow-x: auto;
  font-size: 0.9rem;
  line-height: 1.5;
  text-wrap: wrap;
}
</style>

