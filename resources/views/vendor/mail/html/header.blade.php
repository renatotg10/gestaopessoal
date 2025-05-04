@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://static.wikia.nocookie.net/logocomics/images/e/e5/Spawn_b8112_450x450.png/revision/latest?cb=20180107185442" class="logo" alt="Laravel Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
