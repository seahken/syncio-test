<template>
  <main class="product-view">
    <ais-instant-search :search-client="searchClient" :index-name="indexName">
      <div class="product-view__header">
        <h1>Products</h1>
        <div class="product-view__search">
          <ais-search-box placeholder="Search products..." />
        </div>
      </div>

      <div class="product-view__content">
        <aside class="product-view__sidebar">
          <div class="product-view__filters">
            <h2>Colors</h2>
            <ais-refinement-list
              attribute="color"
              :limit="5"
              :show-more="true"
              :show-more-limit="20"
            />
          </div>
          <div class="product-view__filters">
            <h2>Tags</h2>
            <ais-refinement-list
              attribute="tags"
              :limit="5"
              :show-more="true"
              :show-more-limit="20"
            />
          </div>
        </aside>

        <div class="product-view__main">
          <ais-state-results class="product-view__loading">
            <template v-slot="{ status }">
              <LoadingSpinner v-if="status === 'loading' || status === 'stalled'"
              class="product-view__loading-spinner" 
              />
            </template>              
          </ais-state-results>
          <ais-stats />
          <ais-hits>
            <template #item="{ item }">
              <div class="product-card">
                <div class="product-card__image" v-if="item.showcase_image">
                  <img :src="item.showcase_image" :alt="item.title" />
                </div>
                <div class="product-card__content">
                  <h3 class="product-card__title">{{ item.title }}</h3>
                  <p class="product-card__description" v-html="item.description" />
                  <div v-if="item.color?.length || item.product_type" class="product-card__variants">
                    <span 
                      v-for="(color, index) in item.color" 
                      :key="`color-${index}`"
                      class="variant-tag variant-tag--color"
                    >
                      {{ color }}
                    </span>
                    <span 
                      v-if="item.product_type"
                      class="variant-tag variant-tag--type"
                    >
                      {{ item.product_type }}
                    </span>
                  </div>
                  <div class="product-card__price-container">
                    <p class="product-card__price">${{ item.price }}</p>
                    <button @click="openModal(item)" class="product-card__see-more">See more</button>
                  </div>
                </div>
              </div>
            </template>
          </ais-hits>
          <ais-pagination />
        </div>
      </div>
    </ais-instant-search>

    <ProductModal :is-open="isModalOpen" :product="selectedProduct" @close="closeModal" />
  </main>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { 
  AisInstantSearch, 
  AisSearchBox, 
  AisRefinementList, 
  AisHits, 
  AisStats, 
  AisPagination, 
  AisStateResults 
} from 'vue-instantsearch/vue3/es'
import { searchClient, PRODUCTS_INDEX } from '@/services/algolia'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import ProductModal from '@/components/ProductModal.vue'

console.log('searchClient', searchClient)

const indexName = PRODUCTS_INDEX

const isModalOpen = ref(false)
const selectedProduct = ref<any>(null)

const openModal = (product: any) => {
  selectedProduct.value = product
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  // Clear product after animation completes
  setTimeout(() => {
    selectedProduct.value = null
  }, 300)
}
</script>

<style scoped>
.product-view {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
}

.product-view__header {
  margin-bottom: 2rem;
}

.product-view__header h1 {
  margin-bottom: 1.5rem;
  color: var(--color-heading);
  font-size: 2rem;
  font-weight: 300;
  letter-spacing: -0.02em;
}

.product-view__search {
  width: 100%;
  max-width: 600px;
  position: relative;
}

.product-view__search :deep(.ais-SearchBox) {
  position: relative;
}

.product-view__search :deep(.ais-SearchBox-form) {
  position: relative;
  display: flex;
  align-items: center;
}

.product-view__search :deep(.ais-SearchBox-input) {
  width: 100%;
  padding: 0.875rem 1rem 0.875rem 2.75rem;
  font-size: 0.95rem;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  background: var(--color-background);
  color: var(--color-text);
  transition: all 0.2s ease;
  font-weight: 400;
}

.product-view__search :deep(.ais-SearchBox-input::placeholder) {
  color: var(--color-text);
  opacity: 0.5;
}

.product-view__search :deep(.ais-SearchBox-input:focus) {
  outline: none;
  border-color: hsla(160, 100%, 37%, 0.5);
  box-shadow: 0 0 0 3px hsla(160, 100%, 37%, 0.1);
}

.product-view__search :deep(.ais-SearchBox-submit),
.product-view__search :deep(.ais-SearchBox-reset) {
  position: absolute;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text);
  opacity: 0.5;
  transition: opacity 0.2s;
}

.product-view__search :deep(.ais-SearchBox-submit) {
  left: 0.75rem;
}

.product-view__search :deep(.ais-SearchBox-reset) {
  right: 0.75rem;
}

.product-view__search :deep(.ais-SearchBox-submit:hover),
.product-view__search :deep(.ais-SearchBox-reset:hover) {
  opacity: 1;
}

.product-view__search :deep(.ais-SearchBox-submit svg),
.product-view__search :deep(.ais-SearchBox-reset svg) {
  width: 18px;
  height: 18px;
}

.product-view__loading {  
  position: absolute;
  height: 300px;
  width: 100%;
}

.product-view__loading :deep(p),
.product-view__loading :deep(pre) {
  display: none;
}

.product-view__loading-spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.product-view__content {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 2rem;
  align-items: start;
}

.product-view__sidebar {
  position: sticky;
  top: 2rem;
}

.product-view__filters {
  background: transparent;
  padding: 0;
  margin-bottom: 2rem;
  border: none;
}

.product-view__filters:last-child {
  margin-bottom: 0;
}

.product-view__filters h2 {
  font-size: 0.875rem;
  font-weight: 500;
  margin-bottom: 1rem;
  color: var(--color-heading);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  opacity: 0.7;
}

.product-view__filters :deep(.ais-RefinementList) {
  margin-top: 0.5rem;
}

.product-view__filters :deep(.ais-RefinementList-list) {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.product-view__filters :deep(.ais-RefinementList-item) {
  margin: 0;
}

.product-view__filters :deep(.ais-RefinementList-label) {
  display: flex;
  align-items: center;
  cursor: pointer;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  transition: all 0.15s ease;
  font-size: 0.9rem;
  color: var(--color-text);
  user-select: none;
}

.product-view__filters :deep(.ais-RefinementList-label:hover) {
  background-color: var(--color-background-mute);
}

.product-view__filters :deep(.ais-RefinementList-item--selected .ais-RefinementList-label) {
  background-color: hsla(160, 100%, 37%, 0.1);
  color: hsla(160, 100%, 37%, 1);
  font-weight: 500;
}

.product-view__filters :deep(.ais-RefinementList-checkbox) {
  margin-right: 0.75rem;
  width: 16px;
  height: 16px;
  border: 1.5px solid var(--color-border);
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.15s ease;
  flex-shrink: 0;
  appearance: none;
  -webkit-appearance: none;
  position: relative;
}

.product-view__filters :deep(.ais-RefinementList-checkbox:checked) {
  background-color: hsla(160, 100%, 37%, 1);
  border-color: hsla(160, 100%, 37%, 1);
}

.product-view__filters :deep(.ais-RefinementList-checkbox:checked::after) {
  content: '';
  position: absolute;
  left: 4px;
  top: 1px;
  width: 5px;
  height: 9px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

.product-view__filters :deep(.ais-RefinementList-item--selected .ais-RefinementList-checkbox) {
  background-color: hsla(160, 100%, 37%, 1);
  border-color: hsla(160, 100%, 37%, 1);
}

.product-view__filters :deep(.ais-RefinementList-count) {
  margin-left: auto;
  font-size: 0.85rem;
  color: var(--color-text);
  opacity: 0.6;
  padding-left: 0.5rem;
}

.product-view__filters :deep(.ais-RefinementList-item--selected .ais-RefinementList-count) {
  opacity: 0.8;
}

.product-view__filters :deep(.ais-RefinementList-showMore) {
  margin-top: 0.75rem;
  padding: 0.5rem 0.75rem;
  font-size: 0.85rem;
  color: hsla(160, 100%, 37%, 1);
  background: none;
  border: none;
  cursor: pointer;
  text-decoration: underline;
  text-underline-offset: 2px;
  transition: opacity 0.2s;
}

.product-view__filters :deep(.ais-RefinementList-showMore:hover) {
  opacity: 0.7;
}

.product-view__main {
  min-height: 400px;
  position: relative;
}

.product-view__main :deep(.ais-Stats) {
  margin-bottom: 1.5rem;
  color: var(--color-text);
  font-size: 0.875rem;
  opacity: 0.7;
  font-weight: 400;
}

.product-view__main :deep(.ais-Hits-list) {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
  list-style: none;
  padding: 0;
  margin: 0 0 2rem 0;
}

.product-view__main :deep(.ais-Pagination-list) {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  list-style: none;
  padding: 0;
  margin: 2rem 0 0 0;
}

.product-view__main :deep(.ais-Pagination-item) {
  display: inline-block;
}

.product-view__main :deep(.ais-Pagination-link) {
  display: block;
  padding: 0.5rem 1rem;
  border: 1px solid var(--color-border);
  border-radius: 4px;
  text-decoration: none;
  color: var(--color-text);
  transition: all 0.2s;
}

.product-view__main :deep(.ais-Pagination-link:hover) {
  background-color: var(--color-background-soft);
  border-color: var(--color-border-hover);
}

.product-view__main :deep(.ais-Pagination-item--selected .ais-Pagination-link) {
  background-color: hsla(160, 100%, 37%, 1);
  color: white;
  border-color: hsla(160, 100%, 37%, 1);
}

.product-card {
  background: var(--color-background-soft);
  border: 1px solid var(--color-border);
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.product-card__image {
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: var(--color-background-mute);
}

.product-card__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-card__content {
  padding: 1rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-card__title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: var(--color-heading);
}

.product-card__description {
  font-size: 0.9rem;
  color: var(--color-text);
  margin-bottom: 0.75rem;
  flex: 1;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-card__variants {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.variant-tag {
  display: inline-block;
  padding: 0.25rem 0.625rem;
  font-size: 0.75rem;
  font-weight: 500;
  border-radius: 4px;
  text-transform: capitalize;
  line-height: 1.4;
}

.variant-tag--color {
  background: var(--color-background-mute);
  border: 1px solid var(--color-border);
  color: var(--color-text);
}

.variant-tag--type {
  background: hsla(160, 100%, 37%, 0.1);
  border: 1px solid hsla(160, 100%, 37%, 0.3);
  color: hsla(160, 100%, 37%, 1);
}

.product-card__meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid var(--color-border);
}

.product-card__price {
  font-size: 1.2rem;
  font-weight: 600;
  color: hsla(160, 100%, 37%, 1);
  margin-top: auto;
}

.product-card__category {
  font-size: 0.85rem;
  color: var(--color-text);
  opacity: 0.7;
  padding: 0.25rem 0.5rem;
  background: var(--color-background-mute);
  border-radius: 4px;
}

.product-card__price-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.product-card__see-more {
  background: hsla(160, 100%, 37%, 0.1);
  border: 1px solid hsla(160, 100%, 37%, 0.3);
  color: hsla(160, 100%, 37%, 1);
  border-radius: 4px;
  cursor: pointer;
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
  color: var(--color-text);
  transition: all 0.2s;

}
.product-card__see-more:hover {
  background: hsla(160, 100%, 37%, 0.2);
  border-color: hsla(160, 100%, 37%, 0.5);
  color: hsla(160, 100%, 37%, 1);
}

@media (max-width: 768px) {
  .product-view__content {
    grid-template-columns: 1fr;
  }

  .product-view__sidebar {
    position: static;
  }

  .product-view__main :deep(.ais-Hits-list) {
    grid-template-columns: 1fr;
  }
}
</style>