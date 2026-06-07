<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Models\Gallery;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;
    protected static ?int $navigationSort = 3;

    public static function getNavigationIcon(): string { return 'heroicon-o-photo'; }
    public static function getNavigationGroup(): ?string { return 'Catalogue'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('title')->required()->maxLength(255),

                Select::make('category')
                    ->options([
                        'bespoke' => 'Bespoke',
                        'dry_cleaning' => 'Dry Cleaning',
                        'alteration' => 'Alteration',
                        'bridal' => 'Bridal',
                        'corporate' => 'Corporate',
                    ]),

                Textarea::make('description')->rows(2)->columnSpanFull(),

                FileUpload::make('image')
                    ->image()
                    ->required()
                    ->directory('gallery')
                    ->columnSpanFull(),

                Toggle::make('is_active')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->square(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->reorderable('sort_order')
            ->actions([\Filament\Actions\EditAction::make()])
            ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
