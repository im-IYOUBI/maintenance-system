<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use App\Models\Ticket;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\TicketResource\Pages;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required()->maxLength(255),
            Textarea::make('description')->nullable(),

            Select::make('category')
                ->required()
                ->options([
                    'plumbing' => 'Plumbing',
                    'electricity' => 'Electricity',
                    'hvac' => 'HVAC',
                    'other' => 'Other',
                ]),

            Select::make('priority')
                ->required()
                ->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ])
                ->default('medium'),

            Select::make('status')
                ->required()
                ->options([
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])
                ->default('pending'),

            TextInput::make('location')->nullable(),
            
            DatePicker::make('preferred_date')
                ->label('Client Preferred Date')
                ->nullable(),
                
            Select::make('specialty_required')
                ->label('Specialty Required')
                ->options([
                    'plumbing' => 'Plumbing',
                    'electrical' => 'Electrical',
                    'hvac' => 'HVAC',
                    'carpentry' => 'Carpentry',
                    'painting' => 'Painting',
                    'general' => 'General Maintenance',
                    'other' => 'Other',
                ])
                ->nullable(),

            Select::make('assigned_to')
                ->relationship('assignedTo', 'name', function ($query) {
                    // Only show technicians in the dropdown
                    return $query->role('technician');
                })
                ->label('Assigned Technician')
                ->nullable(),

            Select::make('created_by')
                ->relationship('createdBy', 'name')
                ->label('Created By')
                ->disabled()
                ->default(auth()->id()),
                
            DateTimePicker::make('completed_at')
                ->label('Completion Date/Time')
                ->nullable()
                ->disabled(fn (string $context): bool => $context === 'create'),
                
            Forms\Components\Section::make('Rating Information')
                ->schema([
                    TextInput::make('rating')
                        ->label('Client Rating (1-5)')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(5)
                        ->nullable()
                        ->disabled(fn (string $context): bool => $context === 'create'),
                        
                    Textarea::make('rating_comment')
                        ->label('Client Feedback')
                        ->nullable()
                        ->disabled(fn (string $context): bool => $context === 'create'),
                ])
                ->collapsed()
                ->visible(fn ($record) => $record && $record->status === 'completed'),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category')->badge(),
                TextColumn::make('priority')->badge()
                    ->colors([
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                    ]),
                TextColumn::make('status')->badge()
                    ->colors([
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    ]),
                TextColumn::make('preferred_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label('Technician')
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn ($state) => $state ? "★ {$state}/5" : '-'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->label('Status'),
                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->label('Priority'),
                SelectFilter::make('category')
                    ->options([
                        'plumbing' => 'Plumbing',
                        'electricity' => 'Electricity',
                        'hvac' => 'HVAC',
                        'other' => 'Other',
                    ])
                    ->label('Category'),
                SelectFilter::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->label('Assigned Technician'),
                SelectFilter::make('specialty_required')
                    ->options([
                        'plumbing' => 'Plumbing',
                        'electrical' => 'Electrical',
                        'hvac' => 'HVAC',
                        'carpentry' => 'Carpentry',
                        'painting' => 'Painting',
                        'general' => 'General Maintenance',
                        'other' => 'Other',
                    ])
                    ->label('Specialty Required'),
            ])
            ->actions([
                Action::make('assign')
                    ->label('Assign to Technician')
                    ->form([
                        Select::make('assigned_to')
                            ->label('Technician')
                            ->options(
                                User::role('technician')
                                    ->pluck('name', 'id')
                            )
                            ->required(),
                    ])
                    ->action(function (Ticket $record, array $data): void {
                        $record->update([
                            'assigned_to' => $data['assigned_to'],
                            'status' => 'in_progress',
                        ]);
                    })
                    ->visible(fn (Ticket $record): bool => $record->status === 'pending' && !$record->assigned_to),
                    
                Action::make('markCompleted')
                    ->label('Mark as Completed')
                    ->action(function (Ticket $record): void {
                        $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Ticket $record): bool => $record->status === 'in_progress'),
            ])
            ->bulkActions([
                BulkAction::make('markAsCompleted')
                    ->label('Mark as Completed')
                    ->action(fn (Collection $records) => $records->each->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]))
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion(),
                BulkAction::make('markAsInProgress')
                    ->label('Mark as In Progress')
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'in_progress']))
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
            ]);
        
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTickets::route('/'),
        ];
    }
}
