<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    
    protected static function boot()
    {
        parent::boot();
        
        static::updated(function ($ticket) {
            // Get the changed attributes
            $changed = $ticket->getDirty();
            
            // If status was changed to completed, notify the client
            if (isset($changed['status']) && $changed['status'] === 'completed') {
                $creator = User::find($ticket->created_by);
                if ($creator) {
                    Notification::make()
                        ->title('Ticket Completed')
                        ->body("Your ticket '{$ticket->title}' has been marked as completed. Please rate the service.")
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->button()
                                ->url(route('client.tickets.show', $ticket->id)),
                        ])
                        ->sendToDatabase($creator);
                }
            }
            
            // If ticket was assigned to a worker, notify them
            if (isset($changed['assigned_to']) && $changed['assigned_to']) {
                $worker = User::find($ticket->assigned_to);
                if ($worker) {
                    Notification::make()
                        ->title('New Ticket Assigned')
                        ->body("You have been assigned to ticket '{$ticket->title}'.")
                        ->actions([
                            \Filament\Notifications\Actions\Action::make('view')
                                ->button()
                                ->url(route('worker.tickets.show', $ticket->id)),
                        ])
                        ->sendToDatabase($worker);
                }
            }
            
            Notification::make()
                ->title('Ticket Updated')
                ->body("The ticket titled '{$ticket->title}' has been updated.")
                ->success()
                ->send();
        });
        
        static::created(function ($ticket) {
            // Notify admin about new ticket
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                Notification::make()
                    ->title('New Ticket Created')
                    ->body("A new ticket titled '{$ticket->title}' has been created.")
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->button()
                            ->url(route('admin.tickets.show', $ticket->id)),
                    ])
                    ->sendToDatabase($admin);
            }
            
            Notification::make()
                ->title('New Ticket Created')
                ->body("A new ticket titled '{$ticket->title}' has been created.")
                ->success()
                ->send();
        });
    }

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'created_by',
        'assigned_to',
        'category',
        'due_date',
        'preferred_date',
        'rating',
        'rating_comment',
        'specialty_required',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'preferred_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    // Alias relationships for Filament
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
