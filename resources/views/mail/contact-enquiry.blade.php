<x-mail::message>
# New enquiry from the PixaProof site

<x-mail::table>
| | |
|:--|:--|
@foreach ($details as $label => $value)
| **{{ $label }}** | {{ str_replace(["\r\n", "\n", '|'], [' ', ' ', '/'], $value) }} |
@endforeach
</x-mail::table>

Reply to this email to answer {{ $name }}.
</x-mail::message>
