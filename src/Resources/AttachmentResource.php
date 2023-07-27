<?php

namespace Codedor\MediaLibrary\Resources;

use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Resources\AttachmentResource\Pages;
use Codedor\TranslatableTabs\Forms\TranslatableTabs;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;

class AttachmentResource extends Resource
{
    protected static ?string $model = Attachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    public static function getNavigationLabel(): string
    {
        return __('filament_media.dashboard navigation title');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TranslatableTabs::make('Translations')
                ->icon('heroicon-o-status-online')
                ->iconColor('success')
                ->columnSpan(['lg' => 2])
                ->defaultFields([
                    Placeholder::make('name')
                        ->content(fn (Attachment $record) => $record->name),

                    Grid::make(2)->schema([
                        Placeholder::make('extension')
                            ->content(fn (Attachment $record) => $record->extension),

                        Placeholder::make('mime_type')
                            ->content(fn (Attachment $record) => $record->mime_type),

                        Placeholder::make('type')
                            ->content(fn (Attachment $record) => $record->type),

                        Placeholder::make('size')
                            ->content(fn (Attachment $record) => "{$record->formattedInMbSize} MB"),

                        Placeholder::make('width')
                            ->content(fn (Attachment $record) => $record->width)
                            ->hidden(fn (Attachment $record) => ! $record->isImage()),

                        Placeholder::make('height')
                            ->content(fn (Attachment $record) => $record->height)
                            ->hidden(fn (Attachment $record) => ! $record->isImage()),
                    ]),

                    Select::make('tags')
                        ->relationship('tags', 'title')
                        ->preload()
                        ->multiple(),

                    Section::make('Preview')
                        ->collapsed()
                        ->schema([
                            Placeholder::make('image')
                                ->hidden(fn (Attachment $record) => ! $record->isImage())
                                ->content(fn (Attachment $record) => new HtmlString(
                                    "<a href=\"$record->url\" target=\"_blank\"><img src=\"{$record->url}\" /></a>"
                                )),
                        ]),
                ])
                ->translatableFields(fn () => [
                    // TextInput::make('translated_name'),
                    TextInput::make('alt')->label('Alt text'),
                    TextInput::make('caption'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $types = Attachment::select('type')
            ->groupBy('type')
            ->orderBy('type')
            ->pluck('type')
            ->mapWithKeys(fn ($type) => [$type => $type]);

        $mimeTypes = Attachment::select('mime_type')
            ->groupBy('mime_type')
            ->orderBy('mime_type')
            ->pluck('mime_type')
            ->mapWithKeys(fn ($mime) => [$mime => $mime]);

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (Attachment $record) => new HtmlString(
                        '<strong>' . Str::limit($record->name, 20) . '</strong>'
                    )),

                TextColumn::make('created_at')
                    ->label('Uploaded at')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn (Attachment $record) => $record->created_at->diffForHumans()),

                TextColumn::make('image')
                    ->view('filament-media-library::components.attachment-list'),
            ])
            ->filters([
                Filters\SelectFilter::make('type')
                    ->options($types)
                    ->multiple(),

                Filters\SelectFilter::make('tags')
                    ->relationship('tags', 'title')
                    ->multiple(),

                Filters\SelectFilter::make('mime_type')
                    ->options($mimeTypes)
                    ->multiple(),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])
            ->actions([
                Tables\Actions\Action::make('format')
                    ->icon('heroicon-o-scissors')
                    ->hidden(fn (Attachment $record) => ! $record->isImage())
                    ->action(function (Tables\Actions\Action $action) {
                        /** @var Component $livewire */
                        $livewire = $action->getTable()->getLivewire();

                        $livewire->emit(
                            'filament-media-library::open-formatter-attachment-modal',
                            $action->getRecord()->id
                        );

                        $livewire->dispatch(
                            'open-modal',
                            id: 'filament-media-library::formatter-attachment-modal',
                        );
                    }),

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
            'index' => Pages\ListAttachments::route('/'),
            'edit' => Pages\EditAttachment::route('/{record}/edit'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [100];
    }
}
