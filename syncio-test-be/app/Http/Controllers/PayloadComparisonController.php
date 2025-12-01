<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PayloadComparisonController extends Controller
{
    /**
     * Cache TTL in hours
     */
    private const CACHE_TTL_HOURS = 24;

    /**
     * Handle payload comparison endpoint
     * First request: stores payload in cache
     * Second request: compares with cached payload and returns diff
     */
    public function compare(Request $request): JsonResponse
    {
        $payload = $request->all();
        
        // Validate that payload has an id
        if (!isset($payload['id'])) {
            return response()->json([
                'error' => 'Payload must contain an id field'
            ], 400);
        }

        $cacheKey = 'payload_' . $payload['id'];

        // Check if cached payload exists
        $cachedPayload = Cache::get($cacheKey);

        if ($cachedPayload !== null) {
            // Second request: compare and return diff
            try {
                $diff = $this->calculateDiff($cachedPayload, $payload);
                
                // Update cache with new payload
                Cache::put($cacheKey, $payload, now()->addHours(self::CACHE_TTL_HOURS));
                
                return response()->json([
                    'status' => 'comparison',
                    'diff' => $diff
                ], 200);
            } catch (\Exception $e) {
                Log::error('Failed to calculate diff', [
                    'error' => $e->getMessage(),
                    'payload_id' => $payload['id']
                ]);
                
                return response()->json([
                    'error' => 'Failed to process payload comparison'
                ], 500);
            }
        } else {
            // First request: store payload in cache
            try {
                Cache::put($cacheKey, $payload, now()->addHours(self::CACHE_TTL_HOURS));
                
                return response()->json([
                    'status' => 'cached',
                    'message' => 'Payload cached successfully. Send second payload to get comparison.'
                ], 200);
            } catch (\Exception $e) {
                Log::error('Failed to cache payload', [
                    'error' => $e->getMessage(),
                    'payload_id' => $payload['id']
                ]);
                
                return response()->json([
                    'error' => 'Failed to cache payload'
                ], 500);
            }
        }
    }

    /**
     * Calculate structured diff between two payloads
     */
    private function calculateDiff(array $old, array $new): array
    {
        $diff = [
            'added' => [],
            'removed' => [],
            'modified' => [],
            'unchanged' => []
        ];

        // Get all keys from both arrays
        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($allKeys as $key) {
            $oldValue = $old[$key] ?? null;
            $newValue = $new[$key] ?? null;

            if (!array_key_exists($key, $old)) {
                // Key was added
                $diff['added'][$key] = $newValue;
            } elseif (!array_key_exists($key, $new)) {
                // Key was removed
                $diff['removed'][$key] = $oldValue;
            } elseif ($this->isArray($oldValue) && $this->isArray($newValue)) {
                // Both are arrays - compare nested structures
                $nestedDiff = $this->compareArrays($oldValue, $newValue, $key);
                if (!empty($nestedDiff['added']) || !empty($nestedDiff['removed']) || !empty($nestedDiff['modified'])) {
                    $diff['modified'][$key] = $nestedDiff;
                } else {
                    $diff['unchanged'][$key] = $newValue;
                }
            } elseif ($oldValue !== $newValue) {
                // Value was modified
                $diff['modified'][$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            } else {
                // Value unchanged
                $diff['unchanged'][$key] = $newValue;
            }
        }

        return $diff;
    }

    /**
     * Compare two arrays (for nested structures like images and variants)
     */
    private function compareArrays(array $oldArray, array $newArray, string $parentKey = ''): array
    {
        $diff = [
            'added' => [],
            'removed' => [],
            'modified' => []
        ];

        // Check if arrays are indexed (list) or associative
        $isIndexed = $this->isIndexedArray($oldArray) || $this->isIndexedArray($newArray);

        if ($isIndexed) {
            // For indexed arrays (like images, variants), compare by id if available
            $oldById = [];
            $newById = [];

            foreach ($oldArray as $item) {
                if (isset($item['id'])) {
                    $oldById[$item['id']] = $item;
                }
            }

            foreach ($newArray as $item) {
                if (isset($item['id'])) {
                    $newById[$item['id']] = $item;
                }
            }

            // Find added items
            foreach ($newById as $id => $item) {
                if (!isset($oldById[$id])) {
                    $diff['added'][] = $item;
                } else {
                    // Compare item fields
                    $itemDiff = $this->calculateDiff($oldById[$id], $item);
                    if (!empty($itemDiff['added']) || !empty($itemDiff['removed']) || !empty($itemDiff['modified'])) {
                        $diff['modified'][] = [
                            'id' => $id,
                            'changes' => array_filter([
                                'added' => $itemDiff['added'] ?? [],
                                'removed' => $itemDiff['removed'] ?? [],
                                'modified' => $itemDiff['modified'] ?? []
                            ])
                        ];
                    }
                }
            }

            // Find removed items
            foreach ($oldById as $id => $item) {
                if (!isset($newById[$id])) {
                    $diff['removed'][] = $item;
                }
            }
        } else {
            // For associative arrays, use standard diff
            $allKeys = array_unique(array_merge(array_keys($oldArray), array_keys($newArray)));
            
            foreach ($allKeys as $key) {
                $oldValue = $oldArray[$key] ?? null;
                $newValue = $newArray[$key] ?? null;

                if (!array_key_exists($key, $oldArray)) {
                    $diff['added'][$key] = $newValue;
                } elseif (!array_key_exists($key, $newArray)) {
                    $diff['removed'][$key] = $oldValue;
                } elseif ($oldValue !== $newValue) {
                    if ($this->isArray($oldValue) && $this->isArray($newValue)) {
                        $nestedDiff = $this->compareArrays($oldValue, $newValue, $key);
                        if (!empty($nestedDiff['added']) || !empty($nestedDiff['removed']) || !empty($nestedDiff['modified'])) {
                            $diff['modified'][$key] = $nestedDiff;
                        }
                    } else {
                        $diff['modified'][$key] = [
                            'old' => $oldValue,
                            'new' => $newValue
                        ];
                    }
                }
            }
        }

        return $diff;
    }

    /**
     * Check if value is an array
     */
    private function isArray($value): bool
    {
        return is_array($value);
    }

    /**
     * Check if array is indexed (list) vs associative
     */
    private function isIndexedArray(array $array): bool
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }
}

