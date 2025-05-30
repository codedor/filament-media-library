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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required(),

            Select::make('parent')
                ->relationship('parent', 'title'),

            Toggle::make('is_hidden')
                ->helperText('Hide images with this tag from the media library and picker')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('parent.title'),
                Tables\Columns\IconColumn::make('is_hidden')->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttachmentTags::route('/'),
        ];
    }
}
