<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function handle(TicketClassifier $classifier)
    {
        $result = $classifier->classify($this->ticket->subject, $this->ticket->body);

        $update = [
            'category' => $this->ticket->category ?: $result['category'],
            'explanation' => $result['explanation'],
            'confidence' => $result['confidence'],
        ];

        $this->ticket->update($update);
    }
}
