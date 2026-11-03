<x-mail::message>
# Hi {{ $name }},

Thank you for getting in touch about PixaProof. We've received your message, and someone from our team will reply within three working days.

If anything else comes up in the meantime, just reply to this email.

Regards,<br>
The PixaProof team at Innov8tif

<x-mail::subcopy>
You received this email because this address was entered in the contact form on {{ parse_url(config('app.url'), PHP_URL_HOST) }}. If that wasn't you, you can ignore it.
</x-mail::subcopy>
</x-mail::message>
