<?php

namespace App\Filament\Resources\StateResource\RelationManagers;

use App\Models\city;
use App\Models\country;
use App\Models\state;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $recordTitleAttribute = 'firstname';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('firstname')->required(),
                TextInput::make('lastname')->required(),
                TextInput::make('address')->required(),
                DatePicker::make('dob')->format('d/m/y')->maxDate(now())->required(),
                DatePicker::make('date_hired')->format('d/m/y')->maxDate(now())->required(),
                Select::make('country_id')
                    ->label('country')
                    ->options(country::all()->pluck('name', 'id')->toArray())
                    ->reactive(),
                //->afterStateUpdated(fn (callable $set) => $set('state_id', 'null')),
                Select::make('state_id')->required()
                    ->label('state')
                    ->options(function (callable $get) {
                        $country = Country::find($get('country_id'));
                        if (!$country) {
                            return state::all()->pluck('name', 'id');
                        }
                        return $country->state()->pluck('name', 'id'); //state here is from country model
                        //all there are to create dependent select
                    })
                    ->reactive(),
                //->afterStateUpdated(fn (callable $set) => $set('country_id', 'null')),
                Select::make('city_id')->required()
                    ->label('city')
                    ->options(function (callable $get) {
                        $state = state::find($get('state_id'));
                        if (!$state) {
                            return city::all()->pluck('name', 'id');
                        }
                        return $state->city()->pluck('name', 'id'); //state here is from country model
                    })
                    ->reactive(),
                //->afterStateUpdated(fn (callable $set) => $set('city_id', 'null')),
                // select::make('city_id')->required()
                //     ->relationship('city', 'name'),
                select::make('department_id')->required()
                    ->relationship('department', 'name'),

                TextInput::make('zip_code')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('firstname')->searchable()->sortable(),
                TextColumn::make('lastname')->searchable()->sortable(),
                TextColumn::make('address')->searchable()->sortable(),
                TextColumn::make('dob')->searchable()->sortable(),
                TextColumn::make('date_hired')->searchable()->sortable(),
                TextColumn::make('zip_code')->searchable()->sortable(),
                TextColumn::make('country.name'),
                TextColumn::make('state.name'),
                TextColumn::make('city.name'),
                TextColumn::make('department.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
