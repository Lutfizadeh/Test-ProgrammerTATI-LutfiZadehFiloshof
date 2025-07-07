<?php

namespace App\Filament\Admin\Resources;

use App\Models\Log;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Resources\LogResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\LogResource\RelationManagers;

class LogResource extends Resource
{
    protected static ?string $model = Log::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        $currentUser = Auth::user();

        if ($currentUser->hasRole('super_admin')) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()
            ->where(function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id)
                    ->orWhereHas('user', function ($userQuery) use ($currentUser) {
                        $userQuery->where('atasan_id', $currentUser->id);
                    });
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->label('User ID')
                    ->default(Auth::id()),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Hidden::make('status')
                    ->default('Pending')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Disetujui',
                        'danger' => 'Ditolak',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'Pending',
                        'heroicon-o-check-circle' => 'Disetujui',
                        'heroicon-o-x-circle' => 'Ditolak',
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('setuju')
                    ->label('Setuju')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->color('success')
                    ->visible(function ($record) {
                        $currentUser = Auth::user();

                        return $record->status === 'Pending' &&
                            ($record->user->atasan_id === $currentUser->id || $currentUser->hasRole('super_admin'));
                    })
                    ->action(function ($record) {
                        $record->update(['status' => 'Disetujui']);

                        $record->update(['status' => 'Disetujui']);

                        Notification::make()
                            ->title('Log Disetujui')
                            ->body('Log berhasil disetujui.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->visible(function ($record) {
                        $currentUser = Auth::user();

                        return $record->status === 'Pending' &&
                            ($record->user->atasan_id === $currentUser->id || $currentUser->hasRole('super_admin'));
                    })
                    ->action(function ($record) {
                        $record->update(['status' => 'Ditolak']);

                        Notification::make()
                            ->title('Log Ditolak')
                            ->body('Log berhasil ditolak.')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLogs::route('/'),
        ];
    }
}
