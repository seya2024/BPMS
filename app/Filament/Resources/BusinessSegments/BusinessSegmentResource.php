<?php

namespace App\Filament\Resources\BusinessSegments;

use App\Filament\Resources\BusinessSegments\Pages\CreateBusinessSegment;
use App\Filament\Resources\BusinessSegments\Pages\EditBusinessSegment;
use App\Filament\Resources\BusinessSegments\Pages\ListBusinessSegments;
use App\Filament\Resources\BusinessSegments\Pages\ViewBusinessSegment;
use App\Filament\Resources\BusinessSegments\Schemas\BusinessSegmentForm;
use App\Filament\Resources\BusinessSegments\Schemas\BusinessSegmentInfolist;
use App\Filament\Resources\BusinessSegments\Tables\BusinessSegmentsTable;
use App\Models\BusinessSegment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BusinessSegmentResource extends Resource
{
    protected static ?string $model = BusinessSegment::class;


    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChevronRight;
    protected static ?string $navigationLabel = 'Business Segment';
    protected static ?string $recordTitleAttribute = 'BusinessSegment';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return BusinessSegmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BusinessSegmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusinessSegmentsTable::configure($table);
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
            'index' => ListBusinessSegments::route('/'),
           // 'create' => CreateBusinessSegment::route('/create'),
            'view' => ViewBusinessSegment::route('/{record}'),
            //'edit' => EditBusinessSegment::route('/{record}/edit'),
        ];
    }
}
