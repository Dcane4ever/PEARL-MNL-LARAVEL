@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ asset('image/PearlMNL_LOGO.png') }}" class="logo" alt="The Pearl Manila Logo">
<div style="font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 8px;">
{{ trim($slot) !== '' ? trim($slot) : 'The Pearl Manila' }}
</div>
</a>
</td>
</tr>
