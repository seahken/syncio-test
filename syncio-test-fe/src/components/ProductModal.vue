<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="modal-overlay" @click.self="close">
        <div class="modal-container">
          <button class="modal-close" @click="close" aria-label="Close">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>

          <div class="modal-content">
            <div class="modal-left">
              <div v-if="product?.showcase_image || product?.images?.[0]" class="modal-image">
                <img :src="selectedImage || product?.showcase_image || product?.images?.[0]" :alt="product?.title" />
              </div>
              <div v-if="product?.images && product.images.length > 1" class="modal-thumbnails">
                <button
                  v-for="(image, index) in (product.images || []).slice(0, 4)"
                  :key="index"
                  :class="['thumbnail-button', { active: selectedImage === image }]"
                  @click="selectedImage = image"
                >
                  <img :src="image" :alt="`${product?.title || 'Product'} - Image ${index + 1}`" />
                </button>
              </div>
            </div>

            <div class="modal-right">
              <div class="modal-header">
                <h2 class="modal-title">{{ product?.title }}</h2>
              </div>

              <div class="modal-details">
                <div class="product-price-section">
                  <div class="price">${{ product?.price }}</div>
                  <div v-if="product?.taxable" class="tax-info">Tax included</div>
                </div>

                <div v-if="product?.color?.length || product?.product_type" class="product-tags-section">
                  <span 
                    v-for="(color, index) in product.color" 
                    :key="`color-${index}`"
                    class="product-tag product-tag--color"
                  >
                    {{ color }}
                  </span>
                  <span 
                    v-if="product.product_type"
                    class="product-tag product-tag--type"
                  >
                    {{ product.product_type }}
                  </span>
                </div>

                <div class="modal-tabs">
              <div class="tabs-header">
                <button
                  v-for="tab in tabs"
                  :key="tab.id"
                  :class="['tab-button', { active: activeTab === tab.id }]"
                  @click="activeTab = tab.id"
                >
                  {{ tab.label }}
                </button>
              </div>

              <div class="tabs-content">
                <div v-if="activeTab === 'overview'" class="tab-panel">
                  <div class="product-info">
                    <div v-if="product?.description" class="info-row full-width">
                      <span class="info-label">Description:</span>
                      <div class="info-value description" v-html="product.description"></div>
                    </div>
                    <div v-if="product?.product_type" class="info-row">
                      <span class="info-label">Type:</span>
                      <span class="info-value">{{ product.product_type }}</span>
                    </div>
                    <div v-if="product?.taxable !== undefined" class="info-row">
                      <span class="info-label">Taxable:</span>
                      <span class="info-value">{{ product.taxable ? 'Yes' : 'No' }}</span>
                    </div>
                  </div>
                </div>

                <div v-if="activeTab === 'specs'" class="tab-panel">
                  <div class="product-info">
                    <div v-if="product?.weight" class="info-row">
                      <span class="info-label">Weight:</span>
                      <span class="info-value">{{ product.weight }} lbs</span>
                    </div>
                    <div v-if="product?.units_sold !== undefined" class="info-row">
                      <span class="info-label">Units Sold:</span>
                      <span class="info-value">{{ product.units_sold.toLocaleString() }}</span>
                    </div>
                    <div v-if="product?.objectID" class="info-row">
                      <span class="info-label">Product ID:</span>
                      <span class="info-value">{{ product.objectID }}</span>
                    </div>
                  </div>
                </div>

                <div v-if="activeTab === 'details'" class="tab-panel">
                  <div class="product-info">
                    <div v-if="product?.color?.length" class="info-row full-width">
                      <span class="info-label">Colors:</span>
                      <div class="info-value">
                        <span v-for="(color, index) in product.color" :key="index" class="color-badge">
                          {{ color }}
                        </span>
                      </div>
                    </div>
                    <div v-if="product?.tags?.length" class="info-row full-width">
                      <span class="info-label">Tags:</span>
                      <div class="info-value">
                        <span v-for="(tag, index) in product.tags" :key="index" class="tag-badge">
                          {{ tag }}
                        </span>
                      </div>
                    </div>
                    <div v-if="product?.hierarchical_categories" class="info-row full-width">
                      <span class="info-label">Categories:</span>
                      <div class="info-value">
                        <div v-if="product.hierarchical_categories.lvl0" class="category-path">
                          {{ product.hierarchical_categories.lvl0 }}
                        </div>
                        <div v-if="product.hierarchical_categories.lvl1" class="category-path">
                          {{ product.hierarchical_categories.lvl1 }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch, onUnmounted } from 'vue'

interface Product {
  objectID: string
  product_type?: string
  title: string
  color?: string[]
  tags?: string[]
  description?: string
  hierarchical_categories?: {
    lvl0?: string
    lvl1?: string
  }
  images?: string[]
  price: number
  showcase_image?: string
  taxable?: boolean
  units_sold?: number
  weight?: number
}

interface Props {
  isOpen: boolean
  product: Product | null
}

interface Emits {
  (e: 'close'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const activeTab = ref('overview')
const selectedImage = ref<string | null>(null)

const tabs = [
  { id: 'overview', label: 'Overview' },
  { id: 'specs', label: 'Specifications' },
  { id: 'details', label: 'Details' }
]

let originalBodyOverflow = ''

const lockBodyScroll = () => {
  originalBodyOverflow = document.body.style.overflow
  document.body.style.overflow = 'hidden'
}

const unlockBodyScroll = () => {
  document.body.style.overflow = originalBodyOverflow
}

const close = () => {
  emit('close')
}

// Lock/unlock body scroll when modal opens/closes
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    activeTab.value = 'overview'
    selectedImage.value = null
    lockBodyScroll()
  } else {
    unlockBodyScroll()
  }
})

// Ensure scroll is unlocked when component is destroyed
onUnmounted(() => {
  unlockBodyScroll()
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-container {
  background: var(--color-background);
  border-radius: 12px;
  max-width: 1200px;
  width: 100%;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  overflow: hidden;
  position: relative;
}

.modal-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  border-radius: 50%;
  cursor: pointer;
  padding: 0.5rem;
  color: var(--color-text);
  opacity: 0.8;
  transition: opacity 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
  width: 36px;
  height: 36px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.modal-close:hover {
  opacity: 1;
}

.modal-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  overflow: hidden;
  flex: 1;
}

.modal-left {
  display: flex;
  flex-direction: column;
  padding: 2rem;
  gap: 1rem;
  overflow-y: auto;
  border-right: 1px solid var(--color-border);
}

.modal-image {
  width: 100%;
  aspect-ratio: 1;
  overflow: hidden;
  background: var(--color-background-mute);
  border-radius: 8px;
}

.modal-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.modal-thumbnails {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
}

.thumbnail-button {
  width: 80px;
  height: 80px;
  border: 2px solid transparent;
  border-radius: 8px;
  overflow: hidden;
  background: var(--color-background-mute);
  cursor: pointer;
  padding: 0;
  transition: all 0.2s;
}

.thumbnail-button:hover {
  border-color: var(--color-border);
}

.thumbnail-button.active {
  border-color: hsla(160, 100%, 37%, 1);
}

.thumbnail-button img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.modal-right {
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.modal-header {
  padding: 2rem 2rem 1rem 2rem;
}

.modal-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: var(--color-heading);
  margin: 0;
  line-height: 1.3;
}

.modal-details {
  padding: 0 2rem 2rem 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.product-price-section {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.price {
  font-size: 2rem;
  font-weight: 700;
  color: hsla(160, 100%, 37%, 1);
}

.tax-info {
  font-size: 0.875rem;
  color: var(--color-text);
  opacity: 0.7;
}

.product-tags-section {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.product-tag {
  display: inline-block;
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 6px;
  text-transform: capitalize;
}

.product-tag--color {
  background: var(--color-background-mute);
  border: 1px solid var(--color-border);
  color: var(--color-text);
}

.product-tag--type {
  background: hsla(160, 100%, 37%, 0.1);
  border: 1px solid hsla(160, 100%, 37%, 0.3);
  color: hsla(160, 100%, 37%, 1);
}

.modal-tabs {
  padding: 0;
}

.tabs-header {
  display: flex;
  gap: 0.5rem;
  border-bottom: 1px solid var(--color-border);
  margin-bottom: 1.5rem;
}

.tab-button {
  background: none;
  border: none;
  padding: 0.75rem 1rem;
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--color-text);
  opacity: 0.7;
  cursor: pointer;
  border-bottom: 2px solid transparent;
  margin-bottom: -1px;
  transition: all 0.2s;
}

.tab-button:hover {
  opacity: 1;
}

.tab-button.active {
  opacity: 1;
  color: hsla(160, 100%, 37%, 1);
  border-bottom-color: hsla(160, 100%, 37%, 1);
}

.tabs-content {
  min-height: 200px;
}

.tab-panel {
  animation: fadeIn 0.2s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.product-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.info-row {
  display: grid;
  grid-template-columns: 120px 1fr;
  gap: 1rem;
  align-items: start;
}

.info-row.full-width {
  grid-template-columns: 1fr;
  flex-direction: column;
}

.info-label {
  font-weight: 500;
  color: var(--color-heading);
  font-size: 0.9rem;
}

.info-value {
  color: var(--color-text);
  font-size: 0.9rem;
}

.info-value.price {
  font-size: 1.5rem;
  font-weight: 600;
  color: hsla(160, 100%, 37%, 1);
}

.info-value.description {
  line-height: 1.6;
  margin-top: 0.5rem;
}

.color-badge,
.tag-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  margin: 0.25rem 0.5rem 0.25rem 0;
  background: var(--color-background-soft);
  border: 1px solid var(--color-border);
  border-radius: 6px;
  font-size: 0.85rem;
}

.category-path {
  padding: 0.5rem 0;
  color: var(--color-text);
  font-size: 0.9rem;
}

/* Modal transitions */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
  transform: scale(0.95);
  opacity: 0;
}

@media (max-width: 968px) {
  .modal-content {
    grid-template-columns: 1fr;
  }

  .modal-left {
    border-right: none;
    border-bottom: 1px solid var(--color-border);
    max-height: 50vh;
  }

  .modal-image {
    aspect-ratio: 4/3;
  }
}

@media (max-width: 768px) {
  .modal-container {
    max-width: 100%;
    max-height: 100vh;
    border-radius: 0;
  }

  .modal-overlay {
    padding: 0;
  }

  .modal-left {
    padding: 1rem;
  }

  .modal-header,
  .modal-details {
    padding: 1rem;
  }

  .info-row {
    grid-template-columns: 1fr;
  }

  .tabs-header {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .price {
    font-size: 1.5rem;
  }
}
</style>

