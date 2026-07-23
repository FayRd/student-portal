<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class ConfirmationModal extends Component
{
    public bool   $show       = false;
    public string $title      = '';
    public string $message    = '';
    public string $action     = '';
    public array  $params     = [];
    public int    $countdown  = 5;
    public string $dangerLabel = 'Confirm';

    #[On('confirm')]
    public function open(string $title, string $message, string $action, array $params = [], string $dangerLabel = 'Confirm'): void
    {
        $this->title      = $title;
        $this->message    = $message;
        $this->action     = $action;
        $this->params     = $params;
        $this->countdown  = 5;
        $this->dangerLabel = $dangerLabel;
        $this->show       = true;
    }

    public function confirm(): void
    {
        $this->show = false;
        $this->dispatch($this->action, ...$this->params);
    }

    public function cancel(): void
    {
        $this->show = false;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.confirmation-modal');
    }
}
