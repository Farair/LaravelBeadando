<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use App\Models\User;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function getNavigationGroup(): string
    {
        return __('module_names.navigation_groups.administration');
    }

    public static function getModelLabel(): string
    {
        return __('module_names.task.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('module_names.tasks.plural_label');
    }

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Task Details')
                ->schema([
                    TextInput::make('title')->label(__('fields.title'))
                        ->required()
                        ->maxLength(255),
                    /* TextInput::make('description')->label(__('fields.description'))
                        ->required()
                        ->maxLength(65535)
                        ->columnSpanFull(), */
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->required()
                        ->label(__('fields.user')),
                    /*Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label(__('fields.user'))
                        ->default(auth()->user()->can('update worksheets') ? auth()->user()->id : null)
                        ->disabled(auth()->user()->can('update worksheets') ? true : false),*/
                    /*Forms\Components\Select::make('device_id')->label(__('module_names.devices.label'))
                        ->relationship('device', 'name')
                        ->required(), */
                    Forms\Components\DatePicker::make('due_date')->label(__('fields.due_date'))
                        ->required(),
                    Forms\Components\Textarea::make('description')->label(__('fields.description'))
                        ->required()
                        ->maxLength(65535)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('done')->label(__('fields.done')),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('done')
                ->boolean()
                ->trueIcon('heroicon-o-check-badge')
                ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.name')->sortable()->searchable()->label('Employee'),
                Tables\Columns\TextColumn::make('due_date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->sortable()
                    ->label(__('fields.due_date'))
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->format('Y-m-d');
                    })
                    ->color(fn ($record) => $record->due_date < now() && !$record->done ? 'danger' : null)
                    ->weight(fn ($record) => $record->due_date < now() && !$record->done ? 'bold' : null)
                    ->icon(fn ($record) => $record->due_date < now() && !$record->done ? 'heroicon-o-tag' : null),
                Tables\Columns\TextColumn::make('created_at')->label(__('fields.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }    
}
