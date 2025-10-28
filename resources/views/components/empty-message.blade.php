@props([
    'table' => false,
    'div' => false,
    'textAlign' => 'center',
    'colspan' => '100%',
    'message' => $emptyMessage,
])

@if($table)
    <tr>
        <td class="text-{{ $textAlign }} not-found" colspan="{{ $colspan }}">{{ __($message) }}</td>
    </tr>
@elseif($div) 
    <div class="text-{{ $textAlign }}">
        {{ __($message) }}
    </div>
@else 
    {{ __($message) }}
@endif
