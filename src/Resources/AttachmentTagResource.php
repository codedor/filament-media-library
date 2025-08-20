<?php

namespace Codedor\MediaLibrary\Resources;

use Codedor\MediaLibrary\Models\AttachmentTag;
use Codedor\MediaLibrary\Resources\AttachmentTagResource\Pages\ManageAttachmentTags;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttachmentTagResource extends Resource
{
    protected static ?string $model = AttachmentTag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->label(__('filament-media-library::admin.title'))
                ->required(),

            Select::make('parent')
                ->label(__('filament-media-library::admin.parent'))
                ->relationship('parent', 'title'),

            Toggle::make('is_hidden')
                ->label(__('filament-media-library::admin.is hidden'))
                ->helperText(__('filament-media-library::admin.is hidden help text'))
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament-media-library::admin.title')),

                Tables\Columns\TextColumn::make('parent.title')
                    ->label(__('filament-media-library::admin.parent')),

                Tables\Columns\IconColumn::make('is_hidden')
                    ->label(__('filament-media-library::admin.is hidden'))
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttachmentTags::route('/'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-media-library::admin.attachment tags title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-media-library::admin.attachment tags title');
    }

    public static function getModelLabel(): string
    {
        return __('filament-media-library::admin.attachment tags title singular');
    }
}
