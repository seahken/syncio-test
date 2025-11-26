<script setup lang="ts">
interface Props {
  propertyName: string
  oldValue: any
  newValue: any
}

defineProps<Props>()
</script>

<template>
  <div class="property-change">
    <div class="property-name-small">{{ propertyName }}:</div>
    <div class="property-values">
      <div class="property-value-old">
        <span class="value-label-small">Old:</span>
        <span class="value-text">{{ JSON.stringify(oldValue) }}</span>
      </div>
      <div class="property-value-new">
        <span class="value-label-small">New:</span>
        <span class="value-text">{{ JSON.stringify(newValue) }}</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.property-change {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 0.75rem;
  background-color: rgba(255, 255, 255, 0.5);
  border-radius: 4px;
}

.property-name-small {
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--vt-c-black-mute);
  margin-bottom: 0.25rem;
}

.property-values {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-left: 0.5rem;
}

.property-value-old,
.property-value-new {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
  padding: 0.5rem;
  border-radius: 4px;
}

.property-value-old {
  background-color: hsla(0, 50%, 95%, 1);
  border-left: 3px solid hsla(0, 50%, 50%, 1);
}

.property-value-new {
  background-color: hsla(120, 50%, 95%, 1);
  border-left: 3px solid hsla(120, 50%, 50%, 1);
}

.value-label-small {
  font-weight: 500;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  min-width: 40px;
}

.property-value-old .value-label-small {
  color: hsla(0, 50%, 40%, 1);
}

.property-value-new .value-label-small {
  color: hsla(120, 50%, 40%, 1);
}

.value-text {
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
  word-break: break-word;
}
</style>

