<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class CurrentDateTime extends Component
{
    public function render()
    {
        return view('livewire.current-date-time')->with([
            'now' => Carbon::now(),
        ]);;
    }
}
