<?php

namespace App\Livewire\Guest;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('AdminPro - Modern Admin Dashboard Template')]
class LandingPage extends Component
{
    public function render()
    {
        return view('livewire.guest.landing-page');
    }
}
