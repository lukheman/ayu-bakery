<?php

namespace App\Livewire\Guest;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Ayu Bakery - Toko Roti & Kue Terbaik')]
class LandingPage extends Component
{
    public function render()
    {
        return view('livewire.guest.landing-page');
    }
}
