<?php

namespace App\Filament\Resources\ChainGroupResource\Pages;

use App\Filament\Resources\ChainGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\AifabuService;
use Illuminate\Database\Eloquent\Model;

class EditChainGroup extends EditRecord
{
    protected static string $resource = ChainGroupResource::class;

    
} 