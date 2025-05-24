<div wire:poll.{{60 - $now->second }}s>
    @php
    $weekDays = ['日', '月', '火', '水', '木', '金', '土'];
    @endphp
    <p class="attendance__date">
        {{ $now->year }}年{{ $now->month }}月{{ $now->day }}日({{ $weekDays[$now->dayOfWeek] }})
    </p>
    <p class="attendance__time">
        {{ $now->format('H:i') }}
    </p>
</div>