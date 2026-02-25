@props(['data'])

@php
    // Ensure data is an array
    if (!is_array($data)) {
        $data = [];
    }

    // Recursively clean data: remove null values and cast to appropriate types
    $cleanData = function($array) use (&$cleanData) {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $cleaned = $cleanData($value);
                if (!empty($cleaned)) {
                    $result[$key] = $cleaned;
                }
            } elseif ($value !== null) {
                $result[$key] = is_string($value) ? $value : (is_bool($value) ? $value : (is_int($value) ? $value : (string) $value));
            }
        }
        return $result;
    };

    $data = $cleanData($data);

    // Generate JSON with error handling
    $json = '';
    try {
        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        // If encoding fails, log error and skip output
        logger()->error('Structured data JSON encoding failed: ' . $e->getMessage(), ['data' => $data]);
        $json = '';
    }
@endphp

@if (!empty($json))
    <script type="application/ld+json">
        {!! $json !!}
    </script>
@endif
