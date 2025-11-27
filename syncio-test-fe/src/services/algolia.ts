import { liteClient } from 'algoliasearch/lite'

// Algolia configuration
// Replace these with your actual Algolia credentials
export const searchClient = liteClient(
  import.meta.env.VITE_ALGOLIA_APP_ID || 'YOUR_APP_ID',
  import.meta.env.VITE_ALGOLIA_API_KEY || 'YOUR_API_KEY'
)

// Index name - replace with your actual index name
export const PRODUCTS_INDEX = import.meta.env.VITE_ALGOLIA_INDEX_NAME || 'products'

