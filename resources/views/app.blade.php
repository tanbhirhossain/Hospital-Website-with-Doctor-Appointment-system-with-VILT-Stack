<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
  html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    /* Remove vertical gap caused by transform scaling */
    min-height: 100%;
    display: flex;
    flex-direction: column;
  }

  #app {
    width: 100%;
    flex: 1;
  }

  /* 1. BELOW 15" (Targets 14" laptops @ 1536px width or lower) */
  @media screen and (max-width: 1536px) {
    #app {
      zoom: 80%; /* Works perfectly in Chrome/Edge, no footer gap */
    }
    
    /* Fallback for Firefox (Firefox doesn't support zoom) */
    @-moz-document url-prefix() {
      #app {
        transform: scale(0.8);
        transform-origin: top center;
        width: 125%; /* 100 / 0.8 */
      }
    }
  }

  /* 2. BELOW 19" (Targets 1600px - 1700px width range) */
  @media screen and (min-width: 1537px) and (max-width: 1700px) {
    #app {
      zoom: 90%;
    }

    @-moz-document url-prefix() {
      #app {
        transform: scale(0.9);
        transform-origin: top center;
        width: 111.11%; /* 100 / 0.9 */
      }
    }
  }

  /* 3. 23" AND ABOVE (Standard 1920px+ monitors) */
  @media screen and (min-width: 1701px) {
    #app {
      zoom: 100%;
    }
  }
</style>

        @routes
        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
