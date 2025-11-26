// Helper function to check if a value is a simple modification (has old/new)
export function isSimpleModification(value: any): value is { old: any; new: any } {
    return value && typeof value === 'object' && 'old' in value && 'new' in value && !Array.isArray(value)
  }
  
  // Check if it's an array modification structure (has added/removed/modified)
  export function isArrayModification(value: any): boolean {
    return value && typeof value === 'object' && 
      ('added' in value || 'removed' in value || 'modified' in value) &&
      !('old' in value && 'new' in value)
  }
  
  // Helper to check if array has items
  export function hasArrayItems(arr: any[] | undefined): boolean {
    return Array.isArray(arr) && arr.length > 0
  }
  