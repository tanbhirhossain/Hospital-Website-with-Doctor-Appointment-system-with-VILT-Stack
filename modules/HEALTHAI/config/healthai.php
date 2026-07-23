<?php

// config/healthai.php

return [
    /*
    |--------------------------------------------------------------------------
    | Ollama LLM Configuration
    |--------------------------------------------------------------------------
    | Set these in your .env file:
    |   OLLAMA_URL=http://192.168.10.200:11434
    |   OLLAMA_MODEL=gemma4:31b-cloud
    |   OLLAMA_TIMEOUT=60
    |   HOST_API_TOKEN=your_mis_api_token
    */
    'ollama_url'     => env('OLLAMA_URL',   'http://192.168.10.200:11434'),
    'ollama_model'   => env('OLLAMA_MODEL', 'gemma4:31b-cloud'),
    'ollama_timeout' => (int) env('OLLAMA_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | MIS ERP API Token
    |--------------------------------------------------------------------------
    */
    'mis_api_token'  => env('HOST_API_TOKEN', '37|VXqXcaZAGdQanG21gzibLtp45dpnqXecishdBztl2ac596ac'),
];
