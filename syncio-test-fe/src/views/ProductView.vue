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
          <ais-stats />
          <ais-hits>
            <template #item="{ item }">
              <div class="product-card">
                <div class="product-card__image" v-if="item.images.length > 0">
                  <img :src="item.images[0]" :alt="item.title" />
                </div>
                <div class="product-card__content">
                  <h3 class="product-card__title">{{ item.title }}</h3>
                  <p class="product-card__description" v-html="item.description" />
                  <p class="product-card__price">${{ item.price }}</p>
                </div>
              </div>
            </template>
          </ais-hits>
          <ais-pagination />
        </div>
      </div>
    </ais-instant-search>
  </main>
</template>

<script setup lang="ts">
import { AisInstantSearch, AisSearchBox, AisRefinementList, AisHits, AisStats, AisPagination } from 'vue-instantsearch/vue3/es'
import { searchClient, PRODUCTS_INDEX } from '@/services/algolia'

console.log('searchClient', searchClient)

const indexName = PRODUCTS_INDEX
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
}

.product-view__search {
  width: 100%;
  max-width: 600px;
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
  background: var(--color-background-soft);
  padding: 1.5rem;
  border-radius: 8px;
  border: 1px solid var(--color-border);
}

.product-view__filters h2 {
  font-size: 1.2rem;
  margin-bottom: 1rem;
  color: var(--color-heading);
}

.product-view__main {
  min-height: 400px;
}

.product-view__main :deep(.ais-Stats) {
  margin-bottom: 1.5rem;
  color: var(--color-text);
  font-size: 0.9rem;
}

.product-view__main :deep(.ais-SearchBox) {
  margin-bottom: 1.5rem;
}

.product-view__main :deep(.ais-SearchBox-input) {
  width: 100%;
  padding: 0.75rem 1rem;
  font-size: 1rem;
  border: 1px solid var(--color-border);
  border-radius: 6px;
  background: var(--color-background);
  color: var(--color-text);
}

.product-view__main :deep(.ais-SearchBox-input:focus) {
  outline: none;
  border-color: hsla(160, 100%, 37%, 1);
}

.product-view__main :deep(.ais-RefinementList-list) {
  list-style: none;
  padding: 0;
  margin: 0;
}

.product-view__main :deep(.ais-RefinementList-item) {
  margin-bottom: 0.5rem;
}

.product-view__main :deep(.ais-RefinementList-label) {
  display: flex;
  align-items: center;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.product-view__main :deep(.ais-RefinementList-label:hover) {
  background-color: var(--color-background-mute);
}

.product-view__main :deep(.ais-RefinementList-checkbox) {
  margin-right: 0.5rem;
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

.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
  margin-bottom: 1rem;
  flex: 1;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
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
}

.product-card__category {
  font-size: 0.85rem;
  color: var(--color-text);
  opacity: 0.7;
  padding: 0.25rem 0.5rem;
  background: var(--color-background-mute);
  border-radius: 4px;
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