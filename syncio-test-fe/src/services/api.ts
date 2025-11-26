const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

export interface PayloadResponse {
  status: 'cached' | 'comparison'
  message?: string
  diff?: PayloadDiff
}

export interface PayloadDiff {
  added: Record<string, any>
  removed: Record<string, any>
  modified: Record<string, any>
  unchanged: Record<string, any>
}

export async function sendPayload(payload: Record<string, any>): Promise<PayloadResponse> {
  const response = await fetch(`${API_BASE_URL}/payload/compare`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify(payload),
  })

  if (!response.ok) {
    const error = await response.json().catch(() => ({ error: 'Unknown error' }))
    throw new Error(error.error || `HTTP error! status: ${response.status}`)
  }

  return response.json()
}

