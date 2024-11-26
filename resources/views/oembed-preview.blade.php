<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OEmbed Preview</title>
    <style>
        html, body {
            height: 100%; /* Ensure the body and html fill the iframe */
            margin: 0;
            padding: 0;
            background: #333 !important; /* Keep background transparent */
        }

        div {
            background: transparent !important; /* Keep background transparent */
            padding: 10px;
        }
        #embed-container {
            margin:0 auto;
        }
        .oembed-responsive-container {
            position: relative;
        }
        .oembed-responsive-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

    </style>
</head>
<body>
<div id="embed-container">
    {{ $oembed->render() }}
</div>
    <script>
        const container = document.querySelector('#embed-container');

        const resizeObserver = new ResizeObserver((entries) => {
            for (let entry of entries) {
                const { width, height } = entry.contentRect;
                const iframe = window.parent.document.querySelector('iframe#oembed-preview-frame');

                if (iframe) {
                    iframe.style.height = height + 'px';
                } else {
                    console.warn('Iframe element not found!');
                }
            }
        });

        // Observe size changes
        resizeObserver.observe(container);
    </script>

</body>

