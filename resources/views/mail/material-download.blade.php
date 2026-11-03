<x-mail::message>
# Hi {{ $name }},

Thank you for your interest in PixaProof. Here is **{{ $materialTitle }}**.

<x-mail::button :url="$downloadUrl">
Download the PDF
</x-mail::button>

The link works for seven days. If you'd like to see PixaProof on your own images, reply to this email and we'll set up a demo.

Regards,<br>
The PixaProof team at Innov8tif

<x-mail::subcopy>
You received this email because this address was entered to download a PDF on {{ parse_url(config('app.url'), PHP_URL_HOST) }}. If that wasn't you, you can ignore it.
</x-mail::subcopy>
</x-mail::message>
