<x-mail::message>
# {{ $name }} downloaded {{ $materialTitle }}

<x-mail::table>
| | |
|:--|:--|
@foreach ($details as $label => $value)
| **{{ $label }}** | {{ str_replace(["\r\n", "\n", '|'], [' ', ' ', '/'], $value) }} |
@endforeach
</x-mail::table>

Reply to this email to follow up with {{ $name }}.
</x-mail::message>
