<?php

namespace App\Filament\Resources\GeneratedContents\Tables;

use App\Models\GeneratedContent;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GeneratedContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active'   => 'success',
                        'draft'    => 'gray',
                        'inactive' => 'warning',
                        'archived' => 'danger',
                    }),
                TextColumn::make('admin.name')
                    ->label('Aangemaakt door'),
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'    => 'Concept',
                        'inactive' => 'Inactief',
                        'active'   => 'Actief',
                        'archived' => 'Gearchiveerd',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Goedkeuren')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (GeneratedContent $record): void {
                        $record->reviews()->create([
                            'admin_id'    => auth()->id(),
                            'approved'    => true,
                            'reviewed_at' => now(),
                        ]);
                        $record->update(['status' => 'active']);
                    })
                    ->visible(fn (GeneratedContent $record) => $record->status !== 'active'),

                Action::make('reject')
                    ->label('Afkeuren')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Textarea::make('feedback')
                            ->label('Reden voor afkeuring')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (GeneratedContent $record, array $data): void {
                        $record->reviews()->create([
                            'admin_id'    => auth()->id(),
                            'approved'    => false,
                            'feedback'    => $data['feedback'],
                            'reviewed_at' => now(),
                        ]);
                        $record->update(['status' => 'inactive']);
                    })
                    ->visible(fn (GeneratedContent $record) => $record->status !== 'inactive'),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_approve')
                        ->label('Bulk goedkeuren')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($records): void {
                            $records->each(function (GeneratedContent $record): void {
                                $record->reviews()->create([
                                    'admin_id'    => auth()->id(),
                                    'approved'    => true,
                                    'reviewed_at' => now(),
                                ]);
                                $record->update(['status' => 'active']);
                            });
                        })
                        ->requiresConfirmation(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
